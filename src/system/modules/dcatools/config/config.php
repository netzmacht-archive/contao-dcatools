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
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('DcaTools\Bridge', 'hookLoadDataContainer');


/**
 * events
 */
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getAllowedEntries'][]         = array('DcaTools\Dca\Content', 'getAllowedEntries');
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getAllowedIds'][]             = array('DcaTools\Dca\Content', 'getAllowedIds');
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getAllowedDynamicParents'][]  = array('DcaTools\Dca\Content', 'getAllowedDynamicParents');
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getDynamicParent'][]          = array('DcaTools\Dca\Content', 'getParentName');