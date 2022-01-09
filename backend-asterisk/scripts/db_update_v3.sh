#!/bin/bash
cd /var/www/html/covid2019-auto-dialer-front &&
AST_DB_HOST=`cat .env | grep "AST_DB_HOST" | awk -F"=" '{print$2}'`
AST_DB_USER=`cat .env | grep "AST_DB_USER" | awk -F"=" '{print$2}'`
AST_DB_PASS=`cat .env | grep "AST_DB_PASS" | awk -F"=" '{print$2}'`
AST_DB_NAME=`cat .env | grep "AST_DB_NAME" | awk -F"=" '{print$2}'`

echo "create table contacts
(
    contact_id      int auto_increment,
    first_name      varchar(50)                           null,
    last_name       varchar(50)                           null,
    phone_primary   varchar(255)                          null,
    phone_secondary varchar(50)                           null,
    phone_third     varchar(50)                           null,
    email           varchar(255)                          null,
    company         varchar(100)                          null,
    description     text                                  null,
    created         timestamp default current_timestamp() null,
    modified        timestamp default current_timestamp() null,
    constraint contacts_contact_id_uindex
        unique (contact_id)
);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}

echo "create table if not exists version
(
    instance_type varchar(100) null,
    version       int          null
);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}

echo "INSERT INTO asterisk_dialer_stat.version (instance_type, version) VALUES ('database', 3);" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}

echo "DELIMITER $$ 
CREATE TRIGGER contacts_after_update 
BEFORE UPDATE  
    ON contacts
      FOR EACH ROW 
      BEGIN  
        SET NEW.modified = CURRENT_TIMESTAMP;   
END; $$ 
DELIMITER ;" | mysql -h ${AST_DB_HOST} -u ${AST_DB_USER} -p${AST_DB_PASS} ${AST_DB_NAME}
