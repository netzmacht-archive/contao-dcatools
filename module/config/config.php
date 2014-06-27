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

use \DcaTools\Dca\Button\Condition as CommandCond;

/**
 * hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('DcaTools\Dca\DcaToolsIntegration', 'onLoadDataContainer');
$GLOBALS['TL_HOOKS']['initializeSystem'][]  = array('DcaTools\Contao\Hooks', 'onInitializeSystem');

$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['isAdmin'] = function($manager, $config) {
	global $container;

	return new CommandCond\IsAdminCondition($container['dcatools.acl'], $manager, $config);
};

$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['isAllowed']   = 'DcaTools\Dca\Button\Condition\IsAllowedCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['hasAccess']   = 'DcaTools\Dca\Button\Condition\HasAccessCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['hide']        = 'DcaTools\Dca\Button\Condition\HideCondition';
$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']['disableIcon'] = 'DcaTools\Dca\Button\Condition\DisableCondition';

$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'DcaTools\Contao\CompatibilitySubscriber';