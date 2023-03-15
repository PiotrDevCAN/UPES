/**
 *
 */

class MICleared {

    miReportTable;

    constructor() {
        console.log('+++ Function +++ MICleared.constructor');

        this.populateDataTable();

        console.log('+++ Function +++ MICleared.constructor');
    }

    populateDataTable() {
        this.miReportTable = $('#miReport').DataTable({
            autoWidth: false,
            deferRender: true,
            processing: true,
            responsive: true,
            colReorder: true,
            order: [[0, 'desc']],
            dom: 'Brti',
            buttons: [
                'colvis',
                'excelHtml5',
                'csvHtml5',
                'print'
            ],
        });
    }
}

const MiCleared = new MICleared();

export { MiCleared as default };