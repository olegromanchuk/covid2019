#!/bin/bash

RUNCAMPAIGNFILE=/var/www/html/covid2019-auto-dialer-front/public/runcampaign

cd /usr/local/utils/covid/backend-asterisk/ &&
if [[ -f ${RUNCAMPAIGNFILE} && ! -f /usr/local/utils/covid/backend-asterisk/tmp/campaign_in_progress ]];then
	CAMPAIGN_NUMBER=`cat ${RUNCAMPAIGNFILE} | awk -F"," '{print$1}'`
	CAMPAIGN_EMAIL=`cat ${RUNCAMPAIGNFILE} | awk -F"," '{print$2}'`
	CAMPAIGN_LOG_FOLDER=campaign_${CAMPAIGN_NUMBER}_`date +%Y-%m-%d-%H-%M-%S`
	echo "$(date ) -- Started campaign ${CAMPAIGN_NUMBER}. Email: ${CAMPAIGN_EMAIL}"	touch /usr/local/utils/covid/backend-asterisk/tmp/campaign_in_progress
	mkdir log/${CAMPAIGN_LOG_FOLDER} &&
	/usr/local/utils/covid/backend-asterisk/covidcampaigngenerator.php ${CAMPAIGN_NUMBER} ${CAMPAIGN_LOG_FOLDER} > log/${CAMPAIGN_LOG_FOLDER}/campaign_${CAMPAIGN_NUMBER}_`date +%Y-%m-%d-%H-%M-%S`.log &&
	rm -rf /usr/local/utils/covid/backend-asterisk/tmp/campaign_in_progress
	rm -rf /var/www/html/covid2019-auto-dialer-front/public/runcampaign
	# backup outgoing_done records
	cd /var/spool/asterisk/outgoing_done/ &&
    if [[ $(ls . | wc -l) -gt 0 ]]; then
		tar -czf /usr/local/utils/covid/backend-asterisk/log/${CAMPAIGN_LOG_FOLDER}/outgoing_done_${CAMPAIGN_NUMBER}.tgz * &&
		rm -rf /var/spool/asterisk/outgoing_done/*
    fi
    echo "$(date ) -- Finished campaign ${CAMPAIGN_NUMBER}. Email: ${CAMPAIGN_EMAIL}"
fi
