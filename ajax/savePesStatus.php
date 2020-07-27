<?php

use itdq\AuditTable;
use upes\AccountPersonTable;
use upes\AllTables;
use upes\AccountPersonRecord;
use upes\PersonTable;
use upes\PersonRecord;

ob_start();
// AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$formattedEmailField= null;

try {
    $accountPersonTable= new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
    $accountPersonRecord = new AccountPersonRecord();
    $accountPersonTable->setPesStatus($_POST['psm_upesref'],$_POST['psm_accountid'],$_POST['psm_status'],$_SESSION['ssoEmail'], $_POST['psm_detail'],$_POST['pes_date_db2']);
//     $accountPersonRecord->setFromArray(array('UPES_REF'=>$_POST['psm_upesref'], 'ACCOUNT_ID'=>$_POST['psm_accountid']));
//     $accountPersonData = $accountPersonTable->getRecord($accountPersonRecord);

    $person = new PersonRecord();
    $personTable = new PersonTable(AllTables::$PERSON);
    $person->setFromArray(array('UPES_REF'=>$_POST['psm_upesref']));
    $personData = $personTable->getRecord($person);

    if(!empty($_POST['psm_passportFirst']) || !empty($_POST['psm_passportSurname'])){
        $personData['PASSPORT_FIRST_NAME'] = !empty($_POST['psm_passportFirst']) ? $_POST['psm_passportFirst'] : $personData['PASSPORT_FIRST_NAME'];
        $personData['PASSPORT_LAST_NAME'] = !empty($_POST['psm_passportSurname']) ? $_POST['psm_passportSurname'] : $personData['PASSPORT_LAST_NAME'];
        $person->setFromArray($personData);
        $personTable->update($person);
    }

 //   $formattedEmailField = AccountPersonTable::formatEmailFieldOnTracker($accountPersonData);



//     $personData['ACCOUNT_ID'] = $accountPersonData['ACCOUNT_ID'];
//     $personData['PRIORITY'] = $accountPersonData['PRIORITY'];

//     $formattedEmailField = AccountPersonTable::formatEmailFieldOnTracker($personData);

//     AuditTable::audit("Saved Person <pre>" . print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

    if($_POST['emailNotification']=='send'){

        switch ($_POST['psm_status']) {
            case AccountPersonRecord::PES_STATUS_REMOVED:
            case AccountPersonRecord::PES_STATUS_DECLINED:
            case AccountPersonRecord::PES_STATUS_FAILED:
            case AccountPersonRecord::PES_STATUS_STARTER_REQUESTED:
            case AccountPersonRecord::PES_STATUS_PES_PROGRESSING:
            case AccountPersonRecord::PES_STATUS_EXCEPTION:
        //    case AccountPersonRecord::PES_STATUS_PROVISIONAL;
            case AccountPersonRecord::PES_STATUS_RECHECK_REQ;
            case AccountPersonRecord::PES_STATUS_LEFT_IBM;
            case AccountPersonRecord::PES_STATUS_REVOKED;
            case AccountPersonRecord::PES_STATUS_STAGE_1;
            case AccountPersonRecord::PES_STATUS_STAGE_2;
                $notificationStatus = 'Email not applicable';
                break;
            case AccountPersonRecord::PES_STATUS_PROVISIONAL;  // Temp req to send special email during covid
            case AccountPersonRecord::PES_STATUS_CLEARED:
            case AccountPersonRecord::PES_STATUS_CANCEL_REQ:
                $accountPersonRecord = new AccountPersonRecord();
                $accountPersonTable  = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
                $accountPersonRecord->setFromArray(array('UPES_REF'=>$_POST['psm_upesref'], 'ACCOUNT_ID'=>$_POST['psm_accountid']));
                $personData = $accountPersonTable->getRecord($accountPersonRecord);
                $accountPersonRecord->setFromArray($personData);

                $emailResponse = $accountPersonRecord->sendPesStatusChangedEmail();
                $notificationStatus = $emailResponse ? 'Email sent' : 'No email sent';
                break;
            default:
                $notificationStatus = 'Email not applicable(other)';
            break;
        }

    } else {
        $notificationStatus = 'Email was suppressed.';
    }


    AuditTable::audit("PES Status Email:" . $notificationStatus ,AuditTable::RECORD_TYPE_DETAILS);

    $success = true;

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
    $notificationStatus = "Email not applicable(error)";
}

$messages = ob_get_clean();
$success = $success && empty($messages);
$response = array('success'=>$success,'messages'=>$messages, "emailResponse"=>$notificationStatus,"upesref"=>$_POST['psm_upesref'],"account"=>$_POST['psm_account'],"accountid"=>$_POST['psm_accountid'],'pesStatus'=>$_POST['psm_status']);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));