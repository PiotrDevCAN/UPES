<?php
use itdq\AuditTable;
use upes\allTables;
use upes\AccountPersonTable;


ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {
    $pesTracker = new AccountPersonTable(allTables::$ACCOUNT_PERSON );
    $comment = $pesTracker->savePesComment($_POST['upesref'],$_POST['accountid'], $_POST['comment']);

    $messages  = ob_get_clean();
    $success   = empty($messages);

} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;

}
$response = array('success'=>$success,'messages'=>$messages, 'comment'=>$comment);
echo json_encode($response);