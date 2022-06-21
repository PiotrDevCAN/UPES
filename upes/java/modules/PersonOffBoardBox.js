/**
 *
 */

class PersonOffBoardBox {

    table;

    constructor() {
        console.log('+++ Function +++ PersonOffBoardBox.constructor');

        this.listenForToggleBoarded();

        console.log('--- Function --- PersonOffBoardBox.constructor');
    }

    joinDataTable(DataTable) {
        this.table = DataTable;
    }

    listenForToggleBoarded() {
        var $this = this; 
        $(document).on('click', '.toggleBoarded', function (e) {
            $(this).addClass('spinning').attr('disabled', false);
            var upesref = $(this).data('upesref');
            var accountid = $(this).data('accountid');
            var boarded = $(this).data('boarded');
            $.ajax({
                type: 'post',
                url: '/ajax/toggleBoardedStatus.php',
                data: {
                    upesRef: upesref,
                    accountId: accountid,
                    boarded: boarded
                },
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    if (responseObj.success) {
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                        $this.table.ajax.reload();
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
}

const personOffBoardBox = new PersonOffBoardBox();

export { personOffBoardBox as default };