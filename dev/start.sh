#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
cd $LOCAL_DIR

# Setup the data directory used Docker Compose
mkdir data

docker-compose up
