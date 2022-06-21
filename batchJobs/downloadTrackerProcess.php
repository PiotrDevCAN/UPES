<?php

use itdq\BlueMail;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use upes\AllTables;
use upes\AccountPersonTable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function myErrorHandler($code, $message, $file, $line) {
    $mailError = new PHPMailer();
    if (filter_var($_SESSION['ssoEmail'], FILTER_VALIDATE_EMAIL)) {
        $localEmail = $_SESSION['ssoEmail'];
    } else {
        $localEmail = ! empty($_ENV['devemailid']) ? $_ENV['devemailid'] : 'piotr.tajanowicz@ocean.ibm.com';
    }
    $recipient = $_ENV['email'] == 'user' ? $localEmail : $_ENV['devemailid'];
    $mailError->clearAllRecipients();
    $mailError->addAddress($recipient);
    $mailError->clearCCs();
    $mailError->clearBCCs();

    $mailError->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output ; SMTP::DEBUG_OFF
    $mailError->isSMTP(); // Send using SMTP
    $mailError->Host = 'na.relay.ibm.com'; // Set the SMTP server to send through
    $mailError->SMTPAuth = false;
    $mailError->SMTPAutoTLS = false;
    $mailError->Port = 25;

    $replyto = 'atm.pes.processing@uk.ibm.com';
    $mailError->setFrom($replyto);
    $mailError->isHTML(true);
    $mailError->Subject = "**" . $_ENV['environment'] . "**" . 'Error has occurred while running PHP script';
    $response = array(
        'code' => $code, 
        'message' => $message, 
        'file' => $file, 
        'line' => $line
    );          
    $mailError->Body = serialize($response);
    if (!$mailError->send()) {
            
    }
}

function fatalErrorShutdownHandler() {
    $last_error = error_get_last();
    if ($last_error['type'] === E_ERROR) {
        // fatal error
        myErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
    }
}

set_error_handler('myErrorHandler');
register_shutdown_function('fatalErrorShutdownHandler');

if (isset($argv[1])) {

    $_SESSION['ssoEmail'] = trim($argv[1]);
    $trackerType = strtolower(trim($argv[2]));
    switch ($trackerType) {
        case 'active_requested':
            $type = AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_REQUESTED;
            $title = 'PES_Tracker_';
            break;
        case 'active_plus':
            $type = AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_PLUS;
            $title = 'PES_Tracker_active_plus_';
            break;
        case 'not_active':
            $type = AccountPersonTable::PES_TRACKER_RECORDS_NOT_ACTIVE;
            $title = 'PES_Tracker_recent_';
            break;
        default:
            throw new \Exception('Incorrect way of execution of script.');
            break;
    }

    // require_once __DIR__ . '/../../src/Bootstrap.php';
    // $helper = new Sample();
    // if ($helper->isCli()) {
    //     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    //     return;
    // }

    echo "<pre>";

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    // Set document properties
    $spreadsheet->getProperties()->setCreator('uPES')
        ->setLastModifiedBy('vBAC')
        ->setTitle('uPES Tracker')
        ->setSubject('uPES Tracker')
        ->setDescription('uPES Tracker')
        ->setKeywords('office 2007 openxml php upes tracker')
        ->setCategory('uPES Tracker');
    // Add some data

    $now = new DateTime();

    $pesTrackerTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);

    try {
        $pesTrackerTable->getTracker($type, $spreadsheet);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client�s web browser (Xlsx)
        DbTable::autoSizeColumns($spreadsheet);
        $fileNameSuffix = $now->format('Ymd_His');
        $fileNamePart = $title . $fileNameSuffix . '.xlsx';
        $fileName = '../extracts/'.$fileNamePart;

        // ob_clean();
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="PES_Tracker_' . $fileNameSuffix . '.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');
        // // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // $writer->save('php://output');
        $writer->save($fileName);

        // $excelOutput = ob_get_clean();

        $toEmail = array($_SESSION['ssoEmail']);
        $subject = 'The tracker report RAW extract';
        
        $extractRequestEmail = 'Hello &&requestor&&,
        <br/>
        <br/>Please find the attached Tracker report RAW extract.
        <br/>File name: &&fileName&&
        <hr>
        <br/>Many thanks for your cooperation
        <br>PES Team';
        
        $extractRequestEmailPattern = array('/&&requestor&&/','/&&fileName&&/');

        $replacements = array($_SESSION['ssoEmail'], $fileNamePart);
        $emailBody = preg_replace($extractRequestEmailPattern, $replacements, $extractRequestEmail);

        $pesTaskid = 'atm.pes.processing@uk.ibm.com';

        $handle = fopen($fileName, "r", true);
        $applicationForm = fread($handle, filesize($fileName));
        fclose($handle);
        $encodedAttachmentFile = base64_encode($applicationForm);

        $pesAttachments[] = array(
            'filename'=>$title . $fileNameSuffix . '.xlsx',
            'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'data'=>$encodedAttachmentFile,
            'path'=>$fileName
        );

        $sendResponse = BlueMail::send_mail($toEmail, $subject, $emailBody, $pesTaskid, array(), array(), false, $pesAttachments);

        var_dump($sendResponse);

        unlink($fileName);

    } catch (Exception $e) {

    //    ob_clean();

        echo "<br/><br/><br/><br/><br/>";

        echo $e->getMessage();
        echo $e->getLine();
        echo $e->getFile();
        echo "<h1>Error has occurred while extracting tracker data</h1>";
    }
}