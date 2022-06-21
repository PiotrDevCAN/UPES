/**
 *
 */

class PersonDataUpdate {

	constructor() {
		console.log('+++ Function +++ PersonDataUpdate.constructor');

		let dropArea = document.getElementById('drop-area');

		['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
			dropArea.addEventListener(eventName, preventDefaults, false);
		});

		function preventDefaults(e) {
			e.preventDefault()
			e.stopPropagation()
		}

		['dragenter', 'dragover'].forEach(eventName => {
			dropArea.addEventListener(eventName, highlight, false);
		});

		['dragleave', 'drop'].forEach(eventName => {
			dropArea.addEventListener(eventName, unhighlight, false);
		});

		function highlight(e) {
			dropArea.classList.add('highlight');
		}

		function unhighlight(e) {
			dropArea.classList.remove('highlight');
		}

		dropArea.addEventListener('drop', handleDrop, false);

		function handleDrop(e) {
			let dt = e.dataTransfer;
			let files = dt.files;

			handleFiles(files);
		}

		function handleFiles(files) {
			([...files]).forEach(uploadFile);
		}

		function uploadFile(file) {
			var url = 'ajax/upload.php';
			var xhr = new XMLHttpRequest();
			var formData = new FormData();
			xhr.open('POST', url, true);
			xhr.addEventListener('readystatechange', function (e) {
				if (xhr.readyState == 4 && xhr.status == 200) {
					// Done. Inform the user
					var responseText = xhr.responseText;
					if (responseText.indexOf('has been uploaded') != -1) {
						responseText += "<br/>Upload to DB2 starting.<br/>This can take several minutes (load runs at circa 4 Rows/Sec)<br/>";
						responseText += "<i class='fa fa-spinner fa-spin' style='font-size:24px'></i>";
						$('#drop-area').html(responseText);
						var filename = file.name;
						console.log(filename);
						$.ajax({
							url: "ajax/updatePersonFromXlsx.php",
							type: 'POST',
							data: { filename: filename },
							success: function (result) {
								console.log(result);
								$('#drop-area').html(result);
							}
						});
					} else {
						$('#drop-area').html(responseText);
					}
				}
				else if (xhr.readyState == 4 && xhr.status != 200) {
					// Error. Inform the user
					var responseText = xhr.responseText;
					responseText += "<br/>Error has occured, inform support";
					$('#drop-area').html(responseText);
				}
			});

			formData.append('file', file);
			xhr.send(formData);
		}

		console.log('--- Function --- PersonDataUpdate.constructor');
	}
}