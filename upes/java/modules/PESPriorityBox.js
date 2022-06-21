/*
 *
 *
 *
 */

class PESPriorityBox {

	constructor() {
		console.log('+++ Function +++ PESPriorityBox.constructor');

		this.listenForPesPriorityChange();

		console.log('--- Function --- PESPriorityBox.constructor');
	}

	setAlertClassForPesPriority(priorityField, priority) {

		$(priorityField).removeClass('alert-success');
		$(priorityField).removeClass('alert-warning');
		$(priorityField).removeClass('alert-danger');
		$(priorityField).removeClass('alert-info');

		switch (priority) {
			case 1:
				$(priorityField).addClass('alert-danger');
				break;
			case 2:
				$(priorityField).addClass('alert-warning');
				break;
			case 3:
				$(priorityField).addClass('alert-success');
				break;
			default:
				$(priorityField).addClass('alert-info');
				break;
		}
	}

    listenForPesPriorityChange() {
		var $this = this;
		$(document).on('click', '.btnPesPriority', function () {
			var buttonObj = $(this);
			var pespriority = $(this).data('pespriority');
			var upesref = $(this).data('upesref');
			var accountid = $(this).data('accountid');
			$(this).addClass('spinning');
			$.ajax({
				url: "ajax/savePesPriority.php",
				type: 'POST',
				data: {
					upesref: upesref,
					accountid: accountid,
					pespriority: pespriority,
				},
				success: function (result) {
					var resultObj = JSON.parse(result);
					if (resultObj.success == true) {
						buttonObj.parent('span').siblings('div.priorityDiv:first').html("Priority:" + pespriority);
						$this.setAlertClassForPesPriority(buttonObj.parent('span').siblings('div.priorityDiv:first'), pespriority);
						buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
					}
					$(buttonObj).removeClass('spinning');
				}
			});
		});
	}
}

const PesPriorityBox = new PESPriorityBox();

export { PesPriorityBox as default };