/*
 *
 *
 *
 */

class chaserBox {

	constructor() {
		console.log('+++ Function +++ chaserBox.constructor');

		this.listenForBtnChaser();

		console.log('--- Function --- chaserBox.constructor');
	}

	getAlertClassForPesChasedDate(dateField) {

		$(dateField).parent('div').removeClass('alert-success');
		$(dateField).parent('div').removeClass('alert-warning');
		$(dateField).parent('div').removeClass('alert-danger');
		$(dateField).parent('div').removeClass('alert-info');

		var today = new Date();
		var dateValue = $(dateField).val();
		var lastChased = new Date(dateValue);

		if (typeof (lastChased) == 'object') {
			var timeDiff = Math.abs(today.getTime() - lastChased.getTime());
			var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

			switch (true) {
				case diffDays < 7:
					$(dateField).parent('div').addClass('alert-success');
					break;
				case diffDays < 14:
					$(dateField).parent('div').addClass('alert-warning');
					break;
				default:
					$(dateField).parent('div').addClass('alert-danger');
					break;
			}

		} else {
			$(dateField).parent('div').removeClass('alert-info');
			return;
		}
	}

    listenForBtnChaser() {
		var $this = this;
		$(document).on('click', '.btnChaser', function () {
			var chaser = $(this).data('chaser').trim();
			var personDetails = $(this).parent('span');
			var upesref = $(personDetails).data('upesref');
			var accountid = $(personDetails).data('accountid');
			var account = $(personDetails).data('account');
			var emailaddress = $(personDetails).data('emailaddress');
			var fullName = $(personDetails).data('fullname');
			var requestor = $(personDetails).data('requestor');
			var buttonObj = $(this);
			buttonObj.addClass('spinning');

			var dateField = buttonObj.parents('td').find('.pesDateLastChased').first();
			$.ajax({
				url: "ajax/sendPesEmailChaser.php",
				type: 'POST',
				data: {
					upesref: upesref,
					account: account,
					accountid: accountid,
					emailaddress: emailaddress,
					chaser: chaser,
					fullName: fullName,
					requestor: requestor
				},
				success: function (result) {
					var resultObj = JSON.parse(result);
					$(dateField).val(resultObj.lastChased);
					$this.getAlertClassForPesChasedDate(dateField);
					if (resultObj.success == true) {
						buttonObj.removeClass('spinning');
						buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
					} else {
						alert('error has occured');
						alert(resultObj);
					}
				}
			});
		});
	}
}

export { chaserBox as default };