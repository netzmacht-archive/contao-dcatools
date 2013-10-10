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
use \Netzmacht\DcaTools\Field;

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
		DcaTools::doAutoUpdate(false);
		$this->objDataContainer = DcaTools::getDataContainer('tl_test');
	}

	protected function initializeTlTest()
	{
		$GLOBALS['TL_DCA']['tl_test'] = array
		(
			'config' => array
			(
			),

			'fields' => array
			(
				'test' => array
				(
					'label' => array('Test', 'Label'),
					'inputType' => 'text',
				)
			)
		);
	}

	public function tearDown()
	{
		$this->objDataContainer = null;
		unset($GLOBALS['TL_DCA']['tl_test']);

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


	public function testGetField()
	{
		$objField = new Field('test', $this->objDataContainer);
		$objField->addListener('delete', array($this->objDataContainer, 'fieldListener'));

		$this->assertEquals($objField, $this->objDataContainer->getField('test'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetFieldException()
	{
		$this->objDataContainer->getField('notexisting');
	}

	public function testHasField()
	{
		$this->assertFalse($this->objDataContainer->hasField('notexisting'));
		$this->assertTrue($this->objDataContainer->hasField('test'));
	}

	public function testRemoveField()
	{
		$this->assertTrue($this->objDataContainer->hasField('test'));

		$this->objDataContainer->removeField('test');
		$this->assertFalse($this->objDataContainer->hasField('test'));
	}

	public function testCreateField()
	{
		$this->assertFalse($this->objDataContainer->hasField('new'));

		$objField = $this->objDataContainer->createField('new');

		$this->assertTrue($this->objDataContainer->hasField('new'));
		$this->assertEquals($objField, $this->objDataContainer->getField('new'));
	}
}