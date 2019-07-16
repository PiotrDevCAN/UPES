<?php
namespace upes;

use itdq\DbRecord;
use itdq\FormClass;

class AccountRecord extends DbRecord
{
    protected $ACCOUNT_ID;
    protected $ACCOUNT;


    function displayForm($mode)
    {
        ?>
        <form id='accountsForm' class="form-horizontal"  method='post'>
        <?php

        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';

        ?>
        <div class="form-group required" id="AccountGroup">
            <label for='ACCOUNT' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Account Name'>Account Name</label>
        	<div class='col-md-3'>
				<input id='ACCOUNT' name='ACCOUNT' class='form-control' <?=$notEditable;?> />
				<input id='ACCOUNT_ID' name='ACCOUNT_ID' type='hidden' value='0' />
            </div>
        </div>

   		<div class='form-group'>
   		<div class='col-sm-offset-2 -col-md-3'>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');
        $allButtons = array();
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateAccount',null,'Update') :  $this->formButton('submit','Submit','saveAccount',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetAccountForm',null,'Reset','btn-warning');
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

