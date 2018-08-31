#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

echo "Installing Magento and Sample Data"
docker-compose exec contentor-magento2-web install-magento
docker-compose exec contentor-magento2-web install-sampledata
docker-compose exec --user www-data contentor-magento2-web bin/magento config:set admin/security/session_lifetime 90000

echo "Installing module"
docker-compose exec --user www-data contentor-magento2-web composer config repositories.local path /var/www/contentor
docker-compose exec --user www-data contentor-magento2-web composer config repo.magento false
docker-compose exec --user www-data contentor-magento2-web composer require "contentorab/localizationapi"
docker-compose exec --user www-data contentor-magento2-web bin/magento module:enable Contentor_LocalizationApi

echo "Enabling symlinked templates"
docker-compose exec --user www-data contentor-magento2-web bin/magento config:set dev/template/allow_symlink 1
docker-compose exec --user www-data contentor-magento2-web composer config repositories.metrilo vcs git@github.com:metrilo/magento2-templatesymlinks.git
docker-compose exec --user www-data contentor-magento2-web composer require "fishpig/module-templatesymlinks @dev"
docker-compose exec --user www-data contentor-magento2-web bin/magento module:enable FishPig_TemplateSymlinks

echo "Updating and flushing caches"
docker-compose exec --user www-data contentor-magento2-web bin/magento setup:upgrade
docker-compose exec --user www-data contentor-magento2-web bin/magento setup:di:compile
docker-compose exec --user www-data contentor-magento2-web bin/magento cache:clean
docker-compose exec --user www-data contentor-magento2-web bin/magento cache:flush
docker-compose exec --user www-data contentor-magento2-web chown -R www-data .
