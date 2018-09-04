#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
DIR="$LOCAL_DIR/.."

cd "$DIR"

rm release.zip
zip -r release.zip "." -x '.git/*' -x 'dev/*' -x '.DS_Store/*' -x ".gitignore" -x ".eslintconfig"

echo "Distribution built to release.zip"
