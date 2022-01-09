#!/bin/bash
#tag

WORK_DIR=/usr/local/utils/covid/
WORK_DIR_FRONTEND=/var/www/html/covid2019-auto-dialer-front/

systemctl stop backend_dialer

#shut down frontend
if [[ -d "${WORK_DIR_FRONTEND}" ]]; then
  cd ${WORK_DIR_FRONTEND} &&
  php artisan down
fi
exit 0
