<?php

use upes\ContractTable;

$upesRef = isset($_POST['upesref']) ? $_POST['upesref'] : 'fred';

echo ContractTable::prepareJsonObjectForContractsSelect($upesRef);
