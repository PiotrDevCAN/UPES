<?php


use itdq\FormClass;
use itdq\Trace;
use upes\AccountTable;
use upes\AllTables;
use upes\AccountRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Accounts</h2>
<?php
$accountsRecord = new AccountRecord();
$accountsRecord->displayForm(itdq\FormClass::$modeDEFINE);
?>
</div>


<script>

$(document).ready(function(){

	$('#accountsForm').submit(function(e){
		console.log(e);
		e.preventDefault();
		});



});



</script>






<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>
