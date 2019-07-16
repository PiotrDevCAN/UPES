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


include_once 'includes/modalError.html';

?>
</div>

<div class='container'>

<div class='col-sm-6 col-sm-offset-1'>

<table id='accountTable' class='table table-responsive table-striped' >
<thead>
<tr><th>Action</th><th>Account Id</th><th class='searchable' >Account</th></tr>
</thead>
</table>
</div>
</div>


<script>

var accountTable;

$(document).ready(function(){

console.log(accounts);


	$('#accountsForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/saveAccountRecord.php';

		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#accountsForm").serialize();
		$(disabledFields).attr('disabled',true);

		$.ajax({
			type:'post',
		  	url: url,
		  	data:formData,
		  	context: document.body,
	      	success: function(response) {
	      		var responseObj = JSON.parse(response);
	      		if(responseObj.success){
		    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#accountsForm').trigger("reset");
				} else {
     	    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#accountsForm').trigger("reset");
	                $('.modal-body').html(responseObj.Messages);
	                $('.modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
 	    	    accountTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('.modal-body').html("<h2>Json call to save record Failed.</h2><br>Tell Rob");
	                $('.modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('.modal-body').html("<h2>Json call to save record Errord :<br/>" + error.statusText + "</h2>Tell Rob");
	                $('.modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});
		});

	accountTable = $('#accountTable').DataTable({
    	ajax: {
            url: 'ajax/populateAccountsTable.php',
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
       columns:
                  [{ data: "ACTION"
                  },{
                    data: "ACCOUNT_ID"
                  },{
                    data: "ACCOUNT"
                  }]
	});


	$(document).on('click','.editAccountName',function(e){
 		var button = $(e.target).parent('button').addClass('spinning');
 		var account = $(e.target).data('account');
 		$('#ACCOUNT').val($(e.target).data('account'));
 		$('#ACCOUNT_ID').val($(e.target).data('accountid'));
		$('#mode').val('<?=FormClass::$modeEDIT?>');
		$(button).removeClass('spinning');
	});


	$('#ACCOUNT').on('keyup',function(e){
		var newAccount = $(this).val().trim().toLowerCase();
		var allreadyExists = ($.inArray(newAccount, accounts) >= 0 );


		if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
			$('#Submit').attr('disabled',true);
			$(this).css("background-color","LightPink");
			alert('Account already defined');
		} else {
			$(this).css("background-color","LightGreen");
			$('#Submit').attr('disabled',false);
		};
	});



});
</script>






<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>
