<?php

namespace spec\DcaTools\Dca\Legacy;

use DcaTools\Dca\Legacy;
use DcaTools\Dca\Legacy\Callback;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Dca\Legacy\Callback');
    }

	function it_returns_callbacks()
	{
		$reflect   = new \ReflectionClass('DcaTools\Dca\Legacy\Callback');
		$constants = $reflect->getConstants();

		$this->getCallbacks()->shouldReturn($constants);
	}

	function it_detects_container_global_button_as_single_callback()
	{
		$this->isSingleCallback(Callback::CONTAINER_GLOBAL_BUTTON)->shouldReturn(true);
	}

	function it_detects_container_paste_button_as_single_callback()
	{
		$this->isSingleCallback(Callback::CONTAINER_PASTE_BUTTON)->shouldReturn(true);
	}

	function it_detects_model_group_as_single_callback()
	{
		$this->isSingleCallback(Callback::MODEL_GROUP)->shouldReturn(true);
	}

	function it_detects_model_label_as_single_callback()
	{
		$this->isSingleCallback(Callback::MODEL_LABEL)->shouldReturn(true);
	}

	function it_detects_model_operation_as_single_callback()
	{
		$this->isSingleCallback(Callback::MODEL_OPERATION_BUTTON)->shouldReturn(true);
	}

	function it_detects_model_options_as_single_callback()
	{
		$this->isSingleCallback(Callback::MODEL_OPTIONS)->shouldReturn(true);
	}

	function it_detects_model_child_record_as_single_callback()
	{
		$this->isSingleCallback(Callback::MODEL_CHILD_RECORD)->shouldReturn(true);
	}

	function it_detects_property_input_field_as_single_callback()
	{
		$this->isSingleCallback(Callback::PROPERTY_INPUT_FIELD)->shouldReturn(true);
	}

	function it_detects_property_input_field_get_wizard_as_single_callback()
	{
		$this->isSingleCallback(Callback::PROPERTY_INPUT_FIELD_GET_WIZARD)->shouldReturn(true);
	}
	
	function it_detects_multiple_callbacks_not_as_single_callbacks()
	{
		$this->isSingleCallback(Callback::CONTAINER_GET_BREADCRUMB)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_HEADER)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_ON_COPY)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_ON_CUT)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_ON_DELETE)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_ON_LOAD)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_ON_SUBMIT)->shouldReturn(false);
		$this->isSingleCallback(Callback::CONTAINER_SUBMIT_BUTTON)->shouldReturn(false);

		// property callbacks
		$this->isSingleCallback(Callback::PROPERTY_ON_SAVE)->shouldReturn(false);
		$this->isSingleCallback(Callback::PROPERTY_ON_LOAD)->shouldReturn(false);
	}

	function it_gets_callback_method_name()
	{
		$this->getMethodName(Callback::PROPERTY_INPUT_FIELD_GET_WIZARD)->shouldReturn('propertyInputFieldGetWizard');
	}

	function it_gets_dca_paths()
	{
		$this->getDcaPath(Callback::CONTAINER_GET_BREADCRUMB, '')->shouldReturn(false);
		$this->getDcaPath(Callback::CONTAINER_GLOBAL_BUTTON, 'test')->shouldReturn('list/global_operations/test/button_callback');
		$this->getDcaPath(Callback::CONTAINER_HEADER)->shouldReturn('list/sorting/header_callback');
		$this->getDcaPath(Callback::CONTAINER_ON_COPY)->shouldReturn('config/oncopy_callback');
		$this->getDcaPath(Callback::CONTAINER_ON_CUT)->shouldReturn('config/oncut_callback');
		$this->getDcaPath(Callback::CONTAINER_ON_DELETE)->shouldReturn('config/ondelete_callback');
		$this->getDcaPath(Callback::CONTAINER_ON_LOAD)->shouldReturn('config/onload_callback');
		$this->getDcaPath(Callback::CONTAINER_ON_SUBMIT)->shouldReturn('config/onsubmit_callback');
		$this->getDcaPath(Callback::CONTAINER_PASTE_BUTTON)->shouldReturn('list/sorting/paste_button_callback');
		$this->getDcaPath(Callback::CONTAINER_PASTE_ROOT_BUTTON, 'test')->shouldReturn('list/sorting/paste_button_callback');
		$this->getDcaPath(Callback::CONTAINER_SUBMIT_BUTTON, 'test')->shouldReturn('edit/buttons_callback');

		// model callbacks
		$this->getDcaPath(Callback::MODEL_CHILD_RECORD, 'test')->shouldReturn('list/sorting/child_record_callback');
		$this->getDcaPath(Callback::MODEL_GROUP, 'test')->shouldReturn('list/label/group_callback');
		$this->getDcaPath(Callback::MODEL_LABEL, 'test')->shouldReturn('list/label/label_callback');
		$this->getDcaPath(Callback::MODEL_OPERATION_BUTTON, 'test')->shouldReturn('list/operations/test/button_callback');
		$this->getDcaPath(Callback::MODEL_OPTIONS, 'test')->shouldReturn('fields/test/options_callback');

		// property callbacks
		$this->getDcaPath(Callback::PROPERTY_INPUT_FIELD, 'test')->shouldReturn('fields/test/input_field_callback');
		$this->getDcaPath(Callback::PROPERTY_INPUT_FIELD_GET_WIZARD, 'test')->shouldReturn('fields/test/wizard');
		$this->getDcaPath(Callback::PROPERTY_ON_SAVE, 'test')->shouldReturn('fields/test/save_callback');
		$this->getDcaPath(Callback::PROPERTY_ON_LOAD, 'test')->shouldReturn('fields/test/load_callback');
	}
}
