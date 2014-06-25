<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Callback;

use ContaoCommunityAlliance\DcGeneral\Contao\Callback\Callbacks;
use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\DecodePropertyValueForWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\EncodePropertyValueFromWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetParentHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPasteButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ModelToLabelEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultCollection;
use ContaoCommunityAlliance\DcGeneral\DcGeneral;
use ContaoCommunityAlliance\DcGeneral\Event\PostDeleteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostDuplicateModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPasteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\CreateDcGeneralEvent;
use DcaTools\Data\ModelFacade;


class CallbackDispatcher
{
	/**
	 * @var DcGeneral
	 */
	private $dcGeneral;

	/**
	 * @var array
	 */
	private $callbacks = array();


	/**
	 * @param $dcGeneral
	 */
	function __construct(DcGeneral $dcGeneral=null)
	{
		$this->dcGeneral = $dcGeneral;
	}


	/**
	 * @param $tableName
	 * @param $eventName
	 * @param $callback
	 * @return $this
	 */
	public function registerCallback($tableName, $eventName, $callback)
	{
		$this->callbacks[$tableName][$eventName][] = $callback;

		return $this;
	}


	/**
	 * @param key
	 * @param $href
	 * @param $label
	 * @param $title
	 * @param $class
	 * @param $attributes
	 * @return string
	 */
	public function containerGlobalButton($key, $href, $label, $title, $class, $attributes)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$event 		 = new GetGlobalButtonEvent($environment);

		$event
			->setKey($key)
			->setHref($href)
			->setLabel($label)
			->setTitle($title)
			->setClass($class)
			->setAttributes($attributes);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getHtml();
	}


	/**
	 * @param $additional
	 * @return array
	 */
	public function containerHeader($additional)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$event 		 = new GetParentHeaderEvent($environment);

		$event->setAdditional($additional);
		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getAdditional();
	}


	/**
	 * @param $id
	 * @param \DataContainer $dc
	 */
	public function containerOnCopy($id, \DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$sourceModel = ModelFacade::byDc($environment, $dc);
		$oldModel    = ModelFacade::byId($environment, $id);
		$event 		 = new PostDuplicateModelEvent($environment, $sourceModel, $oldModel);

		$environment->getEventPropagator()->propagate($event::NAME, $event);
	}


	/**
	 * @param \DataContainer $dc
	 */
	public function containerOnCut(\DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model       = ModelFacade::byDc($environment, $dc);
		$event 		 = new PostPasteModelEvent($environment, $model);

		$environment->getEventPropagator()->propagate($event::NAME, $event);
	}


	/**
	 * @param \DataContainer $dc
	 * @param $undoId
	 */
	public function containerOnDelete(\DataContainer $dc, $undoId)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::byDc($environment, $dc);
		$model->setMeta('last-undo-id', $undoId);

		$event 		 = new PostDeleteModelEvent($environment, $model);

		$environment->getEventPropagator()->propagate($event::NAME, $event, array($environment->getDataDefinition()->getName()));
	}


	/**
	 * @param \DataContainer $dc
	 */
	public function containerOnLoad(\DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::byDc($environment, $dc);
		$event 		 = new CreateDcGeneralEvent($environment, $model);

		$environment->getEventPropagator()->propagate($event::NAME, $event);
	}


	/**
	 * @param \DataContainer $dc
	 * @param $row
	 * @param $dataContainerName
	 * @param $circularReference
	 * @param $containedIds
	 * @param null $previous
	 * @param null $next
	 * @return string
	 */
	public function containerPasteButton(\DataContainer $dc, $row, $dataContainerName, $circularReference, $containedIds, $previous=null, $next=null)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::ByArray($environment, $row);
		$event       = new GetPasteButtonEvent($environment);
		$collection  = new DefaultCollection();

		foreach($containedIds as $id) {
			$collection->push(ModelFacade::byId($environment, $id));
		}

		$environment->getClipboard()->getContainedIds();
		$event
			->setModel($model)
			->setCircularReference($circularReference)
			->setContainedModels($collection);

		if($previous) {
			$event->setPrevious(ModelFacade::byId($environment, $previous));
		}

		if($next) {
			$event->setNext(ModelFacade::byId($environment, $previous));
		}

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getHtml();
	}


	/**
	 * @param \DataContainer $dc
	 */
	public function containerOnSubmit(\DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::byDc($environment, $dc);
		$event 		 = new PostPersistModelEvent($environment, $model);

		$environment->getEventPropagator()->propagate($event::NAME, $event);
	}


	/**
	 * @param array $row
	 * @return string
	 */
	public function modelChildRecord($row)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::ByArray($environment, $row);
		$event		 = new ParentViewChildRecordEvent($environment, $model);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getHtml();
	}


	/**
	 * @param $groupField
	 * @param $groupMode
	 * @param $value
	 * @param $row
	 * @return string
	 */
	public function modelGroup($groupField, $groupMode, $value, $row)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::ByArray($environment, $row);
		$event		 = new GetGroupHeaderEvent($environment, $model, $groupField, $value, $groupMode);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getValue();
	}


	/**
	 * @param $row
	 * @param $label
	 * @param \DataContainer $dc
	 * @param null $arguments
	 * @return string
	 */
	public function modelLabel($row, $label, \DataContainer $dc, $arguments=null)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::ByArray($environment, $row);
		$event		 = new ModelToLabelEvent($environment, $model);

		$event->setLabel($label);
		$event->setArgs($arguments);

		$environment->getEventPropagator()->propagate($event::NAME, $event, array($environment->getDataDefinition()->getName()));

		return $event->getLabel();
	}


	/**
	 * @param $key
	 * @param $row
	 * @param $href
	 * @param $label
	 * @param $title
	 * @param $icon
	 * @param $attributes
	 * @param $dataContainerName
	 * @param null $rootEntries
	 * @param null $childRecordIds
	 * @param null $circularReference
	 * @param null $previous
	 * @param null $next
	 * @return string
	 */
	public function modelOperationButton($key, $row, $href, $label, $title, $icon, $attributes, $dataContainerName, $rootEntries=null, $childRecordIds=null, $circularReference=null, $previous=null, $next=null)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model 		 = ModelFacade::ByArray($environment, $row);
		$event		 = new GetOperationButtonEvent($environment);

		/** @var Contao2BackendViewDefinitionInterface $definition */
		$definition = $environment
			->getDataDefinition()
			->getDefinition(Contao2BackendViewDefinitionInterface::NAME);

		$command = $definition
			->getModelCommands()
			->getCommandNamed($key);

		$event
			->setKey($key)
			->setCommand($command)
			->setObjModel($model)
			->setAttributes($attributes)
			->setLabel($label)
			->setTitle($title)
			->setHref($href)
			->setChildRecordIds($childRecordIds)
			->setCircularReference($circularReference)
			->setPrevious($previous)
			->setNext($next);

		$environment->getEventPropagator()->propagate($event::NAME, $event, array($dataContainerName, $key));

		return $event->getHtml();
	}


	/**
	 * @param \DataContainer $dc
	 * @return array
	 */
	public function modelOptionsCallback(\DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model       = ModelFacade::byDc($environment, $dc);
		$event		 = new GetPropertyOptionsEvent($environment, $model);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getOptions();
	}


	/**
	 * @param $property
	 * @param \DataContainer $dc
	 * @return \Widget
	 */
	public function propertyInputField($property, \DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model		 = ModelFacade::byDc($environment, $dc);
		$event 		 = new BuildWidgetEvent($environment, $model, $property);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getWidget();
	}


	/**
	 * @param $value
	 * @param \DataContainer $dc
	 * @return mixed
	 */
	public function propertyOnLoad($value, \DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model		 = ModelFacade::byDc($environment, $dc);
		$event 		 = new DecodePropertyValueForWidgetEvent($environment, $model);
		$event
			->setValue($value)
			->setProperty($dc->field);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getValue();
	}


	/**
	 * @param $value
	 * @param \DataContainer $dc
	 * @return mixed
	 */
	public function propertyOnSave($value, \DataContainer $dc)
	{
		$environment = $this->dcGeneral->getEnvironment();
		$model		 = ModelFacade::byDc($environment, $dc);
		$event 		 = new EncodePropertyValueFromWidgetEvent($environment, $model);
		$event
			->setValue($value)
			->setProperty($dc->field);

		$environment->getEventPropagator()->propagate($event::NAME, $event);

		return $event->getValue();
	}

}