<?php
use itdq\Trace;
use upes\AllTables;
use upes\ContractTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();

$contractsTable = new ContractTable(AllTables::$CONTRACT);
$data = $contractsTable->returnAsArray();

$messages = ob_get_clean();
$Success = empty($messages);

$response = array('data'=>$data,'success'=>$Success,'messages'=>$messages);
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);