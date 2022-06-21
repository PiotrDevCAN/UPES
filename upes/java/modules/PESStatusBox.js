/*
 *
 *
 *
 */

class PESStatusBox {

	constructor() {
		console.log('+++ Function +++ PESStatusBox.constructor');

		this.listenForPesProcessStatusChange();

		console.log('--- Function --- PESStatusBox.constructor');
	}

    listenForPesProcessStatusChange() {
		$(document).on('click', '.btnProcessStatusChange', function () {
			var buttonObj = $(this);
			var processStatus = $(this).data('processstatus');
			var personDetails = $(this).parent('span');
			var upesref = $(personDetails).data('upesref');
			var accountid = $(personDetails).data('accountid');
			var account = $(personDetails).data('account');
			var fullname = $(personDetails).data('fullname');
			var emailaddress = $(personDetails).data('emailaddress');
			var requestor = $(personDetails).data('requestor');
			$(this).addClass('spinning');
			$.ajax({
				url: "ajax/savePesProcessStatus.php",
				type: 'POST',
				data: {
					upesref: upesref,
					accountid: accountid,
					account: account,
					processStatus: processStatus,
					fullname: fullname,
					emailaddress: emailaddress,
					requestor: requestor
				},
				success: function (result) {
					console.log(result);
					var resultObj = JSON.parse(result);
					if (resultObj.success == true) {
						buttonObj.parents('div:first').siblings('div.pesProcessStatusDisplay').html(resultObj.formattedStatusField);
						buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
					}
					$(buttonObj).removeClass('spinning');
				}
			});
		});
	}
}

const PesStatusBox = new PESStatusBox();

export { PesStatusBox as default };