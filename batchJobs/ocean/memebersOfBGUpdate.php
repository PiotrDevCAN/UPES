<?php

use BlueGroupsManager\GroupUpdateManager;

include ('php/ldap.php');

$groupName = 'OAT_Admin';
// $groupName = 'Piotr_test_group';

$manager = new GroupUpdateManager($groupName);
$manager->updateMembersOfGroup();