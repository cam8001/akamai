#!/bin/bash
echo - "Running .travis-before-script.sh"

set -e $DRUPAL_TI_DEBUG

# Ensure the right Drupal version is installed.
# Note: This function is re-entrant.
drupal_ti_ensure_drupal

# Run PHPCS on the module code only (before dependencies are added).
phpcs --report=full --standard=Drupal "$DRUPAL_TI_DRUPAL_DIR/modules/$DRUPAL_TI_MODULE_NAME" || true

# Download dependencies.
mkdir -p "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH"
cd "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH"
git clone --depth 1 --branch 8.x-1.x http://git.drupal.org/project/composer_manager.git

# Ensure the module is linked into the code base and enabled.
# Note: This function is re-entrant.
drupal_ti_ensure_module_linked

# Initialize composer manager.
TMP_CWD=`pwd`
cd "$DRUPAL_TI_DRUPAL_DIR"
wget https://www.drupal.org/files/issues/2664274-19-fix-composer.patch
git apply -v 2664274-19-fix-composer.patch
cd "$TMP_CWD"
php "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH/composer_manager/scripts/init.php"
composer drupal-rebuild
composer update -n --verbose
