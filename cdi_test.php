<?php

use itdq\slack;
use upes\AllTables;
use upes\PesEmail;
use upes\PesStatusAuditRecord;
use upes\PesStatusAuditTable;


// $slack = new slack();

// $slack->sendMessageToChannel("Test message from Rob.", slack::CHANNEL_UPES_AUDIT);

// PesStatusAuditTable::insertRecord('123456', 'someone@uk.ibm.com', 'An Account','Cleared','2021-03-09');

// $now = new \DateTime();
// $updateDate = $now->format('Y-m-d H:i:s.u');
// $pesStatusAuditRecord = new PesStatusAuditRecord();
// $pesStatusAuditRecord->setFromArray(
//     array(
//         'CNUM'=>'', 
//         'EMAIL_ADDRESS'=>'shashiky1991@gmail.com', 
//         'ACCOUNT'=>'Lloyds', 
//         'PES_STATUS'=>'Provisional Clearance', 
//         'PES_CLEARED_DATE'=>'2022-04-29',
//         'UPDATER'=>'RSmith1@uk.ibm.com',
//         'UPDATED'=>$updateDate
//     )
// );
// $pesStatusAuditTable = new PesStatusAuditTable(AllTables::$PES_STATUS_AUDIT);
// $pesStatusAuditTable->saveRecord($pesStatusAuditRecord);

// $account = 'Lloyds';
// $accountType = 'FSS';
// $country = 'UK';
// $candidateEmail = 'Andrew.Moore@ocean.ibm.com';
// $recheck = 'no';

// $return = PesEmail::findEmailBody($account, $accountType, $country, $candidateEmail, $recheck);
// var_dump($return);