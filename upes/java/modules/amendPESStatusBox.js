/*
 *
 *
 *
 */

import DateObject from "./DateObject.js";

class amendPESStatusBox {

	constructor() {
		console.log('+++ Function +++ amendPESStatusBox.constructor');

		this.listenForAmendPesStatusModalShown();
		this.listenForEditPesStatus();
		this.listenForSavePesStatus();

		console.log('--- Function --- amendPESStatusBox.constructor');
	}

	listenForEditPesStatus() {
		$(document).on('click', '.btnPesStatus', function (e) {
			var upesref = ($(this).data('upesref'));
			var account = ($(this).data('account'));
			var accountid = ($(this).data('accountid'));
			var emailaddress = ($(this).data('emailaddress'));
			var status = ($(this).data('pesstatus'));

			if (typeof ($(this).data('passportfirst')) != 'undefined') {
				var passportFirst = $(this).data('passportfirst');
				var passportSurname = $(this).data('passportsurname');
				$('#psm_passportFirst').val($.trim(passportFirst));
				$('#psm_passportSurname').val($.trim(passportSurname));
				$('#psm_passportFirst').prop('disabled', false);
				$('#psm_passportSurname').prop('disabled', false);
			} else {
				$('#passportNameDetails').hide();
				$('#psm_passportFirst').prop('disabled', true);
				$('#psm_passportSurname').prop('disabled', true);
			}

			$('#psm_accountid').val(accountid);
			$('#psm_account').val(account);
			$('#psm_upesref').val(upesref);
			$('#psm_emailaddress').val(emailaddress);
			$("#psm_status").select2({
				data: allStatus
			});
			$('#psm_status').val(status);

			$('#psm_detail').val('');

			// var now = new Date();
			// $('#pes_date').val(now.toLocaleDateString('en-US'));

			var nowDate = new DateObject().get(['dayPadded', 'monthPadded', 'year']);
			var nowDB2Date = new DateObject().get(['year', 'monthPadded', 'dayPadded']);

			// var now = new Date();
			// var nowDate = now.getDate() + '-' + (now.getMonth()+1) + '-' + now.getFullYear();
			// var nowDB2Date = now.getFullYear() + '-' + (now.getMonth()+1) + '-' + now.getDate();

			$('#pes_date').val(nowDate);
			$('#pes_date_db2').val(nowDB2Date);

			// $('#pes_date').datepicker('destroy');
			// $('#pes_date').datepicker({
			// 	dateFormat: 'dd-mm-yy',
			// 	altField: '#pes_date_db2',
			// 	altFormat: 'yy-mm-dd',
			// 	defaultDate: 0,
			// 	maxDate: 0
			// });

			$('#amendPesStatusModal').modal('show');
		});
	}

	listenForSavePesStatus() {
		$(this).attr('disabled', true);
		$(document).on('submit', '#psmForm', function (e) {
			$('#savePesStatus').attr('disabled', true).addClass('spinning');
			var form = document.getElementById('psmForm');
			var formValid = form.checkValidity();
			if (formValid) {
				var allDisabledFields = $("input:disabled");
				$(allDisabledFields)
					.not('#psm_passportFirst')
					.not('#psm_passportSurname')
					.attr('disabled', false);
				var formData = $('#amendPesStatusModal form').serialize();
				$(allDisabledFields).attr('disabled', true);
				$.ajax({
					url: "ajax/savePesStatus.php",
					data: formData,
					type: 'POST',
					success: function (result) {
						var resultObj = JSON.parse(result);
						$('#savePesStatus')
							.attr('disabled', false)
							.removeClass('spinning');
						var success = resultObj.success;
						var currentReportRecords = $('.btnRecordSelection.active').data('pesrecords');
						if (!success) {
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
			}
			return false;
		});
	}

	listenForAmendPesStatusModalShown() {
		$(document).on('shown.bs.modal', '#amendPesStatusModal', function (e) {
			$("#psm_status").select2();
			$("#psm_detail").val("");
			$("#pes_date").datepicker({
				dateFormat: "dd-mm-yy",
				altField: "#pes_date_db2",
				altFormat: "yy-mm-dd",
				maxDate: 0,
			});
		});
	}
}

const amendPesStatusBox = new amendPESStatusBox();

export { amendPesStatusBox as default };