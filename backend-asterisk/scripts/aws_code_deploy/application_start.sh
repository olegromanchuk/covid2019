#!/bin/bash

WORK_DIR=/usr/local/utils/covid/
WORK_DIR_FRONTEND=/var/www/html/covid2019-auto-dialer-front/

#start backend
systemctl start backend_dialer

#up frontend
cd ${WORK_DIR_FRONTEND} &&
php artisan up
php artisan view:clear; php artisan route:clear; php artisan cache:clear; php artisan config:clear;php artisan clear-compiled;php artisan optimize:clear
