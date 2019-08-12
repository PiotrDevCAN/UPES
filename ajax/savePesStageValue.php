<?php
use itdq\AuditTable;
use upes\AllTables;
use upes\AccountPersonTable;
use itdq\Loader;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</pre>",AuditTable::RECORD_TYPE_DETAILS);
try {
    $pesTracker = new AccountPersonTable(AllTables::$ACCOUNT_PERSON   );
    $pesTracker->setPesStageValue($_POST['upesref'],$_POST['accountid'], $_POST['stage'], $_POST['stageValue']);
    $comment = $pesTracker->savePesComment($_POST['upesref'],$_POST['accountid'], " Stage " . $_POST['stage'] . " Set to " . $_POST['stageValue']);

    $messages  = ob_get_clean();
    $success   = empty($messages);

} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;

}
$response = array('success'=>$success,'messages'=>$messages, 'comment'=>$comment);
echo json_encode($response);