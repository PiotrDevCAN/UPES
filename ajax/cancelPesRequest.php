<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\AllTables;
use upes\PersonRecord;
use upes\AccountPersonTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();

$cancelResponse = AccountPersonTable::cancelPesRequest($_POST['accountid'],$_POST['upesref']);

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success, 'cancelResponse' => $cancelResponse, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);