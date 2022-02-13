#!/usr/bin/php
<?php

require '/root/.config/composer/vendor/autoload.php';
require_once 'config_campaign_generator.php';
// $source_email=getenv(string $SOURCE_EMAIL);
//
//
//
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Iam\IamClient;

function getName($n) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';

	for ($i = 0; $i < $n; $i++) {
		$index = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$index];
	}

	return $randomString;
}

function sendEmailSES($recipient_emails, $subject, $sender_email, $html_body) {

	var_dump($recipient_emails);

	$SesClient = new Aws\Ses\SesClient([
		'version' => '2010-12-01',
		'region' => 'us-west-2'
	]);
	$char_set = 'UTF-8';

	try {
		$result = $SesClient->sendEmail([
			'Destination' => [
				'ToAddresses' => $recipient_emails,
			],
			'ReplyToAddresses' => [$sender_email],
			'Source' => $sender_email,
			'Message' => [

				'Body' => [
					'Html' => [
						'Charset' => $char_set,
						'Data' => $html_body,
					],
				],
				'Subject' => [
					'Charset' => $char_set,
					'Data' => $subject,
				],
			],
		]);
	} catch (AwsException $e) {
		// output error message if fails
		echo $e->getMessage();
		echo "\n";
	}

}

/*AMNT=`ls /var/spool/asterisk/outgoing | wc -l`
  while [ ${AMNT} -gt 0 ]
  do
  echo "Waiting for the end of the call"
  sleep 5
  AMNT=`ls /var/spool/asterisk/outgoing | wc -l`
  done
  echo "select customer_name,main_contact_phone from customcallpastdue where pd_30>0 and pd_60=0" | /usr/bin/mysql
# /var/spool/asterisk

 */

$campaignNumber=$argv[1];
$campaignLogFolder=$argv[2];

# define constants
define("DOCUMENT_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);

define("INCLUDE_PATH", DOCUMENT_ROOT ."include". DIRECTORY_SEPARATOR);
define("LOG_PATH", DOCUMENT_ROOT ."log". DIRECTORY_SEPARATOR);
define("TEMP_PATH", DOCUMENT_ROOT ."tmp". DIRECTORY_SEPARATOR);

define("OUTGOING_PATH", "/var/spool/asterisk/outgoing/");

define("SLEEP_BATCH_TIME", 10);

ini_set("include_path", "..:". INCLUDE_PATH .":". ini_get("include_path"));

set_time_limit(0);
#error_reporting(0);

//$requestedAmountOfActiveCalls=2;
//$callFileName="covid2019.call";
$campaignFile="/var/www/html/covid2019-auto-dialer-front/public/runcampaign";
$covidresultfile="/tmp/covidcollector.txt";
$mysqlfile="/tmp/covidreport_sql.txt";
$workdir="/usr/local/utils/covid/backend-asterisk/";

require_once("func.base.php");
require_once("plugin.mysql.php");

//get email
$campaignFileHandler = fopen($campaignFile, 'r');
$campaignRunnerInfo=fgetcsv($campaignFileHandler); //1,email@mail.net
fclose($campaignFileHandler);
$campaignEmail=$campaignRunnerInfo[1];

$db = new mysqli($servername, $username, $password, $dbname);

//Emptifying result file

$fh = fopen($covidresultfile,'w');
fputs($fh, "<html><body><table style='border: 1px'>");
fputs($fh, "<thead><tr><th>Result</th><th>Number</th><th>Name</th><th>ID</th><th>Date</th></tr></thead>");
fclose($fh);

//Emptifying sql result file
$fsql = fopen($mysqlfile,'w');
fclose($fsql);
//change group and add permissions, so asterisk also can access these files
chgrp($covidresultfile, 'asterisk');
chgrp($mysqlfile, 'asterisk');
chmod($covidresultfile, 0660);
chmod($mysqlfile, 0660);


$fh = fopen($mysqlfile,'w');
fclose($fh);


echo "Started COVID2019 campaign " . $campaignNumber . " for " . $campaignEmail . " at " . date("r") . " \n";


//get campaign calls
$query = "
SELECT
`id`,
	`main_contact`,
	`main_contact_phone`
	FROM
	`callrecords`
	where
	`processed` = 0
	AND
	`campaign_id` = " . $campaignNumber . " order by
	`main_contact_phone`
	";

	$result = $db->query($query);

	if ($result === NULL) {
		criticalError("Error - no callers");
	}


	//get application options
$queryOptions = "
SELECT
`value`
	FROM
	`options`
	where
	`name` = 'amount_of_simultaneous_calls'
	";

$resultOptions = $db->query($queryOptions);

if ($resultOptions === NULL) {
	criticalError("Can't get options");
}

$requestedAmountOfActiveCallsArray = $resultOptions->fetch_assoc();
$requestedAmountOfActiveCalls = $requestedAmountOfActiveCallsArray['value'];


while (($row = $result->fetch_assoc())) {
	$phpMainContactPhoneRaw=$row['main_contact_phone'];
	$phpMainContactPhone=preg_replace("/[^0-9]/","",$phpMainContactPhoneRaw);

	$phpMainContactNameRaw=$row['main_contact'];
	$phpMainContactName=preg_replace("/[^A-Za-z0-9]/","_",$phpMainContactNameRaw);

	$phpContactID=$row['id'];

	//$phpCustomerAcctid=trim($row['account_id']);
	//$phpCustomerNameRaw=trim($row['customer_name']);
	//$phpCustomerName=preg_replace("/[^A-Za-z0-9]/","_",$phpCustomerNameRaw);

	echo date("M d H:i:s") . " Process $phpMainContactName  - phone: $phpMainContactPhone ...\n";


	$salt=getName(7);
	$callFileName=$salt . ".call";
	if (($fp = @fopen(TEMP_PATH . $callFileName, "w+")) === FALSE) {
		criticalError("Can not create call file: ". TEMP_PATH . $callFileName);
	}

	fputs($fp, "Channel: PJSIP/dt/sip:+1$phpMainContactPhone@$sip_trunk\n");
	fputs($fp, "CallerID: \"$origination_company_name\" <$origination_phone_number>\n");
	fputs($fp, "Context: from-xxot-covid\n");
	fputs($fp, "MaxRetries: 2\n");
	fputs($fp, "RetryTime: 60\n");
	fputs($fp, "Extension: 588\n");
	fputs($fp, "WaitTime: 45\n");
	fputs($fp, "Priority: 1\n");
	fputs($fp, "Archive: Yes\n");
	fputs($fp, "Set: ACCTID=$phpContactID\n");
	fputs($fp, "Set: CONTACTNAME=$phpMainContactName\n");
	fputs($fp, "Set: DIALEDNUMBER=$phpMainContactPhone\n");

	fclose($fp);

	touch(TEMP_PATH . $callFileName, time() + 2);
	chown(TEMP_PATH . $callFileName, 'asterisk');
	chgrp(TEMP_PATH . $callFileName, 'asterisk');

	rename(TEMP_PATH . $callFileName, OUTGOING_PATH . $callFileName);


	$amountOfCallsActive=exec('ls /var/spool/asterisk/outgoing/ | wc -l');
	while ($amountOfCallsActive >= $requestedAmountOfActiveCalls ) {
		//echo $amountOfCallActive . " active calls present. Going to sleep...\n";
		sleep(10);
		$amountOfCallsActive=exec('ls /var/spool/asterisk/outgoing/ | wc -l');
	}

	////old code with one call only
//	$isCallActive=exec('ls /var/spool/asterisk/outgoing/ | wc -l');
//	while ($isCallActive) {
//		//echo "active calls present. Going to sleep...\n";
//		sleep(20);
//		$isCallActive=exec('ls /var/spool/asterisk/outgoing/ | wc -l');
//	}
}

//End cycle

//wait until all calls are completed and asterisk updates report files
sleep(10);  //in case of one call only. Asterisk needs time to establish a call
$amountOfAsteriskCallsActive=exec('/usr/sbin/asterisk -rx "core show calls" | grep "active" | cut -d " " -f1');
$finalCounter=0;
$errorMsg="";
while ($amountOfAsteriskCallsActive > 0) {
        sleep(10);
        $amountOfAsteriskCallsActive=exec('/usr/sbin/asterisk -rx "core show calls" | grep "active" | cut -d " " -f1');
        $finalCounter++;
        if ($finalCounter > 20) {
        //something unhealthy. Cycle was ended, but calls are still present
        	$errorMsg="something unhealthy. Cycle was ended, but calls are still present";
        	break;
		}
}

$command_update_sql="cat /tmp/covidreport_sql.txt | mysql -h " . $servername . " -u " . $username . " -p" . $password . " " . $dbname;
system($command_update_sql);

$fh = fopen($covidresultfile,'a');
fputs($fh, "</table></body></html>");
//if any errors - add them
fputs($fh, $errorMsg);
fclose($fh);

$headers = "From: \"COVID2019 Dialer\"<" . getenv ('ADMIN_EMAIL', "") . ">\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$mailMessage = file_get_contents($covidresultfile);

//mail($campaignEmail,"COVID2019 report",$mailMessage, $headers);
sendEmailSES([$campaignEmail],"auto-dialer report", $campaignEmail, $mailMessage);
echo "Finished COVID2019 campaign " . $campaignNumber . " at " . date("r") . " \n";

//backup up sqlresult file
$covidreportfile="covidreport_sql_campaign_" . $campaignNumber . "_" . date("U") . ".sql";
copy ($mysqlfile, $workdir . "log/" . $campaignLogFolder . "/" . $covidreportfile);
?>