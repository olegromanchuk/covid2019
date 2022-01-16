#!/bin/bash

#if base software (php, asterisk) is installed - run just specific installation scripts (dialer scripts, FE-laravel, BE-go)
if [[ -f /usr/local/utils/systeminstalled_flag ]]; then
  #the scripts in this section could also be run by codeDeploy
  cd /usr/local/utils/covid/backend-asterisk/
  ./scripts/aws_code_deploy/before_install.sh
  ./scripts/aws_code_deploy/install.sh
  ./scripts/aws_code_deploy/application_start.sh
  ./scripts/aws_code_deploy/validate.sh
  exit
fi

#Install everything first time
mkswap /dev/xvdf
swapon /dev/xvdf
echo "/dev/xvdf none swap sw 0 0" >>/etc/fstab

rm /etc/localtime
ln -sf /usr/share/zoneinfo/America/New_York /etc/localtime

apt-get update -y
apt-get upgrade -y
apt install apache2 php-bcmath unzip libapache2-mod-php php-common php-fpm php-json php-mbstring php-zip php-cli php-xml curl php-tokenizer php-mysql php-curl php-gd php-xml php-bcmath php-pear npm -y

apt install mysql-client -y
apt install golang -y

echo "installing composer..."
curl -sS https://getcomposer.org/installer | php &&
  mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

echo "installing fresh nodejs"
curl -fsSL https://deb.nodesource.com/setup_17.x | sudo -E bash -
sudo apt-get install -y nodejs

systemctl stop apache2.service
systemctl start apache2.service
systemctl enable apache2.service
ufw allow in "Apache Full"
sudo a2enmod rewrite

/usr/local/bin/composer global require laravel/installer

#updating apache config
echo "<VirtualHost *:80>
ServerName ${SITE_URL}
ServerAdmin ${ADMIN_EMAIL}
DocumentRoot /var/www/html/covid2019-auto-dialer-front/public
<Directory /var/www/html/covid2019-auto-dialer-front>
AllowOverride All
</Directory>
ErrorLog /var/log/apache2/error.log
CustomLog /var/log/apache2/access.log combined
</VirtualHost>" >/etc/apache2/sites-available/covid2019.conf

a2enmod rewrite
a2dissite 000-default
a2ensite covid2019
systemctl restart apache2.service

echo "* * * * * /usr/local/utils/covid/cron_campaign_checker.sh" >>/var/spool/cron/root
systemctl restart crond

#install asterisk 18
apt install make wget build-essential git autoconf subversion pkg-config asterisk -y

#apt install festival -y
#mkdir /var/log/festival
#cd /var/log/festival
#/bin/festival_server &
#echo "cd /var/log/festival/; /bin/festival_server &" >> /etc/rc.local

##install codedeploy Agent
#yum update -y
#yum install ruby -y
#yum install wget -y
#cd /usr/local/src/
#wget https://aws-codedeploy-us-east-2.s3.us-east-2.amazonaws.com/latest/install
#chmod +x ./install
#sudo ./install auto
##systemctl status codedeploy-agent

#add flag that everything was installed
touch /usr/local/utils/covid/systeminstalled_flag

#install aws. Probably, not necessary
#cd /usr/local/src/ &&
#curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
#unzip awscliv2.zip
#sudo ./aws/install

cd /usr/local/utils/covid/backend-asterisk/
./scripts/aws_code_deploy/before_install.sh
./scripts/aws_code_deploy/install.sh
./scripts/aws_code_deploy/application_start.sh
./scripts/aws_code_deploy/validate.sh
