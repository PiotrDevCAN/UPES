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
	      		$('#ACCOUNT').css("background-color","white");
 	    	    accountTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('#modalError .modal-body').html("<h2>Json call to save record Failed.</h2><br>Tell Piotr");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('#modalError .modal-body').html("<h2>Json call to save record Errord :<br/>" + error.statusText + "</h2>Tell Piotr");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});
		}
	);

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
				  },{
                    data: "ACCOUNT_TYPE"
                  },{
                    data: "TASKID"
                  }]
	});

	$(document).on('click','.deleteAccount',function(e){
		console.log(e);
 		var account = $(e.target).data('account');
 		var accountId = $(e.target).data('accountid');
 		$('#confirmDeleteAccountName').html($(e.target).data('account'));
 		$('#confirmDeleteAccountId').val(accountId);
        $('#modalDeleteAccountConfirm').modal('show');
	});

	$(document).on('click','.editAccountName',function(e){
 		var button = $(e.target).parent('button').addClass('spinning');
 		var account = $(e.target).data('account');
 		$('#ACCOUNT').val($(e.target).data('account'));
 		$('#ACCOUNT_ID').val($(e.target).data('accountid'));
		$('#ACCOUNT_TYPE').val($(e.target).data('accounttype'));
 		$('#TASKID').val($(e.target).data('taskid'));
		$('#mode').val('<?=FormClass::$modeEDIT?>');
		$(button).removeClass('spinning');
	});

	$(document).on('click','.confirmAccountDelete',function(e){
 		var accountid = $('#confirmDeleteAccountId').val();
 		console.log(accountid);

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/deleteAccountRecord.php';

		$.ajax({
			type:'post',
		  	url: url,
		  	data:{
			  	accountid:accountid
			  	},
		  	context: document.body,
	      	success: function(response) {
	      		var responseObj = JSON.parse(response);
	      		if(!responseObj.success){
		    	    $('#accountsForm').trigger("reset");
	                $('#modalError .modal-body').html(responseObj.Messages);
	                $('#modalError .modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
 	    	    accountTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('#modalError .modal-body').html("<h2>Json call to delete record Failed.</h2><br>Tell Piotr");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('#modalError .modal-body').html("<h2>Json call to delete record Errord :<br/>" + error.statusText + "</h2>Tell Piotr");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});
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

	$('#TASKID').on('keyup change',function(e){
		var emailAddress = $('#TASKID').val();
		var emailReg = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

		console.log(emailAddress);

		if(emailAddress==''){
			$('#TASKID').css('background-color','inherit');
		} else {
			if(emailReg.test(emailAddress)){
				$('#TASKID').css('background-color','LightGreen');
			} else {
				$('#TASKID').css('background-color','LightPink');
			};
		}
	});
});
</script>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>
