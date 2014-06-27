<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Data;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;


/**
 * Class ModelFacade is used to create an instance of model. If possible it keeps connection to the original source
 *
 * @package DcaTools\Data
 */
final class ModelFactory
{

	/**
	 * @param EnvironmentInterface $environment
	 * @param \DataContainer $dc
	 * @return ModelInterface
	 */
	public static function createByDc(EnvironmentInterface $environment, \DataContainer $dc)
	{
		return static::createbyDatabaseResult($environment->getDataDefinition()->getName(), $dc->activeRecord);
	}


	/**
	 * @param $dataContainerName
	 * @param \Database\Result $result
	 * @return ModelInterface
	 */
	public static function createByDatabaseResult($dataContainerName, \Database\Result $result)
	{
		return new ActiveRecordModelDecorator($dataContainerName, $result);
	}


	/**
	 * @param \Model $model
	 * @return ModelInterface
	 */
	public static function createByLegacyModel(\Model $model)
	{
		return new ActiveRecordModelDecorator($model->getTable(), $model->getResult());
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param $id
	 * @param string $source
	 * @param bool $fetch
	 * @return ModelInterface
	 */
	public static function createById(EnvironmentInterface $environment, $id, $source=null, $fetch=true)
	{
		if($fetch) {
			return ConfigBuilder::create($environment, $source)->setId($id)->fetch();
		}
		else {
			$model = $environment->getDataProvider($source)->getEmptyModel();
			$model->setId($id);

			return $model;
		}
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param $row
	 * @param null $source
	 * @return ModelInterface
	 */
	public static function createByArray(EnvironmentInterface $environment, $row, $source=null)
	{
		$model = $environment->getDataProvider($source)->getEmptyModel();
		$model->setPropertiesAsArray($row);
		$model->setId($row['id']);

		return $model;
	}

} 