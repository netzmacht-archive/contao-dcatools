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

use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider;
use ContaoCommunityAlliance\Translator\Contao\LangArrayTranslator;
use ContaoCommunityAlliance\Translator\TranslatorChain;
use DcaTools\Config\Map;
use DcaTools\Dca\Builder\DcaToolsDefinitionBuilder;
use DcaTools\View\ButtonRenderer;
use DcaTools\Condition\Command\CommandConditionHandler;
use DcaTools\Condition\Command\ConditionManager;
use DcaTools\View\DcGeneralBasedViewHelper;

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

$container['dcatools.button-renderer'] = $container->share(function(\Pimple $c) {
	return new ButtonRenderer();
});

$container['dcatools.command-condition-handler'] = $container->share(function($c) {
	return new CommandConditionHandler($c['dcatools.button-renderer']);
});

$container['dcatools.definition-builder'] = function(\Pimple $c) {
	$builder = new DcaToolsDefinitionBuilder(
		$c['dcatools.user'],
		(array) $GLOBALS['DCATOOLS_COMMAND_CONDITIONS'],
		(array) $GLOBALS['DCATOOLS_PERMISSION_CONDITIONS']
	);

	return $builder;
};

$container['dcatools.view-helper'] = $container->share(function() {
	return new DcGeneralBasedViewHelper();
});

$container['dcatools.user'] = $container->share(function() {
	return new \DcaTools\Contao\BackendUserDecorator(\BackendUser::getInstance());
});