<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\DbTable;
use itdq\slack;
use upes\AccountPersonTable;

$slack = new slack();

AuditTable::audit("PES Recheck email to PES Team - invoked.",AuditTable::RECORD_TYPE_DETAILS);
$slack->sendMessageToChannel("PES Recheck email to PES Team - invoked.", slack::CHANNEL_UPES_AUDIT);

set_time_limit(60);

$accountPersonTable = new AccountPersonTable(\upes\AllTables::$ACCOUNT_PERSON);
$accountPersonTable->notifyRecheckDateApproaching();

AuditTable::audit("PES Recheck email to PES Team - completed.",AuditTable::RECORD_TYPE_DETAILS);
$slack->sendMessageToChannel("PES Recheck email to PES Team - completed.", slack::CHANNEL_UPES_AUDIT);

db2_commit($_SESSION['conn']);