<?php
function debug()
{
	$func_args = func_get_args();

        if (count($func_args) === 0) $func_args = array("checkpoint!");

        write2file("DEBUG from toxap.glt@gmail.com", "debug.log");
        write2file(gmdate("H:i:s d-m-Y", time()), "debug.log");
        write2file("--------------------------------------------------------------------\r\n", "debug.log");

        foreach($func_args as $arg) {
                write2file(var_export($arg, TRUE), "debug.log");
                write2file("\r\n--------------------------------------------------------------------\r\n", "debug.log");
        }

        exit;
}

function write2file($text, $file_name = "verbose.log")
{
	if (($fp = @fopen(LOG_PATH . $file_name, "a+")) === FALSE) return FALSE;

	fputs($fp, $text ."\r\n"); fclose($fp);
} 

function error($text)
{
	$separator = "";
	$header = "ERROR [". date("r", time()) ."]:";

	for ($i = 0; $i < strlen($header); $i++) $separator .= "-";

	write2file($separator, "error.log");
	write2file($header ." ". $text, "error.log");
	write2file($separator ."\r\n", "error.log");
}

function criticalError($text)
{
	$separator = "";
        $header = "CRITICAL ERROR [". date("r", time()) ."]:";

        for ($i = 0; $i < strlen($header); $i++) $separator .= "=";

        write2file($separator, "error.log");
        write2file($header ." ". $text, "error.log");
        write2file($separator ."\r\n", "error.log");
	
	exit();
}

function write2socket($resource, $body, $send_line_feed = TRUE)
{
	if ($send_line_feed === TRUE) $body .= "\r\n";

	socket_write($resource, $body, strlen($body));
}

function array_first($array)
{
	if(!is_array($array)) return FALSE;
	
	foreach($array as $value) return $value;
	
	return NULL;
}

function generate_int($length=32)
{
        $int = "";

        if ($length > 32) $length = 32;

        for ($i=0; $i < $length; $i++) {
                $int .= mt_rand(1, 9);
        }

        return (int) $int;
}

function generate_id($length=32)
{
        if (!defined('RANDOM_INITIALIZED')) {
                mt_srand((double) microtime() * 1000000);
                define('RANDOM_INITIALIZED', TRUE);
        }

        if ($length > 32) $length = 32;

        mt_srand((double)microtime()*1000000);

        $id = md5(mt_rand());

        $id = substr($id, 0, $length);

        return strtoupper($id);
}
?>

