#!/bin/bash -e

CURRENT_DIR=`pwd -P`
cd `dirname $BASH_SOURCE`
SCRIPT_PATH=`pwd -P`
PROJECT_DIR=`dirname $SCRIPT_PATH`

DEFAULT_SATIS_URL="http://satis.localhost"
DEFAULT_TO_INSTALL_SATIS="Yes"
DEFAULT_SATIS_SUBDIR=`python -c 'import sys, os.path; print os.path.relpath(sys.argv[1], sys.argv[2])' "$PROJECT_DIR/../satis" "$CURRENT_DIR"`
DEFAULT_PACKAGES_SUBDIR=`python -c 'import sys, os.path; print os.path.relpath(sys.argv[1], sys.argv[2])' "$PROJECT_DIR/tmp/packages" "$CURRENT_DIR"`


if [ -f "$SCRIPT_PATH/update_satis.sh" ]; then
    DEFAULT_TO_INSTALL_SATIS="No"
    echo "$SCRIPT_PATH/update_satis.sh already exists."
    read -p "Are you sure you want to run $BASH_SOURCE again  [No]? [Y/N]:" TO_RUN_SCRIPT_AGAIN
    if [ -z "$TO_RUN_SCRIPT_AGAIN" ]; then
        TO_RUN_SCRIPT_AGAIN="N"
    fi
    TO_RUN_SCRIPT_AGAIN=`echo ${TO_RUN_SCRIPT_AGAIN:0:1} | tr [a-z] [A-Z]`
    if [ $TO_RUN_SCRIPT_AGAIN != "Y" ]; then
        echo "If you want to update your Local Satis instance, run $SCRIPT_PATH/update_satis.sh"
        echo "Exiting.."
        exit 0
    fi
fi


read -p "Should this script try to download and install a new instance of Satis [$DEFAULT_TO_INSTALL_SATIS]? [Y/N]:" TO_INSTALL_SATIS
if [ -z "$TO_INSTALL_SATIS" ]; then
    TO_INSTALL_SATIS="$DEFAULT_TO_INSTALL_SATIS"
fi
TO_INSTALL_SATIS=`echo ${TO_INSTALL_SATIS:0:1} | tr [a-z] [A-Z]`
if [ "$TO_INSTALL_SATIS" = "Y" ]; then
    PART_OF_SENTENCE="want to install"
else
    PART_OF_SENTENCE="have installed"
fi
read -p "Provide an url for Satis [$DEFAULT_SATIS_URL]:" SATIS_URL
if [ -z "$SATIS_URL" ]; then
    SATIS_URL="$DEFAULT_SATIS_URL"
fi
read -p "Provide a directory, relative to $CURRENT_DIR, where you $PART_OF_SENTENCE Satis [$DEFAULT_SATIS_SUBDIR]:" SATIS_SUBDIR
if [ -z "$SATIS_SUBDIR" ]; then
    SATIS_SUBDIR="$DEFAULT_SATIS_SUBDIR"
fi
read -p "Provide a directory, relative to $CURRENT_DIR, where you want to put packages [$DEFAULT_PACKAGES_SUBDIR]:" PACKAGES_SUBDIR
if [ -z "$PACKAGES_SUBDIR" ]; then
    PACKAGES_SUBDIR="$DEFAULT_PACKAGES_SUBDIR"
fi

echo "Satis URL: $SATIS_URL"
echo "Satis subdirectory: $SATIS_SUBDIR"
echo "Packages subdirectory: $PACKAGES_SUBDIR"

cd "$CURRENT_DIR"
mkdir -p "$SATIS_SUBDIR"
cd "$SATIS_SUBDIR"
SATIS_DIR=`pwd -P`
SATIS_RELATIVE_DIR=`python -c 'import sys, os.path; print os.path.relpath(sys.argv[1], sys.argv[2])' "$SATIS_DIR" "$PROJECT_DIR"`
echo
if [ "$TO_INSTALL_SATIS" = "Y" ]; then
    echo "Installing Satis..."
    composer create-project --stability=dev --keep-vcs composer/satis .
else
    echo "Satis is not being installed..."
fi

cd $CURRENT_DIR
mkdir -p "$PACKAGES_SUBDIR/subscribo"
cd "$PACKAGES_SUBDIR/subscribo"
SUBSCRIBO_PACKAGES_DIR=`pwd -P`
SUBSCRIBO_PACKAGES_RELATIVE_DIR=`python -c 'import sys, os.path; print os.path.relpath(sys.argv[1], sys.argv[2])' "$SUBSCRIBO_PACKAGES_DIR" "$PROJECT_DIR"`

source "$SCRIPT_PATH/packages_and_satis_build.sh"

echo "Generating update script bin/update_satis.sh"

cd $SCRIPT_PATH
echo "#!/bin/bash -e" > update_satis.sh
echo "SUBSCRIBO_PACKAGES_RELATIVE_DIR=\"$SUBSCRIBO_PACKAGES_RELATIVE_DIR\"" >> update_satis.sh
echo "SATIS_RELATIVE_DIR=\"$SATIS_RELATIVE_DIR\"" >> update_satis.sh
echo "SATIS_URL=\"$SATIS_URL\"" >> update_satis.sh

cat files/update_satis.sh.end >> update_satis.sh

chmod +x update_satis.sh


mkdir -p "files/composer"
echo "{ \"repositories\": [ { \"type\": \"composer\", \"url\": \"$SATIS_URL\" } ] }" > files/composer/config.json

cd "$CURRENT_DIR"

echo
echo "Summary:"
echo "Satis URL: $SATIS_URL"
echo "Satis directory: $SATIS_DIR"
echo "Packages directory: $SUBSCRIBO_PACKAGES_DIR"
echo

if [ -f ~/.composer/config.json ]; then
    echo "You have some global composer configuration already present."
    echo "To check its content you may you may use the following command:"
    echo "cat  ~/.composer/config.json"
    echo "New composer configuration $SCRIPT_PATH/files/composer/config.json has been generated."
    echo "To overwrite your current composer configuration with a new one you may use the following command:"
    echo "cp $SCRIPT_PATH/files/composer/config.json ~/.composer/config.json"
else
    echo "You seem not to have any global composer configuration already present."
    echo "New composer configuration $SCRIPT_PATH/files/composer/config.json has been generated."
    echo "To install new composer configuration you may use the following command:"
    echo "cp -nv $SCRIPT_PATH/files/composer/config.json ~/.composer/config.json"
fi
echo
