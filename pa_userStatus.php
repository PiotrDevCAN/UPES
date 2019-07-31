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
<h2>User Status Report</h2>
<?php

include_once 'includes/modalError.html';

?>
</div>

<div class='container'>

<div class='col-sm-6 col-sm-offset-1'>

<table id='userStatusTable' class='table table-responsive table-striped' >
<thead>
<tr><th>Action</th><th>CNUM</th><th>Email</th><th>Full Name</th><th >Pes Level</th><th >Account</th><th >Contract</th><th >Pes Status</th></tr>
</thead>
</table>
</div>
</div>


<script>

var userStatusTable;

$(document).ready(function(){
	userStatusTable = $('#userStatusTable').DataTable({
    	ajax: {
            url: 'ajax/populateUserStatusTable.php',
        }	,
    	autoWidth: true,
    	processing: true,
    	responsive: true,
    	dom: 'Blfrtip',
        buttons: [
                  'csvHtml5',
                  'excelHtml5',
                  'print'
              ],
       columns:  [{ data: "ACTION"
                  },{
                    data: "CNUM"
                  },{
                    data: "EMAIL_ADDRESS"
                  },{
                    data: "FULL_NAME"
                  },{
                    data: "PES_LEVEL"
                  },{
                    data: "ACCOUNT"
                  },{
                    data: "CONTRACT"
                  },{
                    data: "PES_STATUS"
                  }]
	});

});
</script>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>
