<?php
use itdq\AuditTable;
use upes\AccountPersonTable;
use upes\AllTables;

ob_start();
// AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {

    $pesTracker = new AccountPersonTable(AllTables::$ACCOUNT_PERSON   );
    $pesTracker->savePesPriority($_POST['upesRef'],$_POST['accountid'],$_POST['pespriority']);
    $comment = $pesTracker->savePesComment($_POST['upesRef'],$_POST['accountid'],"Priority set to : " . $_POST['pespriority']);

    $messages  = ob_get_clean();
    $success   = empty($messages);

} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;

}
$response = array('success'=>$success,'messages'=>$messages, 'comment'=>$comment);
echo json_encode($response);