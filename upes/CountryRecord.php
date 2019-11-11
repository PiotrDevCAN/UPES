<?php
namespace upes;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

class CountryRecord extends DbRecord
{
    protected $COUNTRY;
    protected $INTERNATIONAL;

    const INTERNATIONAL_YES = 'Yes';
    const INTERNATIONAL_NO  = 'No';

    function displayForm($mode)
    {
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';

        ?>
        <form id='countryForm' class="form-horizontal"  method='post'>
        <div class="form-group required">
            <label for='COUNTRY' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Country Name'>Country Name</label>
        	<div class='col-md-3'>
				<input id='COUNTRY' name='COUNTRY' class='form-control' <?=$notEditable;?> />
            </div>
        </div>

         <div class="form-group required" >
            <label for='INTERNATIONAL' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Internatinal'>International</label>
        	<div class='col-md-1'>
				<input type="checkbox" id='INTERNATIONAL' name='INTERNATIONAL' checked data-toggle="toggle" data-size="small" data-on="<?=self::INTERNATIONAL_YES;?>" data-off="<?=self::INTERNATIONAL_NO;?>">
            </div>
        </div>


   		<div class='form-group'>
   		<div class='col-sm-offset-2 -col-md-3'>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');
        $allButtons = array();
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateContract',null,'Update') :  $this->formButton('submit','Submit','saveContract',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetContractForm',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		$this->formBlueButtons($allButtons);
  		?>
  		</div>
  		</div>
	</form>
    <?php
    }

}




