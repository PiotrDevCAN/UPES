<?php

use itdq\AuditTable;
use upes\AccountPersonTable;
use upes\AllTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$formattedEmailField= null;

try {
    $personTable= new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
    $personTable->setPesStatus($_POST['psm_upesref'],$_POST['psm_accountid'],$_POST['psm_status'],$_SESSION['ssoEmail']);

    $person = new personRecord();
    $person->setFromArray(array('CNUM'=>$_POST['psm_cnum'],'PES_STATUS_DETAILS'=>$_POST['psm_detail'],'PES_DATE_RESPONDED'=>$_POST['PES_DATE_RESPONDED']));
    $updateRecordResult = $personTable->update($person,false,false);

    $personData = $personTable->getRecord($person);
    $person->setFromArray($personData);


    if(array_key_exists('psm_passportFirst', $_POST)){
        /// We've been called from the PES TRACKER Screen;
        $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER   );

        $pesTrackeRecord = new pesTrackerRecord();
        $pesTrackeRecord->setFromArray(array('CNUM'=>$_POST['psm_cnum']));

        if (!$pesTracker->existsInDb($pesTrackeRecord)) {
             $pesTracker->createNewTrackerRecord($_POST['psm_cnum']);
        }


        $pesTracker->setPesPassportNames($_POST['psm_cnum'],trim($_POST['psm_passportFirst']), trim($_POST['psm_passportSurname']));

        $pesTrackerData = $pesTracker->getRecord($pesTrackeRecord);

        $row = $pesTrackerData;
        $row['EMAIL_ADDRESS'] = $personData['EMAIL_ADDRESS'];
        $row['FIRST_NAME']    = $personData['FIRST_NAME'];
        $row['LAST_NAME']     = $personData['LAST_NAME'];
        $formattedEmailField = pesTrackerTable::formatEmailFieldOnTracker($row);
    }

    AuditTable::audit("Saved Person <pre>" . print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

    if(!$updateRecordResult){
        echo db2_stmt_error();
        echo db2_stmt_errormsg();
        AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . db2_stmt_error() . "</b> Msg:<b>" . db2_stmt_errormsg() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
        $success = false;
    } else {
//         echo "<br/>PES Status set to : " . $_POST['psm_status'];
//         echo "<br/>Detail : " . $_POST['psm_detail'];



        switch ($_POST['psm_status']) {
            case personRecord::PES_STATUS_REMOVED:
            case personRecord::PES_STATUS_DECLINED:
            case personRecord::PES_STATUS_FAILED:
            case personRecord::PES_STATUS_INITIATED:
            case personRecord::PES_STATUS_REQUESTED:
            case personRecord::PES_STATUS_EXCEPTION:
            case personRecord::PES_STATUS_PROVISIONAL;
            case personRecord::PES_STATUS_RECHECK_REQ;
            case personRecord::PES_STATUS_LEFT_IBM;
            case personRecord::PES_STATUS_REVOKED;
                $notificationStatus = 'Email not applicable';
                 break;
            case personRecord::PES_STATUS_CLEARED:
            case personRecord::PES_STATUS_CLEARED_PERSONAL:
            case personRecord::PES_STATUS_CANCEL_REQ:
                $emailResponse = $person->sendPesStatusChangedEmail();
                $notificationStatus = $emailResponse ? 'Email sent' : 'No email sent';
                break;
            default:
                $notificationStatus = 'Email not applicable(other)';
            break;
        }

        AuditTable::audit("PES Status Email:" . $notificationStatus ,AuditTable::RECORD_TYPE_DETAILS);

        $success = true;

    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
    $notificationStatus = "Email not applicable(error)";
}

$messages = ob_get_clean();
$success = $success && empty($messages);
$response = array('success'=>$success,'messages'=>$messages, "emailResponse"=>$notificationStatus,"cnum"=>$_POST['psm_cnum'], "formattedEmailField"=>$formattedEmailField);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));