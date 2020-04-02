#!/usr/bin/env bash

LOCAL_DIR=${BASH_SOURCE%/*}
DIR="$LOCAL_DIR/.."

cd "$DIR"

rev=`cat ./etc/module.xml | grep "setup_version=" | sed -e 's/.*setup_version=\"\(.*\)\">/\1/'`
pkg="Contentor_LocalizationAPI-${rev}.zip"

rm $pkg

sed -i '' 's/api.contentor.dev/api.contentor.com/g' etc/config.xml

zip -r $pkg "." -x '.git/*' -x 'dev/*' -x '.DS_Store' -x '*/.DS_Store/*' \
 -x ".gitignore" -x ".eslintconfig" -x ".editorconfig" -X "ruleset.xml" \
 -x "vendor/*"

sed -i '' 's/api.contentor.com/api.contentor.dev/g' etc/config.xml

echo "Distribution built to " $pgk
