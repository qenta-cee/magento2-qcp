#!/bin/bash

set -e

trap exit SIGTERM
touch /tmp/shop.log

# If we are in Github plugin repo CI environment
CI_REPO_URL=${GITHUB_SERVER_URL}/${GITHUB_REPOSITORY}
if [[ ${CI_REPO_URL} == ${PLUGIN_URL//.git/} ]]; then
  PLUGIN_VERSION=${GITHUB_SHA}
  CI='true'
fi

if [[ -z ${MAGENTO2_BASEURL} ]]; then
  echo "MAGENTO2_BASEURL not specified."
  if [[ -n ${NGROK_TOKEN} ]]; then 
    echo "Launching ngrok to get temporary URL"
    MAGENTO2_BASEURL=$(ngrok.sh ${NGROK_TOKEN})
  else
    echo "No NGROK_TOKEN specified. Using localhost as URL"
    MAGENTO2_BASEURL=localhost
  fi
fi

echo "Waiting for DB host ${MAGENTO2_DB_HOST}"

while ! mysqladmin ping -h"${MAGENTO2_DB_HOST}" --silent; do
  sleep 10
done

function create_db() {
  echo "Creating Database"
}

function install_core() {
  echo "Install Core"
  composer install
}

function switch_version() {
  echo "Switchting to Magento2 ${MAGENTO2_VERSION}"
  cd /var/www/magento2
  git fetch --all
  git checkout ${MAGENTO2_VERSION} || echo "Invalid MAGENTO2_VERSION specified"
  rm -r /var/www/html
  ln -s /var/www/magento2/pub /var/www/html
}

function install_sample_data() {
  echo "Installing Sample Data"
  cd /var/www/magento2/magento2-sample-data
  git fetch --all
  git checkout ${MAGENTO2_VERSION}
  cd ..
  php -f magento2-sample-data/dev/tools/build-sample-data.php -- --ce-source="/var/www/magento2/"
  bin/magento cache:clean
  bin/magento setup:upgrade
}

function install_language_pack() {
  echo "Installing German Language Pack"
  cd /var/www/magento2
  composer require splendidinternet/mage2-locale-de-de
  bin/magento config:set general/locale/code de_DE
  bin/magento config:set general/country/default at
}

function install_plugin() {
  echo "Installing Extension"
  local PLUGIN_DIR=/tmp/plugin/
  if [[ -n ${PLUGIN_URL} && ${PLUGIN_URL} != 'local' ]]; then
    PLUGIN_DIR=$(mktemp -d)
    if [[ -z ${PLUGIN_VERSION} || ${PLUGIN_VERSION} == 'latest' ]]; then
      git clone -b ${PLUGIN_VERSION} ${PLUGIN_URL} ${PLUGIN_DIR}
    else
      git clone ${PLUGIN_URL} ${PLUGIN_DIR}
    fi 
  fi
  cd /var/www/magento2
  composer config minimum-stability dev
  composer config repositories.qenta path ${PLUGIN_DIR}
  composer config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
  composer config --no-plugins allow-plugins.laminas/laminas-dependency-plugin true
  composer config --no-plugins allow-plugins.magento/magento-composer-installer true
  composer require qenta/magento2-qcp
  bin/magento cache:clean
  bin/magento setup:upgrade

  (sleep 10; bin/magento cache:flush >&/dev/null)&
}

function run_periodic_flush() {
  local INTERVAL=${1:-60}
  while sleep ${INTERVAL}; do bin/magento cache:clean >& /dev/null & done &
}

function setup_store() {
  bin/magento setup:install \
  --admin-firstname=QENTA \
  --admin-lastname=Admin \
  --admin-email=${MAGENTO2_ADMIN_EMAIL} \
  --admin-user=${MAGENTO2_ADMIN_USER} \
  --admin-password=${MAGENTO2_ADMIN_PASS} \
  --base-url=https://${MAGENTO2_BASEURL} \
  --db-host=${MAGENTO2_DB_HOST} \
  --db-name=${MAGENTO2_DB_NAME} \
  --db-user=${MAGENTO2_DB_USER} \
  --db-password=${MAGENTO2_DB_PASS} \
  --db-prefix=qcp \
  --currency=EUR \
  --timezone=Europe/Vienna \
  --language=de_DE \
  --elasticsearch-host=magento2_elasticsearch_qcp \
  --backend-frontname=admin_qenta
  bin/magento cron:run
  bin/magento setup:upgrade
}

function print_info() {
  echo
  echo '####################################'
  echo
  echo "Shop: https://${MAGENTO2_BASEURL}"
  echo "Admin Panel: https://${MAGENTO2_BASEURL}/admin_qenta/"
  echo "User: ${MAGENTO2_ADMIN_USER}"
  echo "Password: ${MAGENTO2_ADMIN_PASS}"
  echo
  echo '####################################'
  echo
}

function _log() {
  echo "${@}" >> /tmp/shop.log
}

if [[ -e wp-config.php ]]; then
  echo "Shop detected. Skipping installations"
  MAGENTO2_BASEURL=$(echo "BLABLABLA")
else
  switch_version ${MAGENTO2_VERSION}
  _log "Magento2 version set to: ${MAGENTO2_VERSION}"

  install_core
  _log "Shop installed"
  
  setup_store
  _log "store set up"
  
  install_language_pack
  _log "installed 3rd party language pack de_DE"

  install_sample_data
  _log "Sample data installed"
  
  if [[ -n ${PLUGIN_URL} ]]; then
    install_plugin
    _log "plugin installed"
  fi
  if [[ -n ${OVERRIDE_api_uri} ]]; then
    change_api_uri "${OVERRIDE_api_uri}" &&
    _log "changed API URL to ${OVERRIDE_api_uri}" &&
    _api_uri_changed=true
  fi
fi
if [[ ${CI} != 'true' ]]; then
  (sleep 1; print_info) &
fi

run_periodic_flush 3m

_log "url=https://${MAGENTO2_BASEURL}"
_log "ready"

echo "ready" > /tmp/debug.log

mkdir -p /var/www/magento2/log
touch /var/www/magento2/log/exception.log

apache2-foreground "$@" &
tail -f /var/www/magento2/log/exception.log
