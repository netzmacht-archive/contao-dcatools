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
use DcaTools\Condition\Command;
use DcaTools\Condition\Permission;

/**
 * hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('DcaTools\Dca\Legacy\DcaToolsIntegration', 'onLoadDataContainer');

$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['isAllowed'] = 'DcaTools\Condition\Command\IsAllowedCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['hasAccess'] = 'DcaTools\Condition\Command\HasAccessCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['hide']      = 'DcaTools\Condition\Command\HideCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['disable']   = 'DcaTools\Condition\Command\DisableCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['isAdmin']   = function($config) {
	return new Command\IsAdminCondition($GLOBALS['container']['dcatools.user'], $config);
};

$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']['isAdmin']   = function($user, $config) {
	return new Permission\IsAdminCondition($user, $config);
};


$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'DcaTools\Contao\CompatibilitySubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'DcaTools\Condition\Permission\PermissionHandler';

$GLOBALS['TL_EVENTS'][BuildDataDefinitionEvent::NAME][] = function(BuildDataDefinitionEvent $event) {
	$GLOBALS['container']['dcatools.definition-builder']->build($event->getContainer(), $event);
};

$GLOBALS['TL_EVENTS'][BuildDataDefinitionEvent::NAME][] = array(function(BuildDataDefinitionEvent $event) {
	$GLOBALS['container']['dcatools.command-condition-handler']->setUp($event);
}, -1000);