<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Dcatools
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

// only call namepsaceloader for debugging
/*
if(!strpos(__DIR__, 'composer'))
{
	NamespaceClassLoader::add('DcaTools', 'system/modules/dcatools');
} */


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'dcatools_operation'            => 'system/modules/dcatools/templates',
	'dcatools_global_operation'     => 'system/modules/dcatools/templates',
));
