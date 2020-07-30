<?php
use itdq\Trace;
use upes\AllTables;
use upes\ContractTable;
use upes\PesLevelTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();

$pesLevelTable = new PesLevelTable(AllTables::$PES_LEVELS);
$data = $pesLevelTable->returnAsArray();

$messages = ob_get_clean();
ob_start();
$Success = empty($messages);

$response = array('data'=>$data,'success'=>$Success,'messages'=>$messages);
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);