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


class ModelFacade
{
	/**
	 * @param EnvironmentInterface $environment
	 * @param \DataContainer $dc
	 * @return ModelInterface
	 */
	public static function byDc(EnvironmentInterface $environment, \DataContainer $dc)
	{
		return new ActiveRecordModelDecorator($dc->activeRecord, $environment->getDataDefinition()->getName());
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param $id
	 * @return ModelInterface
	 */
	public static function byId(EnvironmentInterface $environment, $id)
	{
		return ConfigBuilder::create($environment)->setId($id)->fetch();
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param $row
	 * @return ModelInterface
	 */
	public static function ByArray(EnvironmentInterface $environment, $row)
	{
		$model = $environment->getDataProvider()->getEmptyModel();
		$model->setPropertiesAsArray($row);
		$model->setId($row['id']);

		return $model;
	}

} 