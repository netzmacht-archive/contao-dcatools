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
$GLOBALS['TL_DCA']['tl_content']['dcatools']['getAllowedEntries'][]         = array('DcaTools\DataContainer\Content', 'getAllowedEntries');
$GLOBALS['TL_DCA']['tl_content']['dcatools']['getAllowedIds'][]             = array('DcaTools\DataContainer\Content', 'getAllowedIds');
$GLOBALS['TL_DCA']['tl_content']['dcatools']['getAllowedDynamicParents'][]  = array('DcaTools\DataContainer\Content', 'getAllowedDynamicParents');