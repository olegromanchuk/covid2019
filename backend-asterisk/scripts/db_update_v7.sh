#!/bin/bash
set -e
cd /var/www/html/covid2019-auto-dialer-front &&
AST_DB_HOST=`cat .env | grep "AST_DB_HOST" | cut -d'=' -f2-`
AST_DB_USER=`cat .env | grep "AST_DB_USER" | cut -d'=' -f2-`
AST_DB_PASS=`cat .env | grep "AST_DB_PASS" | cut -d'=' -f2-`
AST_DB_NAME=`cat .env | grep "AST_DB_NAME" | cut -d'=' -f2-`

echo "create table if not exists options (name varchar(100) null,value varchar(255) null);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
echo "INSERT INTO options (name, value) values ('amount_of_simultaneous_calls', '3');" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
#update it!!!
echo "UPDATE version SET version=7 WHERE instance_type='database';" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
