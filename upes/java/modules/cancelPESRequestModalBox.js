/*
 *
 *
 *
 */

class cancelPESRequestModalBox {

    table;

    constructor() {
        console.log('+++ Function +++ cancelPESRequestModalBox.constructor');

        this.listenForCancelPesRequest();
        this.listenForCancelPesRequestConfirmed();

        console.log('--- Function --- cancelPESRequestModalBox.constructor');
    }

    joinDataTable(DataTable) {
        this.table = DataTable;
    }

    listenForCancelPesRequest() {
        $(document).on('click', '.cancelPesRequest', function () {
            $(this).addClass('spinning').attr('disabled', true);
            var upesRef = $(this).data('upesref');
            console.log(upesRef);
            console.log($(this).data());

            $('#cancelEMAIL_ADDRESS').val($(this).data('email'));
            $('#cancelFULL_NAME').val($(this).data('name'));
            $('#cancelupesref').val($(this).data('upesref'));
            $('#cancelACCOUNT').val($(this).data('account'));
            $('#cancelACCOUNT_ID').val($(this).data('accountid'));
            $('#modalCancelPesRequestConfirm').modal('show');

            $('.spinning').removeClass('spinning').attr('disabled', false);
        });
    }

    listenForCancelPesRequestConfirmed() {
        var $this = this;
        $(document).on('click', '.cancelPesRequestConfirmed', function () {
            $(this).addClass('spinning').attr('disabled', false);
            console.log('here');
            var upesref = $('#cancelupesref').val();
            var accountid = $('#cancelACCOUNT_ID').val();
            $.ajax({
                type: 'post',
                url: '/ajax/cancelPesRequest.php',
                data: {
                    upesref: upesref,
                    accountid: accountid
                },
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    if (responseObj.success) {
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                        $('#cancelEMAIL_ADDRESS').val('');
                        $('#cancelFULL_NAME').val('');
                        $('#cancelupesref').val('');
                        $('#cancelACCOUNT').val('');
                        $('#cancelACCOUNT_ID').val('');
                        $('#modalCancelPesRequestConfirm').modal('hide');
                        $this.table.ajax.reload();
                    } else {
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                        $('#errorMessageBody').html(responseObj.Messages);
                        $('#errorMessageBody').addClass('bg-danger');
                        $('#errorMessageModal').modal('show');
                    }
                },
            });
        });
    }
}

const cancelPesRequestModalBox = new cancelPESRequestModalBox();

export { cancelPesRequestModalBox as default };