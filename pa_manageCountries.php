<?php

use itdq\FormClass;
use itdq\Trace;
use upes\CountryTable;
use upes\AllTables;
use upes\CountryRecord;
use itdq\Loader;
use itdq\JavaScript;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Countries</h2>
<?php
$loader = new Loader();
$allCountries = $loader->load('COUNTRY',AllTables::$COUNTRY);
?><script type="text/javascript">
  var country = [];
  <?php
  foreach ($allCountries as $country) {
      ?>country.push("<?=strtolower(trim($country));?>");<?php
  }?>
  console.log(country);
  </script>
<?php

$countryRecord = new CountryRecord();
$countryRecord->displayForm(itdq\FormClass::$modeDEFINE);

include_once 'includes/modalDeleteAccountConfirm.html';

?>
</div>

<div class='container'>

<div class='col-sm-6 col-sm-offset-1'>

<table id='countryTable' class='table table-responsive table-striped' >
<thead>
<tr><th>Country</th><th>Email Body Name</th><th>Additional Application Form</th></tr>
</thead>
</table>
</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>