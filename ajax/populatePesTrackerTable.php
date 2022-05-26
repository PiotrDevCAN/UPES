<?php
use upes\AllTables;
use upes\AccountPersonTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function ob_html_compress($buf){
    return str_replace(array("\n","\r"),'',$buf);
}

set_time_limit(0);
// ob_start();

$draw = isset($_REQUEST['draw']) ? $_REQUEST['draw'] * 1 : 1 ;
$start = isset($_REQUEST['start']) ? $_REQUEST['start'] * 1 : 1 ;
$length = isset($_REQUEST['length']) ? $_REQUEST['length'] : 50;

//$predicate = !empty($_REQUEST['search']['value']) ? " AND LOWER(P.EMAIL_ADDRESS) like '%" . db2_escape_string(strtolower(trim($_REQUEST['search']['value']))) . "%' " : null;
// $predicate = !empty($_REQUEST['search']['value']) ? " AND REGEXP_LIKE(P.EMAIL_ADDRESS, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')" : null;

$predicate = '';
if(!empty($_REQUEST['search']['value'])) {
    $predicate .= " AND (REGEXP_LIKE(P.PASSPORT_FIRST_NAME, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.PASSPORT_LAST_NAME, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.CNUM, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.IBM_STATUS, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.COUNTRY, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(AP.COUNTRY_OF_RESIDENCE, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.UPES_REF, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(A.ACCOUNT_ID, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(A.ACCOUNT_TYPE, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(A.ACCOUNT, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.FULL_NAME, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(P.EMAIL_ADDRESS, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(PL.PES_LEVEL, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(PL.PES_LEVEL_DESCRIPTION, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(AP.PES_REQUESTOR, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(AP.PES_DATE_REQUESTED, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(AP.DATE_LAST_CHASED, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(AP.PES_STATUS, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= " OR REGEXP_LIKE(AP.COMMENT, '". db2_escape_string(trim($_REQUEST['search']['value'])) . "', 'i')";
    $predicate .= ") ";
}

$pesTrackerTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
$records = empty($_REQUEST['records'])   ? AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE : $_REQUEST['records'];

// $table = $pesTrackerTable->buildTable($records);
$data = $pesTrackerTable->returnAsArray($records, 'array', null, null, $start, $length, $predicate);

// $sql = $pesTrackerTable->preparePesEventsStmt($records);
$total = $pesTrackerTable->totalRows($records, 'array', null, null, $start, $length);
$filtered = $pesTrackerTable->recordsFiltered($records, 'array', null, null, $predicate);

// $dataJsonAble = json_encode($table);
$dataJsonAble = json_encode($data);

if($dataJsonAble) {
    $messages = ob_get_clean();
    $success = empty($messages);
    $response = array(
        "draw"=>$draw,
        "data"=>$data,
        'recordsTotal'=>$total,
        'recordsFiltered'=>$filtered,
        "error"=>$messages
        // "records"=>$records,
        // "success"=>$success,
        // "messages"=>$messages,
        // "table"=>$table,
        // "sql"=>$sql,
    );
    
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

