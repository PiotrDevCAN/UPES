/**
 *
 */

class PersonEditBox {

	table;

	constructor() {
		console.log('+++ Function +++ PersonEditBox.constructor');

		this.listenForPersonFormSubmit();
		this.listenForEditPerson();
		this.listenForEditPersonRecordModalShown();

		console.log('--- Function --- PersonEditBox.constructor');
	}

    joinDataTable(DataTable) {
        this.table = DataTable;
    }

	listenForPersonFormSubmit() {
		var $this = this;
		$(document).on('submit', '#personForm', function (e) {
			console.log(e);
			e.preventDefault();

			var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
			var url = 'ajax/savePersonRecord.php';

			var disabledFields = $(':disabled');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#personForm").serialize();
			$(disabledFields).attr('disabled', true);

			$.ajax({
				type: 'post',
				url: url,
				data: formData,
				context: document.body,
				success: function (response) {
					var responseObj = JSON.parse(response);
					if (responseObj.success) {
						$(submitBtn).removeClass('spinning').attr('disabled', false);
						$('#personForm').trigger("reset");
						$('#CNUM').val('');
						$('#EMAIL_ADDRESS').val('');
						$('#EMAIL_ADDRESS').css('background-color', 'White').trigger('change');
						$('#FULL_NAME').val('');
						$('#COUNTRY').val('').trigger('change');
						$('#IBM_STATUS').val('').trigger('change');
						$('#UPES_REF').val('');
						$('#modalEditPersonRecord').modal('hide');
						$this.table.ajax.reload();
					} else {
						$(submitBtn).removeClass('spinning').attr('disabled', false);
						$('#personForm').trigger("reset");
						$('.modal-body').html(responseObj.Messages);
						$('.modal-body').addClass('bg-danger');
						$('#errorMessageModal').modal('show');
					}
				},
				always: function () {
					console.log('--- saved resource request ---');
				}
			});
		});
	}

	listenForEditPerson() {
		$(document).on('click', '.editPerson', function (e) {
			$(this).addClass('spinning').attr('disabled', true);
			var upesRef = $(this).data('upesref');
			console.log(upesRef);
			$.ajax({
				type: 'post',
				url: '/ajax/getEditPersonForm.php',
				data: { upesRef: upesRef },
				success: function (response) {
					var responseObj = JSON.parse(response);
					if (responseObj.success) {
						$('.spinning').removeClass('spinning').attr('disabled', false);
						$('#editPersonRecordModalBody').html(responseObj.form);
						$('#modalEditPersonRecord').modal('show');
						$('#saveCountry').val(responseObj.country);
						$('#saveStatus').val(responseObj.status);
						$('#saveCnum').val(responseObj.cnum);
						$('#saveUpesref').val(upesRef);
					} else {
						$('.spinning').removeClass('spinning').attr('disabled', false);
						$('#errorMessageBody').html(responseObj.Messages);
						$('#errorMessageBody').addClass('bg-danger');
						$('#errorMessageModal').modal('show');
					}
				}
			});
		});
	}

	listenForEditPersonRecordModalShown() {
		$(document).on('shown.bs.modal','#modalEditPersonRecord',function(e){
			$('#COUNTRY').select2({
				placeholder: 'Select Country',
				width: '100%',
				data: countryCodes,
				dataType: 'json'
			});
			$('#COUNTRY').val($('#saveCountry').val()).trigger('change');

			$('#IBM_STATUS').select2({
				placeholder: 'Select Status',
				width: '100%'
			});
			$('#IBM_STATUS').val($('#saveStatus').val()).trigger('change');
			if (isPesTeam != '' || isCdi != '') {
				$('#FULL_NAME').attr('disabled', false);
			} else {
				$('#FULL_NAME').attr('title', 'Only PES Team can edit a person\'s name');
			}
		});
	}
}

const personEditBox = new PersonEditBox();

export { personEditBox as default };