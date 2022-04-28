<?php

use upes\AccountPersonTable;
use upes\allTables;

$filename = "../ots_uploads/" . $_POST['filename'];

// kPES schemas
// 'UPES_NEWCO_DEV';
// 'UPES_NEWCO_UT';
// 'UPES_NEWCO';

// uPES schemas
// 'UPES_DEV';
// 'UPES_UT';
// 'UPES';

// $GLOBALS['Db2Schema'] = 'UPES_NEWCO';
// echo $GLOBALS['Db2Schema'];

$accountPersonTable = new AccountPersonTable(allTables::$ACCOUNT_PERSON);
$accountPersonTable->copyXlsxToDb2($filename);

// $GLOBALS['Db2Schema'] = 'UPES_NEWCO_DEV';
// echo $GLOBALS['Db2Schema'];