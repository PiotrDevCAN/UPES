<?php
use itdq\Trace;
use itdq\Loader;
use upes\AllTables;
use upes\AccountPersonTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

$loader = new Loader();


$date = new DateTime();
$oneMonth = new DateInterval('P1M');
$sixMonth = new DateInterval('P6M');
$date->sub($oneMonth);

$allAccounts = $loader->load('ACCOUNT',AllTables::$ACCOUNT);

$db2Report = AccountPersonTable::upcomingRechecksByAccount();

foreach ($db2Report as $row) {
    $monthWithLeadingZero = substr("0" . $row['MONTH'],-2);
    $report[$row['ACCOUNT']][$monthWithLeadingZero." ".$row['YEAR']] = $row['RESOURCES'];
  
}
?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'>
</div>
<div class='col-sm-6'>
<h1 id='portalTitle' class='text-centre' >Upcoming Rechecks</h1>
</div>
<div class='col-sm-4'>
</div>
</div>
</div>

<div class='container'>
        <table id='upcomingRechecksReport' class='table table-striped table-bordered table-condensed '  style='width:100%'>
		<thead>
		<tr class='' ><th>Account</th>
		<?php
		for ($i = 0; $i < 6; $i++) {
		    ?><td><?=$date->format('M Y');?></td><?php
		    $date->add($oneMonth);
		}

		?>
		</tr>
		</thead>
		<tbody>

		<?php

		foreach ($allAccounts as $account) {
		    if(isset($report[$account])){
		        $date->sub($sixMonth);
		        ?><tr><td><?=$account;?></td><?php
		        for ($i = 0; $i < 6; $i++) {
		            $dateKey = $date->format('m Y');
		            echo isset($report[$account][$dateKey]) ? "<td>" . $report[$account][$dateKey] . "</td>"  : "<td>0</td>";
		            $date->add($oneMonth);
		        }
		        ?></tr><?php
		    }
		}
		?>
		</tbody>
		<tfoot>
		</tfoot>
		</table>


</div>


<script>

var buttonCommon = {
		 exportOptions: {
           format: {
              body: function ( data, row, column, node ) {
              	console.log(data);
              //   return data ?  data.replace( /<br\s*\/?>/ig, "\n") : data ;
              return data ? data.replace( /<br\s*\/?>/ig, "\n").replace(/(&nbsp;|<([^>]+)>)/ig, "") : data ;
              //    data.replace( /[$,.]/g, '' ) : data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
              }
           }
       }
};

$(document).ready( function () {
    $('#processStatusByAccountReport').DataTable(
    		{
	    	autoWidth: false,
	    	deferRender: true,
	    	processing: true,
	    	responsive: true,
	    	colReorder: true,
	    	dom: 'Blfrtip',
	        buttons: [
                'colvis',
                $.extend( true, {}, buttonCommon, {
                    extend: 'excelHtml5',
                    exportOptions: {
                        orthogonal: 'sort',
                        stripHtml: true,
                        stripNewLines:false
                    },
                    filename: 'upes process status by account',
                    customize: function( xlsx ) {
                         var sheet = xlsx.xl.worksheets['sheet1.xml'];
                     }
            }),
            $.extend( true, {}, buttonCommon, {
                extend: 'csvHtml5',
                exportOptions: {
                    orthogonal: 'sort',
                    stripHtml: true,
                    stripNewLines:false
                },
                filename: 'upes process status by account',
            }),
            $.extend( true, {}, buttonCommon, {
                extend: 'print',
                exportOptions: {
                    orthogonal: 'sort',
                    stripHtml: true,
                    stripNewLines:false
                }
            })
            ],
    		});
} );


</script>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);