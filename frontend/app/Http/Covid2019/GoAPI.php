<?php

namespace App\Http\Covid2019;

use GuzzleHttp;
use GuzzleHttp\Stream\Stream;
use Validator;
use Request;
use Response;

class GoAPI {

    public static function getContacts() {
        $result = self::sendRequest("contacts", '', "GET");
        return $result;
    }

    public static function uploadContacts($data) {
        $result = self::sendRequest("upload-contacts", $data, "POST");
        return $result;
    }

    public static function updateContacts($data) {
        $result = self::sendRequest("contacts", $data, "PUT");
        return $result;
    }

    public static function deleteContacts($data) {
        $result = self::sendRequest("contacts/delete", $data, "POST");
        return $result;
    }

    public static function createContact($data) {
        $result = self::sendRequest("contacts/create", $data, "POST");
        return $result;
    }

    public static function getCampaigns() {
        $result = self::sendRequest("campaigns", '', "GET");
        return $result;
    }

    public static function createCampaign($data) {
        $result = self::sendRequest("campaigns/create", $data, "POST");
        return $result;
    }

    public static function deleteCampaigns($data) {
        $result = self::sendRequest("campaigns/delete", $data, "POST");
        return $result;
    }

    public static function createCampaignCallRecords($data) {
        $result = self::sendRequest("campaign-call-records", $data, "POST");
        return $result;
    }


    /**
     * Send get to
     * @param type $url
     * @param type $debug
     * @return type
     */
    public static function get($url, $debug = false) {
        $result = self::sendRequest($url, "", "GET", $debug);
        return $result;
    }

    public static function post($url, $data, $debug = false) {
        $result = self::sendRequest($url, $data, "POST", $debug);
        return $result;
    }

    public static function put($url, $data, $debug = false) {
        $result = self::sendRequest($url, $data, "PUT", $debug);
        return $result;
    }

    public static function delete($url, $debug = false) {
        $result = self::sendRequest($url, "", "DELETE", $debug);
        return $result;
    }

    /**
     * Send API request
     * @param string $whatToDo /customers/bla-bla-bla/yo-ho-ho
     * @param array $data data array for POST requests
     * @param type $method GET/POST/PUT/DELETE/PDF default: POST
     * @return result in json, output page if PDF or Exception if error
     */
    private static function sendRequest($whatToDo, $data, $method = "POST", $debug = false) {
        $timeout = 5; //http connect_timeout

        if ($debug) {
            $GOAPIURL = env("GOAPI_DEBUG_URL", "");
        } else {
            $GOAPIURL = env("GOAPI_URL", "");
        }
        $client = new GuzzleHttp\Client();

        if ($data) {
            $data = self::normalizeDataTypes($data);
        }

        switch ($method) {
            case "GET":
                try {
                    $response = $client->get($GOAPIURL . $whatToDo, ['connect_timeout' => $timeout]);
                } catch (\Exception $e) {
                    return $e;
                }
                break;
            case "POST":
                try {
//                    $response = $client->post($GOAPIURL . $whatToDo, [
//                        'body' => json_encode($data, JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION),
//                        'headers' => ['Content-Type' => 'application/json']
//                    ]);

                    $response = $client->post($GOAPIURL . $whatToDo, [
                        'connect_timeout' => $timeout,
                        'body' => json_encode($data),
                        'headers' => ['Content-Type' => 'application/json']
                    ]);
                } catch (\Exception $e) {
                    return $e;
                }
                break;
            case "PUT":
                try {
                    $finalUrl = $GOAPIURL . $whatToDo;
//                    error_log($finalUrl);
                    //  $response = $client->put($finalUrl, [
                    //      'body' => json_encode($data, JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION),
                    //      'headers' => ['Content-Type' => 'application/json']
                    //  ]);
                    $response = $client->put($finalUrl, [
                        'connect_timeout' => $timeout,
                        'body' => json_encode($data),
                        'headers' => ['Content-Type' => 'application/json']
                    ]);
                } catch (\Exception $e) {
                    return $e;
                }
                break;
            case "DELETE":
                try {
                    $response = $client->delete($GOAPIURL . $whatToDo, ['connect_timeout' => $timeout]);
                } catch (\Exception $e) {
                    return $e;
                }
                break;
            default:
        }
        $response = json_decode($response->getBody()->getContents());
//        if (isset($response->error)) {   //check regenerateLastBill before enabling this code
//            $ex = new \Exception($response->error);
//            return $ex;
//        }
        return $response;
    }

    public static function normalizeDataTypes($arrData) {
        $arrTypes = [
            'customerid' => 'int',
            'accountid' => 'int',
            'balance' => 'float',
            'stateid' => 'int'
        ];
        foreach ($arrData as $key => $value) {
            if (array_key_exists($key, $arrTypes)) {
                switch ($arrTypes["$key"]) {
                    case 'int':
                        $value = intval($value);
                        break;
                    case 'string':
                        $value = (string) $value;
                        break;
                    case 'float':
                        $value = (float) $value;
                        break;
                }
            }
            $arrReturn[$key] = $value;
        }
        return $arrReturn;
    }

}
