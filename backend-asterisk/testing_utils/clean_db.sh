#!/bin/bash
if [[ $# > 0 && $1 == "completely" ]]; then
	cat /usr/local/utils/covid/backend-asterisk/database_empty.sql | mysql asterisk_dialer_stat
	exit
fi
cat /usr/local/utils/covid/database.sql | mysql asterisk_dialer_stat
rm -rf /usr/local/utils/covid/log/*
