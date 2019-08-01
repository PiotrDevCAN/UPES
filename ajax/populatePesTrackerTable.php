<?php
use upes\PesTrackerTable;
use upes\allTables;

set_time_limit(0);
ob_start();

$pesTrackerTable = new PesTrackerTable(\upes\AllTables::$ACCOUNT_PERSON);
$table = $pesTrackerTable->buildTable($_REQUEST['records']);

$dataJsonAble = json_encode($table);

if($dataJsonAble) {
    $messages = ob_get_clean();
    $success = empty($messages);
    $response = array("records"=>$_REQUEST['records'],"success"=>$success,'messages'=>$messages,'table'=>$table);
    echo json_encode($response);
} else {
    var_dump($dataJsonAble);
    $messages = ob_get_clean();
    $success = empty($messages);
    $response = array("records"=>$_REQUEST['records'], "success"=>$success,'messages'=>$messages);

    var_dump($response);

    echo "ending here";
}

