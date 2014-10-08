<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Legacy;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Backend\AddToUrlEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\DataDefinition\Definition\Contao2BackendViewDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\DecodePropertyValueForWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\EncodePropertyValueFromWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetParentHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPasteButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ModelToLabelEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultCollection;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\BasicDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\DcGeneral;
use ContaoCommunityAlliance\DcGeneral\Event\PostDeleteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostDuplicateModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPasteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\CreateDcGeneralEvent;
use DcaTools\Data\ModelFactory;
use DcaTools\View\ViewHelper;

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
	 * @var ViewHelper
	 */
    private $viewHelper = array();

    /**
	 * @param \ContaoCommunityAlliance\DcGeneral\DcGeneral $dcGeneral
	 * @param \DcaTools\View\ViewHelper $viewHelper
	 */
    public function __construct(DcGeneral $dcGeneral, ViewHelper $viewHelper)
    {
        $this->dcGeneral  = $dcGeneral;
        $this->viewHelper = $viewHelper;
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
        $event         = new GetGlobalButtonEvent($environment);

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
        $event         = new GetParentHeaderEvent($environment);

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
        $sourceModel = ModelFactory::createByDc($environment, $dc);
        $oldModel    = ModelFactory::createById($environment, $id);
        $event         = new PostDuplicateModelEvent($environment, $sourceModel, $oldModel);

        $environment->getEventPropagator()->propagate($event::NAME, $event);
    }

    /**
	 * @param \DataContainer $dc
	 */
    public function containerOnCut(\DataContainer $dc)
    {
        $environment = $this->dcGeneral->getEnvironment();
        $model       = ModelFactory::createByDc($environment, $dc);
        $event         = new PostPasteModelEvent($environment, $model);

        $environment->getEventPropagator()->propagate($event::NAME, $event);
    }

    /**
	 * @param \DataContainer $dc
	 * @param $undoId
	 */
    public function containerOnDelete(\DataContainer $dc, $undoId)
    {
        $environment = $this->dcGeneral->getEnvironment();
        $model         = ModelFactory::createByDc($environment, $dc);
        $model->setMeta('last-undo-id', $undoId);

        $event         = new PostDeleteModelEvent($environment, $model);

        $environment->getEventPropagator()->propagate($event::NAME, $event, array($environment->getDataDefinition()->getName()));
    }

    /**
	 *
	 */
    public function containerOnLoad()
    {
        $environment = $this->dcGeneral->getEnvironment();
        $event         = new CreateDcGeneralEvent($this->dcGeneral);

        $environment->getEventPropagator()->propagate($event::NAME, $event);
    }

    /**
	 * @param \DataContainer $dc
	 * @param $row
	 * @param $dataContainerName
	 * @param $isCircular
	 * @param $containedIds
	 * @param null $previous
	 * @param null $next
	 * @return string
	 */
    public function containerPasteButton(\DataContainer $dc, $row, $dataContainerName, $isCircular, $containedIds, $previous, $next)
    {
        $environment = $this->dcGeneral->getEnvironment();
        $propagator  = $environment->getEventPropagator();
        $model         = ModelFactory::createByArray($environment, $row, $dataContainerName);
        $collection  = new DefaultCollection();
        $clipboard   = $environment->getClipboard();

        foreach ($containedIds as $id) {
            $collection->push(ModelFactory::createById($environment, $id, $dataContainerName, false));
        }

        /** @var AddToUrlEvent $urlAfter */
        $add2UrlAfter = sprintf('act=copy&mode=2&pid=%s&id=%s', $model->getProperty('pid'), $model->getId());
        $urlAfter     = $propagator->propagate(ContaoEvents::BACKEND_ADD_TO_URL, new AddToUrlEvent($add2UrlAfter));

        /** @var AddToUrlEvent $urlInto */
        $add2UrlInto = sprintf('act=copy&mode=1&pid=%s&id=%s', $model->getProperty('pid'), $model->getId());
        $urlInto     = $propagator->propagate(ContaoEvents::BACKEND_ADD_TO_URL,    new AddToUrlEvent($add2UrlInto));

        $buttonEvent = new GetPasteButtonEvent($environment);
        $buttonEvent
            ->setModel($model)
            ->setCircularReference($isCircular)
            ->setPrevious(ModelFactory::createById($environment, $previous, $dataContainerName, false))
            ->setNext(ModelFactory::createById($environment, $next, $dataContainerName, false))
            ->setHrefAfter($urlAfter->getUrl())
            ->setHrefInto($urlInto->getUrl())
            // Check if the id is in the ignore list.
            ->setPasteAfterDisabled($clipboard->isCut() && $isCircular)
            ->setPasteIntoDisabled($clipboard->isCut() && $isCircular)
            ->setContainedModels($collection);

        $propagator->propagate(
            $buttonEvent::NAME,
            $buttonEvent,
            array($environment->getDataDefinition()->getName())
        );

        $buffer  = $this->viewHelper->renderPasteAfterButton($buttonEvent);

        if ($environment->getDataDefinition()->getBasicDefinition()->getMode() == BasicDefinitionInterface::MODE_HIERARCHICAL) {
            $buffer .= ' ' . $this->viewHelper->renderPasteIntoButton($buttonEvent);
        }

        return $buffer;
    }

    /**
	 * @param \DataContainer $dc
	 */
    public function containerOnSubmit(\DataContainer $dc)
    {
        $environment = $this->dcGeneral->getEnvironment();
        $model         = ModelFactory::createByDc($environment, $dc);
        $event         = new PostPersistModelEvent($environment, $model);

        $environment->getEventPropagator()->propagate($event::NAME, $event);
    }

    /**
	 * @param array $row
	 * @return string
	 */
    public function modelChildRecord($row)
    {
        $environment = $this->dcGeneral->getEnvironment();
        $model         = ModelFactory::createByArray($environment, $row);
        $event         = new ParentViewChildRecordEvent($environment, $model);

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
        $model         = ModelFactory::createByArray($environment, $row);
        $event         = new GetGroupHeaderEvent($environment, $model, $groupField, $value, $groupMode);

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
        $model         = ModelFactory::createByArray($environment, $row);
        $event         = new ModelToLabelEvent($environment, $model);

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
        $model         = ModelFactory::createByArray($environment, $row, $dataContainerName);
        $previous    = $previous ? ModelFactory::createById($environment, $previous, $dataContainerName, false) : null;
        $next        = $next ? ModelFactory::createById($environment, $next, $dataContainerName, false) : null;

        /** @var Contao2BackendViewDefinitionInterface $definition */
        $definition = $environment
            ->getDataDefinition()
            ->getDefinition(Contao2BackendViewDefinitionInterface::NAME);

        $command = $definition
            ->getModelCommands()
            ->getCommandNamed($key);

        $buffer = $this->viewHelper->renderCommand($command, $model, $circularReference, $childRecordIds, $previous, $next);

        return $buffer;
    }

    /**
	 * @param \DataContainer $dc
	 * @return array
	 */
    public function modelOptionsCallback(\DataContainer $dc)
    {
        $environment = $this->dcGeneral->getEnvironment();
        $model       = ModelFactory::createByDc($environment, $dc);
        $event         = new GetPropertyOptionsEvent($environment, $model);

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
        $model         = ModelFactory::createByDc($environment, $dc);
        $event         = new BuildWidgetEvent($environment, $model, $property);

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
        $model         = ModelFactory::createByDc($environment, $dc);
        $event         = new DecodePropertyValueForWidgetEvent($environment, $model);
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
        $model         = ModelFactory::createByDc($environment, $dc);
        $values      = new PropertyValueBag($dc->activeRecord->row());
        $event         = new EncodePropertyValueFromWidgetEvent($environment, $model, $values);
        $event
            ->setValue($value)
            ->setProperty($dc->field);

        $environment->getEventPropagator()->propagate($event::NAME, $event);

        return $event->getValue();
    }

}
