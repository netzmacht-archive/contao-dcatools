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


/**
 * hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('DcaTools\Dca\DcaToolsIntegration', 'onLoadDataContainer');


/**
 * events
 */
