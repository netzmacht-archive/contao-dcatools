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

/**
 * driver manager
 */

/** @var \Pimple $container */
$container = $GLOBALS['container'];

$container['dcatools.event-propagator'] = $container->share(function(\Pimple $c) {
	return new \ContaoCommunityAlliance\DcGeneral\Event\EventPropagator($c['event-dispatcher']);
});

$container['dcatools.translator'] = $container->share(function(\Pimple $c) {
	$translator = new TranslatorChain();
	$translator->add(new LangArrayTranslator($c['event-dispatcher']));

	return $translator;
});
