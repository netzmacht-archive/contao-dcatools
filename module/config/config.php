<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */

use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\CreateDcGeneralEvent;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\Permission;

/**
 * hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('DcaTools\Dca\Legacy\DcaToolsIntegration', 'onLoadDataContainer');

$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['hide']      = 'DcaTools\Definition\Command\Condition\HideCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['disable']   = 'DcaTools\Definition\Command\Condition\DisableCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['isAllowed'] = 'DcaTools\Definition\Command\Condition\IsAllowedCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['hasAccess'] = 'DcaTools\Definition\Command\Condition\HasAccessCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['isAdmin']   = 'DcaTools\Definition\Command\Condition\IsAdminCondition';

$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['isAdmin']   = 'DcaTools\Definition\Permission\Condition\IsAdminCondition';
$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['role']      = 'DcaTools\Definition\Permission\Condition\RoleCondition';
$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['hasAccess'] = 'DcaTools\Definition\Permission\Condition\HasAccessCondition';
$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['isAllowed'] = 'DcaTools\Definition\Permission\Condition\IsAllowedCondition';
$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['owner']     = 'DcaTools\Definition\Permission\Condition\OwnerCondition';
$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['or']        = 'DcaTools\Definition\Permission\Condition\OrCondition';

$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'DcaTools\Contao\CompatibilitySubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = function() {
	global $container;

	return $container['dcatools.permission-handler'];
};

$GLOBALS['TL_EVENTS'][BuildDataDefinitionEvent::NAME][] = function(BuildDataDefinitionEvent $event) {
	$GLOBALS['container']['dcatools.definition-builder']->build($event->getContainer(), $event);
};

$GLOBALS['TL_EVENTS'][BuildDataDefinitionEvent::NAME][] = array(function(BuildDataDefinitionEvent $event) {
	$GLOBALS['container']['dcatools.command-condition-handler']->setUp($event);
}, -1000);