<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\Data;

use DcGeneral\Data\ModelInterface;
use DcGeneral\DC_General;

/**
 * Class ModelFactory is used for converting Contao style models and DC General models
 * @package DcaTools\Data
 */
class ModelFactory
{

	/**
	 * Create a model by given table name, id and row
	 *
	 * @param $tableName
	 * @param $id
	 * @param null $row
	 * @param bool $empty
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public static function create($tableName, $id, $row=null, $empty=false)
	{
		/** @var \DcaTools\Data\DriverManagerInterface $manager */
		$manager = $GLOBALS['container']['dcatools.driver-manager'];
		$driver  = $manager->getDataProvider($tableName);

		if($row !== null) {
			if(is_object($row)) {
				$row = $row->row();
			}

			$model = $driver->getEmptyModel();
			$model->setPropertiesAsArray($row);
			$model->setId($id);
		}
		else {
			$model = ConfigBuilder::create($driver)->setId($id)->fetch();
		}

		if(!$model && $empty) {
			$model = $driver->getEmptyModel();
		}

		return $model;
	}


	/**
	 * Create a row by passing the DC
	 *
	 * @param $dc
	 * @param bool $empty
	 *
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public static function byDc($dc, $empty=false)
	{
		if($dc instanceof DC_General) {
			$model = $dc->getEnvironment()->getCurrentModel();

			if($model && $model->hasProperties()) {
				return $model;
			}
			else {
				$model = static::create($dc->getName(), $dc->getId(), $empty);
			}
		}
		else {
			$model = static::create($dc->table, $dc->id, $dc->activeRecord, $empty);
		}

		return $model;
	}


	/**
	 * Create a mobel by passing a Contao model
	 *
	 * @param \Model $model
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public static function byContaoModel(\Model $model)
	{
		return static::create($model->getTable(), $model->{$model->getPk()}, $model->row());
	}


	/**
	 * Create a model by passing an result set
	 * @param $tableName
	 * @param $result
	 * @param $idColumn
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public static function byResult($tableName, $result, $idColumn='id')
	{
		return static::create($tableName, $result->$idColumn, $result);
	}


	/**
	 * Create legacy model
	 *
	 * @param ModelInterface $model
	 * @return \Model
	 * @throws
	 */
	public static function createLegacy(ModelInterface $model)
	{
		$class = \Model::getClassFromTable($model->getProviderName());

		if(!class_exists($class)) {
			throw new \RuntimeException(sprintf('No model class found for "%s"', $model->getProviderName()));
		}

		/** @var \Model $legacy */
		$legacy = new $class;
		$legacy->setRow($model->getPropertiesAsArray());

		return $legacy;
	}

}
