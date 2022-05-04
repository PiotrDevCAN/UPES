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

include_once 'includes/modalError.html';
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


<script>

var countryTable;

$(document).ready(function(){

console.log(country);

	$('#EMAIL_BODY_NAME').select2({
		width:'100%',
		placeholder: "Select email body"
	});
	$('#ADDITIONAL_APPLICATION_FORM').select2({
		width:'100%',
		placeholder:"Select additional application form"
	});


	$('#countryForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
		var url = 'ajax/saveCountryRecord.php';

		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#countryForm").serialize();
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
		    	    $('#countryForm').trigger("reset");
				} else {
     	    	    $(submitBtn).removeClass('spinning').attr('disabled',false);
		    	    $('#countryForm').trigger("reset");
	                $('.modal-body').html(responseObj.Messages);
	                $('.modal-body').addClass('bg-danger');
	                $('#modalError').modal('show');
				}
	      		$('#COUNTRY').css("background-color","white");
	      		countryTable.ajax.reload();
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
		});

	countryTable = $('#countryTable').DataTable({
    	ajax: {
            url: 'ajax/populateCountryTable.php',
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
                  [ { data: "COUNTRY" , render : {_:'display',sort:'sort' }}
                  ,{ data: "EMAIL_BODY_NAME" }
                  ,{ data: "ADDITIONAL_APPLICATION_FORM" }
                  ]
	});


// 	$(document).on('click','.deleteAccount',function(e){
// 		console.log(e);
//  		var account = $(e.target).data('account');
//  		var accountId = $(e.target).data('accountid');
//  		$('#confirmDeleteAccountName').html($(e.target).data('account'));
//  		$('#confirmDeleteAccountId').val(accountId);
//         $('#modalDeleteAccountConfirm').modal('show');
// 	});

	$(document).on('click','.editCountry',function(e){
 		var button = $(e.target).parent('button').addClass('spinning');
 		$('#COUNTRY').val($(e.target).data('country')).attr('disabled',true);
 		$('#EMAIL_BODY_NAME').val($(e.target).data('emailbodyname')).trigger('change');
 		$('#ADDITIONAL_APPLICATION_FORM').val($(e.target).data('additionaldocs')).trigger('change');

 		$('#mode').val('<?=FormClass::$modeEDIT?>');
 		$('.spinning').removeClass('spinning');

	});


// 	$(document).on('click','.confirmAccountDelete',function(e){
//  		var accountid = $('#confirmDeleteAccountId').val();
//  		console.log(accountid);

// 		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
// 		var url = 'ajax/deleteAccountRecord.php';

// 		$.ajax({
// 			type:'post',
// 		  	url: url,
// 		  	data:{
// 			  	accountid:accountid
// 			  	},
// 		  	context: document.body,
// 	      	success: function(response) {
// 	      		var responseObj = JSON.parse(response);
// 	      		if(!responseObj.success){
// 		    	    $('#accountsForm').trigger("reset");
// 	                $('#modalError .modal-body').html(responseObj.Messages);
// 	                $('#modalError .modal-body').addClass('bg-danger');
// 	                $('#modalError').modal('show');
// 				}
//  	    	    accountTable.ajax.reload();
//           	},
// 	      	fail: function(response){
// 					console.log('Failed');
// 					console.log(response);
// 	                $('#modalError .modal-body').html("<h2>Json call to delete record Failed.</h2><br>Tell Piotr");
// 	                $('#modalError .modal-body').addClass('bg-warning');
// 	                $('#modalError').modal('show');
// 	                $(submitBtn).removeClass('spinning').attr('disabled',false);
// 				},
// 	      	error: function(error){
// 	        		console.log('Ajax error');
// 	        		console.log(error.statusText);
// 	                $('#modalError .modal-body').html("<h2>Json call to delete record Errord :<br/>" + error.statusText + "</h2>Tell Piotr");
// 	                $('#modalError .modal-body').addClass('bg-warning');
// 	                $('#modalError').modal('show');
// 	                $(submitBtn).removeClass('spinning').attr('disabled',false);
// 	        	},
// 	      	always: function(){
// 	        		console.log('--- saved resource request ---');
// 	      		}
// 			});
// 	});

	$('#COUNTRY').on('keyup',function(e){
		var newCountry = $(this).val().trim().toLowerCase();
		var allreadyExists = ($.inArray(newCountry, country) >= 0 );


		if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
			$('#Submit').attr('disabled',true);
			$(this).css("background-color","LightPink");
			alert('Account already defined');
		} else {
			$(this).css("background-color","LightGreen");
			$('#Submit').attr('disabled',false);
		};
	});


// 	$('#INTERNATIONAL').bootstrapToggle();

});
</script>






<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>
