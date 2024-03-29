# Automated Dialing System (open source) - ADSos
[![License](http://img.shields.io/:license-mit-blue.svg)](http://doge.mit-license.org)
[![CI](https://github.com/olegromanchuk/covid2019/actions/workflows/frontend.yaml/badge.svg)](https://github.com/olegromanchuk/covid2019/actions/workflows/frontend.yaml)
<!-- [![Go Report Card](https://goreportcard.com/badge/github.com/olegromanchuk/covid2019)](https://goreportcard.com/report/github.com/olegromanchuk/covid2019) -->
<!-- [![Build Status](https://github.com/olegromanchuk/covid2019/workflows/build/badge.svg)] -->

This software originally was created to inform multiple users over the phone about some event and collect simple data, like confirmation, via DTMF tones.
It could be used for weather events, informational ads and so on. The systems like this is also known as "school dialing system".
The list of contacts could be uploaded from csv file and edited withing the system, if necessary. It is possible to create different campaigns and track the results withing each campaign.   
The system can detect answering machine and is based on open source software "asterisk".

## Description and architecture
The system was designed to run on aws EC2 instance and RDS and consists of next components:
- EC2 instance (Ubuntu 20.04.4 LTS) - runs core voip engine (asterisk 18), frontend, backend
- asterisk PBX (runs on EC2)
- frontend GUI (runs on EC2 - Laravel) 
- backend for GUI (runs on EC2 - Golang)
- MariaDB - used for frontend authentication and stores information about contacts, campaigns and calls (runs on RDS)


<details><summary>Screenshots</summary>

Contacts view:
![](docs/screenshots/contacts.png)
  
Calls view:
![](docs/screenshots/campaign_progress.png)
  
</details> 

<!-- ### Site of the project:
[https://adsos.us](http://adsos.us) -->

---
## Installation

### **DISCLAIMER!!!**  
*Voice over IP (a technology, which is a part of this stack) is an area which involves many fraudulent schemas. Multiple bots are constantly scanning internet and searching for unprotected SIP servers. It is absolutely necessary to make sure that this instance is throughfully protected and SIP traffic is allowed ONLY from trusted servers, like Amazon Chime or your SIP provider. During the cloudformation stack creation you will be asked for SIP signalling and media IP addresses. By default there Chime IP addresses are set. NEVER set this value to 0.0.0.0 even for testing. The server could easily be hacked withing hours and you may end up with 4-5 figures bill from your SIP provider just in one night.*


There are 2 options on how to install: via cloudformation or codedeploy.
### Installation via cloudformation script
1. Create a stack from cloudformation script (cloudformation-template.yml). 
Must be set to custom values:
- KeyName (aws ssh key)
- PhoneNumber - chime phone number (chime->calling->phone number management)
- SIPHost - Chime custom url (Calling->Voice connectors. Outbound host name)
- VPCId
It will create all necessary components, except Amazon Chime. You should have a configured Chime account (or any other SIP provider account)
2. Update Origination/Termination IP address in Chime's settings. IP address can be found in the "Outputs" section " of cloudformation (InstanceIPAddress).You need origination (incoming calls) to be able to record a greeting which will be played by the dialer. The original text see below in the "Misc information". If you do not want to make recording over the phone you may skip the origination setup.
Allow plenty of time for installation even after CF reports that the stack is completed (20 minutes)!!!

3. After installation is complete you need to login on EC2 instance and enable user registration. Check cloudformation outputs to get an IP address of the instance. Run the next commands to login to the instance:
```
ssh -p 22 ubuntu@EC2ADDRESS -i ~/.ssh/AWS-key.pem
sudo /var/www/html/covid2019-auto-dialer-front/enable_registration.sh on 
```
5. Login to http://INSTANCE_IP_ADDRESS/register and QUICKLY register a new user.
6. IMPORTANT: run next command to disable registration. Otherwise, anybody will be able to register. By default, there are no restrictions for registration, but it is disabled by default. If you plan to keep it open - make sure that only trusted IP addresses can reach the instance ports tcp 80 and 443 
```
sudo /var/www/html/covid2019-auto-dialer-front/enable_registration.sh off
```
7. The system is ready. Read "Help->Help" or instructions below to learn how to use the system.

### Installation via cloudformation script
Not deployed in public repo yet.


## Local development (frontend)  
To run frontend in docker for local development run frontend/scripts/run-dev-env.sh

## Misc information
---

### covid_recorded_human.wav
Hello. This is a test message from the automatic dialing system. Press "one" to confirm this message or "two" to listen it again. Thank you.


### Help
To start a dialing campaign you need to create it first (Call Records -> Campaigns).
Then you need to copy contacts to this campaign. Go to Contacts->Contacts, select required contacts (use SHIFT+mouse_click to select multiple contacts) and press the button "Add to campaign". Select desired campaign. To start campaign go to Call Records->Call Records. Select a campaign. The records for this campaign should be displayed. In the upper right corner select "Start campaign". Withing 1 minute the system should start dialing numbers. After the campaign is finished you should receive an email and records in database should update. Note, that email just a notification and contains just basic information about records. Use web interface to get all records from the campaign.
It is enough to import Contacts only once (Contacts -> Load Contacts). You can just paste CSV data directly into the web form. Another option to add contacts - to use the button "New" on the "Contacts -> Contacts" page. Also, you can edit/delete existing contacts. Select a contact by mouse click and use Edit/Delete buttons. You can also do mass edit for multiple contacts (use SHIFT+mouse_click to select more than one contact)


### cli utils for sound conversion to asterisk compatible format
afconvert -d LEI16 -f 'WAVE' covid_recorded_human_backup.mp3 covid_recorded_human_backup.wav
sox covid_recorded_human_backup.wav -r 8000 -c1 covid_recorded_human_backup_8000.wav
