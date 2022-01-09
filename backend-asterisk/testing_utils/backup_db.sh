#!/bin/bash
BACKUPFILE=/usr/local/utils/covid/backup_db/asterisk_dialer_stat_`date +%Y%m%d%H%M%S`.sql
mysqldump --extended-insert=FALSE -u root asterisk_dialer_stat > ${BACKUPFILE}
