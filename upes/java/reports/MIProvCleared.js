/**
 *
 */

class MIProvCleared {

    miReportTable;

    constructor() {
        console.log('+++ Function +++ MIProvCleared.constructor');

        this.populateDataTable();

        console.log('+++ Function +++ MIProvCleared.constructor');
    }

    populateDataTable() {
        this.miReportTable = $('#miReportProv').DataTable({
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

const MiProvCleared = new MIProvCleared();

export { MiProvCleared as default };