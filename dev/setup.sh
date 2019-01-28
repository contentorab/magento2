#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

echo "Installing Magento and Sample Data"
sudo docker-compose exec contentor-magento2-web install-magento
sudo docker-compose exec contentor-magento2-web install-sampledata
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento config:set admin/security/session_lifetime 90000

echo "Enabling symlinked templates"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento config:set dev/template/allow_symlink 1
sudo docker-compose exec --user www-data contentor-magento2-web composer config repositories.metrilo vcs git@github.com:metrilo/magento2-templatesymlinks.git
sudo docker-compose exec --user www-data contentor-magento2-web composer require "fishpig/module-templatesymlinks @dev"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento module:enable FishPig_TemplateSymlinks

echo "Installing module"
sudo docker-compose exec --user www-data contentor-magento2-web composer config repositories.local path /var/www/contentor
sudo docker-compose exec --user www-data contentor-magento2-web composer config repo.magento false
sudo docker-compose exec --user www-data contentor-magento2-web composer require "contentorab/localizationapi"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento module:enable Contentor_LocalizationApi

echo "Updating and flushing caches"
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento setup:upgrade
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento setup:di:compile
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento cache:clean
sudo docker-compose exec --user www-data contentor-magento2-web bin/magento cache:flush
sudo docker-compose exec --user www-data contentor-magento2-web chown -R www-data .
