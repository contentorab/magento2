#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

echo "Installing Magento"
docker-compose exec contentor-magento2-web install-magento

echo "Installing Sample Data"
docker-compose exec contentor-magento2-web install-sampledata
#docker-compose exec web /var/www/html/bin/magento cron:run
