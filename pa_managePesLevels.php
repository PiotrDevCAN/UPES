<?php
use itdq\Trace;
use itdq\Loader;
use itdq\FormClass;
use upes\AllTables;
use upes\ContractRecord;
use upes\PesLevelTable;
use upes\PesLevelRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Accounts Pes Levels</h2>

<?php
$pesLevelRecord = new PesLevelRecord();
$pesLevelRecord->displayForm(itdq\FormClass::$modeDEFINE);

include_once 'includes/modalError.html';
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


<script>

var contractTable;

$(document).ready(function(){

	$('#ACCOUNT_ID').select2({
		placeholder: 'Select Account',
		width: '100%'
	});


	$('#pesLevelForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/savePesLevelRecord.php';

		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#pesLevelForm").serialize();
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
		    	    $('#pesLevelForm').trigger("reset");
		    	    $('#ACCOUNT_ID').trigger('change');
				} else {
     	    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#pesLevelForm').trigger("reset");
	                $('.modal-body').html(responseObj.Messages);
	                $('.modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
 	    	    pesLevelTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('.modal-body').html("<h2>Json call to save record Failed.</h2><br>tell Piotr");
	                $('.modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('.modal-body').html("<h2>Json call to save record Errord :<br/>" + error.statusText + "</h2>tell Piotr");
	                $('.modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});
		});

	pesLevelTable = $('#pesLevelTable').DataTable({
    	ajax: {
            url: 'ajax/populatePesLevelsTable.php',
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
                    data: "ACCOUNT"
                  },{
                    data: "PES_LEVEL"
                  },{
                    data: "PES_LEVEL_DESCRIPTION"
                  },{
                    data: "RECHECK_YEARS"
                  }]
	});


	$(document).on('click','.deletePesLevel',function(e){
		console.log(e);
 		$('#confirmDeletePesLevel').html($(e.target).data('peslevel'));
 		$('#confirmDeletePesLevelRef').val($(e.target).data('peslevelref'));
 		$('#confirmDeletePesLevelAccount').html($(e.target).data('peslevelaccount'));
        $('#modalDeletePesLevelConfirm').modal('show');
	});

	$(document).on('click','.editPesLevel',function(e){
 		var button = $(e.target).parent('button').addClass('spinning');
 		$('#PES_LEVEL').val($(e.target).data('peslevel'));
 		$('#PES_LEVEL_REF').val($(e.target).data('peslevelref'));
 		$('#ACCOUNT_ID').val($(e.target).data('peslevelaccountid')).trigger('change');
 		$('#PES_LEVEL_DESCRIPTION').val($(e.target).data('pesleveldescription'));
 		$('#RECHECK_YEARS').val($(e.target).data('recheckyears'));
		$('#mode').val('<?=FormClass::$modeEDIT?>');
		$(button).removeClass('spinning');
	});


	$(document).on('click','.confirmPesLevelDelete',function(e){
 		var peslevelref = $('#confirmDeletePesLevelRef').val();
 		console.log(peslevelref);

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/deletePesLevelRecord.php';

		$.ajax({
			type:'post',
		  	url: url,
		  	data:{
		  		peslevelref:peslevelref
			  	},
		  	context: document.body,
	      	success: function(response) {
	      		var responseObj = JSON.parse(response);
	      		if(!responseObj.success){
		    	    $('#pesLevelForm').trigger("reset");
	                $('#modalError .modal-body').html(responseObj.Messages);
	                $('#modalError .modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
	      		pesLevelTable.ajax.reload();
          	},
	      	fail: function(response){
					console.log('Failed');
					console.log(response);
	                $('#modalError .modal-body').html("<h2>Json call to delete record Failed.</h2><br>tell Piotr");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
				},
	      	error: function(error){
	        		console.log('Ajax error');
	        		console.log(error.statusText);
	                $('#modalError .modal-body').html("<h2>Json call to delete record Errord :<br/>" + error.statusText + "</h2>tell Piotr");
	                $('#modalError .modal-body').addClass('bg-warning');
	                $('#modalError').modal('show');
	                $(submitBtn).removeClass('spinning').attr('disabled',false);
	        	},
	      	always: function(){
	        		console.log('--- saved resource request ---');
	      		}
			});
	});

});
</script>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);