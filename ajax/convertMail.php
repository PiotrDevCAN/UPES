<?php
use itdq\Trace;
use itdq\BluePages;

ob_start();
Trace::pageOpening($_SERVER['PHP_SELF']);


$emailAddresses = $_POST['emailaddresses'];

foreach ($emailAddresses as $email) {
    $details = BluePages::getDetailsFromNotesId($email);
    $convertedEmail[$email] = $details['INTERNET'];
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success,'converted' => $convertedEmail, 'messages'=>$messages);

ob_clean();
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);