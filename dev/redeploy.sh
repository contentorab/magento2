#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

echo "Installing module"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento module:disable Contentor_LocalizationApi
sudo docker-compose exec --user www-data contentor-magento2-web composer require "contentorab/localizationapi"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento module:enable Contentor_LocalizationApi

echo "Updating and flushing caches"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento setup:upgrade
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento setup:di:compile
