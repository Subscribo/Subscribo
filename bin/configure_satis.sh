#!/bin/bash -x

set -e

DEFAULT_SATIS_URL="satis.localhost"
DEFAULT_SATIS_SUBDIR="satis10"
DEFAULT_PACKAGES_SUBDIR="packages10"

CURRENT_DIR=`pwd -P`
cd `dirname $BASH_SOURCE`
SCRIPT_PATH=`pwd -P`
cd "$CURRENT_DIR"

read -p "Provide an url for Satis[$DEFAULT_SATIS_URL]:" SATIS_URL
if [ -z "$SATIS_URL" ]; then
    SATIS_URL="$DEFAULT_SATIS_URL"
fi
read -p "Provide a subdirectory, where you want to install Satis[$DEFAULT_SATIS_SUBDIR]:" SATIS_SUBDIR
if [ -z "$SATIS_SUBDIR" ]; then
    SATIS_SUBDIR="$DEFAULT_SATIS_SUBDIR"
fi
read -p "Provide a subdirectory, where you want to put packages[$DEFAULT_PACKAGES_SUBDIR]:" PACKAGES_SUBDIR
if [ -z "$PACKAGES_SUBDIR" ]; then
    PACKAGES_SUBDIR="$DEFAULT_PACKAGES_SUBDIR"
fi

echo "Satis URL: $SATIS_URL"
echo "Satis subdirectory: $SATIS_SUBDIR"
echo "Packages subdirectory: $PACKAGES_SUBDIR"
echo
echo "Installing Satis..."

composer create-project --stability=dev --keep-vcs composer/satis "$SATIS_SUBDIR"

cd "$SATIS_SUBDIR"
SATIS_DIR=`pwd -P`

cd $CURRENT_DIR
mkdir -p "$PACKAGES_SUBDIR/subscribo"
cd "$PACKAGES_SUBDIR/subscribo"
SUBSCRIBO_PACKAGES_DIR=`pwd -P`

source "$SCRIPT_PATH/packages_and_satis_build.sh"

echo "Generating update script bin/update_satis.sh"

cd $SCRIPT_PATH
echo "#!/bin/bash" > update_satis.sh
echo "SUBSCRIBO_PACKAGES_DIR=\"$SUBSCRIBO_PACKAGES_DIR\"" >> update_satis.sh
echo "SATIS_DIR=\"$SATIS_DIR\"" >> update_satis.sh
echo "SATIS_URL=\"$SATIS_URL\"" >> update_satis.sh

cat files/update_satis.sh.end >> update_satis.sh

chmod +x update_satis.sh

echo "Summary:"
echo "Satis URL: $SATIS_URL"
echo "Satis directory: $SATIS_DIR"
echo "Packages directory: $SUBSCRIBO_PACKAGES_DIR"

cd "$CURRENT_DIR"
