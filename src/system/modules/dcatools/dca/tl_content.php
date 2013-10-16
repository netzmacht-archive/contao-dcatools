<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   LGPL/3.0
 * @copyright 2013 netzmacht creative David Molineus
 */


/**
 * Events
 */
$GLOBALS['TL_DCA']['tl_content']['dcatools']['events']['getAllowedEntries'][]         = array('DcaTools\DataContainer\Content', 'getAllowedEntries');
$GLOBALS['TL_DCA']['tl_content']['dcatools']['events']['getAllowedIds'][]             = array('DcaTools\DataContainer\Content', 'getAllowedIds');
$GLOBALS['TL_DCA']['tl_content']['dcatools']['events']['getAllowedDynamicParents'][]  = array('DcaTools\DataContainer\Content', 'getAllowedDynamicParents');