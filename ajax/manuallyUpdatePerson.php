<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\AccountRecord;
use upes\AccountTable;
use upes\AllTables;
use upes\AccountPersonTable;
use upes\AccountPersonRecord;


Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();

$accountPersonTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);

try {
    $references = explode(":", $_POST['personAccount']);
    $upesRef = $references[0];
    $accountId = $references[1];

    $accountPersonTable->setPesStatus($upesRef,$accountId,$_POST['status'],$_SESSION['ssoEmail'], null, $_POST['pes_date_db2']);

    if(trim($_POST['emailNotification'])!='suppress'){
        switch ($_POST['status']) {
            case AccountPersonRecord::PES_STATUS_REMOVED:
            case AccountPersonRecord::PES_STATUS_DECLINED:
            case AccountPersonRecord::PES_STATUS_FAILED:
            case AccountPersonRecord::PES_STATUS_STARTER_REQUESTED:
            case AccountPersonRecord::PES_STATUS_PES_PROGRESSING:
            case AccountPersonRecord::PES_STATUS_EXCEPTION:
            case AccountPersonRecord::PES_STATUS_PROVISIONAL;
            case AccountPersonRecord::PES_STATUS_RECHECK_REQ;
            case AccountPersonRecord::PES_STATUS_LEFT_IBM;
            case AccountPersonRecord::PES_STATUS_REVOKED;
            case AccountPersonRecord::PES_STATUS_STAGE_1;
            case AccountPersonRecord::PES_STATUS_STAGE_2;
                $notificationStatus = 'Email not applicable';
            break;
            case AccountPersonRecord::PES_STATUS_CLEARED:
            case AccountPersonRecord::PES_STATUS_CANCEL_REQ:
                $accountPersonRecord = new AccountPersonRecord();
                $accountPersonTable  = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
                $accountPersonRecord->setFromArray(array('UPES_REF'=>$upesRef, 'ACCOUNT_ID'=>$accountId));
                $personData = $accountPersonTable->getRecord($accountPersonRecord);
                $accountPersonRecord->setFromArray($personData);

                $emailResponse = $accountPersonRecord->sendPesStatusChangedEmail();
                              
                $notificationStatus = $emailResponse ? $emailResponse['sendResponse']['response']  : 'Error sending Pes Status Changed Email';
                break;
            default:
                $notificationStatus = 'Email not applicable(other)';
            break;
        }
    } else {
        $notificationStatus = 'Email was suppressed.';
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

if(is_array($emailResponse)){
    print_r($emailResponse['sendResponse']['response'],true);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'Messages'=>$messages,'Notification'=>$notificationStatus,'email'=>$_SERVER['email'],'emailNotification'=>$_POST['emailNotification']);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);