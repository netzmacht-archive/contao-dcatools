<?php

/**
 * driver manager
 */
$GLOBALS['container']['dcatools.driver-manager'] = $GLOBALS['container']->share(function() {
	return new \DcaTools\Data\DriverManager();
});