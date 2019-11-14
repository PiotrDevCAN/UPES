<?php
use itdq\Trace;
use itdq\AuditTable;
use itdq\AuditRecord;
use itdq\BlueMail;
use upes\PesEmail;

Trace::pageOpening($_SERVER['PHP_SELF']);

$candidate_first_name = 'John';
$candidateEmail = array('rob.daniel@uk.ibm.com');
$serial = '123456866';

// $account = 'CLS';
// $country = "India";
// $candidateName = 'CLS_India';
// PesEmail::sendPesApplicationForms($account, $country, $serial,  $candidateName, $candidate_first_name, $candidateEmail);

// $account = 'LBG';
// $country = "Turkey";
// $candidateName = 'LBG_Turkey';
// PesEmail::sendPesApplicationForms($account, $country, $serial,  $candidateName, $candidate_first_name, $candidateEmail);

// $account = 'Nationwide';
// $country = "Taiwan";
// $candidateName = 'LBG_Taiwan';
// PesEmail::sendPesApplicationForms($account, $country, $serial,  $candidateName, $candidate_first_name, $candidateEmail);

// $account = 'Barclays';
// $country = "Belgium";
// $candidateName = 'Barclays Belgium';
// PesEmail::sendPesApplicationForms($account, $country, $serial,  $candidateName, $candidate_first_name, $candidateEmail);

$account = 'ATM';
$country = "UK";
$candidateName = 'ATM_UK';
$sendResponse = PesEmail::sendPesApplicationForms($account, $country, $serial,  $candidateName, $candidate_first_name, $candidateEmail);

var_dump($sendResponse);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);