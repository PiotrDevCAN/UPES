<?php
namespace upes;


use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;

class PesLevelRecord extends DbRecord
{

    protected $PES_LEVEL_REF;
    protected $ACCOUNT_ID;
    protected $PES_LEVEL;
    protected $PES_LEVEL_DESCRIPTION;
    protected $RECHECK_YEARS;


    function displayForm($mode){
        $mode = empty($mode) ? FormClass::$modeDEFINE : $mode;
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        $loader = new Loader();
        $allAccounts = $loader->loadIndexed('ACCOUNT','ACCOUNT_ID',AllTables::$ACCOUNT);

        ?>
        <form id='pesLevelForm' class="form-horizontal"  method='post'>
        <div class="form-group required">
            <label for='ACCOUNT_ID' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Account Name'>Account Name</label>
        	<div class='col-md-3'>
        		<select id='ACCOUNT_ID' class='form-group select2' name='ACCOUNT_ID' <?=$notEditable?> required='required'>
        		<option value=''></option>
        		<?php
        		foreach ($allAccounts as $accountId => $account) {
        		    ?><option value='<?=$accountId ?>'><?=$account?></option><?php
        		}
        		?>
        		</select>
            </div>
        </div>
         <div class="form-group required" >
            <label for='PES_LEVEL' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='PES Level'>PES Level</label>
        	<div class='col-md-3'>
				<input id='PES_LEVEL' name='PES_LEVEL' class='form-control' maxlength="25" <?=$notEditable;?> required='required'/>
				<input id='PES_LEVE_REF' name='PES_LEVEL_REF' type='hidden' value='0' />
            </div>
        </div>
        <div class="form-group required" >
            <label for='PES_LEVEL_DESCRIPTION' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Pes Level Description'>Pes Level Description</label>
        	<div class='col-md-3'>
				<input id='PES_LEVEL_DESCRIPTION' name='PES_LEVEL_DESCRIPTION' class='form-control' maxlength="50" <?=$notEditable;?> required='required'/>
            </div>
        </div>
        <div class="form-group required" >
            <label for='RECHECK_YEARS' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Pes Recheck Period(Years)'>Pes Recheck Period(Years)</label>
        	<div class='col-md-3'>
				<input id='RECHECK_YEARS' name='RECHECK_YEARS' class='form-control' type="number" <?=$notEditable;?> required='required'/>
            </div>
        </div>


   		<div class='form-group'>
   		<div class='col-sm-offset-2 -col-md-3'>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');
        $allButtons = array();
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updatePesLevel',null,'Update') :  $this->formButton('submit','Submit','savePesLevel',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetPesLevelForm',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		$this->formBlueButtons($allButtons);
  		?>
  		</div>
  		</div>
  		<input id='PES_LEVEL_REF' name='PES_LEVEL_REF' type="hidden" />
	</form>
    <?php
    }
}

