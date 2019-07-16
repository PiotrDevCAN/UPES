<?php


use itdq\FormClass;
use itdq\Trace;
use upes\AccountTable;
use upes\AllTables;
use upes\AccountRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Manage Accounts</h2>
<?php
$accountsRecord = new AccountRecord();
$accountsRecord->displayForm(itdq\FormClass::$modeDEFINE);


include_once 'includes/modalError.html';

?>
</div>


<script>

$(document).ready(function(){

	$('#accountsForm').submit(function(e){
		console.log(e);
		e.preventDefault();

		var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');

console.log(submitBtn);

		var url = 'ajax/saveAccountRecord.php';

		var disabledFields = $(':disabled');
		$(disabledFields).removeAttr('disabled');
		var formData = $("#accountsForm").serialize();

		console.log(formData);

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
// 	    	    psRateTable.ajax.reload();
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
	            //	handle errors here. What errors	            :-)!
	        		console.log('Ajax error' );
	        		console.log(error.statusText);
	                $('.modal-body').html("<h2>Json call to save record Error'd :<br/>" + error.statusText + "</h2>Tell Rob");
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
