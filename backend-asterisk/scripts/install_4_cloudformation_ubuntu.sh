#!/bin/bash

#Install everything first time

if [[ ! -f /usr/local/utils/systeminstalled_flag ]]; then

mkswap /dev/xvdf
swapon /dev/xvdf
echo "/dev/xvdf none swap sw 0 0" >> /etc/fstab

rm /etc/localtime
ln -sf /usr/share/zoneinfo/America/New_York /etc/localtime

apt-get update -y
apt-get upgrade -y
apt install php-bcmath composer unzip nginx php-common php-fpm php-json php-mbstring php-zip php-cli php-xml curl php-tokenizer mailx -y

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

sudo systemctl stop nginx.service
sudo systemctl start nginx.service
sudo systemctl enable nginx.service

composer global require laravel/installer

#updating apache config
sed -i 's#DocumentRoot "/var/www/html"#DocumentRoot "/var/www/html/covid2019-auto-dialer-front/public"#' /etc/httpd/conf/httpd.
echo "<LocationMatch "^/+\$">
          Options -Indexes
          ErrorDocument 403 /.noindex.html
      </LocationMatch>

      <Directory /var/www/html/covid2019-auto-dialer-front/public>
          AllowOverride All
      </Directory>" > /etc/httpd/conf.d/covid2019.conf
systemctl restart httpd

echo "* * * * * /usr/local/utils/covid/cron_campaign_checker.sh" >> /var/spool/cron/root
systemctl reload crond

mkdir /var/log/festival
cd /var/log/festival
/bin/festival_server &
echo "cd /var/log/festival/; /bin/festival_server &" >> /etc/rc.local

systemctl enable iptables
systemctl disable firewalld
systemctl stop firewalld
systemctl disable mysqld
dnf install git -y


##install codedeploy Agent
#yum update -y
#yum install ruby -y
#yum install wget -y
#cd /usr/local/src/
#wget https://aws-codedeploy-us-east-2.s3.us-east-2.amazonaws.com/latest/install
#chmod +x ./install
#sudo ./install auto
##systemctl status codedeploy-agent

#add flag that everything is installed
touch /usr/local/utils/systeminstalled_flag
fi

#install aws. Probably, not necessary
#cd /usr/local/src/ &&
#curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
#unzip awscliv2.zip
#sudo ./aws/install


scripts/aws_code_deploy/before_install.sh
scripts/aws_code_deploy/install.sh
scripts/aws_code_deploy/application_start.sh
scripts/aws_code_deploy/validate.sh