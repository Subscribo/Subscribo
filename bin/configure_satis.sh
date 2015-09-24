#!/bin/bash -e

DEFAULT_SATIS_URL="http://satis.localhost"
DEFAULT_SATIS_SUBDIR="satis"
DEFAULT_PACKAGES_SUBDIR="packages"
DEFAULT_TO_INSTALL_SATIS="Yes"

CURRENT_DIR=`pwd -P`
cd `dirname $BASH_SOURCE`
SCRIPT_PATH=`pwd -P`
PROJECT_DIR=`dirname $SCRIPT_PATH`
if [ "${CURRENT_DIR##$PROJECT_DIR}" != "$CURRENT_DIR" ]; then
    echo "You should not run this command from within the project, but rather from some parent directory of some level"
    exit 1
fi

cd "$CURRENT_DIR"

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
read -p "Provide a subdirectory, where you $PART_OF_SENTENCE Satis [$DEFAULT_SATIS_SUBDIR]:" SATIS_SUBDIR
if [ -z "$SATIS_SUBDIR" ]; then
    SATIS_SUBDIR="$DEFAULT_SATIS_SUBDIR"
fi
read -p "Provide a subdirectory, where you want to put packages [$DEFAULT_PACKAGES_SUBDIR]:" PACKAGES_SUBDIR
if [ -z "$PACKAGES_SUBDIR" ]; then
    PACKAGES_SUBDIR="$DEFAULT_PACKAGES_SUBDIR"
fi

echo "Satis URL: $SATIS_URL"
echo "Satis subdirectory: $SATIS_SUBDIR"
echo "Packages subdirectory: $PACKAGES_SUBDIR"
echo
if [ "$TO_INSTALL_SATIS" = "Y" ]; then
    echo "Installing Satis..."
    composer create-project --stability=dev --keep-vcs composer/satis "$SATIS_SUBDIR"
else
    echo "Satis is not being installed, just making sure the directory is present..."
    mkdir -p "$SATIS_SUBDIR"
fi


cd "$SATIS_SUBDIR"
SATIS_DIR=`pwd -P`

cd $CURRENT_DIR
mkdir -p "$PACKAGES_SUBDIR/subscribo"
cd "$PACKAGES_SUBDIR/subscribo"
SUBSCRIBO_PACKAGES_DIR=`pwd -P`

source "$SCRIPT_PATH/packages_and_satis_build.sh"

echo "Generating update script bin/update_satis.sh"

cd $SCRIPT_PATH
echo "#!/bin/bash -e" > update_satis.sh
echo "SUBSCRIBO_PACKAGES_DIR=\"$SUBSCRIBO_PACKAGES_DIR\"" >> update_satis.sh
echo "SATIS_DIR=\"$SATIS_DIR\"" >> update_satis.sh
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
echo "To check your current composer configuration you may you may use the following command:"
echo "cat  ~/.composer/config.json"
echo "New composer configuration $SCRIPT_PATH/files/composer/config.json has been generated."
echo "To overwrite your current composer configuration (if any) with a new one you may use the following command:"
echo "cp $SCRIPT_PATH/files/composer/config.json ~/.composer/config.json"
echo
