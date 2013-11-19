<?php

namespace DcaTools\Data;

use DcGeneral\DC_General;

class ModelFactory
{


	public static function create($tableName, $id, $row=null)
	{
		/** @var \DcaTools\Data\DriverManagerInterface $manager */
		$manager = $GLOBALS['container']['dcatools.driver-manager'];
		$driver  = $manager->getDataProvider($tableName);

		if($row)
		{
			if(is_object($row))
			{
				$row = $row->row();
			}

			$model = $driver->getEmptyModel();
			$model->setPropertiesAsArray($row);
			$model->setId($id);
		}
		else {
			$model = ConfigBuilder::create($driver)->setId($id)->fetch();
		}

		return $model;
	}


	public static function byDc($dc)
	{
		if($dc instanceof DC_General)
		{
			$model = $dc->getEnvironment()->getCurrentModel();

			if($model && $model->hasProperties())
			{
				return $model;
			}
			else {
				return static::create($dc->getName(), $dc->getId());
			}
		}

		return static::create($dc->table, $dc->id, $dc->activeRecord);
	}


	public static function byContaoModel(\Model $model)
	{
		return static::create($model->getTable(), $model->{$model->getPk()}, $model->row());
	}


} 