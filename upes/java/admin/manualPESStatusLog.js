/**
 *
 */

import buttonCommon from '../modules/buttonCommon.js';

class manualPESStatusLog {

    pesStatusChangeTable;

    constructor() {
        console.log('+++ Function +++ manualPESStatusLog.constructor');

        this.populateDataTable();

        console.log('--- Function --- manualPESStatusLog.constructor');
    }

    populateDataTable() {
        // Setup - add a text input to each footer cell
        $('#pesStatusChangeTable tfoot th').each(function () {
            var title = $(this).text();
            var titleCondensed = title.replace(' ', '');
            $(this).html('<input type="text" id="footer' + titleCondensed + '" placeholder="Search ' + title + '" size="5" />');
        });

        this.pesStatusChangeTable = $('#pesStatusChangeTable').DataTable({
            ajax: {
                url: 'ajax/populatePesStatusChangeTable.php',
            },
            autoWidth: true,
            processing: true,
            responsive: true,
            dom: 'Blfrtip',
            buttons: [
                'colvis',
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
                }),
                $.extend(true, {}, buttonCommon, {
                    extend: 'print',
                    exportOptions: {
                        orthogonal: 'sort',
                        stripHtml: true,
                        stripNewLines: false
                    }
                })
            ],
            columns: [{
                data: "CNUM"
            }, {
                data: "EMAIL_ADDRESS",
            }, {
                data: "ACCOUNT",
            }, {
                data: "PES_STATUS",
            }, {
                data: "PES_CLEARED_DATE",
            }, {
                data: "UPDATER"
            }, {
                data: "UPDATED"
            }],
        });

        // Apply the search
        this.pesStatusChangeTable.columns().every(function () {
            var that = this;
            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that
                        .search(this.value)
                        .draw();
                }
            });
        });
    }
}

const ManualPESStatusLog = new manualPESStatusLog();

export { ManualPESStatusLog as default };