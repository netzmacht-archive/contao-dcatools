<?php


$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('DcaTools\Bridge', 'hookLoadDataContainer');


/**
 * events
 */
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getAllowedEntries'][]         = array('DcaTools\Dca\Content', 'getAllowedEntries');
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getAllowedIds'][]             = array('DcaTools\Dca\Content', 'getAllowedIds');
$GLOBALS['TL_EVENTS']['dcatools.tl_content.getAllowedDynamicParents'][]  = array('DcaTools\Dca\Content', 'getAllowedDynamicParents');


$GLOBALS['TL_EVENTS']['dcatools.tl_content.getDynamicParent'][] = array('DcaTools\Dca\Content', 'getParentName');