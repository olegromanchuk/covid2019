#!/bin/bash

TABLENAME=callrecords
REPORT=/tmp/covidcollector.txt
REPORT_4SQL=/tmp/covidreport_sql.txt

WORK_DIR=/usr/local/utils/covid/backend-asterisk/
WORK_DIR_FRONTEND=/var/www/html/covid2019-auto-dialer-front/

#check if we need to update database
cd ${WORK_DIR_FRONTEND} &&
DB_HOST=`cat .env | grep "AST_DB_HOST" | cut -d'=' -f2-`
DB_USER=`cat .env | grep "AST_DB_USER" | cut -d'=' -f2-`
DB_PASS=`cat .env | grep "AST_DB_PASS" | cut -d'=' -f2-`
DB_NAME=`cat .env | grep "AST_DB_NAME" | cut -d'=' -f2-`

case $2 in
	0)
		echo "Confirmed: $1-$4 `date`" >> ${REPORT}
	;;
	1)
		echo "<tr><td>Fail</td><td>$3</td><td>$4</td><td>$1</td><td>`date`</td></tr>" >> ${REPORT}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='call failed',dialed_number_debug=$3 where id=$1;" >> ${REPORT_4SQL}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='call failed',dialed_number_debug=$3 where id=$1;" | mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME}
	;;
	2)
		echo "<tr><td>Human confirmed</td><td>$3</td><td>$4</td><td>$1</td><td>`date`</td></tr>" >> ${REPORT}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='confirmed human',dialed_number_debug=$3 where id=$1;" >> ${REPORT_4SQL}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='confirmed human',dialed_number_debug=$3 where id=$1;" | mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME}
	;;
	3)
		echo "<tr><td>Voicemail/answering service</td><td>$3</td><td>$4</td><td>$1</td><td>`date`</td></tr>" >> ${REPORT}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='voicemail or answering service',dialed_number_debug=$3 where id=$1;" >> ${REPORT_4SQL}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='voicemail or answering service',dialed_number_debug=$3 where id=$1;" | mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME}
	;;
	4)
		echo "<tr><td>Human not confirmed</td><td>$3</td><td>$4</td><td>$1</td><td>`date`</td></tr>" >> ${REPORT}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='not confirmed human',dialed_number_debug=$3 where id=$1;" >> ${REPORT_4SQL}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='not confirmed human',dialed_number_debug=$3 where id=$1;" | mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME}
	;;
	5)
		echo "<tr><td>Hangup:</td><td>$3</td><td>$4</td><td>$1</td><td>`date`</td></tr>" >> ${REPORT}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='hangup',dialed_number_debug=$3 where id=$1;" >> ${REPORT_4SQL}
		echo "update ${TABLENAME} set processed=1,processed_datetime='`date +"%Y-%m-%d %H:%M:%S"`',result='hangup',dialed_number_debug=$3 where id=$1;" | mysql -h ${DB_HOST} -u ${DB_USER} -p${DB_PASS} ${DB_NAME}
	;;
	*)
		echo "<tr><td>Unknown error. Contact admin</td><td>$3</td><td>$4</td><td>$1</td><td>`date`</td></tr>" >> ${REPORT}
		#echo "update ${TABLENAME} set processed=0,processed_datetime=now(),result='unknown error',dialed_number_debug=$3 where id=$1;" >> ${REPORT_4SQL}
	;;
esac
