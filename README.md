### TODO
- [ ] screenshots
- [ ] config_campaign_generator_template
- [ ] covidcampaigngenerator.php
- [ ] embed install_4_cloudformation.sh into cloudformation-template.yml
- [ ] add to cloudformation-template ami from all regions. Currently, the static ami-0892d3c7ee96c0bf7 is set for us-west-2 only
- [ ] installation via code deploy pipeline. Create codedeploy pipeline template for cloudformation
- [ ] update git clone ${!REPO_ADDRESS} covid in user-data in ec2. Remove authorization
- [ ] update cloudformation-template.yml - remove ssh private key for repo. Remove after repo is public!!!
- [ ] festival

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

## covid2019-auto-dialer-front


## Install

2 ways:
* installation via script (not via code deploy)