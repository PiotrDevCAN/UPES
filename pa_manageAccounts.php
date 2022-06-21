<?php


use itdq\FormClass;
use itdq\Trace;
use upes\AccountTable;
use upes\AllTables;
use upes\AccountRecord;
use itdq\Loader;
use itdq\JavaScript;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Accounts</h2>
<?php
$loader = new Loader();
$allAccounts = $loader->load('ACCOUNT',AllTables::$ACCOUNT);

?><script type="text/javascript">
  var accounts = [];
  <?php
  foreach ($allAccounts as $account) {
      ?>accounts.push("<?=strtolower(trim($account));?>");<?php
  }?>
  console.log(accounts);
  </script>
<?php

$accountsRecord = new AccountRecord();
$accountsRecord->displayForm(itdq\FormClass::$modeDEFINE);

include_once 'includes/modalDeleteAccountConfirm.html';

?>
</div>

<div class='container'>

<div class='col-sm-10 col-sm-offset-1'>

<table id='accountTable' class='table table-responsive table-striped' >
<thead>
<tr><th>Action</th><th>Account Id</th><th class='searchable' >Account</th><th class='searchable' >Account Type</th><th class='searchable' >PES Taskid</th></tr>
</thead>
</table>
</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>
