/**
 *
 */

import buttonCommon from '../modules/buttonCommon.js';

class mailConvert {

    Addresses;

    constructor() {
        console.log('+++ Function +++ mailConvert.constructor');

        this.populateDataTable();
        this.listenForConvertMail();

        console.log('--- Function --- mailConvert.constructor');
    }

    validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1, 3}\.[0-9]{1, 3}\.[0-9]{1, 3}\.[0-9]{1, 3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    validateNotesId(notesid) {
        var re = /^([a-z0-9 ]+\/[a-z]*(\/ibm|\/contr\/ibm))$/;
        return re.test(String(notesid).toLowerCase());
    }

    populateDataTable() {
        this.Addresses = $('#Addresses').DataTable({
            order: [[0, "asc"], [1, "asc"]],
            processing: true,
            responsive: true,
            dom: 'Blfrtip',
            buttons: [
                $.extend(true, {}, buttonCommon, {
                    extend: 'excelHtml5',
                    exportOptions: {
                        orthogonal: 'sort',
                        stripHtml: true,
                        stripNewLines: false
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                    }
                }),
                $.extend(true, {}, buttonCommon, {
                    extend: 'csvHtml5',
                    exportOptions: {
                        orthogonal: 'sort',
                        stripHtml: true,
                        stripNewLines: false
                    }
                })
            ],
        });
    }

    listenForConvertMail() {
        var $this = this;
        $(document).on('click', '#convertMail', function (e) {
            e.preventDefault();
            var validatedEmail = [];
            var emailAddresses = $('#CONTACTS').html();
            var emailAddressesH = $('#CONTACTS').html();
            var emailAddressesT = $('#CONTACTS').text();
            var emailArray = emailAddresses.replace(/<\/div><div>/gm, '\n').replace(/\"/gm, ' ').replace(/<[^>]*>?/gm, '').split(/[,;\n]/g);
            emailArray = emailArray.filter(Boolean);
            console.log(emailArray);

            var arrayLength = emailArray.length;
            $('#emailsToSave').val(arrayLength);
            $('#emailsSaved').val(0);
            for (var i = 0; i < arrayLength; i++) {
                console.log('loop starts');
                console.log(emailArray[i].trim());
                //Do something
                console.log($this.validateNotesId(emailArray[i].trim()));
                if ($this.validateNotesId(emailArray[i].trim())) {
                    validatedEmail.push(emailArray[i].trim());
                } else {
                    var email = emailArray[i].replace(/[\(\)]/g, '').trim();
                    console.log(email);
                    var regex = new RegExp(email);
                    $('#CONTACTS').html($('#CONTACTS').html().replace(regex, "<span style='color:red;text-decoration:line-through;'>" + email + "</span>"));
                }
            }
            console.log(validatedEmail);
            $.ajax({
                url: "ajax/convertMail.php",
                type: 'POST',
                data: { emailaddresses: validatedEmail },
                success: function (result) {
                    resultObj = JSON.parse(result);
                    var table = $('#Addresses').DataTable();
                    table.clear();
                    for (var notesid in resultObj.converted) {
                        var email = resultObj.converted[notesid]
                        table.row.add([
                            email,
                            notesid,
                        ]).draw(false);
                    }
                }
            });
        });
    }
}

const MailConvert = new mailConvert();

export { MailConvert as default };