<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 30.09.13
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */

require_once dirname(dirname(__FILE__)) . 'src/system/modules/dcatools';

$GLOBALS['TL_DCA']['tl_test'] = array();


class DcaToolsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provideDoAutoUpdate
	 */
	public function testDoAutoUpdate($value, $expected)
	{
		$actual = \Netzmacht\DcaTools\DcaTools::doAutoUpdate($value);
		$this->assertEquals($expected, $actual);
	}

	public function provideDoAutoUpdate()
	{
		return array
		(
			array(null, false),
			array(false, false),
			array(true, true),
		);
	}


	/**
	 * @dataProvider provideGetDataContainer
	 * @param $value
	 * @param $expected
	 */
	public function testGetDataContainer($value, $expected)
	{
		$objDc = \Netzmacht\DcaTools\DcaTools::getDataContainer('tl_test');
		$this->assertEquals($objDc, $expected);
	}

	public function provideGetDataContainer()
	{
		return array
		(
			array('tl_test', new \Netzmacht\DcaTools\DataContainer('tl_test'))
		);
	}


	public function testHookLoadDataContainer()
	{
		$objTools = new \Netzmacht\DcaTools\DcaTools();
		$objTools->hookLoadDataContainer('tl_test');

		$this->assertEquals(
			$GLOBALS['TL_DCA']['tl_test'],
			array()
		);

		$GLOBALS['TL_DCA']['tl_test']['dcatools'] = array();

		$this->assertEquals(
			$GLOBALS['TL_DCA']['tl_test'],
			array(
				'dcatools' => array(),
				'config' => array(
					'onload_callback' => array('Netzmacht\DcaTools\DcaTools', 'initializeDataContainer')
				),
			)
		);
	}

}