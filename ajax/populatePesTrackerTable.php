<?php
use upes\AllTables;
use upes\AccountPersonTable;

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
// ob_start();

$pesTrackerTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
$records = empty($_REQUEST['records'])   ? AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE : $_REQUEST['records'];

$table = $pesTrackerTable->buildTable($records);

$dataJsonAble = json_encode($table);

if($dataJsonAble) {
    $messages = ob_get_clean();
    $success = empty($messages);
    $response = array("records"=>$records,"success"=>$success,'messages'=>$messages,'table'=>$table);
    
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ob_start("ob_gzhandler");
        } else {
            ob_start("ob_html_compress");
        }
    } else {
        ob_start("ob_html_compress");
    }
    echo json_encode($response);
} else {
    var_dump($dataJsonAble);
    $messages = ob_get_clean();
    // ob_start();
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
    $response = array("success"=>$success,'messages'=>$messages);
    echo json_encode($response);
}

