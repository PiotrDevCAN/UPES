<?php
use itdq\Trace;
use itdq\Loader;
use itdq\FormClass;
use upes\AllTables;
use upes\ContractRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Contracts</h2>
<?php
$loader = new Loader();
$allcontracts = $loader->load('CONTRACT',AllTables::$CONTRACT);
?><script type="text/javascript">
  var contracts = [];
  <?php
  foreach ($allcontracts as $contract) {
      ?>contracts.push("<?=strtolower(trim($contract));?>");<?php
  }?>
  </script>
<?php

$contractRecord = new ContractRecord();
$contractRecord->displayForm(itdq\FormClass::$modeDEFINE);

include_once 'includes/modalDeleteContractConfirm.html';

?>
</div>

<div class='container'>

<div class='col-sm-6 col-sm-offset-1'>

<table id='contractTable' class='table table-responsive table-striped' >
<thead>
<tr><th>Action</th><th>Contract Id</th><th>Account</th><th class='searchable' >Contract</th></tr>
</thead>
</table>
</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);