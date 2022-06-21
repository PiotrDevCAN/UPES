/*
 *
 *
 *
 */

class stageValueChangeBox {

	constructor() {
		console.log('+++ Function +++ stageValueChangeBox.constructor');

		this.listenForPesStageValueChange();

		console.log('--- Function --- stageValueChangeBox.constructor');
	}

    getAlertClassForPesStage(pesStageValue) {
		var alertClass = '';
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

    listenForPesStageValueChange() {
		var $this = this;
		$(document).on('click', '.btnPesStageValueChange', function () {
			var personDetails = $(this).parent('span');
			var setPesTo = $(this).data('setpesto');
			var column = $(this).parents('.columnDetails').first().data('pescolumn');
			var upesref = $(personDetails).data('upesref');
			var accountid = $(personDetails).data('accountid');
			var alertClass = $this.getAlertClassForPesStage(setPesTo);

			$(this).parents('div').prev('div.pesStageDisplay').html(setPesTo);
			$(this).parents('div').prev('div.pesStageDisplay').removeClass('alert-info').removeClass('alert-warning').removeClass('alert-success').addClass(alertClass);
			$(this).addClass('spinning');

			var buttonObj = $(this);

			$.ajax({
				url: "ajax/savePesStageValue.php",
				type: 'POST',
				data: {
					upesref: upesref,
					stageValue: setPesTo,
					accountid: accountid,
					stage: column,
				},
				success: function (result) {
					var resultObj = JSON.parse(result);
					if (resultObj.success == true) {
						buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
					} else {
						$(this).parents('div').prev('div.pesStageDisplay').html(resultObj.message);
					}
					buttonObj.removeClass('spinning');
				}
			});
		});
	}
}

const StageValueChangeBox = new stageValueChangeBox();

export { StageValueChangeBox as default };