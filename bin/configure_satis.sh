#!/bin/sh

set -e

CURRENT_DIR=`pwd -P`
cd `dirname $0`
SCRIPT_PATH=`pwd -P`
cd $CURRENT_DIR

read -p "Provide an url for Satis:" SATIS_URL
read -p "Provide a subdirectory, where you want to install Satis:" SATIS_SUBDIR
read -p "Provide a subdirectory, where you want to put packages:" PACKAGES_SUBDIR

composer create-project --stability=dev --keep-vcs composer/satis $SATIS_SUBDIR

cd $SATIS_SUBDIR

SATIS_DIR=`pwd -P`

cp $SCRIPT_PATH/files/satis.json.start satis.json

cd $CURRENT_DIR

mkdir -p $PACKAGES_SUBDIR

cp -R $SCRIPT_PATH/../vendor/subscribo $PACKAGES_SUBDIR

cd $PACKAGES_SUBDIR/subscribo

FIRST_ITEM=YES
for ONE_PACKAGE_SUBDIR in */; do
    if [ -d "${ONE_PACKAGE_SUBDIR}" ]; then
        cd $ONE_PACKAGE_SUBDIR;
        PACKAGE_SUBDIR_PATH=`pwd -P`
        git init
        git add .
        git commit -m "Initial commit"
        if [ $FIRST_ITEM = "NO" ]; then
            echo "," >> $SATIS_DIR/satis.json
        fi
        FIRST_ITEM="NO"
        echo "{" >> $SATIS_DIR/satis.json
            echo "\"type\": \"vcs\"," >> $SATIS_DIR/satis.json
            echo "\"url\": \"$PACKAGE_SUBDIR_PATH\"" >> $SATIS_DIR/satis.json
        echo "}" >> $SATIS_DIR/satis.json
        cd ..
    fi
done
echo "]," >> $SATIS_DIR/satis.json
echo "\"homepage\": \"$SATIS_URL\"" >> $SATIS_DIR/satis.json
echo "}" >> $SATIS_DIR/satis.json

cd $SATIS_DIR

php bin/satis build

cd $CURRENT_DIR
