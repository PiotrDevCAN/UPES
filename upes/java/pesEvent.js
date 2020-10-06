	/*
 *
 *
 *
 */

$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

function searchTable(){
	  var filter = $('#pesTrackerTableSearch').val().toUpperCase();

	  if(filter.length > 3){
		  $('#pesTrackerTable tr').hide();
		  $('#pesTrackerTable th').parent('tr').show();
		  
		  $('#pesTrackerTable tbody tr').children('td').not('.nonSearchable').each(function(){
			  var text = $(this).text().trim().replace(/[\xA0]/gi, ' ').replace(/  /g,'').toUpperCase();
			  if(text.indexOf(filter) > -1){
				  var tr = $(this).parent('tr').show();
			  }
		  });		  
	  } else {
		  $('#pesTrackerTable tr').show	()
	  }
}





function pesEvent() {

  this.init = function(){
    console.log('+++ Function +++ pesEvent.init');
    
    $(document).on('ready',function(){
        $('.pesDateLastChased').datepicker({
        	dateFormat: 'dd M yy',
    		maxDate:0,
            onSelect: function(dateText) {
            	var cnum = $(this).data('cnum');
            	var pesevent = new pesEvent();
            	pesevent.saveDateLastChased(dateText, cnum, this);
              }		
    		}
        ).on("change", function() {
            alert("Got change event from field");
        });
    });	
    
    
    
    console.log('--- Function --- pesEvent.init');
  },   
    

  
  this.listenForBtnRecordSelection = function() {
	  $(document).on('click','.btnRecordSelection', function(){
		  $('.btnRecordSelection').removeClass('active');
		  $(this).addClass('active');	  
		  var pesevent = new pesEvent();
		  pesevent.populatePesTracker($(this).data('pesrecords'));
	  });
  }, 
  
  this.listenForBtnChaser = function() {
	  $(document).on('click','.btnChaser', function(){
		  var chaser = $(this).data('chaser').trim();
		  var details = $(this).parent('span').parents('tr.personDetails');
		  var upesref = $(details).data('upesref');
		  var accountid = $(details).data('accountid');
		  var account   = $(details).data('account');
		  var emailaddress   = $(details).data('emailaddress');
		  var fullName = $(details).data('fullname');
		  var requestor = $(details).data('requestor');
		  
		  var buttonObj = $(this);
		  buttonObj.addClass('spinning');
		  
		  var dateField = buttonObj.parents('td').find('.pesDateLastChased').first();
		  $.ajax({
			  	url: "ajax/sendPesEmailChaser.php",
			  	type: 'POST',
			  	data : { upesref: upesref,
			  		     account:account,
			  		     accountid:accountid,
			  		     emailaddress:emailaddress, 
			  		     chaser: chaser,
			  		     fullName : fullName,
			  		     requestor : requestor
			  			},
			    success: function(result){
			    	var resultObj = JSON.parse(result);
			    	pesevent = new pesEvent();
			    	$(dateField).val(resultObj.lastChased);
			    	pesevent.getAlertClassForPesChasedDate(dateField);
			    	if(resultObj.success==true){
				    	buttonObj.removeClass('spinning');
				    	buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);			    		
			    	} else {
			    		alert('error has occured');
			    		alert(resultObj);
			    	}

			    }
		  });

		  
//		  var pesevent = new pesEvent();
//		  pesevent.populatePesTracker($(this).data('pesrecords'));
	  });
  },
  
  
  
  
  this.populatePesTracker = function(records){
 
	  var buttons = $('.btnRecordSelection');	  
  
	  $('#pesTrackerTableDiv').html('<i class="fa fa-spinner fa-spin" style="font-size:68px"></i>');

	  pesTrackerTable = $.ajax({
		  	url: "ajax/populatePesTrackerTable.php",
		  	type: 'POST',
		  	data : { records: records,
		  			},
		    success: function(result){
		    	var resultObj = JSON.parse(result);
		    	if(resultObj.success){
		    		$('#pesTrackerTableDiv').html(resultObj.table);	

		    		$('#pesTrackerTable thead th').each( function () {
		    	        var title = $(this).text();
		    	        $(this).html(title + '<input class="secondInput" type="hidden"  />' );
		    	    } );		    		
		    		
		    	    $('#pesTrackerTable thead td').not('.nonSearchable').not('.shortSearch').each( function () {
		    	        var title = $(this).text();
		    	        $(this).html('<input class="firstInput" type="text" size="10" placeholder="Search '+title+'" />' );
		    	    });

		    	    $('.shortSearch').each( function () {
		    	        var title = $(this).text();
		    	        $(this).html('<input class="firstInput" type="text" size="5" placeholder="Search '+title+'" />' );
		    	    });


		    	    
		    	    $('.btnTogglePesTrackerStatusDetails').remove();
		    		
		    	} else {
		    		$('#pesTrackerTableDiv').html(resultObj.messages);
		    	}		    	
		    }
	  });
      // Apply the search
    
	        $(document).on( 'keyup change', '.firstInput', function (e) {
	        	var searchFor = this.value;
	        	var col = $(this).parent().index();      	
	        	var searchCol = col + 1;
	        	if(searchFor.length >= 3){
		        	$('#pesTrackerTable tbody tr').hide();	        	
		        	$('#pesTrackerTable tbody td:nth-child(' + searchCol + '):contains(' + searchFor + ')	').parent().show();	        		
	        	} else {
	        		$('#pesTrackerTable tbody tr').show();
	        	}

	       } );


	  
	  
	  
  }
  
  
  this.saveDateLastChased = function(date,cnum, field){
	  var parentDiv = $(field).parent('div');
	  $.ajax({
		  	url: "ajax/savePesDateLastChased.php",
		  	type: 'POST',
		  	data : { cnum: cnum,
		  		     date: date
		  			},
		    success: function(result){
		    	var resultObj = JSON.parse(result);
		    	pesevent = new pesEvent();
		    	pesevent.getAlertClassForPesChasedDate(field);
		    	buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		    }
	  });
  }
  
  this.listenForComment = function() {
	  $('textarea').on('input', function(){	  
	  });
  }, 
  
  
  
  this.listenForSavePesComment = function() {
	  $(document).on('click','.btnPesSaveComment', function(){
		  
		  var upesref   =  $(this).siblings('textarea').data('upesref');
		  var accountid =  $(this).siblings('textarea').data('accountid');
		  var comment   = $(this).siblings('textarea').val();
		  var button    = $(this);
	  
		  button.addClass('spinning');
		  $.ajax({
			  	url: "ajax/savePesComment.php",
			  	type: 'POST',
			  	data : { upesref: upesref,
			  		   accountid:accountid, 
			  		     comment: comment,
			  			},
			  	success: function(result){
			        var resultObj = JSON.parse(result);
			  		button.removeClass('spinning');
			  		button.siblings('div.pesComments').html(resultObj.comment);
			  		button.siblings('textarea').val('');
		      		}
	        	});
	  	});
  }

  
  this.listenForPesStageValueChange = function(){
	  $(document).on('click','.btnPesStageValueChange', function(){  
		  var personDetails = $(this).parents('.personDetails');
		  var setPesTo = $(this).data('setpesto');	
		  var column   = $(this).parents('.columnDetails').first().data('pescolumn');		  
		  var upesref  = $(personDetails).data('upesref');
		  var accountid= $(personDetails).data('accountid');

		  var pesevent = new pesEvent();
		  var alertClass = pesevent.getAlertClassForPesStage(setPesTo);
		  		  
		  $(this).parents('div').prev('div.pesStageDisplay').html(setPesTo);
		  $(this).parents('div').prev('div.pesStageDisplay').removeClass('alert-info').removeClass('alert-warning').removeClass('alert-success').addClass(alertClass);
		  $(this).addClass('spinning');
		  
		  var buttonObj = $(this);
		  
		   $.ajax({
			   url: "ajax/savePesStageValue.php",
		       type: 'POST',
		       data : {upesref:upesref,
		    	   	   stageValue:setPesTo,
		    	       accountid:accountid, 
		    	   	   stage:column,
		    	   	   },
		       success: function(result){
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		           } else {
		       		  $(this).parents('div').prev('div.pesStageDisplay').html(resultObj.message);	 
		           };
		           buttonObj.removeClass('spinning');
		       }
		   });
	  });
  }
  
  
  this.getAlertClassForPesStage = function(pesStageValue){
      switch (pesStageValue) {
      case 'Yes':
          var alertClass = ' alert-success ';
          break;
      case 'Prov':
          var alertClass = ' alert-warning ';
          break;
      case 'N/A':
          var alertClass = ' alert-secondary ';
          break; 
      default:
          var alertClass = ' alert-info ';
          break;
  }
  return alertClass;
}
  
  this.listenForPesProcessStatusChange = function(){
	  $(document).on('click','.btnProcessStatusChange', function(){  
		  var buttonObj = $(this);
		  var processStatus = $(this).data('processstatus');	
		  var dataDiv       = $(this).parents('tr.personDetails');
		  var upesref       = $(dataDiv).data('upesref');
		  var accountid     = $(dataDiv).data('accountid');
		  var account       = $(dataDiv).data('account');
		  var fullname      = $(dataDiv).data('fullname');
		  var emailaddress  = $(dataDiv).data('emailaddress');
		  var requestor     = $(dataDiv).data('requestor');
//		  $(this).parents('div').prev('div.pesProcessStatusDisplay').html(processStatus);
		  $(this).addClass('spinning');
		   $.ajax({
			   url: "ajax/savePesProcessStatus.php",
		       type: 'POST',
		       data : {      upesref:upesref,
		    	           accountid:accountid,
                             account: account, 
		    	       processStatus:processStatus,
		    	           fullname : fullname,
		    	       emailaddress : emailaddress,
		    	          requestor : requestor
		    	   	   },
		       success: function(result){
			       console.log(result);
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   buttonObj.parents('div:first').siblings('div.pesProcessStatusDisplay').html(resultObj.formattedStatusField);	
		        	   buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		           }
		           $(buttonObj).removeClass('spinning');
		       }
		   });
	  });
  },
  
  
  this.listenForPesPriorityChange = function(){
	  $(document).on('click','.btnPesPriority', function(){  
		  var buttonObj   = $(this);
		  var pespriority = $(this).data('pespriority');					  
		  var upesref     = $(this).data('upesref');
		  var accountid   = $(this).data('accountid');
//		  $(this).parents('div').prev('div.pesProcessStatusDisplay').html(processStatus);
		  $(this).addClass('spinning');
		   $.ajax({
			   url: "ajax/savePesPriority.php",
		       type: 'POST',
		       data : {    upesref:upesref,
		    	         accountid:accountid,
		    	       pespriority:pespriority,
		    	   	   },
		       success: function(result){
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   buttonObj.parent('span').siblings('div.priorityDiv:first').html("Priority:" + pespriority);
		        	   var pesevent = new pesEvent();
		        	   pesevent.setAlertClassForPesPriority(buttonObj.parent('span').siblings('div.priorityDiv:first'),pespriority);
		        	           	   
		        	   buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		        	   
		        	   
		           }
		           $(buttonObj).removeClass('spinning');
		       }
		   });
	  });
  },
  
  this.setAlertClassForPesPriority = function(priorityField, priority){
	  
	  $(priorityField).removeClass('alert-success');
	  $(priorityField).removeClass('alert-warning');
	  $(priorityField).removeClass('alert-danger');
	  $(priorityField).removeClass('alert-info'); 

	  
	  
	  switch(priority){
	  case 1:
		  $(priorityField).addClass('alert-danger');	  
		  break;
	  case 2:
		  $(priorityField).addClass('alert-warning');
		  break;
	  case 3:
		  $(priorityField).addClass('alert-success');
		  break;			  
	  default :
		  $(priorityField).addClass('alert-info');
		  break;
	  }			  
  } ,

  
  this.getAlertClassForPesChasedDate = function(dateField){
	  
	  $(dateField).parent('div').removeClass('alert-success');
	  $(dateField).parent('div').removeClass('alert-warning');
	  $(dateField).parent('div').removeClass('alert-danger');
	  $(dateField).parent('div').removeClass('alert-info');  
	  
	  var today = new Date();
//	  var date1 = new Date("7/13/2010");  
	  var dateValue = $(dateField).val();	  
	  var lastChased = new Date(dateValue);	  
	  
	  if(typeof(lastChased)=='object'){
		  var timeDiff = Math.abs(today.getTime() - lastChased.getTime());
		  var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 	  
		  
		  switch(true){
		  case diffDays < 7:
			  $(dateField).parent('div').addClass('alert-success');	  
			  break;
		  case diffDays < 14:
			  $(dateField).parent('div').addClass('alert-warning');
			  break;
		  default :
			  $(dateField).parent('div').addClass('alert-danger');
			  break;
		  }		  
		  
	  } else {
		  $(dateField).parent('div').removeClass('alert-info');		  
		  return;
	  } 
  },
  
  this.listenForFilterPriority = function(){
	  $(document).on('click','.btnSelectPriority', function(){
		  var priority = $(this).data('pespriority');
		  if(priority!=0){
			  $('tr').hide();
			  $(".priorityDiv:contains('" + priority + "')").parents('tr').show();
			  $('th').parent('tr').show();			  
		  } else {
			  $('tr').show();
		  }
	  });
  },
  
  this.listenForFilterProcess = function(){
	  $(document).on('click','.btnSelectProcess', function(){
		  var pesprocess = $(this).data('pesprocess');
		  $('tr').hide();
		  $(".pesProcessStatusDisplay:contains('" + pesprocess + "')").parents('tr').show();
		  $('th').parent('tr').show();			  
	  });
  },
  
  this.listenForEditPesStatus = function(){
	    $(document).on('click','.btnPesStatus', function(e){  
	    	console.log('btnPesStatus clicked');
	           var upesref = ($(this).data('upesref'));
	           var account = ($(this).data('account'));
	           var accountid = ($(this).data('accountid'));
	           var emailaddress = ($(this).data('emailaddress'));     
           
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
	               $('#pes_date').datepicker('destroy');
	               $('#pes_date').datepicker({ dateFormat: 'dd-mm-yy',
	            	   						   altField:'#pes_date_db2',
	            	   						   altFormat:'yy-mm-dd',
	            	                           defaultDate: 0,
	            	                           maxDate:0 } );
	           });          
	           $('#amendPesStatusModal').modal('show');
	      });
	  },
  
  this.listenForSavePesStatus = function(){
	    $(this).attr('disabled',true);
	    $('#psmForm').submit(function(e){
	    	
            console.log($('#pes_date').val());
            console.log($('#pes_date_db2').val());
	    	
	    	
	    	$('#savePesStatus').attr('disabled',true).addClass('spinning');
	        var form = document.getElementById('psmForm');
	        var formValid = form.checkValidity();
	        if(formValid){
	          var allDisabledFields = ($("input:disabled"));
	          $(allDisabledFields).not('#psm_passportFirst').not('#psm_passportSurname').attr('disabled',false);
	          var formData = $('#amendPesStatusModal form').serialize();
	          $(allDisabledFields).attr('disabled',true);
	          $.ajax({
	              url: "ajax/savePesStatus.php",
	              data : formData,
	              type: 'POST',
	              success: function(result){
	                var resultObj = JSON.parse(result);
	                $('#savePesStatus').attr('disabled',false).removeClass('spinning');	                
	                var success = resultObj.success;
	      		    var currentReportRecords = $('.btnRecordSelection.active').data('pesRecords');                
	                // pesevent.populatePesTracker(currentReportRecords);
	                if(!success){
	                	alert('Save PES Status, may not have been successful');
	                	alert(resultObj.messages + resultObj.emailResponse);
	                } else {                    
	                    $('#amendPesStatusModal').modal('hide');
	                    var upesref = resultObj.upesref;
	                    var accountid = resultObj.accountid;	                    
	                    console.log($('.pesStatusTd[data-upesacc="' + upesref + accountid + '"]'));	                    
	                    $('.pesStatusTd[data-upesacc="' + upesref + accountid + '"]').html(resultObj.pesStatus);                   
	                    
	                }
              	
	              }
	            });

	        };
	        return false;
	      });
	  }

  
  
}

$( document ).ready(function() {	  
	  var Pesevent = new pesEvent();
	  Pesevent.init();
	});