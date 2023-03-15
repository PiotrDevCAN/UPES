/**
 *
 */
import buttonCommon from '../modules/buttonCommon.js';
import personEditBox from '../modules/boxes/PersonEditBox.js';
import personOffBoardBox from '../modules/boxes/PersonOffBoardBox.js';
import PESLevelEditBox from '../modules/boxes/PESLevelEditBox.js';
import cancelPESRequestBox from '../modules/boxes/cancelPESRequestModalBox.js';

class userStatus {

    table;

    constructor() {
        console.log('+++ Function +++ userStatus.constructor');

        this.populateDataTable();

        console.log('--- Function --- userStatus.constructor');
    }

    populateDataTable() {
        var selectors = [];
        if (isCdi != '') {
            selectors.push('.accessCdi');
        }
        if (isPesTeam != '') {
            selectors.push('.accessPesTeam');
        }
        if (isUser != '') {
            selectors.push('.accessUser');
        }
        var selectorsStr = selectors.join(',');

        this.table = $('#userStatusTable').DataTable({
            ajax: {
                url: 'ajax/populateUserStatusTable.php',
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
                data: "ACTION"
            }, {
                data: "EMAIL_ADDRESS", visible: false
            }, {
                data: "CNUM"
            }, {
                data: "FULL_NAME"
            }, {
                data: "ACCOUNT", render: { _: 'display', sort: 'sort' },
            }, {
                data: "CONTRACT"
            }, {
                data: "COUNTRY_OF_RESIDENCE", visible: false
            }, {
                data: "PES_REQUESTOR", visible: false
            }, {
                data: "REQUESTED", render: { _: 'display', sort: 'sort' }, visible: false
            }, {
                data: "PES_LEVEL"
            }, {
                data: "PES_LEVEL_DESCRIPTION", visible: false
            }, {
                data: "PROCESSING_STATUS", render: { _: 'display', sort: 'sort' },
            }, {
                data: "PROCESSING_STATUS_CHANGED", visible: false
            }, {
                data: "PES_STATUS"
            }, {
                data: "PES_CLEARED_DATE"
            }, {
                data: "DATE_LAST_CHASED"
            }, {
                data: "OFFBOARDED_DATE", visible: false
            }, {
                data: "OFFBOARDED_BY", visible: false
            }],
            drawCallback: function (settings) {
                $('.btn-info').parent('td').parent('tr').addClass('warning');
                $('.btn-info').parent('td').parent('tr').children('td').css({ 'font-style': 'italic' });
                $('button.accessRestrict').not(selectorsStr).remove();
            }
        });
    }
}

const UserStatus = new userStatus();

const PersonEditBox = new personEditBox(UserStatus);
const PersonOffBoardBox = new personOffBoardBox(UserStatus);
const PesLevelEditBox = new PESLevelEditBox(UserStatus);
const CancelPesRequestBox = new cancelPESRequestBox(UserStatus);

export { UserStatus as default };