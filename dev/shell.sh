#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

sudo docker-compose exec --user www-data contentor-magento2-web bash
