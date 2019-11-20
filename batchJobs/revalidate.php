<?php
use itdq\Loader;
use upes\PersonRecord;
use itdq\BluePages;
use upes\PersonTable;
use upes\AllTables;
use itdq\AuditTable;
use itdq\DbTable;
use itdq\slack;



$slack = new slack();

AuditTable::audit("Revalidation invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation invoked.", slack::CHANNEL_UPES_AUDIT);

set_time_limit(60);

$personTable = new personTable(AllTables::$PERSON);
$slack = new slack();

$loader = new Loader();

db2_commit($_SESSION['conn']);

$activeIbmErsPredicate = "   ( trim(BLUEPAGES_STATUS) = '' or BLUEPAGES_STATUS is null or BLUEPAGES_STATUS =  '" . PersonRecord::BLUEPAGES_STATUS_FOUND . "') ";
$allNonLeavers = $loader->load('CNUM',allTables::$PERSON, $activeIbmErsPredicate ); //
AuditTable::audit("Revalidation will check " . count($allNonLeavers) . " people currently flagged as found.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation will check " . count($allNonLeavers) . " people currently flagged as found.", slack::CHANNEL_UPES_AUDIT);


$chunkedCnum = array_chunk($allNonLeavers, 100);
$detailsFromBp = "&mail";
$bpEntries = array();
$allFound = array();

foreach ($chunkedCnum as $key => $cnumList){
    $bpEntries[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    foreach ($bpEntries[$key]->search->entry as $bpEntry){
        set_time_limit(20);
        $serial = substr($bpEntry->dn,4,9);
        unset($allNonLeavers[$serial]);
        $allFound[] = $serial;
    }
}

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP and so is now POTENTIALLY a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . count($allNonLeavers) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation found " . count($allNonLeavers) . "  leavers.", slack::CHANNEL_UPES_AUDIT);

PersonTable::setCnumsToFound($allFound);
PersonTable::setCnumsToNotFound($allNonLeavers);

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation completed.", slack::CHANNEL_UPES_AUDIT);

db2_commit($_SESSION['conn']);