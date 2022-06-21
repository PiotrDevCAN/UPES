<?php
use itdq\Trace;
use itdq\Loader;
use upes\AllTables;
use upes\AccountPersonTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

$loader = new Loader();

$allStatus = $loader->load('PES_STATUS',AllTables::$ACCOUNT_PERSON);
$allAccounts = $loader->load('ACCOUNT',AllTables::$ACCOUNT);

$db2report = AccountPersonTable::statusByAccount();

foreach ($db2report as $row) {
    $report[$row['ACCOUNT']][$row['PES_STATUS']] = $row['RESOURCES'];
}

?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'>
</div>
<div class='col-sm-6'>
<h1 id='portalTitle' class='text-centre' >PES Status By Account</h1>
</div>
<div class='col-sm-4'>
</div>
</div>
</div>

<div class='container'>
<table id='statusByAccountReport' class='table table-striped table-bordered table-condensed '  style='width:100%'>
<thead>
<tr class='' ><th>Account</th>
<?php
foreach ($allStatus as $status) {
    ?><th class='pesStatus' ><?=trim($status);?></th><?php
}
?>
</tr>
</thead>
<tbody>

<?php
foreach ($allAccounts as $account) {
    if(isset($report[$account])){
        ?><tr><td><?=$account;?></td><?php
        foreach ($allStatus as $status) {
            echo isset($report[$account][$status]) ? "<td>" . $report[$account][$status] . "</td>"  : "<td>0</td>";
        }
        ?></tr><?php
    }
}
?>
</tbody>
<tfoot>
</tfoot>
</table>
</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);