#!/bin/bash
cd /var/www/html/covid2019-auto-dialer-front &&
AST_DB_HOST=`cat .env | grep "AST_DB_HOST" | awk -F"=" '{print$2}'`
AST_DB_USER=`cat .env | grep "AST_DB_USER" | awk -F"=" '{print$2}'`
AST_DB_PASS=`cat .env | grep "AST_DB_PASS" | awk -F"=" '{print$2}'`
AST_DB_NAME=`cat .env | grep "AST_DB_NAME" | awk -F"=" '{print$2}'`

echo "ALTER table callrecords add COLUMN campaign_name varchar(255);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
echo "ALTER table callrecords add COLUMN created timestamp;" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
echo "CREATE INDEX ind_campaign_name ON callrecords (campaign_name);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
echo "UPDATE version SET version=4 WHERE instance_type='database'; | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}

echo "create table campaigns
(
    id      int auto_increment,
    name      varchar(255)                           null,
    description     text                                  null,
    created         timestamp default current_timestamp() null,
    modified        timestamp default current_timestamp() null,
    constraint campaigns_id_uindex
        unique (id)
);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
