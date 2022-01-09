### TODO
- [ ] screenshots
- [ ] config_campaign_generator_template
- [ ] covidcampaigngenerator.php
- [ ] embed install_4_cloudformation.sh into cloudformation-template.yml
- [ ] add to cloudformation-template ami from all regions. Currently, the static ami-0892d3c7ee96c0bf7 is set for us-west-2 only
- [ ] installation via code deploy pipeline. Create codedeploy pipeline template for cloudformation

# Automated Dialing System MaWaSys
#### (mass warning system)

This software originally was created to inform multiple users over the phone about some event and collect simple data, like confirmation, rejection via DTMF tones.
It could be used for weather events, informational ads and so on. The systems like this also is known as "school dialing system".
The list of contacts could be uploaded from csv file and edited withing the system if necessary. It is possible to create different campaigns and track the results withing each campaign.  
The system can detect answering machine and is based on open source asterisk software.

## Description and architecture
The system was designed to run on aws EC2 instance and consists of next components:
- EC2 instance (Centos 7) - runs core voip engine (asterisk 18), frontend (Laravel), backend (goland)
- asterisk PBX (runs on EC2)
- frontend GUI (runs on EC2 - Laravel) 
- backend for GUI (runs on EC2 - golang)
- MariaDB - used for frontend authentication and store information about contacts and campaigns


<details><summary>Screenshots</summary>

Contacts view:
![](screenshots/main_page.png)
  
Campaign view:
![](screenshots/campaign.png)
  
</details>

## covid2019-predictive-dialer-front


## Install

2 ways:
* installation via scrip (not via code deploy)

Create EC2 instalnce with next params:
* AMI - covid-deploy-ast17-php7-codedeploy (ami-09ef5547bc1ff6640)
* Instance type: t2-small
* Network: VPC Covid2019
* IAM Role: CodeDeploy-EC2-Instance-Profile
* User data:
````
DB_NAME=covid2019_test
DOMAIN_NAME=covid-test.mydomain.com
PHONE_NUMBER=6463401048
IP_ADDRESS=74.117.148.101
````
* Tags:
````
Name: covid-test
Project: covid2019
Environment: development (production)
CodeDeploy: yes
````
* Security Groups:
````
covid2019
Vitelity
````

Assign IP address.  
Create domain-name in Route53.  
Route phone number on Vitelity to assigned IP.


Proceed to codedeploy -> Deployments. Select deployment where deployment group is "covid2019-development" (or covid2019-production) and retry deployment.
New version will be installed automatically if tags "CodeDeploy=yes" and Environment="development" (or production). CodeDeploy will look into "appspec.yml" file in the repo "covid2019-predictive-dialer-backend-asterisk". This is a file where all the logic where to copy the files is described.



## Deployment
### On development workstation
Use Alfred keyword "deploy" to commit all and update EC2 instances. The keyword "deploycovidfrontdevel" requires an argument "commit message" for frontend.
The keyword "deploycovidfrontproduction" requires an argument "OK" to start deployment.

````
#compile binary for backend
cd backend-go/
env GOOS=linux GOARCH=amd64 go build -o backend-dialer rest.go
cp backend-dialer ~/DEVEL_local/covid2019-predictive-dialer-backend-asterisk/backend/

#commit frontend
cd ~/DEVEL_local/covid2019-predictive-dialer-front/ &&
git add *
git commit -m "commit message"; git push
echo `date +%s` > ~/DEVEL_local/covid2019-predictive-dialer-backend-asterisk/framework_version.txt
````
To push frontend update on production codedeploy it is necessary to update main repo covid2019-predictive-dialer-backend-asterisk 
We use file framework_version.txt to reflect that frontend was updated. After that you need to 
1. push updates to covid2019-predictive-dialer-backend-asterisk. 
2. Create new deployment on codedeploy (copy from previous deployment and update git commit ID).
Make sure to test all before pushing to production.

````
### MYSQL dump
cd ~/DEVEL_local/covid2019-predictive-dialer-backend-asterisk/
mysqldump --extended-insert=false -d asterisk_dialer_stat > database.sql
mysqldump --extended-insert=false -d -h cdrrapport.improcom.com -u coviduser -piVenusCovidpss covid2019dev >> database.sql
#if the update requires adding new param do not forget to update:
1. scripts/db_update_vXXX.sh (create new one)
2. database_version.txt
3. create dump of current DB, so new installations get the latest db_version
4. install.sh - if necessary to add new INSERT for param to the database

### Update asterisk files
cd ~/DEVEL_local/covid2019-predictive-dialer-backend-asterisk/
echo "1" > asterisk/update_asterisk
git add *

### Update asterisk voice files
cd ~/DEVEL_local/covid2019-predictive-dialer-backend-asterisk/
echo "1" > asterisk/update_asterisk_sound
git add *
````



#### Clean
See instructions in 1Password. Search for "Install covid2019 dialer"

### Changelog

#### Version 1.4
- [x] Added status for running campaign
- [x] Implemented code deploy

#### Version 1.3
- [x] Added restriction and validation for repetitive phone numbers. Only unique phone numbers are allowed in contacts and campaign records.

#### Version 0.5
- [x] Added Contact-to-Campaign converting logic

#### Version 0.4
- [x] Added Contacts and CRUD logic


#### Version 0.3
- [x] Added Contacts


#### Version 0.2
- [x] Added Datatables
- [x] Added "About"

#### Version 0.1
- [x] Initial version


## Installation

##### 1. Add swap https://dev.to/hardiksondagar/how-to-use-aws-ebs-volume-as-a-swap-memory-5d15
````



mkswap /dev/xvdf
swapon /dev/xvdf
echo "/dev/xvdf none swap sw 0 0" >> /etc/fstab

rm /etc/localtime
ln -sf /usr/share/zoneinfo/America/New_York /etc/localtime


wget -q http://rpms.remirepo.net/enterprise/remi-release-7.rpm &&
wget -q https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm &&

rpm -Uhv remi-release-7.rpm &&
rpm -Uhv epel-release-latest-7.noarch.rpm &&

yum-config-manager --enable remi-php72
yum update -y
yum install composer -y
yum install mailx -y
yum install npm -y
yum install festival -y
yum install yum-utils -y
yum install rpmconf -y 
yum install ruby -y
yum install wget -y
echo "https://www.tecmint.com/upgrade-centos-7-to-centos-8/"
read -p "Answer: N Y"
package-cleanup --leaves
package-cleanup --orphans
yum install dnf -y
dnf upgrade -y



composer global require laravel/installer

#updating apache config
sed -i 's#DocumentRoot "/var/www/html"#DocumentRoot "/var/www/html/covid2019-predictive-dialer-front/public"#' /etc/httpd/conf/httpd.
echo "<LocationMatch "^/+$">
          Options -Indexes
          ErrorDocument 403 /.noindex.html
      </LocationMatch>
      
      <Directory /var/www/html/covid2019-predictive-dialer-front/public>
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


#install codedeploy Agent
yum update -y
yum install ruby -y
yum install wget -y
cd /usr/local/src/
wget https://aws-codedeploy-us-east-2.s3.us-east-2.amazonaws.com/latest/install
chmod +x ./install
sudo ./install auto
#systemctl status codedeploy-agent


#install aws. Probably, not necessary
#cd /usr/local/src/ &&
#curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
#unzip awscliv2.zip
#sudo ./aws/install

````

read -p "Edit .env to match db settings"
echo "Check crontab. Remove * * * * * /asterisk from there"
echo "php artisan route:cache"
echo "-A INPUT -s 64.2.142.0/24 -j ACCEPT
-A INPUT -s 66.241.99.0/24 -j ACCEPT
-A INPUT -s 207.166.136.0/24 -j ACCEPT"



echo "https://bootstrapious.com/p/bootstrap-sidebar"


````
/scaffold authorization
php artisan ui bootstrap --auth

//create authentication tables
php artisan migrate
//create controller
php artisan make:controller ContactController --resource
php artisan view:clear; php artisan route:clear; php artisan cache:clear; php artisan config:clear
````

### Help
Please, note that there are significant changes in the web interface and application logic. Contacts were separated from Campaigns and now you do not need to import contacts each time you want to create a campaign.
It is enough to import Contacts only once (Contacts -> Load Contacts). The logic is the same as in previous version - you can just paste CSV data directly into the web form. Note, that field sequence is different from the previous version: name, phone number, email, note.
You can use "note" as a patient name (until we release a next version with required fields). 
Another option to add contacts - to use the button "New" on the "Contacts -> Contacts" page. Also, you can edit/delete existing contacts. Select a contact by mouse click and use Edit/Delete buttons. You can also do mass edit for multiple contacts (use SHIFT+mouse_click to select more than one contact)

To start a dialing campaign you need to create it first (Call Records -> Campaigns). 
Then you need to copy contacts to this campaign. Go to Contacts->Contacts, select required contacts (use SHIFT+mouse_click to select multiple contacts) and press the button "Create Campaign". Select desired campaign.
To start campaign go to Call Records->Call Records. Select a campaign. The records for this campaign should be displayed. In the upper right corner select "Start campaign". Withing 1 minute the system should start dialing numbers. 
After the campaign is finished you should receive an email and records in database should update. Note, that email just a notification and contains just basic information about records. Use web interface to get all records from the campaign.


#### TODO
- [ ] add visibility to campaign!!!
- [ ] add delete button for callrecords which are not processed
- [ ] add "stop campaign" button
- [ ] add option to set amount of simultaneous calls
- [ ] automation!!! 1. Create instance from template. 2. Assign IP. 3. Assign domain name. 4. Assign vitelity number. 5. Mark on M6 as used.
- [ ] backup database with call records to S3 in 2 places
- [ ] check errors if db unaccessible. 
- [ ] Fix the bug with incorrect results!!!
- [ ] check if fail 2ban blocks ssh and httpd wrong username attempts
- [ ] /var/spool/asterisk/outgoing_done/ (covid/logs) to S3
- [ ] change password: https://www.5balloons.info/setting-up-change-password-with-laravel-authentication/
- [ ] increase speed (maybe run it in parallel)
- [ ] add update number on vitelity
- [ ] add campaign number automatically when insert new numbers
- [ ] http://www.digium.com/en/products/software/cepstral.php
- [ ] Campaign status. Remove hunged campaigns
- [ ] logrotate for /usr/local/utils/covid/log/
- [ ] Should call history to be editable? 
- [ ] Do you want an easy selection for the "voicemail/answering service" results and the ability to run a campaign on these numbers again?
- [ ] Do you want to export/edit the history; how long it should be kept? 
- [ ] Do you want text-to-speech notification generator? 
- [ ] Do you want to keep the history of notifications? Do you want to see which message was played for a certain number?
- [ ] Do you want a better web design for the interface?
- [ ] festival with AWS
- [ ] Ubuntu image
- [ ] fix reset pass link
- [ ] aws text-to-speech
- [x] update logic on cron_campaign_checker.sh, so each record inserts into DB immediately, not at the end of campaign.
- [x] chrome vs IE vs firefox. Set alert that works only in chrome
- [x] SETUP archiver for /var/spool/asterisk/outgoing_done directory!!!
- [x] Disable laravel error messages
- [x] update logic in cron_campaign_checker, so failed calls do not overrides with "hangup result"
- [x] on M6 reserve 6463401050
                    6463401045
                    6463401046
                    6463401047
                    6463401048
                    6463401053
##### Testing
- [ ] successfull call human with confirmation
- [ ] successfull call human hangup his side
- [ ] successfull call human hangup our side
- [ ] successfull call aa hangup his side
- [ ] successfull call aa hangup our side
- [ ] failed call 
- [ ] check if IVR works
- [ ] check that registration is disabled

