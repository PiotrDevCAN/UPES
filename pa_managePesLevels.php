<?php
use itdq\Trace;
use itdq\FormClass;
use upes\PesLevelRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Accounts Pes Levels</h2>

<?php
$pesLevelRecord = new PesLevelRecord();
$pesLevelRecord->displayForm(itdq\FormClass::$modeDEFINE);

include_once 'includes/modalDeletePesLevelConfirm.html';

?>
</div>

<div class='container'>

<div class='col-sm-7 col-sm-offset-1'>

<table id='pesLevelTable' class='table table-responsive table-striped' >
<thead>
<tr><th>Action</th><th>Account</th><th class='searchable' >PES Level</th><th class='searchable' >PES Level Description</th><th class='searchable' >Recheck Years</th></tr>
</thead>
</table>
</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);