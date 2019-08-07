 /*
 *
 *
 *
 */

function toTitleCase(str) {
	    return str.replace(/\w\S*/g, function(txt){
	        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	    });
};


function personRecord() {

  var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';

  this.init = function(){
    console.log('+++ Function +++ personRecord.init');
    console.log('--- Function --- personRecord.init');
  },
  
  this.listenForEditPesStatus = function(){
	    $(document).on('click','.btnPesStatus', function(e){
	    	
	    	console.log(this);
	    	
	    	
	           var upesref = ($(this).data('upesref'));
	           
	           var account = ($(this).data('account'));
	           var accountid = ($(this).data('accountid'));
	           var emailaddress = ($(this).data('emailaddress'));
	           
	           console.log($(this).data('passportfirst'));
	           
	           if(typeof($(this).data('passportfirst'))!='undefined'){
	        	   var passportFirst = $(this).data('passportfirst');
	        	   var passportSurname = $(this).data('passportsurname');
	               $('#psm_passportFirst').val($.trim(passportFirst));
	               $('#psm_passportSurname').val($.trim(passportSurname));
	        	   $('#psm_passportFirst').prop('disabled',false);
	        	   $('#psm_passportSurname').prop('disabled',false);
	           } else {
	        	   $('#passportNameDetails').hide();
	        	   $('#psm_passportFirst').prop('disabled',true);
	        	   $('#psm_passportSurname').prop('disabled',true);
	           }
	           
	           var status  = ($(this).data('pesstatus'));
	           
	           $('#psm_accountid').val(accountid);
	           $('#psm_account').val(account);
	           $('#psm_upesref').val(upesref);
	           $('#psm_emailaddress').val(emailaddress);

	           $('#amendPesStatusModal').on('shown.bs.modal', { status: status}, function (e) {
	               $('#psm_status').select2();
	               $('#psm_status').val(e.data.status).trigger('change');
	               $('#psm_detail').val('');
	               $('#pes_date').datepicker({ dateFormat: 'dd M yy',
	            	   						   altField: '#pes_date_db2',
	               							   altFormat: 'yy-mm-dd' ,
	               							   maxDate:0 }
	               							  );
	           });
	           $('#amendPesStatusModal').modal('show');
	      });
	  },

	  this.listenForSavePesStatus = function(){
	    $(this).attr('disabled',true);
	    $('#psmForm').submit(function(e){
	    	$('#savePesStatus').attr('disabled',true).addClass('spinning');
	        var form = document.getElementById('psmForm');
	        var formValid = form.checkValidity();
	        if(formValid){
	          var allDisabledFields = ($("input:disabled"));
	          $(allDisabledFields).not('#psm_passportFirst').not('#psm_passportSurname').attr('disabled',false);
	          var formData = $('#amendPesStatusModal form').serialize();
	          console.log(formData);
	          $(allDisabledFields).attr('disabled',true);
	          $.ajax({
	              url: "ajax/savePesStatus.php",
	              data : formData,
	              type: 'POST',
	              success: function(result){
	                console.log(result);
	                var resultObj = JSON.parse(result);
	                $('#savePesStatus').attr('disabled',false).removeClass('spinning');
	                
	                var success = resultObj.success;
	                
	                if(!success){
	                	alert('Save PES Status, may not have been successful');
	                	alert(resultObj.messages + resultObj.emailResponse);
	                	if(typeof( personWithSubPRecord.table) != 'undefined'){
	                        // We came from the PERSON PORTAL
	                		 personWithSubPRecord.table.ajax.reload();	
	                	}
	                	
	                } else {
	                    if(typeof( personWithSubPRecord.table) != 'undefined'){
	                        // We came from the PERSON PORTAL
	                    	 personWithSubPRecord.table.ajax.reload();	
	                    }  else {
	                    	// We came from the PES TRACKER
	                    	console.log('find and amend the email details');
	                    	var cnum = resultObj.cnum;
	                    	var formattedEmail = resultObj.formattedEmailField;
	                    	$('#pesTrackerTable tr.' + cnum).children('.formattedEmailTd:first').children('.formattedEmailDiv:first').html(formattedEmail);                	
	                    }             
	                    
	                    $('#amendPesStatusModal').modal('hide');
	                }
	              }
	            });

	        };
	        return false;
	      });
	  },
	  
	  
	  this.listenForCancelPes = function(){
		    $(document).on('click','.btnPesCancel', function(e){
		    	 $(this).addClass('spinning');
		           var cnum = ($(this).data('cnum'));
		           var notesid = ($(this).data('notesid'));
		           var email = ($(this).data('email'));
		           var now = new Date();
		           var passportFirst = $(this).data('passportfirst');
		           var passportSurname = $(this).data('psm_passportSurname');
		           
		           $.ajax({
		               url: "ajax/savePesStatus.php",
		               data : {
		            	   psm_cnum : cnum,
		            	   psm_status : 'Cancel Requested',
		            	   psm_detail : 'PES Cancel Requested',
		            	   PES_DATE_RESPONDED : now.toLocaleDateString('en-US'),
		            	   psm_passportFirst : passportFirst ,
		            	   psm_passportSurname : passportSurname ,
		               },
		               type: 'POST',
		               success: function(result){
		                 console.log(result);
		                 var resultObj = JSON.parse(result);
		                 $('#savePesStatus').attr('disabled',false);
		                
		                 if(typeof( personWithSubPRecord.table) != 'undefined'){
		                     // We came from the PERSON PORTAL
		                	 personWithSubPRecord.table.ajax.reload();	
		                 }  else {
		                 	// We came from the PES TRACKER
		                 	var cnum = resultObj.cnum;
		                 	var formattedEmail = resultObj.formattedEmailField;
		                 	$('#pesTrackerTable tr.' + cnum).children('.formattedEmailTd:first').children('.formattedEmailDiv:first').html(formattedEmail);                	
		                 }             
		                 
		                 $('#amendPesStatusModal').modal('hide');
		               }
		             });
		    	});
		  } 
  

}

$( document ).ready(function() {
  var person = new personRecord();
    person.init();
});
