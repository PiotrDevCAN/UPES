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
// $contractSelectObj = ContractTable::prepareJsonObjectForContractsSelect();
?>
</div>

<script>

function changePesLevels(dataCategory){
    $("#PES_LEVEL").select2({
        data:dataCategory,
        placeholder:'Select Pes Level',
		width: '100%'
    })
    .attr('disabled',false)
    .attr('required',true);
};

$(document).ready(function(){
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

	$('#COUNTRY_OF_RESIDENCE').select2({
		width: '100%',
		placeholder:'Country of Residence'
	});

	$('#contract_id').change(function(e){
		console.log(e);
		var contractId = $('#contract_id').val();
		console.log(contractId);

		$('#ACCOUNT_ID').val(accountContractLookup[contractId]);
        $("#PES_LEVEL").select2("destroy");
        $("#PES_LEVEL").html("<option><option>");
        changePesLevels(pesLevelByAccount[accountContractLookup[contractId]]);
	});

	$('#contract_id').select2({
		placeholder: 'Select Contract',
		width: '100%',
// 		data : contractsSelect,
// 		dataType : 'json',
		 ajax: {
			    url: 'ajax/prepareContractsDropdown.php',
			    dataType: 'json',
			    type: 'POST',
			    data: function (params) {
				    var upesref = $('#UPES_REF').val();
			        var query = {
			          search: params.term,
			          upesref: upesref
			        }
			        // Query parameters will be ?search=[term]&type=public
			        return query;
			    }
		 }
	});

	$('#UPES_REF').on('select2:open',function(){
		console.log('select2:open');
		$('#contract_id').select2('data',null);
		$('#contract_id').val('').trigger('change');
		});

	$('#UPES_REF').change(function(e){
		var upesRef = $('#UPES_REF').val();
		var fullName = upesrefToNameMapping[upesRef];
		$('#FULL_NAME').val(fullName);
		if($('#UPES_REF').val() != ''){
			$('#contract_id').attr('disabled',false).trigger('change');
		} else {
			$('#contract_id').attr('disabled',true).trigger('change');
		}
	});

	$('#PES_REQUESTOR').on('keyup change',function(e){
		var emailAddress = $('#PES_REQUESTOR').val();
		var emailReg = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		var emailIBMReg = /(ibm.com)/i;
		var emailOceanReg = /(ocean.ibm.com)/i;

		console.log(emailAddress);

		if(emailAddress==''){
			$('#PES_REQUESTOR').css('background-color','inherit');
		} else {
			if(emailReg.test(emailAddress)){
				$('#PES_REQUESTOR').css('background-color','LightGreen');
			} else {
				$('#PES_REQUESTOR').css('background-color','LightPink');
			};
		}
	});

	$('#accountPersonForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/boardPersonToAccount.php';

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
		    	    $('#accountPersonForm').trigger("reset");
		    	    $("#contract_id").trigger("change");
		    	    $("#UPES_REF").trigger("change");
		    	    $('#COUNTRY_OF_RESIDENCE').val('').trigger('change');
		    	    $('#ACCOUNT_ID').val('');
		            $("#PES_LEVEL").select2("destroy");
		            $("#PES_LEVEL").html("<option><option>");
		        	$('#PES_LEVEL').select2({width: '100%'})
		        	    .attr('disabled',false)
		                .attr('required',true);
	                $('.modal-body').html(responseObj.Messages);
	                $('.modal-body').addClass('bg-success').removeClass('bg-danger');
	                $('.modal-title').html('Response Message');
	                $('#modalError').modal('show');
		    	} else {
     	    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#accountPersonForm').trigger("reset");
		    	    $("#contract_id").trigger("change");
		    	    $("#UPES_REF").trigger("change");
		    	    $('#COUNTRY_OF_RESIDENCE').val('').trigger('change');
		    	    $('#ACCOUNT_ID').val('');
	                $('.modal-body').html(responseObj.Messages);
	                $('.modal-body').addClass('bg-danger').removeClass('bg-success');
	                $('.modal-title').html('Error Message');
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