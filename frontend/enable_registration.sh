#!/bin/bash
if [[ $# != 1 ]]; then
	echo "Use $0 on|off"
	exit 1
fi

cd /var/www/html/covid2019-auto-dialer-front/ &&

if [[ $1 == "on" ]]; then
	cp resources/views/auth/register_enabled.blade.php resources/views/auth/register.blade.php
else
	cp resources/views/auth/register_disabled.blade.php resources/views/auth/register.blade.php
fi	
php artisan cache:clear
echo "Use http://`hostname`/register"
