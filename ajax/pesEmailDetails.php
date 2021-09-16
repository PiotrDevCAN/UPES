<?php

use itdq\Loader;
use upes\AllTables;
use upes\PesEmail;

ob_start();
$pesEmailObj = new PesEmail();

try {
    
    $recheck = $_POST['recheck']=='yes';
    $emailDetails = PesEmail::determinePesApplicationForms($_POST['country'], $_POST['accounttype']);

    echo '<pre>';
    var_dump($emailDetails);
    echo '</pre>';

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