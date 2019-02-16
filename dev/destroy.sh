#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

sudo docker-compose down --volumes
