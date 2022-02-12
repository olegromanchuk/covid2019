#!/bin/bash
#exit on error
set -e
WORK_DIR=/usr/local/utils/covid/backend-asterisk/

cd ${WORK_DIR} &&
echo "Moving asterisk files in place..."
cp -prf ${WORK_DIR}/covid_sounds/* /usr/share/asterisk/sounds/covid2019/
