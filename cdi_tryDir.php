<?php
use itdq\Trace;


echo "<div class='container'>";

// $person1 = array('ACCOUNT'=>'RBS','COUNTRY'=>'India', 'STATUS'=>'Contractor');
// $person2 = array('ACCOUNT'=>'RBS','COUNTRY'=>'India', 'STATUS'=>'Regular');
// $person3 = array('ACCOUNT'=>'RBS','COUNTRY'=>'Poland', 'STATUS'=>'Regular');
// $person4 = array('ACCOUNT'=>'RBS','COUNTRY'=>'Poland', 'STATUS'=>'Contractor');
// $person5 = array('ACCOUNT'=>'RBS','COUNTRY'=>'UK', 'STATUS'=>'Regular');

// $person6 = array('ACCOUNT'=>'CLS','COUNTRY'=>'UK', 'STATUS'=>'Regular');
// $person7 = array('ACCOUNT'=>'CLS','COUNTRY'=>'India', 'STATUS'=>'Contractor');
// $person8 = array('ACCOUNT'=>'CLS','COUNTRY'=>'Poland', 'STATUS'=>'Contractor');
// $person9 = array('ACCOUNT'=>'CLS','COUNTRY'=>'Poland', 'STATUS'=>'Regular');

// $person10 = array('ACCOUNT'=>'Halifax','COUNTRY'=>'Portugal', 'STATUS'=>'Contractor');
// $person11 = array('ACCOUNT'=>'M&S','COUNTRY'=>'France', 'STATUS'=>'Regular');


$personA = array('ACCOUNT'=>'RBS','COUNTRY'=>'India', 'STATUS'=>'Contractor');
$personB = array('ACCOUNT'=>'CLS','COUNTRY'=>'India', 'STATUS'=>'Regular');
$personC = array('ACCOUNT'=>'M&S','COUNTRY'=>'Poland', 'STATUS'=>'Regular');
$personC2 = array('ACCOUNT'=>'M&S','COUNTRY'=>'Poland', 'STATUS'=>'Contractor');
$personC3 = array('ACCOUNT'=>'M&S','COUNTRY'=>'UK', 'STATUS'=>'Regular');
$personC4 = array('ACCOUNT'=>'M&S','COUNTRY'=>'UK', 'STATUS'=>'Contractor');


$personD = array('ACCOUNT'=>'RBS','COUNTRY'=>'Poland', 'STATUS'=>'Contractor');
$personE = array('ACCOUNT'=>'RBS','COUNTRY'=>'UK', 'STATUS'=>'Regular');
$personF = array('ACCOUNT'=>'RBS','COUNTRY'=>'Poland', 'STATUS'=>'Regular');



// $people = array($person1,$person2,$person3,$person4,$person5,$person6,$person7,$person8,$person9,$person10,$person11);
$people = array($personA,$personB,$personC,$personC2,$personC3,$personC4,$personD,$personE, $personF);

// $one   = file_exists('emailAttachments');
// $two   = file_exists('emailAttachments/rob');
// $three = file_exists('emailAttachments/rob/India');




// var_dump($one);
// var_dump($two);
// var_dump($three);


function findConsentForm(array $person, $fileNamePattern = null, $subFolder='consentForms'){
    if(empty($person) or empty($fileNamePattern)){
        throw new Exception('Incorrect parms passed');
    }

    $pathToAccountCountryStatus     = "emailAttachments/$subFolder/" . $person['ACCOUNT'] . "/" . $person['COUNTRY'] . "/" . $person['STATUS'];
    $pathToAccountCountry           = "emailAttachments/$subFolder/" . $person['ACCOUNT'] . "/" . $person['COUNTRY'];
    $pathToAccountStatus            = "emailAttachments/$subFolder/" . $person['ACCOUNT'] . "/" . $person['STATUS'];

    $pathToAccount                  = "emailAttachments/$subFolder/" . $person['ACCOUNT'];

    $pathToCountry                  = "emailAttachments/$subFolder/" . $person['COUNTRY'];
    $pathToCountryStatus            = "emailAttachments/$subFolder/" . $person['COUNTRY'] . "/" . $person['STATUS'];

    $pathToStatus                   = "emailAttachments/$subFolder/" . $person['STATUS'];

    $pathToDefault    = "emailAttachments/$subFolder";

    $pathsToTry = array($pathToAccountCountryStatus,$pathToAccountCountry, $pathToAccountStatus
        , $pathToAccount
        , $pathToCountry, $pathToCountryStatus
        , $pathToStatus
        , $pathToDefault);

    $pathFound = false;
    $consentFound = false;
    $pathIndex = 0;
    while(!$consentFound && $pathIndex < count($pathsToTry)){
        $pathToTest = $pathsToTry[$pathIndex];
        $pathFound = file_exists($pathToTest);
        $pathIndex++;

        $pathFoundYesNo = $pathFound ? "Yes" : "No";

        if($pathFound){
            $filesFound = scandir($pathToTest);
        //    $consentPattern = '/(.*?)consent(.*?).(.*?)/i';
            $consentFiles = preg_grep($fileNamePattern,$filesFound);
            $consentFiles = array_values($consentFiles);

            if(!empty($consentFiles[0])){
                $consentFound = true;
                return $consentFiles[0];
            }
        }
    }
    return false;
}


foreach ($people as $person){

    $consentForm = findConsentForm($person,'/(.*?)consent(.*?).(.*?)/i');
    $odcForm = findConsentForm($person,'/(.*?)odc(.*?).(.*?)/i');

    ?>
    <div class='row'>
    <div class='col-sm-1'><b><?=$person['STATUS']?></b></div>
    <div class='col-sm-1'><b><?=$person['ACCOUNT']?></b></div>
    <div class='col-sm-1'><b><?=$person['COUNTRY']?></b></div>
    <div class='col-sm-4'><b><?=$consentForm ? $consentForm : "Not found";?></b></div>
    <div class='col-sm-5'><b><?=$odcForm ? $odcForm : "Not found";?></b></div>
    </div>
	<?php


}


