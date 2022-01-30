#!/bin/bash
#exit on error
set -e


echo "Moving asterisk files in place..."
#unalias cp
cp asterisk/extensions_covid2019.conf /etc/asterisk/
cp asterisk/pjsip_covid2019.conf /etc/asterisk/

#check if we need to update asterisk config
SIPCONF_INCLUDE_US=`cat /etc/asterisk/pjsip.conf  | grep "pjsip_covid2019" | wc -l`
if [[ ${SIPCONF_INCLUDE_US} == 0 ]]; then
        echo "#include pjsip_covid2019.conf" >> /etc/asterisk/pjsip.conf
fi
EXTENSIONS_INCLUDE_US=`cat /etc/asterisk/extensions.conf  | grep "extensions_covid2019" | wc -l`
if [[ ${EXTENSIONS_INCLUDE_US} == 0 ]]; then
        echo "#include extensions_covid2019.conf" >> /etc/asterisk/extensions.conf
fi

/sbin/asterisk -rx "sip reload"
/sbin/asterisk -rx "dialplan reload"
