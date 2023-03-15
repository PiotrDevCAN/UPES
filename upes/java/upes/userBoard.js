/**
 *
 */

import changePesLevels from '../modules/changePesLevels.js';

class userBoard {

    constructor() {
        console.log('+++ Function +++ userBoard.constructor');

        this.listenForPersonFormSubmit();
        this.listenForKeyUpPesRequestor();
        this.listenForContractIdChange();
        this.listenForUpesRefChange();

        $('#CONTRACT_ID').select2({
            placeholder: 'Select Contract',
            width: '100%',
            ajax: {
                url: 'ajax/prepareContractsDropdown.php',
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    var upesref = $('#UPES_REF').val();
                    var query = {
                        search: params.term,
                        upesref: upesref
                    }
                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            }
        });

        $('#UPES_REF').on('select2:open', function () {
            console.log('select2:open');
            $('#CONTRACT_ID').select2('data', null);
            $('#CONTRACT_ID').val('').trigger('change');
        });

        $('#PES_LEVEL').select2({
            width: '100%'
        });

        $('#UPES_REF').select2({
            width: '100%',
            placeholder: 'Select Email'
        });

        $('#COUNTRY_OF_RESIDENCE').select2({
            width: '100%',
            placeholder: 'Country of Residence'
        });

        console.log('--- Function --- userBoard.constructor');
    }

    listenForPersonFormSubmit() {
        $('#accountPersonForm').submit(function (e) {
            console.log(e);
            e.preventDefault();

            var submitBtn = $(e.target).find('input[name="Submit"]').addClass('spinning');
            var url = 'ajax/boardPersonToAccount.php';

            var disabledFields = $(':disabled');
            $(disabledFields).removeAttr('disabled');
            var formData = $("#accountPersonForm").serialize();
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
                        $('#accountPersonForm').trigger("reset");
                        $("#CONTRACT_ID").trigger("change");
                        $("#UPES_REF").trigger("change");
                        $('#COUNTRY_OF_RESIDENCE').val('').trigger('change');
                        $('#ACCOUNT_ID').val('');
                        $("#PES_LEVEL").select2("destroy");
                        $("#PES_LEVEL").html("<option><option>");
                        $('#PES_LEVEL').select2({ width: '100%' })
                            .attr('disabled', false)
                            .attr('required', true);
                        $('.modal-body').html(responseObj.Messages);
                        $('.modal-body').addClass('bg-success').removeClass('bg-danger');
                        $('.modal-title').html('Response Message');
                        $('#errorMessageModal').modal('show');
                    } else {
                        $(submitBtn).removeClass('spinning').attr('disabled', false);
                        $('#accountPersonForm').trigger("reset");
                        $("#CONTRACT_ID").trigger("change");
                        $("#UPES_REF").trigger("change");
                        $('#COUNTRY_OF_RESIDENCE').val('').trigger('change');
                        $('#ACCOUNT_ID').val('');
                        $('.modal-body').html(responseObj.Messages);
                        $('.modal-body').addClass('bg-danger').removeClass('bg-success');
                        $('.modal-title').html('Error Message');
                        $('#errorMessageModal').modal('show');
                    }
                },
                always: function () {
                    console.log('--- saved resource request ---');
                }
            });
        });
    }

    listenForKeyUpPesRequestor() {
        $(document).on('keyup change', '#PES_REQUESTOR', function (e) {
            var emailAddress = $('#PES_REQUESTOR').val();
            var emailReg = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2, 4}$/;
            var emailIBMReg = /(ibm.com)/i;
            var emailOceanReg = /(ocean.ibm.com)/i;

            console.log(emailAddress);

            if (emailAddress == '') {
                $('#PES_REQUESTOR').css('background-color', 'inherit');
            } else {
                if (emailReg.test(emailAddress)) {
                    $('#PES_REQUESTOR').css('background-color', 'LightGreen');
                } else {
                    $('#PES_REQUESTOR').css('background-color', 'LightPink');
                };
            }
        });
    }

    listenForContractIdChange() {
        $(document).on('change', '#CONTRACT_ID', function (e) {
            console.log(e);
            var contractId = $('#CONTRACT_ID').val();
            console.log(contractId);

            $('#ACCOUNT_ID').val(accountContractLookup[contractId]);
            $("#PES_LEVEL").select2("destroy");
            $("#PES_LEVEL").html("<option><option>");
            changePesLevels(pesLevelByAccount[accountContractLookup[contractId]]);
        });
    }

    listenForUpesRefChange() {
        $(document).on('change', '#UPES_REF', function (e) {
            var upesRef = $('#UPES_REF').val();
            var fullName = upesrefToNameMapping[upesRef];
            $('#FULL_NAME').val(fullName);
            if ($('#UPES_REF').val() != '') {
                $('#CONTRACT_ID').attr('disabled', false).trigger('change');
            } else {
                $('#CONTRACT_ID').attr('disabled', true).trigger('change');
            }
        });
    }
}

const UserBoard = new userBoard();

export { UserBoard as default };