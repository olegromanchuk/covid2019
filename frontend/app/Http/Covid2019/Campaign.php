<?php

namespace App\Http\Covid2019;

use Illuminate\Support\Facades\Auth;

class Campaign
{

    private $db_user;
    private $db_pass;
    private $db_name;
    private $db_host;
    private $db_port;
    private $dbconnection;


    function __construct()
    {
        $this->db_user = env("AST_DB_USER", "");
        $this->db_pass = env("AST_DB_PASS", "");
        $this->db_name = env("AST_DB_NAME", "");
        $this->db_host = env("AST_DB_HOST", "");
        $this->db_port = env("AST_DB_PORT", "");
    }

    public function getAsteriskPredictiveDialerRecords($campaignID)
    {
        $this->dbconnection = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_port);

        $sql = "select r.id,r.main_contact,r.main_contact_phone,r.email_address,r.processed,r.processed_datetime,r.result,r.description,c.name as campaign_name from callrecords r left join campaigns c on r.campaign_id=c.id WHERE c.id=" . $campaignID . ";";
        try {
            if ($result = mysqli_query($this->dbconnection, $sql)) {
                $rows = [];
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                return $rows;
            };
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function setAsteriskPredictiveDialerRecords($recordSet)
    {
        $this->dbconnection = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_port);

        $values = "";
        foreach ($recordSet as $record) {
            $values .= "('" . $record[1] . "','" . $record[0] . "'," . $record[2] . "),";
        }
        $values = substr($values, 0, -1);
        $sql = "INSERT into callrecords(main_contact, main_contact_phone, campaign_id) values " . $values . ";";
        try {
            if ($result = mysqli_query($this->dbconnection, $sql)) {
                return;
            };
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public static function startCampaign($campaignID)
    {
        $myfile = fopen("runcampaign", "w+");
        fwrite($myfile, $campaignID . "," . Auth::user()->email);
        fclose($myfile);
    }
}
