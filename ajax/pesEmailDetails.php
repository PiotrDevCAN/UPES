<?php

use itdq\Loader;
use upes\AllTables;
use upes\PesEmail;

ob_start();
$pesEmailObj = new PesEmail();

try {
    // $loader = new Loader();

    // $accountType = '';
    // $accountTypes = $loader->load('ACCOUNT_TYPE',AllTables::$ACCOUNT, " ACCOUNT = '" . $_POST['account'] . "'" );
    // foreach ($accountTypes as $value) {
    //     $accountType = $value;
    // }

    // $_POST['accounttype'];

    // $emailDetails = $pesEmailObj->getEmailDetails($_POST['upesref'],  $_POST['account'], $_POST['country'],$_POST['ibmstatus']);
    $recheck = $_POST['recheck']=='yes';
    $emailDetails = PesEmail::determinePesApplicationForms($_POST['country'], $_POST['accounttype']);

} catch ( \Exception $e) {
    switch ($e->getCode()) {
        case 803:
            $emailDetails['warning']['filename'] = 'No email exists for combination of Internal/External and Country';
            echo "Warning";
        break;
        default:
            var_dump($e);
        break;
    }
}

$messages = ob_get_clean();
ob_start();
$success = strlen($messages)==0;

unset($emailDetails['attachments']); // dont need them at this point.
$emailDetails['success'] = $success;
$emailDetails['messages'] = $messages;
$emailDetails['cnum'] = $_REQUEST['cnum'];
$emailDetails['recheck'] = $_REQUEST['recheck'];


ob_clean();
echo json_encode($emailDetails);