<?php

use itdq\BlueMail;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use upes\AllTables;
use upes\AccountPersonTable;
// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}

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
    $pesTrackerTable->getTracker(AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_PLUS, $spreadsheet);
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client's web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    $fileNameSuffix = $now->format('Ymd_His');
    $fileNamePart = 'PES_Tracker__active_plus_' . $fileNameSuffix . '.xlsx';
    $fileName = './extracts/'.$fileNamePart;

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
        'filename'=>'PES_Tracker_' . $fileNameSuffix . '.xlsx',
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
    echo "<h1>No data found to export to tracker</h1>";
}