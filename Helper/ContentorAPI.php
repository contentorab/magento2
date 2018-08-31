<?php

namespace Contentor\LocalizationApi\Helper;

class ContentorAPI extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Contentor\LocalizationApi\Helper\Data $helper,
			\Magento\Catalog\Model\ProductFactory $productFactory,
			\Magento\Framework\App\ResourceConnection $resource,
			\Magento\Store\Model\StoreManagerInterface $storeManager
			//\Psr\Log\LoggerInterface $logger
			) {
				$this->_helper = $helper;
				$this->_productFactory = $productFactory;
				$this->_resource = $resource;
				$this->_storeManager = $storeManager;
				$this->_logger = $context->getLogger();
				$this->_scopeConfig = $context->getScopeConfig();
				parent::__construct($context);
	}
	
	public function getFieldData($entity, $entityType, $extraField=false) {
		$messages = array();
		if($entityType == 'product') {
			$sku = $entity->getSku();
			$productID = $entity->getId();
			$fieldArray = unserialize($this->_helper->getConfig('contentor_options/fieldDetails/productFieldDetails'));

			if(is_array($fieldArray)) {
				$fields[] = array('id' => 'auto_sku',
						'type' => 'internal',
						'data' => 'string',
						'value' => $sku);

				// Add extra content if sent
				if($extraField) {
					$fields[] = array('id' => 'extraField',
							'name' => $extraField['name'],
							'type' => $extraField['type'],
							'data' => $extraField['data'],
							'value' => $extraField['value']);
				}
	
				foreach($fieldArray as $field) {
					$textLocale = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $field['store']);
					
					if($field['attribute'] == 'productURL') {
						$value = $entity->setStoreId($field['store'])->getUrlInStore();
						$name = 'Product URL';
					} else {
						$attribute	= $entity->getResource()->getAttribute($field['attribute']);
						$value 		=	$entity->getResource()->getAttributeRawValue($productID, $field['attribute'], $field['store']);
						$name 		=	$attribute->getFrontendLabel();
					}

					if(!isset($n[$field['attribute']])) {
						$n[$field['attribute']] = 1;
					} else {
						$n[$field['attribute']]++;
					}
					$id = $field['attribute'] . '_' . sprintf("%03d", $n[$field['attribute']]);

					if($value != '') {
						$fields[] = array('id'=>$id,
								'name'=>$name,
								'type'=>$field['type'],
								'data'=>$field['data'],
								'value'=>$value);
					} else {
						if(isset($fields['required'])) {
							$messages[] = 'Required field: [' . $field['attribute'] . '] empty';
						}
					}
				}
			} else {
				$messages[] = 'No settings found';
			}
			
		} 
		
		if(!count($messages)) {
			return $fields;
		} else {
			$message = implode('<br>', $messages);
			$this->_logger->critical('No data sent!<br>' . $message);
			return false;
		} 
	}
	
	public function createRequest($sourceLocale, $targetLocale, $fields, $type, $previd=false) {
		$data['language']['source'] = str_replace('_', '-', $sourceLocale);
		$data['language']['target'] = str_replace('_', '-', $targetLocale);
		$data['type'] = $type;
		if($previd && $type == 'update') {
			$data['previous'] = $previd;
		}
		$data['fields'] = $fields;
		return $data;
	}
	
	public function logSent($contentorID, $sku, $sourceLocale, $targetLocale, $targetID, $product, $type) {
		$connection = $this->_resource->getConnection('core_write');
		
		$table = $this->_resource->getTableName('contentor_products');
		
		$query = "INSERT INTO " . $table . "
						  (contentor_id, sku, source_locale, target_locale, target_store, sent_time, state, type)
						  VALUES
						  (:contentor_id, :sku, :source_locale, :target_locale, :target_store, NOW(), 'pending', :type)";
		$binds = array(
				'contentor_id'	=> $contentorID,
				'sku'			=> $sku,
				'source_locale' => $sourceLocale,
				'target_locale' => $targetLocale,
				'target_store'  => $targetID,
				'type'			=> $type
		);

		$connection->query($query, $binds);

		
		$typeTable = $this->_resource->getTableName('contentor_type');
		$typeQuery = "INSERT INTO `" . $typeTable . "` (`contentor_id`, `type`) VALUES (:contentor_id, 'product')";
		$typeBinds = array('contentor_id' => $contentorID);
		
		$connection->query($typeQuery, $typeBinds);

		
		$statusTable = $this->_resource->getTableName('contentor_status');
		$statusQuery = "INSERT INTO " . $statusTable . "
							(contentor_id, status, status_time)
							VALUES
							(:contentor_id, :status, NOW())";
		
		$statusBinds = array(
			'contentor_id'	=> $contentorID,
			'status'		=> 'Sent for localisation to: ' . $targetLocale,
		);
		
		if($connection->query($statusQuery, $statusBinds)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function send($request) {
		$token = $this->getToken();
		$url = $this->getURL() . 'content';
		
		//$request = Mage::getModel('Contentor_LocalizationApi/Hooks')->beforeSend($request);
		// Curl the object and get result
		$entry = json_encode($request);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token, 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $entry);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if($response = curl_exec($ch)) {
			$response = json_decode($response, true);
			if(!isset($response['id'])) {
				// No ID returned
				$contentorID = 'No ID returned';
			} else {
				$contentorID = $response['id'];
			}
			curl_close($ch);
			return $contentorID;
		} else {
			//Mage::log(curl_getinfo($ch), null, 'contentor.log');
			curl_close($ch);
			return 'Curl failed';
		}

	}

	public function receive($date) {
		$token = $this->getToken();
		$url = $this->getURL() . 'content';
		// New url with new uery for modified!
		$args = array('criteria'=>array(array('type'=>'modified','criteria'=>array('from'=>$date,'to'=>'tomorrow'))),'sortBy'=>array('created:desc'));
		$query = array('query' => json_encode($args));
		$queryParams = http_build_query($query);
		$url .= '?' . $queryParams;
		// Result
		$result = $this->getCurl($token, $url);
		
		if($result->total > 0) {
			// Process results from page 1
			foreach($result->requests as $request) {
				// This is a request, so fint out what happend with it, pending, confirmed, canceled or completed
				$requestId = $request->id;
				$newState = $request->state;
				$currentStatus = $this->getRequestStatus($request->id);

				if($currentStatus['state'] != $newState && $currentStatus['state']) {
					// OK, I have to do stuff!
					if($newState == 'completed') {
						// Put product
						if($currentStatus['type'] == 'product') {
							$this->putProduct($request);
						} else if($currentStatus['type'] == 'category') {
							$this->_logger->addDebug('Category Ready');
							//self::putCategory($request);
						} else if($currentStatus['type'] == 'cmspage') {
							$this->_logger->addDebug('CMS Page Ready');
							//self::putCmspage($request);
						} else {
							$this->_logger->addDebug($id . ' had no type to be found.');
						}
					} else {
						// The state changed, so I have to log it!
						$this->logStateChange($request, $currentStatus['type']);						
					}
				}
			}
			if($result->pages > 1) {
				for($i = 2;$i <= $result->pages;$i++) {
					// Curl page number $i
					$pageUrl = $url . '&page=' . $i;
					$pageResult = $this->getCurl($token, $pageUrl);
					foreach($pageResult->requests as $request) {
						// This is a request, so fint out what happend with it, pending, confirmed, canceled or completed
						$requestId = $request->id;
						$newState = $request->state;
						$currentStatus = $this->getRequestStatus($request->id);
						if($currentStatus['state'] != $newState && $currentStatus['state']) {
							// OK, I have to do stuff!
							if($newState == 'completed') {
								// Put product
								if($currentStatus['type'] == 'product') {
									$this->putProduct($request);
								} else if($currentStatus['type'] == 'category') {
									$this->_logger->addDebug('Product Ready');
									//self::putCategory($request);
								} else if($currentStatus['type'] == 'cmspage') {
									$this->_logger->addDebug('Product Ready');
									//self::putCmspage($request);
								} else {
									$this->_logger->addDebug($id . ' had no type to be found.');
								}
							} else {
								// The state changed, so I have to log it!
								$this->logStateChange($request, $currentStatus['type']);
							}
						}
					}
				}
				return true;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}


	protected function logStateChange($response, $type) {
		$connection = $this->_resource->getConnection('core_write');
		
		switch ($type) {
			case 'product':
				$tableName = "contentor_products";
				break;
			case 'category':
				$tableName = "contentor_categories";
				break;
			case 'category':
				$tableName = "contentor_cmspages";
				break;
		}
		
		$table = $this->_resource->getTableName($tableName);
		
		$query = "UPDATE `" . $table . "` SET `state` = :state WHERE `contentor_id` = :contentor_id";
		$binds = array(
				'state'			=> $response->state,
				'contentor_id'	=> $response->id,
		);
		
		$connection->query($query, $binds);
		
		if($response->state == 'confirmed') {
			$statusMessage = 'Order with this entry was confirmed';
		} else if($response->state == 'canceled') {
			$statusMessage = 'Order with this entry was canceled';
		} else {
			// Generic message
			$statusMessage = 'Status was change to ' . $response->state;
		}
		
		$table = $this->_resource->getTableName('contentor_status');
		$query = "INSERT INTO " . $table . " (`contentor_id`, `status`, `status_time`)
							VALUES (:contentor_id, :status, NOW())";
		$binds = array(
				'contentor_id'	=> $response->id,
				'status'		=> $statusMessage
		);
		$connection->query($query, $binds);
	}

	protected function putProduct($object) {
		// Get sku and target store from Magento DB
		
		$connection = $this->_resource->getConnection('core_read');
		$table = $this->_resource->getTableName('contentor_products');
		
		$query = "SELECT sku, target_store FROM `" . $table . "` WHERE `contentor_id` = :contentor_id AND completed_time IS NULL";
		$binds = array('contentor_id' => $object->id);
		
		if($productInfo = $connection->fetchRow($query, $binds)) {

			$product = $this->_productFactory->create()->setStoreId($productInfo['target_store'])->loadByAttribute('sku',$productInfo['sku']);
			
			if($product) {
				// setData on product depending on licalizationsfields received on the right store
				$this->_storeManager->setCurrentStore($productInfo['target_store']);
				
				foreach($object->fields as $field) {
					if($field->type == 'localizable') {
						// Update
						$attribute = substr($field->id, 0, -4);
						$product->setData($attribute, $field->value);
						
					}
				}
				
				
				if($this->_helper->getConfig('contentor_options/automation/import')) {
					// Set product enabled
					$product->setStatus(1);
				}
				/*
				if(!$urlKey) {
					$product->setUrlKey(false);
				}
				*/
				$product->save();
				
				// Log this
				$connection = $this->_resource->getConnection('core_write');
				$table = $this->_resource->getTableName('contentor_products');
				
				$query = "SELECT sku, target_store FROM `" . $table . "` WHERE `contentor_id` = :contentor_id AND completed_time IS NULL";
				$binds = array('contentor_id' => $object->id);
				
				$query = "UPDATE " . $table . " SET `completed_time` = :completed_time, `state` = 'completed' WHERE `contentor_id` = :contentor_id";
				$binds = array(
						'completed_time'	=> $object->completed,
						'contentor_id'		=> $object->id,
				);
				if(!$connection->query($query, $binds)) {
					$this->_logger->addDebug('Couldn\'t update request status');
					return false;
				}

				$table = $this->_resource->getTableName('contentor_status');
				$query = "INSERT INTO " . $table . " (`contentor_id`, `status`, `status_time`) 
						VALUES (:contentor_id, :status, NOW())";
				$binds = array(
						'contentor_id'	=> $object->id,
						'status'		=> 'Received as completed for ' . $object->language->target . ', completion time: ' . date("Y-m-d H:i:s", strtotime($object->completed)),
				);
				$connection->query($query, $binds);
				
				return true;
			}
		} else {
			// Not waiting for a product with this id
			$this->_logger->addDebug('no products');
			return false;
		}
	}

	public function testAuth($token=false) {
		$url = $this->getURL() . 'auth';
		if(!$token) {
			$token = $this->getToken();
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$token, 'Accept: application/json'));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$debug = curl_getinfo($ch);
		curl_close($ch);
		$info = json_decode($response);
		
		if(isset($info->companyName)) {
			return $info->companyName;
		} else {
			//Mage::log($info, null, 'contentor.log');
			return 'false';
		}
	}

	protected function getCurl($token, $url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$token, 'Accept: application/json'));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		// If error log curl_getinfo();
		if(!$response) {
			$this->_logger->addDebug(curl_getinfo($ch));
		}
		curl_close($ch);
		$return =  json_decode($response);
		
		return $return;
	}

	protected function getURL() {
		$dev = $this->getDEV();
		if($dev) {
			return 'http://api.dev.contentor.com:8080/v1/';
		} else {			
			return 'https://api.contentor.com/v1/';
		}
	}
	
	protected function getToken() {
		return $this->_helper->getConfig('contentor_options/token/apitoken');
	}

	protected function getRequestStatus($id) {
		$return['type'] = $this->getType($id);
		
		if($return['type'] == 'product') {
			$tableName = 'contentor_products';
		} else if($return['type'] == 'category') {
			$tableName = 'contentor_categories';
		} else if($return['type'] == 'cmspage') {
			$tableName = 'contentor_cmspages';
		} else {
			return false;
		}
		
		$connection = $this->_resource->getConnection('core_read');
		$typeTable = $this->_resource->getTableName($tableName);
		$query = "SELECT `state` FROM `" . $typeTable . "` WHERE `contentor_id` = :contentor_id";
		$binds = array('contentor_id' => $id);
		$requestInfo = $connection->fetchAll($query, $binds);
		$return['state'] = $requestInfo[0]['state'];

		return $return;
	}

	protected function getType($id) {
		$connection = $this->_resource->getConnection('core_read');
		$typeTable = $this->_resource->getTableName('contentor_type');
		$query = "SELECT `type` FROM `" . $typeTable . "` WHERE `contentor_id` = :contentor_id";
		$binds = array('contentor_id' => $id);
		if($type = $connection->fetchOne($query, $binds)) {
			return $type;
		} else {
			return false;
		}
	}
	
	private function getDEV() {
		return false;
	}
}