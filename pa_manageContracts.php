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

include_once 'includes/modalError.html';
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


<script>

var contractTable;

$(document).ready(function(){

	$('#ACCOUNT_ID').select2({
		placeholder: 'Select Account',
		width: '100%'
	});


	$('#contractsForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/saveContractRecord.php';

		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#contractsForm").serialize();
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
		    	    $('#contractsForm').trigger("reset");
				} else {
     	    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#contractsForm').trigger("reset");
	                $('#modalError .modal-body').html(responseObj.Messages);
	                $('#modalError .modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
	      		$('#CONTRACT').css("background-color","white");
 	    	    contractTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('#modalError .modal-body').html("<h2>Json call to save record Failed.</h2><br>Tell Rob");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('#modalError .modal-body').html("<h2>Json call to save record Errord :<br/>" + error.statusText + "</h2>Tell Rob");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});
		});

	contractTable = $('#contractTable').DataTable({
    	ajax: {
            url: 'ajax/populateContractsTable.php',
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
                    data: "CONTRACT_ID"
                  },{
                    data: "ACCOUNT"
                  },{
                    data: "CONTRACT"
                  }]
	});


	$(document).on('click','.deleteContract',function(e){
		console.log(e);
 		$('#confirmDeleteContractName').html($(e.target).data('contract'));
 		$('#confirmDeleteContractId').val($(e.target).data('contractid'));
        $('#modalDeleteContractConfirm').modal('show');
	});

	$(document).on('click','.editContractName',function(e){
 		var button = $(e.target).parent('button').addClass('spinning');
 		$('#CONTRACT').val($(e.target).data('contract'));
 		$('#CONTRACT_ID').val($(e.target).data('contractid'));
		$('#mode').val('<?=FormClass::$modeEDIT?>');
		$(button).removeClass('spinning');
	});


	$(document).on('click','.confirmContractDelete',function(e){
 		var contractid = $('#confirmDeleteContractId').val();
 		console.log(contractid);

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/deleteContractRecord.php';

		$.ajax({
			type:'post',
		  	url: url,
		  	data:{
			  	contractid:contractid
			  	},
		  	context: document.body,
	      	success: function(response) {
	      		var responseObj = JSON.parse(response);
	      		if(!responseObj.success){
		    	    $('#contractsForm').trigger("reset");
	                $('#modalError .modal-body').html(responseObj.Messages);
	                $('#modalError .modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
 	    	    contractTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('#modalError .modal-body').html("<h2>Json call to delete record Failed.</h2><br>Tell Rob");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('#modalError .modal-body').html("<h2>Json call to delete record Errord :<br/>" + error.statusText + "</h2>Tell Rob");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});



	});

	$('#CONTRACT').on('keyup',function(e){
		var newContract = $(this).val().trim().toLowerCase();
		var allreadyExists = ($.inArray(newContract, contracts) >= 0 );


		if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
			$('#Submit').attr('disabled',true);
			$(this).css("background-color","LightPink");
			alert('Contract already defined');
		} else {
			$(this).css("background-color","LightGreen");
			$('#Submit').attr('disabled',false);
		};
	});



});
</script>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);