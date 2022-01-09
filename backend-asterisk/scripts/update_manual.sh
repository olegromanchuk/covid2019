#!/bin/bash
#exit on error
set -e

WORK_DIR=/usr/local/utils/covid/

git pull &&


#check if we need to update database
cd /var/www/html/covid2019-auto-dialer-front/ &&
AST_DB_HOST=`cat .env | grep "AST_DB_HOST" | cut -d'=' -f2-`
AST_DB_USER=`cat .env | grep "AST_DB_USER" | cut -d'=' -f2-`
AST_DB_PASS=`cat .env | grep "AST_DB_PASS" | cut -d'=' -f2-`
AST_DB_NAME=`cat .env | grep "AST_DB_NAME" | cut -d'=' -f2-`

#current_db_version
CURRENT_DB_VERSION=`echo "SELECT version FROM version WHERE instance_type='database';" | mysql -s -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}`
cd ${WORK_DIR}
#latest version
UPGRADE_SCRIPT_VERSION=`cat database_version.txt`
while [[ ${CURRENT_DB_VERSION} < ${UPGRADE_SCRIPT_VERSION} ]]; do
		SAVE_CURRENT_DB_VERSION=${CURRENT_DB_VERSION}
		NEXT_VER=$(( ${CURRENT_DB_VERSION} + 1 )) &&
		if [[ -s scripts/db_update_v${NEXT_VER}.sh ]]; then
			echo "Updating DB till version ${NEXT_VER}"
			scripts/db_update_v${NEXT_VER}.sh
		else
			echo "Database update script v${NEXT_VER} does not exists, but database_version.txt is ${UPGRADE_SCRIPT_VERSION}"
			exit 1
		fi
		CURRENT_DB_VERSION=`echo "SELECT version FROM version WHERE instance_type='database';" | mysql -s -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}`
		if [[ ${CURRENT_DB_VERSION} == ${SAVE_CURRENT_DB_VERSION} ]]; then
			echo "DB version was not updated. Check script db_update_v${NEXT_VER}.sh"
			exit 1
		fi
done

#check if we need to update asterisk
if [[ -s asterisk/update_asterisk ]]; then
    echo "Updating Asterisk files ..."
    scripts/update_asterisk_files.sh
fi

#check if we need to update asterisk sound files
if [[ -s asterisk/update_asterisk_sound ]]; then
    echo "Updating Asterisk sound files ..."
    scripts/update_asterisk_sound_files.sh
fi

cd /var/www/html/covid2019-auto-dialer-front/ &&
git pull &&
php artisan view:clear; php artisan route:clear; php artisan cache:clear; php artisan config:clear;php artisan clear-compiled;php artisan optimize:clear

systemctl restart backend_dialer
