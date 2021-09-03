<?php

use itdq\slack;
use upes\PesEmail;
use upes\PesStatusAuditTable;


// $slack = new slack();

// $slack->sendMessageToChannel("Test message from Rob.", slack::CHANNEL_UPES_AUDIT);


// PesStatusAuditTable::insertRecord('123456', 'someone@uk.ibm.com', 'An Account','Cleared','2021-03-09');

$account = 'Lloyds';
$accountType = 'FSS';
$country = 'UK';
$candidateEmail = 'Andrew.Moore@ocean.ibm.com';
$recheck = 'no';

$return = PesEmail::findEmailBody($account, $accountType, $country, $candidateEmail, $recheck);
var_dump($return);