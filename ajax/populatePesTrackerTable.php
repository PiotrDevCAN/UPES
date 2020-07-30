<?php
use upes\AllTables;
use upes\AccountPersonTable;

set_time_limit(0);
ob_start();

$pesTrackerTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
$records = empty($_REQUEST['records'])   ? AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE : $_REQUEST['records'];

$table = $pesTrackerTable->buildTable($records);

$dataJsonAble = json_encode($table);

if($dataJsonAble) {
    $messages = ob_get_clean();
    $success = empty($messages);
    $response = array("records"=>$records,"success"=>$success,'messages'=>$messages,'table'=>$table);
    echo json_encode($response);
} else {
    var_dump($dataJsonAble);
    $messages = ob_get_clean();
    ob_start();
    $success = empty($messages);
    $response = array("success"=>$success,'messages'=>$messages);
    echo json_encode($response);
}

