/**
 *
 */

import sleep from "../functions/sleep.js";
import box from "./box.js";

class PersonEditBox extends box {

	countryCodes;
	saveCountry;
	saveStatus;

	constructor(parent) {
		console.log('+++ Function +++ PersonEditBox.constructor');

		super(parent);
		
		this.countryCodes = countryCodes;

		this.listenForPersonFormSubmit();
		this.listenForEditPerson();
		this.listenForEditPersonRecordModalShown();
		this.listenForEditPersonRecordModalHidden();

		console.log('--- Function --- PersonEditBox.constructor');
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
		var $this = this;
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
						$this.saveCountry = responseObj.country;
						$this.saveStatus = responseObj.status;
						$('#modalEditPersonRecord').modal('show');
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
		var $this = this;
		$(document).on('shown.bs.modal', '#modalEditPersonRecord', function (e) {
			var promise = sleep(10);
			promise.then(function (result) {
				$('#COUNTRY').select2({
					placeholder: 'Select Country',
					width: '100%',
					data: $this.countryCodes,
					dataType: 'json'
				});
				$('#COUNTRY').val($this.saveCountry).trigger('change');

				$('#IBM_STATUS').select2({
					placeholder: 'Select Status',
					width: '100%'
				});
				$('#IBM_STATUS').val($this.saveStatus).trigger('change');

				if (isPesTeam != '' || isCdi != '') {
					$('#FULL_NAME').attr('disabled', false);
				} else {
					$('#FULL_NAME').attr('title', 'Only PES Team can edit a person\'s name');
				}
			});
		});
	}

	listenForEditPersonRecordModalHidden() {
		$(document).on('hidden.bs.modal', '#modalEditPersonRecord', function (e) {
			if ($("#COUNTRY").data("select2")) {
                $("#COUNTRY").select2("destroy");
            }
			if ($("#IBM_STATUS").data("select2")) {
                $("#IBM_STATUS").select2("destroy");
            }
		});
	}
}

export { PersonEditBox as default };