#!/bin/sh

#########################################################
#                                                       #
# Auxiliary script to be called from other scripts      #
# to rebuild packages directory and Satis configuration #
#                                                       #
#########################################################


cd "$PROJECT_DIR"
cd "$SUBSCRIBO_PACKAGES_RELATIVE_DIR"
SUBSCRIBO_PACKAGES_DIR=`pwd -P`
echo "Cleaning $SUBSCRIBO_PACKAGES_DIR"
for ONE_PACKAGE_SUBDIR in */; do
    if [ -f "${ONE_PACKAGE_SUBDIR}composer.json" ]; then
        rm -rf ./$ONE_PACKAGE_SUBDIR/*;
    fi
done

echo "Copying over new content from $SCRIPT_PATH/../vendor/subscribo"

cp -R "$SCRIPT_PATH/../vendor/subscribo" "$SUBSCRIBO_PACKAGES_DIR/.."

echo "Commiting to git and updating satis.json"

cd "$PROJECT_DIR"
cd "$SATIS_RELATIVE_DIR"
SATIS_DIR=`pwd -P`
cp "$SCRIPT_PATH/files/satis.json.start" "$SATIS_DIR/satis.json"

FIRST_ITEM=YES
cd "$SUBSCRIBO_PACKAGES_DIR"
for ONE_PACKAGE_SUBDIR in */; do
    if [ -f "${ONE_PACKAGE_SUBDIR}composer.json" ]; then
        cd "$ONE_PACKAGE_SUBDIR"
        PACKAGE_SUBDIR_PATH=`python -c 'import sys, os.path; print os.path.relpath(sys.argv[1], sys.argv[2])' "$(pwd -P)" "$SATIS_DIR"`
        if [ ! -d .git ]; then
            echo "Initializing new repository for $ONE_PACKAGE_SUBDIR"
            git init
            git add .
            git commit -m "Initial commit"
        else
            git add .
            if `git diff-index --quiet --cached HEAD`; then
                echo "No differences for $ONE_PACKAGE_SUBDIR"
            else
                echo "Commiting $ONE_PACKAGE_SUBDIR"
                git commit -m "Package update"
            fi
        fi
        if [ $FIRST_ITEM = "NO" ]; then
            echo "        }," >> $SATIS_DIR/satis.json
        fi
        FIRST_ITEM="NO"
        echo "        {" >> $SATIS_DIR/satis.json
        echo "            \"type\": \"git\"," >> $SATIS_DIR/satis.json
        echo "            \"url\": \"$PACKAGE_SUBDIR_PATH\"" >> $SATIS_DIR/satis.json
        cd ..
    fi
done
if [ $FIRST_ITEM = "NO" ]; then
    echo "        }" >> $SATIS_DIR/satis.json
fi
echo "    ]," >> $SATIS_DIR/satis.json
echo "    \"homepage\": \"$SATIS_URL\"" >> $SATIS_DIR/satis.json
echo "}" >> $SATIS_DIR/satis.json

echo "Updating Satis"

cd "$SATIS_DIR"
php bin/satis build
