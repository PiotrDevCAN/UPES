<?php
use itdq\Trace;
use upes\CountryTable;
use upes\AllTables;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

Trace::pageOpening($_SERVER['PHP_SELF']);
// ob_start();

$countryTable = new CountryTable(AllTables::$COUNTRY);
$data = $countryTable->returnAsArray();

$messages = ob_get_clean();
if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}
$success = empty($messages);

$response = array('data'=>$data,'success'=>$success,'messages'=>$messages);
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);