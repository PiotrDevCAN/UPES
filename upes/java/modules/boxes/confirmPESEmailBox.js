/*
 *
 *
 *
 */

class confirmPESEmailBox {

	constructor() {
		console.log('+++ Function +++ confirmPESEmailBox.constructor');

		this.listenForSendPesEmail();
		this.listenForConfirmSendPesEmail();

		console.log('--- Function --- confirmPESEmailBox.constructor');
	}

	listenForSendPesEmail() {
		$(document).on('click', '.btnSendPesEmail', function (e) {
			$(this).addClass('spinning');
			var data = $(this).data();
			console.log(data);
			$.ajax({
				url: "ajax/pesEmailDetails.php",
				type: 'POST',
				data: {
					country: data.country,
					account: data.account,
					accounttype: data.accounttype,
					cnum: data.cnum,
					recheck: data.recheck
				},
				success: function (result) {
					$('.btnSendPesEmail').removeClass('spinning');
					var resultObj = JSON.parse(result);
					if (resultObj.success == true) {
						$('#pesEmailUpesRef').val(data.upesref);
						$('#pesEmailFullName').val(data.fullname);
						$('#pesEmailAddress').val(data.emailaddress);
						$('#pesEmailCountry').val(data.country);
						$('#pesEmailAccount').val(data.account);
						$('#pesEmailAccountId').val(data.accountid);
						$('#pesEmailCnum').val(data.cnum);
						$('#pesEmailRecheck').val(data.recheck);
						$('#pesEmailApplicationForm').val(''); // clear it out the first time.
						var arrayLength = resultObj.pesAttachments.length;
						for (var i = 0; i < arrayLength; i++) {
							var attachments = $('#pesEmailApplicationForm').val();
							$('#pesEmailApplicationForm').val(resultObj.pesAttachments[i].filename + "\n" + attachments);
						}
						$('#confirmSendPesEmail').prop('disabled', false);
						$('#confirmSendPesEmailModal').modal('show');
					} else {
						$('#errorMessageBody').html(resultObj.messages);
						$('#errorMessageModal').modal('show');
					}
				}
			});
		});
	}

	listenForConfirmSendPesEmail() {
		$(document).on('click', '#confirmSendPesEmail', function (e) {
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
				data: {
					upesref: upesref,
					country: country,
					account: account,
					accountid: accountid,
					cnum: cnum,
					recheck: recheck
				},
				success: function (result) {
					$('#confirmSendPesEmail').removeClass('spinning');
					$('#confirmSendPesEmailModal').modal('hide');

					var resultObj = JSON.parse(result);

					$('.pesComments[data-upesacc="' + upesref + accountid + '"]').html('<small>' + resultObj.comment + '</small>');
					$('.pesStatusTd[data-upesacc="' + upesref + accountid + '"]').html(resultObj.pesStatus);
					$('.pesProcessStatusDisplay[data-upesacc="' + upesref + accountid + '"]').html(resultObj.processingStatus);
				}
			});
		});
	}
}

export { confirmPESEmailBox as default };