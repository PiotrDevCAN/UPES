/*
 *
 *
 *
 */

class extractTrackerBox {

	constructor() {
		console.log('+++ Function +++ extractTrackerBox.constructor');

		this.listenForExtract();

		console.log('--- Function --- extractTrackerBox.constructor');
	}

	listenForExtract() {
		$(document).on('click', '.trackerExtract', function (e) {
			e.preventDefault();
			var type = $(this).data('trackertype');
			$.ajax({
				url: "ajax/downloadTracker.php",
				type: 'POST',
				data: {
					type: type
				},
				success: function (result) {
					var resultObj = JSON.parse(result);
					if (resultObj.success == true) {
						$('#messageModalBody').html(resultObj.messages);
						$('#messageModal').modal('show');
					} else {
						$('#errorMessageBody').html(resultObj.messages);
						$('#errorMessageModal').modal('show');
					}
				}
			});
		});
	}
}

const ExtractTrackerBox = new extractTrackerBox();

export { ExtractTrackerBox as default };