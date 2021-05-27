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
  
  this.initEditPersonForm = function(){
  
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
					var resultObj = JSON.parse(result);
					$('#savePesStatus').attr('disabled',false);
					pesevent.table.ajax.reload();	
					$('#amendPesStatusModal').modal('hide');
				}
				});
		});
  },

  this.listenforSendPesEmail = function(){
	console.log('set listener');
	console.log($('.btnSendPesEmail'));
	$(document).on('click','.btnSendPesEmail', function(e){
		$(this).addClass('spinning');
		var data = $(this).data();
		
		console.log(data);
		
			$.ajax({
				url: "ajax/pesEmailDetails.php",
				type: 'POST',
				data : {country:data.country,					    	       
						account:data.account,
						accounttype:data.accounttype,
						cnum:data.cnum,
						recheck:data.recheck
						},
				success: function(result){
					$('.btnSendPesEmail').removeClass('spinning');		    	 
					var resultObj = JSON.parse(result);
					if(resultObj.success==true){						
						$('#pesEmailUpesRef').val(data.upesref);
						$('#pesEmailFullName').val(data.fullname);
						$('#pesEmailAddress').val(data.emailaddress);
						$('#pesEmailCountry').val(data.country);
						$('#pesEmailAccount').val(data.account);
						$('#pesEmailAccountId').val(data.accountid );
						$('#pesEmailCnum').val(data.cnum);	
						$('#pesEmailRecheck').val(data.recheck);
						$('#pesEmailApplicationForm').val(''); // clear it out the first time.
						var arrayLength = resultObj.pesAttachments.length;
						for (var i = 0; i < arrayLength; i++) {
							var attachments = $('#pesEmailApplicationForm').val();
							$('#pesEmailApplicationForm').val(resultObj.pesAttachments[i].filename + "\n" + attachments);
						}		
						$('#confirmSendPesEmail').prop('disabled',false);
						$('#confirmSendPesEmailModal').modal('show');
						} else {
							$('#modalError .modal-body').html(resultObj.messages);
							$('#modalError').modal('show');
						};
				}
			});	
	});
  },

  this.listenforConfirmSendPesEmail = function(){ 
	$(document).on('click','#confirmSendPesEmail', function(e){
		$('#confirmSendPesEmail').addClass('spinning');
		var country = $('#pesEmailCountry').val();
		var upesref = $('#pesEmailUpesRef').val();
		var account = $('#pesEmailAccount').val();
		var accountid = $('#pesEmailAccountId').val();
		var cnum = $('#pesEmailCnum').val();
		var recheck = $('#pesEmailRecheck').val();
			$.ajax({
				url: "ajax/sendPesEmail.php",
				type: 'POST',
				data : { 
					upesref:upesref,
					country:country,
					account:account,
					accountid:accountid,
					cnum:cnum,
					recheck:recheck				    	       
				},
				success: function(result){
					$('#confirmSendPesEmail').removeClass('spinning');	  		    	   					    	   
					$('#confirmSendPesEmailModal').modal('hide');
					
					var resultObj = JSON.parse(result);
					
					$('.pesComments[data-upesacc="' + upesref + accountid + '"]').html('<small>' + resultObj.comment + '</small>');
					$('.pesStatusTd[data-upesacc="' + upesref + accountid + '"]').html(resultObj.pesStatus);	
					$('.pesProcessStatusDisplay[data-upesacc="' + upesref + accountid + '"]').html(resultObj.processingStatus);
					//  $('.pesStatusField[data-upesref="' + upesref + '"]').siblings('.btnSendPesEmail').remove();
				}
			});
		});	  
  }
}

$( document ).ready(function() {
  var person = new personRecord();
    person.init();
});
