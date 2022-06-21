/**
 *
 */

import buttonCommon from '../modules/buttonCommon.js';

class processStatus {

    table;

    constructor() {
        console.log('+++ Function +++ processStatus.constructor');

        this.populateDataTable();

        console.log('+++ Function +++ processStatus.constructor');
    }

    populateDataTable() {
        this.table = $('#processStatusByAccountReport').DataTable({
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
                    filename: 'upes process status by account',
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
                    filename: 'upes process status by account',
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

const ProcessStatus = new processStatus();

export { ProcessStatus as default };