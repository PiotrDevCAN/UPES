<?php
use itdq\AuditTable;
use upes\pesEmail;
use upes\AccountPersonTable;
use upes\AllTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);
$response = array();
$upesref = trim($_POST['upesref']);
$accountid = trim($_POST['accountid']);
$fullname = trim($_POST['fullname']);
$emailAddress = trim($_POST['emailaddress']);
$requestor = trim($_POST['requestor']);


try {
//*    $pesEmailObj = new pesEmail();


    $pesTracker = new AccountPersonTable( AllTables::$ACCOUNT_PERSON );
    $pesTracker->setPesProcessStatus($_POST['upesref'], $_POST['accountid'],$_POST['processStatus']);

    $comment = $pesTracker->savePesComment($_POST['cnum'],"Process Status set to " . $_POST['processStatus']);


    $messages  = ob_get_clean();
    $success   = empty($messages);


    if($success){
        // Some Status Changes - we notify the subject, so they know what's happening.
        switch (trim($_POST['processStatus'])) {
            case 'CRC':
            case 'PES':
            //*    $emailResponse = $pesEmailObj->sendPesProcessStatusChangedConfirmation($upesref,$accountid,  $fullname, $emailAddress, trim($_POST['processStatus']), $requestor);
                $response['emailResponse'] = $emailResponse;
            break;
            case 'User':
            //*    $emailResponse = $pesEmailObj->sendPesProcessStatusChangedConfirmation($cnum, $firstName, $lastName, $requestor, trim($_POST['processStatus']));
                $response['emailResponse'] = $emailResponse;

            default:
                ;
            break;
        }

    }


    $now = new DateTime();
    $row = array('CNUM'=>$_POST['cnum'],'PROCESSING_STATUS'=>$_POST['processStatus'],'PROCESSING_STATUS_CHANGED'=>$now->format('Y-m-d H:i:s'));

    ob_start();
    pesTrackerTable::formatProcessingStatusCell($row);
    $formattedStatusField = ob_get_clean();

} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;

}
$response['success']=$success;
$response['messages']=$messages;
$response['formattedStatusField']=$formattedStatusField;
$response['comment']=$comment;
echo json_encode($response);