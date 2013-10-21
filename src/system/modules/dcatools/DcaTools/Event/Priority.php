<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 21.10.13
 * Time: 07:24
 * To change this template use File | Settings | File Templates.
 */

namespace DcaTools\Event;

/**
 * Class Priority stores constants for event priority definition
 *
 * @package DcaTools\Event
 */
class Priority
{

	/**
	 * Use this if you want to run your event before the output is generated or before the Contao callback is called
	 */
	const BEFORE = 3;


	/**
	 * This priority will be used for Contao callbacks which are wrapped in an event
	 */
	const CALLBACK = 2;


	/**
	 * Use this if your event will generate the output.
	 */
	const GENERATE = 1;


	/**
	 * The standard priority does not have to be set.
	 */
	const STANDARD = 0;


	/**
	 * Use this if your event shall be called after generating the output
	 */
	const POST = -1;

}