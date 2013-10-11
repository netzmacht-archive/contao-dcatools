<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 30.09.13
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

use DcaTools\Definition\DataContainer;
use DcaTools\Definition\Property;
use DcaTools\Definition;


$GLOBALS['TL_DCA']['tl_test'] = array();

class DataContainerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var DataContainer
	 */
	protected $objDataContainer;

	public function setUp()
	{
		$GLOBALS['TL_DCA']['tl_test'] = $GLOBALS['TEST_DCA'];
		$this->objDataContainer = Definition::getDataContainer('tl_test');
	}

	public function tearDown()
	{
		$this->objDataContainer = null;
		$GLOBALS['TL_DCA']['tl_test'] = null;

		$obj         = new Definition();
		$refObject   = new ReflectionObject( $obj );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}


	public function testGetDataContainer()
	{
		$this->assertEquals($this->objDataContainer, $this->objDataContainer->getDataContainer());
	}

	public function testGetModel()
	{
		$this->assertNull($this->objDataContainer->getModel());

		$objModel = new ContentModel();
		$objDcModel = new \DcGeneral\Data\DefaultModel();
		$objDcModel->setPropertiesAsArray($objModel->row());
		$this->objDataContainer->setModel($objModel);
		$this->assertEquals($objDcModel, $this->objDataContainer->getModel());

		$objDataContainer = new DataContainer('tl_test', $objModel);
		$this->assertEquals($objDcModel, $objDataContainer->getModel());
	}

	public function testSetModel()
	{
		// Contao SUCKS, cannot test against the same model
		$objModel = ArticleModel::findAll(array('limit' => 1, 'return' => 'Model'));
		$objDcModel = new \DcGeneral\Data\DefaultModel();
		$objDcModel->setPropertiesAsArray($objModel->row());
		$this->assertEquals($this->objDataContainer->setModel($objModel), $this->objDataContainer);
		$this->assertEquals($objDcModel, $this->objDataContainer->getModel($objModel));

		$objCollection = PageModel::findAll(array('limit' => 2));

		$this->objDataContainer->setModel($objCollection);
		$objDcModel = new \DcGeneral\Data\DefaultModel();
		$objDcModel->setPropertiesAsArray($objCollection->current()->row());
		$this->assertEquals($objDcModel, $this->objDataContainer->getModel());

		$objResult = \Database::getInstance()->query('SELECT * FROM tl_content limit 2');
		$objDcModel = new \DcGeneral\Data\DefaultModel();
		$objDcModel->setPropertiesAsArray($objResult->row());
		$this->objDataContainer->setModel($objResult);
		$this->assertEquals($objDcModel, $this->objDataContainer->getModel());

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
	}

	public function testHasModel()
	{
		$this->assertFalse($this->objDataContainer->hasModel());

		$objModel = new StyleModel();
		$this->objDataContainer->setModel($objModel);
		$this->assertTrue($this->objDataContainer->hasModel());
	}


	public function testGetProperty()
	{
		$objProperty = new Property('test', $this->objDataContainer);

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

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
	}

	public function testCreateProperty()
	{
		$this->assertFalse($this->objDataContainer->hasProperty('new'));

		$objProperty = $this->objDataContainer->createProperty('new');

		$this->assertTrue($this->objDataContainer->hasProperty('new'));
		$this->assertEquals($objProperty, $this->objDataContainer->getProperty('new'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
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
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
	}

	public function testHasPalette()
	{
		$this->assertFalse($this->objDataContainer->hasPalette('test'));
		$this->assertTrue($this->objDataContainer->hasPalette('default'));
	}

	public function testGetPalette()
	{
		$this->assertInstanceOf('DcaTools\Definition\Palette', $this->objDataContainer->getPalette('default'));
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
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
	}

	public function testCreateSubPalette()
	{
		$objSubPalette = $this->objDataContainer->createSubPalette('subpalette');
		$objSubPalette->addProperty($this->objDataContainer->getProperty('test'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['subpalette'], $objSubPalette->getDefinition());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
	}

	public function testHasSubPalette()
	{
		$this->assertFalse($this->objDataContainer->hasSubPalette('test'));
		$this->assertTrue($this->objDataContainer->hasSubPalette('sub'));
	}

	public function testGetSubPalette()
	{
		$this->assertInstanceOf('DcaTools\Definition\SubPalette', $this->objDataContainer->getSubPalette('sub'));
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

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test'], $this->objDataContainer->getDefinition());
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