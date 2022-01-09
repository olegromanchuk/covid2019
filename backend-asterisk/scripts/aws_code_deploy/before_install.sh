#!/bin/bash
#exit on error
set -e

#check that no campaigns are running
if [[ -s ${WWW_DIR}/covid2019-auto-dialer-front/public/runcampaign ]]; then
	echo "Campaign is running. Can not do an upgrade now"
	exit 1
fi