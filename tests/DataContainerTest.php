<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 30.09.13
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */

require_once dirname(__FILE__) . '/bootstrap.php';

use \Netzmacht\DcaTools\DataContainer;
use \Netzmacht\DcaTools\DcaTools;
use \Netzmacht\DcaTools\Property;

$GLOBALS['TL_DCA']['tl_test'] = array();

class DataContainerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var DataContainer
	 */
	protected $objDataContainer;

	public function setUp()
	{
		$this->initializeTlTest();
		$this->objDataContainer = DcaTools::getDataContainer('tl_test');
	}

	protected function initializeTlTest()
	{
		$GLOBALS['TL_DCA']['tl_test'] = array
		(
			'config' => array
			(
			),

			'palettes' => array
			(
				'__selector__' => array('done'),
				'default' => '{title_legend},test',
			),

			'subpalettes' => array
			(
				'sub' => 'test',
			),

			'fields' => array
			(
				'test' => array
				(
					'label' => array('Test', 'Label'),
					'inputType' => 'text',
				),

				'done' => array
				(
					'label' => array('Get', 'it done'),
					'inputType' => 'checkbox',
				)
			)
		);
	}

	public function tearDown()
	{
		$this->objDataContainer = null;
		$GLOBALS['TL_DCA']['tl_test'] = null;

		$obj         = new DcaTools();
		$refObject   = new ReflectionObject( $obj );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}


	public function testGetDataContainer()
	{
		$this->assertEquals($this->objDataContainer, $this->objDataContainer->getDataContainer());
	}

	public function testGetRecord()
	{
		$this->assertNull($this->objDataContainer->getRecord());

		$objModel = new ContentModel();
		$this->objDataContainer->setRecord($objModel);
		$this->assertEquals($objModel, $this->objDataContainer->getRecord());

		$objDataContainer = new DataContainer('tl_test', $objModel);
		$this->assertEquals($objModel, $objDataContainer->getRecord());
	}

	public function testSetRecord()
	{
		// Contao SUCKS, cannot test against the same model
		$objModel = ArticleModel::findAll(array('limit' => 1, 'return' => 'Model'));
		$this->assertEquals($this->objDataContainer->setRecord($objModel), $this->objDataContainer);
		$this->assertEquals($objModel, $this->objDataContainer->getRecord($objModel));

		$objCollection = PageModel::findAll(array('limit' => 2));

		$this->objDataContainer->setRecord($objCollection);
		$this->assertEquals($objCollection->current(), $this->objDataContainer->getRecord());

		$objResult = \Database::getInstance()->query('SELECT * FROM tl_content limit 2');
		$this->objDataContainer->setRecord($objResult);
		$this->assertEquals($objResult, $this->objDataContainer->getRecord());
	}

	public function testHasRecord()
	{
		$this->assertFalse($this->objDataContainer->hasRecord());

		$objModel = new StyleModel();
		$this->objDataContainer->setRecord($objModel);
		$this->assertTrue($this->objDataContainer->hasRecord());
	}


	public function testGetProperty()
	{
		$objProperty = new Property('test', $this->objDataContainer);
		$objProperty->addListener('delete', array($this->objDataContainer, 'propertyListener'));

		$this->assertEquals($objProperty, $this->objDataContainer->getProperty('test'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetPropertyException()
	{
		$this->objDataContainer->getProperty('notexisting');
	}

	public function testHasProperty()
	{
		$this->assertFalse($this->objDataContainer->hasProperty('notexisting'));
		$this->assertTrue($this->objDataContainer->hasProperty('test'));
	}

	public function testRemoveProperty()
	{
		$this->assertTrue($this->objDataContainer->hasProperty('test'));

		$this->objDataContainer->removeProperty('test');
		$this->assertFalse($this->objDataContainer->hasProperty('test'));
		$this->assertFalse(isset($GLOBALS['TL_DCA']['tl_test']['fields']['test']));
	}

	public function testCreateProperty()
	{
		$this->assertFalse($this->objDataContainer->hasProperty('new'));

		$objProperty = $this->objDataContainer->createProperty('new');

		$this->assertTrue($this->objDataContainer->hasProperty('new'));
		$this->assertEquals($objProperty, $this->objDataContainer->getProperty('new'));
	}

	public function testGetPropertyNames()
	{
		$this->assertEquals(array('test', 'done'), $this->objDataContainer->getPropertyNames());
	}

	public function testCreatePalette()
	{
		$objPalette = $this->objDataContainer->createPalette('test');
		$objPalette->createLegend('default');
		$objPalette->addProperty($this->objDataContainer->getProperty('test'), 'default');

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['test'], $objPalette->getDefinition());
	}

	public function testHasPalette()
	{
		$this->assertFalse($this->objDataContainer->hasPalette('test'));
		$this->assertTrue($this->objDataContainer->hasPalette('default'));
	}

	public function testGetPalette()
	{
		$this->assertInstanceOf('Netzmacht\DcaTools\Palette\Palette', $this->objDataContainer->getPalette('default'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetPaletteException()
	{
		$this->objDataContainer->getPalette('notexisting');
	}

	public function testGetPalettes()
	{
		$this->assertEquals(array('default' => $this->objDataContainer->getPalette('default')), $this->objDataContainer->getPalettes());
	}

	public function testRemovePalette()
	{
		$this->assertTrue($this->objDataContainer->hasPalette('default'));
		$this->assertEquals($this->objDataContainer, $this->objDataContainer->removePalette('default'));
		$this->assertFalse($this->objDataContainer->hasPalette('default'));
		$this->assertFalse(isset($GLOBALS['TL_DCA']['tl_test']['palettes']['default']));
	}

	public function testCreateSubPalette()
	{
		$objSubPalette = $this->objDataContainer->createSubPalette('subpalette');
		$objSubPalette->addProperty($this->objDataContainer->getProperty('test'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['subpalette'], $objSubPalette->getDefinition());
	}

	public function testHasSubPalette()
	{
		$this->assertFalse($this->objDataContainer->hasSubPalette('test'));
		$this->assertTrue($this->objDataContainer->hasSubPalette('sub'));
	}

	public function testGetSubPalette()
	{
		$this->assertInstanceOf('Netzmacht\DcaTools\Palette\SubPalette', $this->objDataContainer->getSubPalette('sub'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetSubPaletteException()
	{
		$this->objDataContainer->getSubPalette('notexisting');
	}

	public function testGetSubPalettes()
	{
		$this->assertEquals(array('sub' => $this->objDataContainer->getSubPalette('sub')), $this->objDataContainer->getSubPalettes());
	}

	public function testRemoveSubPalette()
	{
		$this->assertTrue($this->objDataContainer->hasSubPalette('sub'));
		$this->assertEquals($this->objDataContainer, $this->objDataContainer->removeSubPalette('sub'));
		$this->assertFalse($this->objDataContainer->hasSubPalette('sub'));
		$this->assertFalse(isset($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub']));
	}

	public function testGetSelectors()
	{
		$this->assertEquals(array('done' => $this->objDataContainer->getProperty('done')), $this->objDataContainer->getSelectors());

		$this->objDataContainer->getProperty('test')->isSelector(true);
		$this->assertEquals(
			array(
				'done' => $this->objDataContainer->getProperty('done'),
				'test' => $this->objDataContainer->getProperty('test')
			),
			$this->objDataContainer->getSelectors());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetSelectorException()
	{
		$this->objDataContainer->getSelector('notexisting');
	}

	public function testHasSelector()
	{
		$this->assertTrue($this->objDataContainer->hasSelectors());
		$this->assertTrue($this->objDataContainer->hasSelector('done'));
		$this->assertFalse($this->objDataContainer->hasSelector('test'));
	}
}