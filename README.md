### TODO specific
- [x] sort desc by id campaigns in frontend
- [x] remove role "removeme"
- [ ] add role for EC2 to use SES and fix cloudformation
- [x] add HELP description for structure of covid_recorded_human_backup.wav
- [ ] test on clean aws account (IAM existing roles check)
- [ ] screenshots
- [ ] config_campaign_generator_template
- [ ] covidcampaigngenerator.php
- [ ] check that useddata .evn variables survive instance reboot. Move .env to ec2 tags  
- [ ] remove default values from cloudformation template
- [ ] add SSL for FE  
- [ ] embed install_4_cloudformation.sh into cloudformation-template.yml
- [ ] add to cloudformation-template ami from all regions. Currently, the static ami-0892d3c7ee96c0bf7 is set for us-west-2 only
- [ ] installation via code deploy pipeline. Create codedeploy pipeline template for cloudformation
- [ ] update git clone ${!REPO_ADDRESS} covid in user-data in ec2. Remove authorization
- [ ] update cloudformation-template.yml - remove ssh private key for repo. Remove after repo is public!!!
- [ ] festival
- [ ] asterisk in docker on fargate - release 2.0.0
- [ ] laravel in docker - release 3.0.0
- [ ] improve cf-template for better support sip media and sip signalling IP addresses. Should allow multiple CIDRs via csv. List<Strings>  1.1.1.0/24,2.2.2.2/24  etc ...
- [ ] set rules in iptables

### TODO general
- [ ] Integrate with Amazon Polly
- [ ] Add sms/email notifications


# Automated Dialing System (open source) ADSoS

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

## covid2019-auto-dialer-front


## Install

2 ways:
* installation via script (not via code deploy)

#### covid_recorded_human.wav
Hello. This is a test message from the automatic dialing system. Press one to confirm this message or two to listen it again. Thank you.

#### convertion
afconvert -d LEI16 -f 'WAVE' covid_recorded_human_backup.mp3 covid_recorded_human_backup.wav
sox covid_recorded_human_backup.wav -r 8000 -c1 covid_recorded_human_backup_8000.wav