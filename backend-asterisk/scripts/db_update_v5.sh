#!/bin/bash
set -e
cd /var/www/html/covid2019-auto-dialer-front &&

AST_DB_HOST=`cat .env | grep "AST_DB_HOST" | cut -d'=' -f2-`
AST_DB_USER=`cat .env | grep "AST_DB_USER" | cut -d'=' -f2-`
AST_DB_PASS=`cat .env | grep "AST_DB_PASS" | cut -d'=' -f2-`
AST_DB_NAME=`cat .env | grep "AST_DB_NAME" | cut -d'=' -f2-`

echo "create unique index contacts_phone_primary_uindex on contacts (phone_primary);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
echo "UPDATE version SET version=5 WHERE instance_type='database';" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
