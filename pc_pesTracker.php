<?php

use upes\AllTables;
use upes\AccountPersonTable;
use upes\PersonRecord;
use upes\AccountPersonRecord;

?>
<div class='container'>
<div class='row'>
<div class='col-sm-4'>
</div>
<div class='col-sm-4'>
<h1 id='portalTitle' class='text-centre' >PES Tracker.</h1>
</div>
<div class='col-sm-4'>
</div>
</div>
</div>

<div class='container-fluid'>
<?php
$pesTrackerTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
$pesTrackerTable->displayTable(AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE);

$person = new AccountPersonRecord();
$person->amendPesStatusModal();
$person->confirmSendPesEmailModal();



?>
</div>
<script>
var pesevent = new pesEvent();
var person = new personRecord();

$(document).ready(function(){

	pesevent.populatePesTracker('<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_REQUESTED?>');
 	pesevent.listenForBtnRecordSelection();
 	pesevent.listenForPesStageValueChange();
 	pesevent.listenForSavePesComment();
 	pesevent.listenForPesProcessStatusChange();
 	pesevent.listenForPesPriorityChange();
 	pesevent.listenForFilterPriority();
 	pesevent.listenForFilterProcess();
 	pesevent.listenForBtnChaser();
 	pesevent.listenForEditPesStatus();
 	pesevent.listenForSavePesStatus();
// 	person.listenForInitiatePesFromPortal();
 	person.listenforSendPesEmail();
// 	person.listenforConfirmSendPesEmail();

    console.log($('button[name=pesRecordFilter]:checked'));
    console.log($('button[name=pesRecordFilter]:checked').val());
    console.log($('button[name=pesRecordFilter]:checked').data('pesRecords'));

    console.log($('.btnRecordSelection'));
    console.log($('.btnRecordSelection:checked'));





});
</script>
<style>

.alert-secondary {
	background-color:light-grey;
	color: black;
}


.pesComments {
	overflow-y: auto;
    height: 100px;
}

.btn-success:focus, .btn-success.focus {
    color: #ffffff;
    background-color: #2c8d3a;
    border-color: #1a5322;

}

.btn-warning:focus, .btn-warning.focus {

    color: #ffffff;
    background-color: #e7a413;
    border-color: #a0720d;

}

.btn-default {

    color: #ffffff;
    background-color: #aea79f;
    border-color: #aea79f;

}

.btn-info:focus, .btn-info.focus {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #3a87ad;

}

.alert-info {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #3a87ad;

}

.alert-warning {
    background-color: #fcf8e3;
    border-color: #fbeed5;
    color: #c09853;

}


.alert-success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #468847;

}

.alert-danger {
    background-color: #f2dede;
    border-color: #eed3d7;
    color: #b94a48;

}

td {
    font-size: 8px;
}

.pesComments {
    font-size: 10px;
}

.formattedEmailTd {
    font-size: 10px;
}

</style>