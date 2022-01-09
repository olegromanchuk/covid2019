#!/bin/bash
#exit on error
set -e

WORK_DIR=/usr/local/utils/covid/
cd ${WORK_DIR} &&

#check that configs are exist
if [[ ! -s config_campaign_generator.php ]]; then
	echo "Could not find config config_campaign_generator.php"
	exit 1
fi

if [[ ! -s backend/config.yml ]]; then
	echo "Could not find config backend/config.yml"
	exit 1
fi

if [[ ! -s /var/www/html/covid2019-auto-dialer-front/.env ]]; then
	echo "Could not find config /var/www/html/covid2019-auto-dialer-front/.env"
	exit 1
fi

systemctl status backend_dialer
if [[ $? != 0 ]]; then #process is not started
  echo "backend-dialer is not running. Exiting..."
  exit 1
fi
