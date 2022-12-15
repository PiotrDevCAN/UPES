/*
 *
 *
 *
 */

import box from "./box.js";

class cancelPESRequestBox extends box {

	constructor(parent) {
		console.log('+++ Function +++ cancelPESRequestBox.constructor');

		super(parent);
		this.listenForCancelPes();

		console.log('--- Function --- cancelPESRequestBox.constructor');
	}

	listenForCancelPes() {
		var $this = this;
		$(document).on('click', '.btnPesCancel', function (e) {
			$(this).addClass('spinning');
			var upesref = ($(this).data('upesref'));
			var accountid = ($(this).data('accountid'));
			var now = new Date();
			var passportFirst = $(this).data('passportfirst');
			var passportSurname = $(this).data('psm_passportSurname');
			$.ajax({
				url: "ajax/savePesStatus.php",
				data: {
					psm_upesref: upesref,
					psm_accountid: accountid,
					psm_status: 'Cancel Requested',
					psm_detail: 'PES Cancel Requested',
					PES_DATE_RESPONDED: now.toLocaleDateString('en-US'),
					psm_passportFirst: passportFirst,
					psm_passportSurname: passportSurname
				},
				type: 'POST',
				success: function (result) {
					var resultObj = JSON.parse(result);
					$('#savePesStatus').attr('disabled', false);
					$this.table.ajax.reload();
					$('#amendPesStatusModal').modal('hide');
				}
			});
		});
	}
}

export { cancelPESRequestBox as default };