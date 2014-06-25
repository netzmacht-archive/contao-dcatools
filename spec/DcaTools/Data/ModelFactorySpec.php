<?php

namespace spec\DcaTools\Data;

use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface;
use ContaoCommunityAlliance\DcGeneral\Data\DataProviderInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class_alias('Contao\System', 'System');
class_alias('Contao\Controller', 'Controller');
class_alias('Contao\Backend', 'Backend');
class_alias('Contao\DataContainer', 'DataContainer');
class_alias('Contao\Database\Result', 'Database\Result');

class ModelFactorySpec extends ObjectBehavior
{
	const CONTAINER_NAME = 'tl_test';
	const ID = 4;

	function let(
		EnvironmentInterface $environment,
		ContainerInterface $dataDefinition,
		DataProviderInterface $dataProvider,
		ModelInterface $model,
		ConfigInterface $config
	)
	{
		$dataDefinition->getName()->willReturn(static::CONTAINER_NAME);

		$dataProvider->getEmptyModel()->willReturn($model);
		$dataProvider->getEmptyConfig()->willReturn($config);

		$environment->getDataDefinition()->willReturn($dataDefinition);
		$environment->getDataProvider(Argument::any())->willReturn($dataProvider);

	}

    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Data\ModelFactory');
    }

	function it_converts_array_to_model(EnvironmentInterface $environment, ModelInterface $model)
	{
		$array  = array('id' => 4, 'name' => 'test');
		$this->createByArray($environment, $array)->shouldReturn($model);
	}

	function it_creates_model_by_id(EnvironmentInterface $environment, ModelInterface $model)
	{
		$this->createById($environment, static::ID, false)->shouldReturn($model);
	}

	function it_creates_model_by_id_loading_from_data_provider(EnvironmentInterface $environment, ModelInterface $model)
	{
		$this->createById($environment, static::ID, false)->shouldReturn($model);
	}

	function it_creates_model_from_legacy_data_container(
		EnvironmentInterface $environment,
		\DataContainer $dc,
		\Database\Result $result
	) {
		$dc->activeRecord = $result;

		$this->createByDc($environment, $dc)->shouldHaveType('ContaoCommunityAlliance\DcGeneral\Data\ModelInterface');
	}

	function it_creates_model_from_database_result(
		EnvironmentInterface $environment,
		\Database\Result $result
	) {
		$this->createByDatabaseResult($environment, $result)->shouldHaveType('ContaoCommunityAlliance\DcGeneral\Data\ModelInterface');
	}
}
