/**
 *
 */

class managePESLevels {

	pesLevelTable;

	constructor() {
		console.log('+++ Function +++ managePESLevels.constructor');

		this.populateDataTable();
		this.listenForPesLevelFormSubmit();
		this.listenForDeletePesLevel();
		this.listenForEditPesLevel();
		this.listenForConfirmPesLevelDelete();

		$('#ACCOUNT_ID').select2({
			placeholder: 'Select Account',
			width: '100%'
		});

		console.log('--- Function --- managePESLevels.constructor');
	}

	populateDataTable() {
		this.pesLevelTable = $('#pesLevelTable').DataTable({
			ajax: {
				url: 'ajax/populatePesLevelsTable.php',
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
				[{
					data: "ACTION"
				}, {
					data: "ACCOUNT"
				}, {
					data: "PES_LEVEL"
				}, {
					data: "PES_LEVEL_DESCRIPTION"
				}, {
					data: "RECHECK_YEARS"
				}]
		});
	}

	listenForPesLevelFormSubmit() {
		var $this = this;
		$(document).on('submit', '#pesLevelForm', function (e) {
			console.log(e);
			e.preventDefault();

			var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
			var url = 'ajax/savePesLevelRecord.php';

			var disabledFields = $(':disabled');
			$(disabledFields).removeAttr('disabled');
			var formData = $("#pesLevelForm").serialize();
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
						$('#pesLevelForm').trigger("reset");
						$('#ACCOUNT_ID').trigger('change');
					} else {
						$(submitBtn).removeClass('spinning').attr('disabled', false);
						$('#pesLevelForm').trigger("reset");
						$('.modal-body').html(responseObj.Messages);
						$('.modal-body').addClass('bg-danger');
						$('#errorMessageModal').modal('show');
					}
					$this.pesLevelTable.ajax.reload();
				},
				always: function () {
					console.log('--- saved resource request ---');
				}
			});
		});
	}

	listenForDeletePesLevel() {
		$(document).on('click', '.deletePesLevel', function (e) {
			console.log(e);
			$('#confirmDeletePesLevel').html($(e.target).data('peslevel'));
			$('#confirmDeletePesLevelRef').val($(e.target).data('peslevelref'));
			$('#confirmDeletePesLevelAccount').html($(e.target).data('peslevelaccount'));
			$('#modalDeletePesLevelConfirm').modal('show');
		});
	}

	listenForEditPesLevel() {
		$(document).on('click', '.editPesLevel', function (e) {
			var button = $(e.target).parent('button').addClass('spinning');
			$('#PES_LEVEL').val($(e.target).data('peslevel'));
			$('#PES_LEVEL_REF').val($(e.target).data('peslevelref'));
			$('#ACCOUNT_ID').val($(e.target).data('peslevelaccountid')).trigger('change');
			$('#PES_LEVEL_DESCRIPTION').val($(e.target).data('pesleveldescription'));
			$('#RECHECK_YEARS').val($(e.target).data('recheckyears'));
			$('#mode').val('edit');
			$(button).removeClass('spinning');
		});
	}

	listenForConfirmPesLevelDelete() {
		var $this = this;
		$(document).on('click', '.confirmPesLevelDelete', function (e) {
			var peslevelref = $('#confirmDeletePesLevelRef').val();
			console.log(peslevelref);

			var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
			var url = 'ajax/deletePesLevelRecord.php';

			$.ajax({
				type: 'post',
				url: url,
				data: {
					peslevelref: peslevelref
				},
				context: document.body,
				success: function (response) {
					var responseObj = JSON.parse(response);
					if (!responseObj.success) {
						$('#pesLevelForm').trigger("reset");
						$('#errorMessageBody').html(responseObj.Messages);
						$('#errorMessageBody').addClass('bg-danger');
						$('#errorMessageModal').modal('show');
					}
					$this.pesLevelTable.ajax.reload();
				},
				always: function () {
					console.log('--- saved resource request ---');
				}
			});
		});
	}
}

const ManagePESLevels = new managePESLevels();

export { ManagePESLevels as default };