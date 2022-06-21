/**
 *
 */

class manageCountries {

	countryTable;

	constructor() {
		console.log('+++ Function +++ manageCountries.constructor');

		this.populateDataTable();
		this.listenForCountryFormSubmit();
		this.listenForEditCountry();
		this.listenForKeyUpCountry();

		$('#EMAIL_BODY_NAME').select2({
			width: '100%',
			placeholder: "Select email body"
		});
		$('#ADDITIONAL_APPLICATION_FORM').select2({
			width: '100%',
			placeholder: "Select additional application form"
		});

		console.log('--- Function --- manageCountries.constructor');
	}

	populateDataTable() {
		this.countryTable = $('#countryTable').DataTable({
			ajax: {
				url: 'ajax/populateCountryTable.php',
			},
			autoWidth: true,
			processing: true,
			responsive: true,
			dom: 'Blfrtip',
			buttons: [
				'csvHtml5',
				'excelHtml5',
				'print'
			],
			columns:
				[{ data: "COUNTRY", render: { _: 'display', sort: 'sort' } }
					, { data: "EMAIL_BODY_NAME" }
					, { data: "ADDITIONAL_APPLICATION_FORM" }
				]
		});
	}

	listenForCountryFormSubmit() {
		var $this = this;
		$(document).on('submit', '#countryForm', function (e) {
			console.log(e);
			e.preventDefault();

			var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
			var url = 'ajax/saveCountryRecord.php';

			var disabledFields = $(':disabled');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#countryForm").serialize();
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
						$('#countryForm').trigger("reset");
					} else {
						$(submitBtn).removeClass('spinning').attr('disabled', false);
						$('#countryForm').trigger("reset");
						$('.modal-body').html(responseObj.Messages);
						$('.modal-body').addClass('bg-danger');
						$('#errorMessageModal').modal('show');
					}
					$('#COUNTRY').css("background-color", "white");
					$this.countryTable.ajax.reload();
				},
				always: function () {
					console.log('--- saved resource request ---');
				}
			});
		});
	}

	listenForEditCountry() {
		$(document).on('click', '.editCountry', function (e) {
			var button = $(e.target).parent('button').addClass('spinning');
			$('#COUNTRY').val($(e.target).data('country')).attr('disabled', true);
			$('#EMAIL_BODY_NAME').val($(e.target).data('emailbodyname')).trigger('change');
			$('#ADDITIONAL_APPLICATION_FORM').val($(e.target).data('additionaldocs')).trigger('change');
			$('#mode').val('edit');
			$('.spinning').removeClass('spinning');
		});
	}

	listenForKeyUpCountry() {
		$(document).on('keyup', '#COUNTRY', function (e) {
			var newCountry = $(this).val().trim().toLowerCase();
			var allreadyExists = ($.inArray(newCountry, country) >= 0);
			if (allreadyExists) { // comes back with Position in array(true) or false is it's NOT in the array.
				$('#Submit').attr('disabled', true);
				$(this).css("background-color", "LightPink");
				alert('Account already defined');
			} else {
				$(this).css("background-color", "LightGreen");
				$('#Submit').attr('disabled', false);
			};
		});
	}
}

const ManageCountries = new manageCountries();

export { ManageCountries as default };