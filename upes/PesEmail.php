<?php
namespace upes;

use itdq\DbTable;
use upes\AllTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use upes\PersonTable;
use itdq\BlueMail;
use itdq\slack;
use itdq\Loader;

class PesEmail {

    const ODC = 'odc';
    const OWENS = 'owens';
    const VF = 'vf';

    const FILE_TYPE_WORD = 'application/msword';
    const FILE_TYPE_PDF = 'application/pdf';

    const CONSENT_PATTERN = '/(.*?)consent(.*?).(.*?)/i';
    const ODC_PATTERN = '/(.*?)odc(.*?).(.*?)/i';
    const EMAIL_PATTERN = '/(.*?)email(.*?).(.*?)/i';
    const APPLICATION_PATTERN = '/(.*?)application(.*?).(.*?)/i';

    const EMAIL_ROOT_ATTACHMENTS     = 'emailAttachments';
    const EMAIL_SUBDIRECTORY_COMMON  = 'common';
    const EMAIL_SUBDIRECTORY_IBM     = 'IBM';
    const EMAIL_SUBDIRECTORY_KYNDRYL = 'Kyndryl';
    const EMAIL_APPLICATION_FORMS    = 'applicationForms';
    const EMAIL_BODIES               = 'emailBodies';

    // files for IBM
    const IBM_APPLICATION_FORM_GLOBAL_FSS       = 'FSS Global Application Form v2.5.doc';
    const IBM_APPLICATION_FORM_GLOBAL_NON_FSS   = 'PES Global Application Form v1.2.doc';
    const IBM_APPLICATION_FORM_ODC              = 'ODC application form v3.0.xls';

    // files for Kyndryl
    const KYNDRYL_APPLICATION_FORM_GLOBAL_FSS       = 'Kyndryl FSS Global Application Form v1.1.doc';
    const KYNDRYL_APPLICATION_FORM_GLOBAL_NON_FSS   = 'Kyndryl PES Global Application Form v1.1.doc';
    const KYNDRYL_APPLICATION_FORM_ODC              = 'Kyndryl ODC Application Form v1.0.xls';

    // common file for both companies
    const APPLICATION_FORM_OWENS            = 'Owens_Consent_Form.pdf';
    const APPLICATION_FORM_VF               = 'VF Overseas Consent Form.pdf';

    const EMAIL_SUBJECT          = "IBM Confidential: URGENT - &&account_name&&  Pre Employment Screening- &&serial_number&& &&candidate_name&&";

    static private $notifyPesEmailAddresses = array('to'=>array('carrabooth@uk.ibm.com'),'cc'=>array('Rsmith1@uk.ibm.com'));

    static private function checkIfIsKyndryl(){
        $isKyndryl = stripos($_ENV['environment'], 'newco');
        if ($isKyndryl === false) {
            return false;
        } else {
            return true;
        }
    }

    static private function getApplicationFormFileNameByKey($key = ''){
        switch($key) {
            case self::ODC:
                $fileName = self::getOdcApplicationFormFileName();
                break;
            case self::OWENS:
                $fileName = self::getOwensConsentFormFileName();
                break;
            case self::VF:
                $fileName = self::getVfConsentFormFileName();
                break;
            default:
                $fileName = '';
                break;
        }
        return $fileName;
    }

    static private function addDirectorySeparator($directory = ''){
        return $directory . DIRECTORY_SEPARATOR;
    }

    static private function getEmailRootAttachmentsName(){
        $directory = self::EMAIL_ROOT_ATTACHMENTS;
        return $directory;
    }

    static private function getEmailCommonSubdirectoryName(){
        $directory = self::EMAIL_SUBDIRECTORY_COMMON;
        return $directory;
    }

    static private function getEmailIBMSubdirectoryName(){
        $directory = self::EMAIL_SUBDIRECTORY_IBM;
        return $directory;
    }
    static private function getEmailKyndrylSubdirectoryName(){
        $directory = self::EMAIL_SUBDIRECTORY_KYNDRYL;
        return $directory;
    }

    static private function getEmailApplicationFormsDirectoryName(){
        $directory = self::EMAIL_APPLICATION_FORMS;
        return $directory;
    }

    static private function getEmailBodiesDirectoryName(){
        $directory = self::EMAIL_BODIES;
        return $directory;
    }

    static private function getEmailCompanySubdirectory(){
        $directory = self::checkIfIsKyndryl() === true ? self::getEmailKyndrylSubdirectoryName() : self::getEmailIBMSubdirectoryName();
        return $directory;
    }

    static private function getRootAttachmentsDirectory(){
        return self::getEmailRootAttachmentsName() . DIRECTORY_SEPARATOR . self::getEmailCompanySubdirectory();
    }

    static private function getRootAttachmentsCommonDirectory(){
        return self::getEmailRootAttachmentsName() . DIRECTORY_SEPARATOR . self::getEmailCommonSubdirectoryName();
    }

    static private function getApplicationFormsDirectory(){
        return self::getRootAttachmentsDirectory() . DIRECTORY_SEPARATOR . self::getEmailApplicationFormsDirectoryName();
    }

    static private function getApplicationFormsCommonDirectory(){
        return self::getRootAttachmentsCommonDirectory() . DIRECTORY_SEPARATOR . self::getEmailApplicationFormsDirectoryName();
    }
    
    static private function getEmailBodiesDirectory(){
        return self::getRootAttachmentsDirectory() . DIRECTORY_SEPARATOR . self::getEmailBodiesDirectoryName();
    }

    static private function getApplicationFormsDirectoryPath(){
        return "../" . self::getApplicationFormsDirectory() . DIRECTORY_SEPARATOR;
        // return self::getApplicationFormsDirectory() . DIRECTORY_SEPARATOR;
    }

    static private function getApplicationFormsCommonDirectoryPath(){
        return "../" . self::getApplicationFormsCommonDirectory() . DIRECTORY_SEPARATOR;
        // return self::getApplicationFormsCommonDirectory() . DIRECTORY_SEPARATOR;
    }

    static public function getDirectoryPathToAttachmentFile($fileName){        
        return self::getApplicationFormsDirectoryPath() . $fileName;
    }

    static public function getDirectoryPathToCommonAttachmentFile($fileName){        
        return self::getApplicationFormsCommonDirectoryPath() . $fileName;
    }

    static private function getEmailBodiesDirectoryPath(){
        return "../" . self::getEmailBodiesDirectory() . DIRECTORY_SEPARATOR;
        // return self::getEmailBodiesDirectory() . DIRECTORY_SEPARATOR;
    }

    static private function getAccountPath($account){
        switch(strtolower($account)) {
            case 'lloyds ce':
                $path = 'Lloyds';
                break;
            default:
                $path = $account;
                break;
        }
        return $path;
    }

    static private function getApplicationFormFile($fileName){
        
        echo '<pre>';
        var_dump($fileName);
        echo '</pre>';

        $handle = fopen($fileName, "r", true);
        $applicationForm = fread($handle, filesize($fileName));
        fclose($handle);
        // return base64_encode($applicationForm);
        return null;
    }

    static private function getApplicationFormCompanyFile($formName){
        $fileName = self::getDirectoryPathToAttachmentFile($formName);
        return static::getApplicationFormFile($fileName);
    }

    static private function getApplicationFormCommonFile($formName){
        $fileName = self::getDirectoryPathToCommonAttachmentFile($formName);
        return static::getApplicationFormFile($fileName);
    }

    static private function getOdcApplicationFormFile($fileName){

        $inputFileName = self::getDirectoryPathToAttachmentFile($fileName);

        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

        // $spreadsheet = new Spreadsheet();
        // Set document properties
        $spreadsheet->getProperties()->setCreator('uPES')
            ->setLastModifiedBy('uPES')
            ->setTitle('PES Application Form generated by uPES')
            ->setSubject('PES Application')
            ->setDescription('PES Application Form generated by uPES')
            ->setKeywords('office 2007 openxml php upes tracker')
            ->setCategory('category');

        $spreadsheet->getActiveSheet()
            ->getCell('C17')
            ->setValue('Emp no. here');

        $spreadsheet->setActiveSheetIndex(0);
//         ob_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $xlsAttachment = ob_get_clean();

        return base64_encode($xlsAttachment);
    }

    // --------------------- file names START ---------------------

    static private function getGlobalFSSApplicationFormFileName(){
        $fileName = self::checkIfIsKyndryl() === true ? self::KYNDRYL_APPLICATION_FORM_GLOBAL_FSS : self::IBM_APPLICATION_FORM_GLOBAL_FSS;        
        return $fileName;
    }

    static private function getGlobalNonFSSApplicationFormFileName(){
        $fileName = self::checkIfIsKyndryl() === true ? self::KYNDRYL_APPLICATION_FORM_GLOBAL_NON_FSS : self::IBM_APPLICATION_FORM_GLOBAL_NON_FSS;
        return $fileName;
    }

    static private function getOdcApplicationFormFileName(){
        $fileName = self::checkIfIsKyndryl() === true ? self::KYNDRYL_APPLICATION_FORM_ODC : self::IBM_APPLICATION_FORM_ODC;        
        return $fileName;
    }

    static private function getOwensConsentFormFileName(){
        $fileName = self::APPLICATION_FORM_OWENS;
        return $fileName;
    }

    static private function getVfConsentFormFileName(){
        $fileName = self::APPLICATION_FORM_VF;
        return $fileName;
    }

    // --------------------- file names END ---------------------

    static function findEmailBody($account, $accountType, $country, $emailAddress, $recheck='no'){
        
        $loader = new Loader();

         if(is_array($emailAddress)){
            $email = $emailAddress[0];
        } else {
            $email = $emailAddress;
        }
        
        if(empty($account) or empty($country)){
            throw new Exception('Incorrect parms passed');
        }
        
        $offboarded = AccountPersonTable::offboardedStatusFromEmail($email, $account);
        if($offboarded){
            $pathToRecheckOffboarded = self::getEmailBodiesDirectoryPath() . DIRECTORY_SEPARATOR .  "recheck_offboarded.php";
            return $pathToRecheckOffboarded;
        }
 
        $intExt = stripos($email, ".ibm.com") !== false ? null : "_ext" ;

        $emailBodyName = CountryTable::getEmailBodyNameForCountry($country);

        $accountPath = self::getAccountPath($account);

        $emailPrefix = strtolower($recheck)=='yes' ? 'recheck' : 'request';
        $intExt      = strtolower($recheck)=='yes' ? null : $intExt; // For recheck email there is no difference.

        $pathToAccountTypeBody = self::getEmailBodiesDirectoryPath() . $accountType . DIRECTORY_SEPARATOR . $emailPrefix . "_"  .  $emailBodyName['EMAIL_BODY_NAME'] . $intExt . ".php";
        $pathToAccountBody     = self::getEmailBodiesDirectoryPath() . $accountPath . DIRECTORY_SEPARATOR . $emailPrefix . "_"  .  $emailBodyName['EMAIL_BODY_NAME'] . $intExt . ".php";
        $pathToDefaultBody     = self::getEmailBodiesDirectoryPath() . DIRECTORY_SEPARATOR .  $emailPrefix . "_" . $emailBodyName['EMAIL_BODY_NAME'] . $intExt . ".php";
        
        $pathsToTry = array($pathToAccountTypeBody, $pathToAccountBody, $pathToDefaultBody);

        $pathFound = false;
        $pathIndex = 0;
        while(!$pathFound && $pathIndex < count($pathsToTry)){
            $pathToTest = $pathsToTry[$pathIndex];
            $pathFound = file_exists($pathToTest);
            $pathIndex++;
            if($pathFound){
                return $pathToTest;
            }
        }
        return false;
    }

    static function testParam($dir = ''){
        echo '<br> BROWSING DIRECTORY = ' . $dir;
        echo '<pre>';
        var_dump(file_exists($dir));
        $files2 = scandir($dir);
        print_r($files2);
        foreach ($files2 as $value) {
            if($value === '.' || $value === '..') {continue;} 
            echo '<br> file size ';
            var_dump(filesize($dir . DIRECTORY_SEPARATOR . $value));
            echo '<br> file exists ';
            var_dump(file_exists($dir . DIRECTORY_SEPARATOR . $value));
        }
        echo '</pre>';
    }

    static function sendPesApplicationForms($account, $country, $serial,  $candidateName, $candidate_first_name, $candidateEmail, $recheck='no', $test = false){
        
        if ($test === true) {
            
            // $appForms = PesEmail::getApplicationFormsDirectoryPath();
            // $appFormsPath = PesEmail::getApplicationFormsDirectory();

            // $appFormsCommon = PesEmail::getApplicationFormsCommonDirectoryPath();
            // $appFormsCommonPath = PesEmail::getApplicationFormsCommonDirectory();

            // $emailBodies = PesEmail::getEmailBodiesDirectoryPath();
            // $emailBodiesPath = PesEmail::getEmailBodiesDirectory();

            // $dir = $appForms;
            // self::testParam($dir);

            // $dir = $appFormsPath;
            // self::testParam($dir);

            // $dir = $appFormsCommon;
            // self::testParam($dir);

            // $dir = $appFormsCommonPath;
            // self::testParam($dir);

            // $dir = $emailBodies;
            // self::testParam($dir);

            // $dir = $emailBodiesPath;
            // self::testParam($dir);

            $fileName = self::getGlobalFSSApplicationFormFileName();
            echo '<br>' . $fileName;
            echo '<br>' . self::getApplicationFormCompanyFile($fileName);
            
            $fileName = self::getGlobalNonFSSApplicationFormFileName();
            echo '<br>' . $fileName;
            echo '<br>' . self::getApplicationFormCompanyFile($fileName);

            $fileName = self::getOwensConsentFormFileName();
            echo '<br>' . $fileName;
            echo '<br>' . self::getApplicationFormCommonFile($fileName);
            
            $fileName = self::getVfConsentFormFileName();
            echo '<br>' . $fileName;
            echo '<br>' . self::getApplicationFormCommonFile($fileName);
            
            $fileName = self::getOdcApplicationFormFileName();
            echo '<br>' . $fileName;
            echo '<br>' . self::getOdcApplicationFormFile($fileName);
        }

        $loader = new Loader();
        $allPesTaskid = $loader->loadIndexed('TASKID','ACCOUNT',AllTables::$ACCOUNT);

        $accountType = '';
        $accountTypes = $loader->load('ACCOUNT_TYPE',AllTables::$ACCOUNT, " ACCOUNT = '" . $account . "'" );
        foreach ($accountTypes as $value) {
            $accountType = $value;
        }

        $emailSubjectPattern = array('/&&account_name&& /','/&&serial_number&&/','/&&candidate_name&&/');
        $emailBodyPattern    = array('/&&candidate_first_name&&/','/&&name_of_application_form&&/','/&&account_name&& /','/&&pestaskid&&/');
        $emailBody = '';// overwritten by include

        $applicationFormDetails = self::determinePesApplicationForms($country, $accountType);
        $nameOfApplicationForm = $applicationFormDetails['nameOfApplicationForm'];
        $pesAttachments        = $applicationFormDetails['pesAttachments'];

        $emailBodyFile = PesEmail::findEmailBody($account, $accountType, $country, $candidateEmail, $recheck);
        $pesTaskid = $allPesTaskid[$account];

        include $emailBodyFile;
        $subjectReplacements = array($account,$serial,$candidateName);
        $subject = preg_replace($emailSubjectPattern, $subjectReplacements, PesEmail::EMAIL_SUBJECT);

        $emailBodyReplacements = array($candidate_first_name, $nameOfApplicationForm, $account, $pesTaskid);

        $email = preg_replace($emailBodyPattern, $emailBodyReplacements, $emailBody);

        if(!$email){
            throw new \Exception('Error preparing Pes Application Form email');
        }

        return $email ? BlueMail::send_mail($candidateEmail, $subject, $email, $pesTaskid,array(),array(),false,$pesAttachments) : false;
    }

    static function determinePesApplicationForms($country, $accountType){

        $additionalApplicationFormDetails = CountryTable::getAdditionalAttachmentsNameCountry($country);

        echo '<pre>';
        var_dump($fileName);
        echo '</pre>';
        
        $pesAttachments = array();
        $nameOfApplicationForm = '';

        switch ($accountType) {
            case AccountRecord::ACCOUNT_TYPE_FSS:
                $fileName = self::getGlobalFSSApplicationFormFileName();
                
                $nameOfApplicationForm = "<ul><li><i>" . $fileName . "</i></li>";
                $nameOfApplicationForm.= !empty($additionalApplicationFormDetails['ADDITIONAL_APPLICATION_FORM']) ? "<li><i>" . self::getApplicationFormFileNameByKey($additionalApplicationFormDetails['ADDITIONAL_APPLICATION_FORM']) . "</i></li>" : null;
                $nameOfApplicationForm.= "</ul>";

                $encodedAttachmentFile = self::getApplicationFormCompanyFile($fileName);
                $pesAttachments[] = array(
                    'filename'=>$fileName,
                    'content_type'=>self::FILE_TYPE_WORD,
                    'data'=>$encodedAttachmentFile,
                    'path'=>self::getDirectoryPathToAttachmentFile($fileName)
                );
                break;
            case AccountRecord::ACCOUNT_TYPE_NONE_FSS:
                $fileName = self::getGlobalNonFSSApplicationFormFileName();

                $nameOfApplicationForm = "<ul><li><i>" . $fileName . "</i></li>";
                $nameOfApplicationForm.= !empty($additionalApplicationFormDetails['ADDITIONAL_APPLICATION_FORM']) ? "<li><i>" . self::getApplicationFormFileNameByKey($additionalApplicationFormDetails['ADDITIONAL_APPLICATION_FORM']) . "</i></li>" : null;
                $nameOfApplicationForm.= "</ul>";

                $encodedAttachmentFile = self::getApplicationFormCompanyFile($fileName);
                $pesAttachments[] = array(
                    'filename'=>$fileName,
                    'content_type'=>self::FILE_TYPE_WORD,
                    'data'=>$encodedAttachmentFile,
                    'path'=>self::getDirectoryPathToAttachmentFile($fileName)
                );
                break;
        }

        switch ($additionalApplicationFormDetails['ADDITIONAL_APPLICATION_FORM']) {
            case self::ODC:
                $fileName = self::getOdcApplicationFormFileName();

                $encodedAttachmentFile = self::getOdcApplicationFormFile($fileName);
                $pesAttachments[] = array(
                    'filename'=>$fileName,
                    'content_type'=>self::FILE_TYPE_WORD,
                    'data'=>$encodedAttachmentFile,
                    'path'=>self::getDirectoryPathToAttachmentFile($fileName)
                );
                break;
            case self::OWENS:
                $fileName = self::getOwensConsentFormFileName();

                $encodedAttachmentFile = self::getApplicationFormCommonFile($fileName);
                $pesAttachments[] = array(
                    'filename'=>$fileName,
                    'content_type'=>self::FILE_TYPE_PDF,
                    'data'=>$encodedAttachmentFile,
                    'path'=>self::getDirectoryPathToCommonAttachmentFile($fileName)
                );
                break;
            case self::VF:
                $fileName = self::getVfConsentFormFileName();

                $encodedAttachmentFile = self::getApplicationFormCommonFile($fileName);
                $pesAttachments[] = array(
                    'filename'=>$fileName,
                    'content_type'=>self::FILE_TYPE_PDF,
                    'data'=>$encodedAttachmentFile,
                    'path'=>self::getDirectoryPathToCommonAttachmentFile($fileName)
                );
                break;
            default:
                null;
                break;
        }
        return array('pesAttachments'=> $pesAttachments,'nameOfApplicationForm'=>$nameOfApplicationForm);
    }

    function sendPesEmailChaser($upesref, $account, $emailAddress, $chaserLevel){

        $loader = new Loader();
        $allPesTaskid = $loader->loadIndexed('TASKID','ACCOUNT',AllTables::$ACCOUNT);
        $pesTaskid = $allPesTaskid[$account];

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.
        $names = PersonTable::getNamesFromUpesref($upesref);
        $fullName = $names['FULL_NAME'];
        $requestor = trim($_POST['requestor']);

        $emailBodyFileName = 'chaser' . trim($chaserLevel) . ".php";
        $replacements = array($fullName,$account);

        include_once self::getEmailBodiesDirectory() . '/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

        $sendResponse = BlueMail::send_mail(array($emailAddress), "PES Reminder - $fullName($upesref) on $account", $emailBody,$pesTaskid,array($requestor));
              
        return $sendResponse;
    }

    function sendPesProcessStatusChangedConfirmation($upesref, $account,  $fullname, $emailAddress, $processStatus, $requestor=null){
        $loader = new Loader();
        $allPesTaskid = $loader->loadIndexed('TASKID','ACCOUNT',AllTables::$ACCOUNT);
        $pesTaskid = $allPesTaskid[$account];

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $emailBodyFileName = 'processStatus' . trim($processStatus) . ".php";
        $replacements = array($fullname, $account);

        include_once self::getEmailBodiesDirectory() . '/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

        return BlueMail::send_mail(array($emailAddress), "PES Status Change - $fullname($upesref) : $account", $emailBody,$pesTaskid, array($requestor));
    }

    static function notifyPesTeamOfUpcomingRechecks($detialsOfPeopleToBeRechecked=null){

        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        include_once self::getEmailBodiesDirectory() . '/recheckReport.php';

        $pesEmail.= "<h4>Generated by uPes: " . $now->format('jS M Y') . "</h4>";

        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  >";
        $pesEmail.= "<thead style='background-color: #cce6ff; padding:25px;'>";
        $pesEmail.= "<tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Full Name</th><th style='padding:25px;'>Account</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Recheck Date</th>";
        $pesEmail.= "</tr></thead><tbody>";

        foreach ($detialsOfPeopleToBeRechecked as $personToBeRechecked) {
            $pesEmail.="<tr><td style='padding:15px;'>" . $personToBeRechecked['CNUM'] . "</td><td style='padding:15px;'>" . $personToBeRechecked['FULL_NAME']  . "</td><td style='padding:15px;'>" . $personToBeRechecked['ACCOUNT']  . "</td><td style='padding:15px;'>" . $personToBeRechecked['PES_STATUS'] . "</td><td style='padding:15px;'>" . $personToBeRechecked['PES_RECHECK_DATE'] . "</td></tr>";
        }

        $pesEmail.="</tbody>";
        $pesEmail.="</table>";

        $pesEmail.= "<style> th { background:red; padding:!5px; } </style>";

        $emailBody = $pesEmail;

        $sendResponse = BlueMail::send_mail(self::$notifyPesEmailAddresses['to'], "UPES Upcoming Rechecks", $emailBody,self::$notifyPesEmailAddresses['to'][0],self::$notifyPesEmailAddresses['cc']);
        return $sendResponse;
    }

    static function notifyPesTeamNoUpcomingRechecks(){

        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        include_once self::getEmailBodiesDirectory() . '/recheckReport.php';

        $pesEmail.= "<h4>Generated by uPes: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<p>No upcoming rechecks have been found</p>";
        $emailBody = $pesEmail;

        $sendResponse = BlueMail::send_mail(self::$notifyPesEmailAddresses['to'], "Upcoming Rechecks-None", $emailBody,self::$notifyPesEmailAddresses['to'][0],self::$notifyPesEmailAddresses['cc']);
        return $sendResponse;
    }

    static function notifyPesTeamLeaversFound($detailsOfLeavers){
        $shortDetails = array();
        $fullDetails = array();
        foreach ($detailsOfLeavers as $leaver){
            $shortDetails[$leaver['CNUM']]  = $leaver['FULL_NAME'];
            $fullDetails[$leaver['CNUM']][] = $leaver;
        }

        $slack = new slack();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        include_once self::getEmailBodiesDirectory() . '/leaversFound.php';

        $pesEmail.= "<h4>Generated by uPes: " . $now->format('jS M Y') . "</h4>";

        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  >";
        $pesEmail.= "<thead style='background-color: #cce6ff; padding:25px;'>";
        $pesEmail.= "<tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Full Name</th><th style='padding:25px;'>Account</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Cleared Date</th><th style='padding:25px;'>Recheck Date</th>";
        $pesEmail.= "</tr></thead><tbody>";

//         foreach ($detailsOfLeavers as $leaver) {
//             $pesEmail.="<tr><td style='padding:15px;'>" . $leaver['CNUM'] . "</td><td style='padding:15px;'>" . $leaver['FULL_NAME']  . "</td><td style='padding:15px;'>" . $leaver['ACCOUNT']  . "</td><td style='padding:15px;'>" . $leaver['PES_STATUS'] . "</td><td style='padding:15px;'>" . $leaver['PES_CLEARED_DATE'] . "</td><td style='padding:15px;'>" . $leaver['PES_RECHECK_DATE'] . "</td></tr>";
//         }

        foreach ($shortDetails as $cnum => $fullName) {
            $slack->sendMessageToChannel("Leaver :  " . $cnum . " : " . $fullName , slack::CHANNEL_UPES_AUDIT);
            foreach ($fullDetails[$cnum] as $leaver){
                $pesEmail.="<tr><td style='padding:15px;'>" . $leaver['CNUM'] . "</td><td style='padding:15px;'>" . $leaver['FULL_NAME']  . "</td><td style='padding:15px;'>" . $leaver['ACCOUNT']  . "</td><td style='padding:15px;'>" . $leaver['PES_STATUS'] . "</td><td style='padding:15px;'>" . $leaver['PES_CLEARED_DATE'] . "</td><td style='padding:15px;'>" . $leaver['PES_RECHECK_DATE'] . "</td></tr>";
            }
        }

        $pesEmail.="</tbody>";
        $pesEmail.="</table>";

        $pesEmail.= "<style> th { background:blue; padding:5px; } </style>";

        $emailBody = $pesEmail;

        $sendResponse = BlueMail::send_mail(self::$notifyPesEmailAddresses['to'], "uPES Notification of Leavers", $emailBody,self::$notifyPesEmailAddresses['to'][0],self::$notifyPesEmailAddresses['cc']);
        
        return $sendResponse;
    }
}
