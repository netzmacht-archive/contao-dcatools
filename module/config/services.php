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

use ContaoCommunityAlliance\Translator\Contao\LangArrayTranslator;
use ContaoCommunityAlliance\Translator\TranslatorChain;
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Condition\Command\CommandConditionHandler;
use DcaTools\Condition\Permission\PermissionConditionFactory;
use DcaTools\Condition\Permission\PermissionHandler;
use DcaTools\Dca\Builder\DcaToolsDefinitionBuilder;
use DcaTools\View\ButtonRenderer;
use DcaTools\View\DcGeneralViewHelper;

/**
 * driver manager
 */

/** @var \Pimple $container */
global $container;

$container['dcatools.event-propagator'] = $container->share(function(\Pimple $c) {
	return new \ContaoCommunityAlliance\DcGeneral\Event\EventPropagator($c['event-dispatcher']);
});

$container['dcatools.translator'] = $container->share(function(\Pimple $c) {
	$translator = new TranslatorChain();
	$translator->add(new LangArrayTranslator($c['event-dispatcher']));

	return $translator;
});

$container['dcatools.command-condition-handler'] = $container->share(function($c) {
	return new CommandConditionHandler($c['dcatools.user']);
});

$container['dcatools.permission-handler'] = $container->share(function($c) {
	return new PermissionHandler($c['dcatools.user']);
});

$container['dcatools.definition-builder'] = function(\Pimple $c) {
	$builder = new DcaToolsDefinitionBuilder(
		new CommandConditionFactory(
			new \DcaTools\Condition\Command\FilterFactory($GLOBALS['DCATOOLS_COMMAND_FILTERS']),
			$GLOBALS['DCATOOLS_COMMAND_CONDITIONS']
		),
		new PermissionConditionFactory(
			new \DcaTools\Condition\Permission\FilterFactory($GLOBALS['DCATOOLS_PERMISSION_FILTERS']),
			$GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']
		)
	);

	return $builder;
};

$container['dcatools.view-helper.default'] = $container->share(function() {
	return new DcGeneralViewHelper();
});

$container['dcatools.view-helper.legacy'] = $container->share(function() {
	return new \DcaTools\View\LegacyViewHelper();
});

$container['dcatools.user'] = $container->share(function() {
	return new \DcaTools\Contao\BackendUserDecorator(\BackendUser::getInstance());
});