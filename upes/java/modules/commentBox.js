/*
 *
 *
 *
 */

class commentBox {

	constructor() {
		console.log('+++ Function +++ commentBox.constructor');

		this.listenForSavePesComment();
		this.listenForComment();

		console.log('--- Function --- commentBox.constructor');
	}

	listenForSavePesComment() {
		$(document).on('click', '.btnPesSaveComment', function () {

			var upesref = $(this).siblings('textarea').data('upesref');
			var accountid = $(this).siblings('textarea').data('accountid');
			var comment = $(this).siblings('textarea').val();
			var button = $(this);

			button.addClass('spinning');
			$.ajax({
				url: "ajax/savePesComment.php",
				type: 'POST',
				data: {
					upesref: upesref,
					accountid: accountid,
					comment: comment,
				},
				success: function (result) {
					var resultObj = JSON.parse(result);
					button.removeClass('spinning');
					button.siblings('div.pesComments').html(resultObj.comment);
					button.siblings('textarea').val('');
				}
			});
		});
	}

	listenForComment() {
		$('textarea').on('input', function () {
		});
	}
}

const CommentBox = new commentBox();

export { CommentBox as default };