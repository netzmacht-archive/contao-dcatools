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
use DcaTools\Dca\Button\ButtonRenderer;
use DcaTools\Dca\Button\Condition\ConditionManager;
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

$container['dcatools.button-condition-manager'] = $container->share(function(\Pimple $c) {
	$map        = new Map($GLOBALS['DCATOOLS_COMMAND_CONDITIONS']);
	$dispatcher = $c['event-dispatcher'];
	$renderer   = $c['dcatools.button-renderer'];

	return new ConditionManager($dispatcher, $map, $renderer, new InputProvider());
});

$container['dcatools.view-helper'] = $container->share(function() {
	return new DcGeneralBasedViewHelper();
});

$container['dcatools.acl'] = $container->share(function() {
	$user = \BackendUser::getInstance();

	return new \DcaTools\Contao\Acl($user);
});