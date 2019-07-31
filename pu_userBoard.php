<?php


use itdq\FormClass;
use itdq\Trace;
use upes\AccountTable;
use upes\AllTables;
use upes\AccountRecord;
use itdq\Loader;
use itdq\JavaScript;
use upes\PersonRecord;
use upes\ContractTable;
use upes\PesLevelTable;
use upes\PersonTable;
use upes\AccountPersonRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

$pesLevelTable = new PesLevelTable(AllTables::$PES_LEVELS);
$pesLevelByAccount  = PesLevelTable::prepareJsonArraysForPesSelection();
$allContractAccountMapping = ContractTable::prepareJsonObjectMappingContractToAccount();
$accountIdLookup = AccountTable::prepareJsonAccountIdLookup();
$upesrefToNameMapping = PersonTable::prepareJsonUpesrefToNameMapping();

?>
<div class='container'>
<h2>Board Individual</h2>

<?php
$accountPersonRecord = new AccountPersonRecord();
$accountPersonRecord->displayForm(itdq\FormClass::$modeDEFINE);

include_once 'includes/modalError.html';
$contractSelectObj = ContractTable::prepareJsonObjectForContractsSelect();
?>
</div>

<script>

function changePesLevels(dataCategory){
    $("#PES_LEVEL").select2({
        data:dataCategory,
        placeholder:'Select Pes Level'
    })
    .attr('disabled',false)
    .attr('required',true);

};

$(document).ready(function(){
	var contractsSelect = <?=$contractSelectObj;?>;
	var pesLevelByAccount = <?=json_encode($pesLevelByAccount);?>;
	var accountContractLookup = <?=json_encode($allContractAccountMapping); ?>;
	var accountIdLookup = <?=json_encode($accountIdLookup); ?>;
	var upesrefToNameMapping = <?= json_encode($upesrefToNameMapping); ?>;


	$('#PES_LEVEL').select2({
		width: '100%'
	});

	$('#UPES_REF').select2({
		width: '100%',
		placeholder:'Select Email'
	});

	$('#CONTRACT_ID').change(function(e){
		console.log(e);
		var contractId = $('#CONTRACT_ID').val();
        $("#PES_LEVEL").select2("destroy");
        $("#PES_LEVEL").html("<option><option>");
        changePesLevels(pesLevelByAccount[accountContractLookup[contractId]]);
	});

	$('#CONTRACT_ID').select2({
		placeholder: 'Select Contract',
		width: '100%',
		data : contractsSelect,
		dataType : 'json'
	});

	$('#UPES_REF').change(function(e){
		console.log(e);
		var upesRef = $('#UPES_REF').val();
		console.log(upesRef);
		var fullName = upesrefToNameMapping[upesRef];
		$('#FULL_NAME').val(fullName);
	});




	$('#accountPersonForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/boardPersonToAccountRecord.php';

		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#accountPersonForm").serialize();
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
		    	    $('#personForm').trigger("reset");
		            $("#PES_LEVEL").select2("destroy");
		            $("#PES_LEVEL").html("<option><option>");
		        	$('#PES_LEVEL').select2({width: '100%'})
		        	    .attr('disabled',false)
		                .attr('required',true);
		    	} else {
     	    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#personForm').trigger("reset");
	                $('.modal-body').html(responseObj.Messages);
	                $('.modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
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
});
</script>






<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>