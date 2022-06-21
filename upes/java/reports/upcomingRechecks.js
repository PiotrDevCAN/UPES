/**
 *
 */

import buttonCommon from '../modules/buttonCommon.js';

class upcomingRechecks {

    table;

    constructor() {
        console.log('+++ Function +++ upcomingRechecks.constructor');

        this.populateDataTable();

        console.log('+++ Function +++ upcomingRechecks.constructor');
    }

    populateDataTable() {
        this.table = $('#upcomingRechecksReport').DataTable({
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

const UpcomingRechecks = new upcomingRechecks();

export { UpcomingRechecks as default };