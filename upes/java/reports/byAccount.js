/**
 *
 */

import buttonCommon from '../modules/buttonCommon.js';

class byAccount {

    table;

    constructor() {
        console.log('+++ Function +++ byAccount.constructor');

        this.populateDataTable();

        console.log('+++ Function +++ byAccount.constructor');
    }

    populateDataTable() {
        this.table = $('#statusByAccountReport').DataTable({
            autoWidth: false,
            deferRender: true,
            processing: true,
            responsive: true,
            colReorder: true,
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
                    filename: 'upes status by account',
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
                    },
                    filename: 'upes status by account',
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
        });
    }
}

const ByAccount = new byAccount();

export { ByAccount as default };