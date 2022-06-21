/**
 *
 */

import changePesLevels from '../modules/changePesLevels.js';
import checkIfEmailKnown from '../modules/checkIfEmailKnown.js';

class userAdd {

    constructor() {
        console.log('+++ Function +++ userAdd.constructor');

        this.listenForPersonFormSubmit();
        this.listenForTypeaheadSelect();
        this.listenForKeyUpEmailAddress();

        $('#COUNTRY').select2({
            placeholder: 'Select Country',
            width: '100%',
            data: countryCodes,
            dataType: 'json'
        });

        $('#PES_LEVEL').select2({
            width: '100%'
        });

        $('#IBM_STATUS').select2({
            placeholder: 'Select Status',
            width: '100%'
        });

        console.log('--- Function --- userAdd.constructor');
    }

    listenForPersonFormSubmit() {
        $(document).on('submit', '#personForm', function (e) {
            var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning').attr('disabled', true);
            console.log(e);
            e.preventDefault();

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
                    $(submitBtn).removeClass('spinning').attr('disabled', false);
                    $('#personForm').trigger("reset");
                    $('#CNUM').val('');
                    $('#EMAIL_ADDRESS').val('');
                    $('#EMAIL_ADDRESS').css('background-color', 'White').trigger('change');
                    $('#FULL_NAME').val('');
                    $('#COUNTRY').val('').trigger('change');
                    $('#IBM_STATUS').val('').trigger('change');
                    $('#UPES_REF').val('');
                    if (responseObj.success) {
                        $('.modal-title').html('Message');
                        $('.modal-body').html('<p>You have now ADDED a new IBMer to uPES<br/>The next step in getting them PES Cleared is to request they be "Boarded" to the appropriatge Account.<br/>You do this using the "Board to Contract" menu option, under the "Upes" drop down above.</p>');
                        $('.modal-body').addClass('bg-success');
                    } else {
                        $('.modal-body').html(responseObj.Messages);
                        $('.modal-body').addClass('bg-danger');
                    }
                    $('#errorMessageModal').modal('show');
                },
                always: function () {
                    console.log('--- saved resource request ---');
                }
            });
        });
    }

    listenForTypeaheadSelect() {
        $('.typeahead').bind('typeahead:select', function (ev, suggestion) {
            console.log(suggestion);
            $('.tt-menu').hide();
            $('#CNUM').val(suggestion.cnum).attr('disabled', 'disabled');
            $('#EMAIL_ADDRESS').val(suggestion.mail).attr('disabled', true).css('background-color', 'lightgreen');
            $('#FULL_NAME').val(suggestion.value);
            $('#COUNTRY').val(suggestion.country).trigger('change');
            checkIfEmailKnown();
        });
    }

    listenForKeyUpEmailAddress() {
        $(document).on('keyup change', '#EMAIL_ADDRESS', function (e) {
            var emailAddress = $('#EMAIL_ADDRESS').val();
            var emailReg = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            var emailIBMReg = /(ibm.com)/i;

            console.log(emailAddress);

            if (emailAddress == '') {
                $('#ibmer').val('').attr('disabled', false);
                $('#EMAIL_ADDRESS').css('background-color', 'inherit');
            } else {
                $('#ibmer').val('Not an IBMer').attr('disabled', true);
                $('#CNUM').val('');

                if (emailReg.test(emailAddress)) {
                    $('#EMAIL_ADDRESS').css('background-color', 'LightGreen');
                    checkIfEmailKnown();
                } else {
                    $('#EMAIL_ADDRESS').css('background-color', 'LightPink');
                }

                if (emailIBMReg.test(emailAddress)) {
                    $('#ibmer').val('Email address implies IBMer').attr('disabled', true);
                    $('#EMAIL_ADDRESS').css('background-color', 'LightPink');
                    $('.modal-body').html('<p>You entered an IBM Email Address</p><p>If you are boarding an IBMer<br/> please leave the <b>EMAIL_ADDRESS</b> field blank and use the <b>IBMer</b> field to find their bluepages entry. This will auto-complete the <b>EMAIL_ADDRESS</b> field</p>');
                    $('.modal-body').addClass('bg-danger');
                    $('#errorMessageModal').modal('show');
                    $('#ibmer').val('').attr('disabled', false);
                    $('#EMAIL_ADDRESS').val('');
                    $('#EMAIL_ADDRESS').css('background-color', 'inherit');
                }
            }
        });
    }
}

const UserAdd = new userAdd();

export { UserAdd as default };