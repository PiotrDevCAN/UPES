<?php



use upes\AllTables;

if($_REQUEST['token']!= $token) {
    header('Content-Type: application/json');
    echo json_encode(array('success'=>false,'data'=>array(),'messages'=>'Invalid Token'));    
    return;    
}



$sql = " select AP.ACCOUNT_ID, P.CNUM, PROCESSING_STATUS, PROCESSING_STATUS_CHANGED, COMMENT ";
$sql.= ", PES_DATE_REQUESTED, PES_REQUESTOR, PES_DATE_RESPONDED, PES_STATUS_DETAILS, PES_STATUS";
$sql.= ",PL.PES_LEVEL, PES_RECHECK_DATE, PES_CLEARED_DATE ";
$sql.= "from " . $GLOBALS['Db2Schema'] . "." . AllTables::$ACCOUNT_PERSON . " as AP "; 
$sql.= "left join " . $GLOBALS['Db2Schema'] . "." . AllTables::$PERSON . " as P ";
$sql.= "on AP.UPES_REF = P.UPES_REF ";
$sql.= "left join " . $GLOBALS['Db2Schema'] . "." . AllTables::$PES_LEVELS . " as PL ";
$sql.= "on AP.PES_LEVEL = PL.PES_LEVEL_REF and AP.ACCOUNT_ID = PL.ACCOUNT_ID ";
$sql.= "where AP.account_id = '" . db2_escape_string($_GET['accountid']) . "' ";

ob_start();

$rs = DB2_EXEC($GLOBALS['conn'], $sql);
if (! $rs) {
    self::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
    return false;
}


$data = array();
$count = 1;
while(($row=db2_fetch_assoc($rs))==true){
    $row = array_map('trim',$row);
    $data[] = $row;
}

$messages = ob_get_clean();

$success = empty($messages);

header('Content-Type: application/json');
echo json_encode(array('success'=>$success,'data'=>$data,'messages'=>$messages));

