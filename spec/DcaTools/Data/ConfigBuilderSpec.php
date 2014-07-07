<?php

namespace spec\DcaTools\Data;

use ContaoCommunityAlliance\DcGeneral\Data\CollectionInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface;
use ContaoCommunityAlliance\DcGeneral\Data\DataProviderInterface;
use ContaoCommunityAlliance\DcGeneral\Data\DCGE;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigBuilderSpec extends ObjectBehavior
{

	function let(DataProviderInterface $dataProvider, ConfigInterface $config)
	{
		$dataProvider->getEmptyConfig()->willReturn($config);

		$this->beConstructedWith($dataProvider);
	}

    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Data\ConfigBuilder');
    }

	function it_sets_id(ConfigInterface $config)
	{
		$config->setId(1)->shouldBeCalled();

		$this->setId(1)->shouldReturn($this);
	}

	function it_sets_id_if_only_one_is_passed(ConfigInterface $config)
	{
		$config->getIds()->willReturn(null);
		$config->getId()->willReturn(null);
		$config->setId(1)->shouldBeCalled();

		$this->id(1)->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_ids_at_once(ConfigInterface $config)
	{
		$config->setIds(Argument::type('array'))->shouldBeCalled();

		$this->ids(array(1, 2))->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_ids(ConfigInterface $config)
	{
		$config->setIds(Argument::type('array'))->shouldBeCalled();

		$this->id(1)->shouldReturn($this);
		$this->id(2)->shouldReturn($this);
		$this->getConfig();
	}

	function it_sets_id_only(ConfigInterface $config)
	{
		$config->setIdOnly(true)->shouldBeCalled();

		$this->idOnly(true)->shouldReturn($this);
	}

	function it_adds_a_field(ConfigInterface $config)
	{
		$config->setFields(Argument::containing('test'))->shouldBeCalled();

		$this->field('test')->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_multiple_fields(ConfigInterface $config)
	{
		$config->setFields(Argument::containing('test'))->shouldBeCalled();
		$config->setFields(Argument::containing('test2'))->shouldBeCalled();

		$this->field('test')->shouldReturn($this);
		$this->field('test2')->shouldReturn($this);

		$this->getConfig();
	}

	function it_adds_fields_at_once(ConfigInterface $config)
	{
		$config->setFields(Argument::containing('test'))->shouldBeCalled();
		$config->setFields(Argument::containing('test2'))->shouldBeCalled();

		$this->fields('test', 'test2')->shouldReturn($this);

		$this->getConfig();
	}

	function it_adds_fields_at_once_by_array(ConfigInterface $config)
	{
		$config->setFields(Argument::containing('test'))->shouldBeCalled();
		$config->setFields(Argument::containing('test2'))->shouldBeCalled();

		$this->fields(array('test', 'test2'))->shouldReturn($this);

		$this->getConfig();
	}

	function it_adds_sorting(ConfigInterface $config)
	{
		$config->setSorting(Argument::type('array'))->shouldBeCalled();

		$this->sorting('test')->shouldReturn($this);
		$this->getConfig();
	}

	function it_sets_sorting(ConfigInterface $config)
	{
		$config->setSorting(Argument::type('array'))->shouldBeCalled();

		$this->setSorting(array('test' => 'ASC'))->shouldReturn($this);
		$this->getConfig();
	}

	function it_sets_amount(ConfigInterface $config)
	{
		$config->setAmount(Argument::type('integer'))->shouldBeCalled();

		$this->amount(10)->shouldReturn($this);
	}

	function it_sets_start(ConfigInterface $config)
	{
		$config->setStart(Argument::type('integer'))->shouldBeCalled();

		$this->start(10)->shouldReturn($this);
	}

	function it_adds_or_filter(ConfigInterface $config)
	{
		$filter = array (
			'operation' => 'OR',
			'children'  => array(1, 2)
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterOr(array(1, 2))->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_and_filter(ConfigInterface $config)
	{
		$filter = array (
			'operation' => 'AND',
			'children'  => array(1, 2)
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterAnd(array(1, 2))->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_in_filter(ConfigInterface $config)
	{
		$filter = array (
			'property' => 'test',
			'operation' => 'IN',
			'values'  => array(1, 2)
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterIn('test', array(1, 2))->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_like_filter(ConfigInterface $config)
	{
		$filter = array (
			'property'   => 'test',
			'operation' => 'LIKE',
			'value'     => 'val'
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterLike('test', 'val')->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_equals_filter(ConfigInterface $config)
	{
		$filter = array (
			'property'   => 'test',
			'operation' => '=',
			'value'     => 'val'
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterEquals('test', 'val')->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_greater_than_filter(ConfigInterface $config)
	{
		$filter = array (
			'property'   => 'test',
			'operation' => '>',
			'value'     => 3
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterGreaterThan('test', 3)->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_lesser_than_filter(ConfigInterface $config)
	{
		$filter = array (
			'property'   => 'test',
			'operation' => '<',
			'value'     => 3
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filterLesserThan('test', 3)->shouldReturn($this);
		$this->getConfig();
	}

	function it_adds_generic_filter(ConfigInterface $config)
	{
		$filter = array (
			'property'   => 'foo',
			'operation' => 'TEST',
			'test'     => 'val'
		);

		$config->setFilter(Argument::containing($filter))->shouldBeCalled();

		$this->filter('TEST', array('test' => 'val'), 'foo')->shouldReturn($this);
		$this->getConfig();
	}


	function it_builds_config(ConfigInterface $config)
	{
		$this->getConfig()->shouldReturn($config);
	}


	function it_fetchs_result(DataProviderInterface $dataProvider, ConfigInterface $config, ModelInterface $model)
	{
		$dataProvider->fetch($config)->willReturn($model);

		$this->fetch()->shouldReturn($model);
	}

	function it_fetches_all(DataProviderInterface $dataProvider, ConfigInterface $config, CollectionInterface $collection)
	{
		$dataProvider->fetchAll($config)->willReturn($collection);

		$this->fetchAll()->shouldReturn($collection);
	}

	function it_counts_record(DataProviderInterface $dataProvider, ConfigInterface $config)
	{
		$dataProvider->getCount($config)->willReturn(12);

		$this->getCount()->shouldReturn(12);
	}

	function it_deletes_collection(DataProviderInterface $dataProvider, ConfigInterface $config, CollectionInterface $collection)
	{
		$collection->getIterator()->willReturn(new \ArrayIterator(array(1, 2)));
		$dataProvider->fetchAll($config)->willReturn($collection);

		$dataProvider->delete(Argument::type('integer'))->shouldBeCalled();

		$this->id(1)->id(2);
		$this->delete();
	}


	function it_deletes_single_record(DataProviderInterface $dataProvider, ConfigInterface $config, CollectionInterface $collection)
	{
		$dataProvider->delete($config)->shouldBeCalled();

		$this->id(1);
		$this->delete();
	}

}
