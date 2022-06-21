/**
 *
 */

class manualPESStatusUpdate {

	constructor() {
		console.log('+++ Function +++ manualPESStatusUpdate.constructor');

		this.listenForPersonAccount();
		this.listenForPesStatus();
		this.listenForUpdatePerson();

		$('.select2').select2();

		$('#pes_date').datepicker({
			dateFormat: 'dd-mm-yy',
			altField: '#pes_date_db2',
			altFormat: 'yy-mm-dd',
			defaultDate: 0,
			maxDate: 0
		});

		console.log('--- Function --- manualPESStatusUpdate.constructor');
	}

	listenForPersonAccount() {
		$(document).on('change', '#personAccount', function () {
			$('#pesStatus').attr('disabled', false);
		});
	}

	listenForPesStatus() {
		$(document).on('change', '#pesStatus', function () {
			$('#updatePerson').attr('disabled', false);
		});
	}

	listenForUpdatePerson() {
		$(document).on('click', '#updatePerson', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			e.preventDefault();
			console.log(e);
			var formData = $('#updateStatus').serialize();
			$.ajax({
				type: 'post',
				url: 'ajax/manuallyUpdatePerson.php',
				data: formData,
				success: function (response) {
					var resultObj = JSON.parse(response);
					console.log(resultObj);

					$('.spinning').removeClass('spinning').attr('disabled', false);

					var message = resultObj.success ? "<br />Status Update Successful" : "<br />Status Update Failed";
					message += resultObj.emailNotification != 'suppress' ? "<br />Email Notification was Enabled" : "<br />Email Notification was Suppressed";
					message += resultObj.success && resultObj.emailNotification != 'suppress' ? "<br />" + resultObj.Notification : '';
					message += "<br />" + resultObj.Messages;
					$('#updateReport').html(message);
					$('#showUpdateResultModal').modal('show');
				}
			});
		});
	}
}

const ManualPESStatusUpdate = new manualPESStatusUpdate();

export { ManualPESStatusUpdate as default };