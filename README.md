contao-dcatools
==============

This library is a toolkit for working in the data container context of Contao.

It provides an object oriented API for manipulating the data container definitions, it provides event based permission
checking and operation rendering and it finally comes with tools which simplify working in the context of the legacy
DC_Drivers and the upcoming Dc_General.

Features
------

### Definition ###

The definition API following the currently (2013/11/26) published interfaces and naming conventions of the DC_General. So
there is no yet another API. But it also allows to easily change and modify the definition.

Example:
```php
<?php

$definition = Definition::getDataContainer('tl_example');
$definition->getPalette('default')->addProperty('foo');
$definition->getProperty('bar')->appendTo($definition->getPalette('default'), 'foo', Definition::BEFORE);
```

### Events ###

DcaTools uses the Event [Dispatcher](https://github.com/contao-community-alliance/event-dispatcher) for event handling.
At the moment there is event support added for permission checking and operation rendering

Events can be added to the dispatcher itself, assigned to `GLOBALS['TL_EVENTS']` or being defined in the dca file. It is
recommend to use the dca file for registering the events. Otherwise `DcaTools` does not detect that events are used and
could not dispatch them (or you have to do it for yourself).

Besides DcaTools allow to pass configuration to the event. This is useful for the shipped generic events. So you can
limit them. Example:

```php
<?php

// only allow the admin deleting elements
$GLOBALS['TL_DCA']['tl_example']['dcatools']['dcatools.tl_example.check-permission'][] = array (
	array('\DcaToos\Listener\DataContainerListener', 'isAdmin'), array('act' => array('delete', 'deleteAll'))
);
```

#### Event names ####

 * `dcatools.tl_example.initialize` called when DcaTools instance is initializd for the datacontainer
 * `dcatools.tl_example.check-permission` is called after initialisation
 * `dcatools.tl_example.operation.name` called when operation is rendered (only called if event is registered)
 * `dcatools.tl_example.global_operation.name` called when a global operation is rendered


#### Operation events ###

Operations are rendered following the MCV pattern. Then a operation is rendered the event is dispatched so you can
apply your own logic. Former Contao callbacks are triggered as well. By default they get called after other events. You
can use the priorities for changing the order.

```php
function(\DcaTools\Event\GenerateEvent $event)
{
	// do not render the operation
	if($event->getModel()->getId() == '2') {
		$event->getView()->setVisible(false);
	}

	// disable an icon
	if($event->getModel()->getId() == '3') {
		\DcaTools\Listener\OperationListener::disableIcon($event, array('value' => true));
	}
}
```

### Dc General ###

For making the work in the context of DC_General and the legacy DC drivers easier DcaTools provides some helpers to use
the same data structure no matter which DC you use.

#### Model Factory ####
```php
<?php

// DC_Table context
public function callbackOnSubmit($dc)
{
	// get the model \DcGeneral\Data\ModelInterface
	$model = \DcaTools\Data\ModelFactoy::byDc($dc);

	// get a legacy model
	$legacy = \DcaTools\Data\ModelFactory::createLegacy($model);
}
```

### Driver manager ###

Using the driver architecture of the DC_General makes you independent of the used data structure. If you want to develop
something which is independent of the data structure it is the best to use the drivers. In the context of the DC_Table
it is not possible to get the driver. That's why where is a driver manager which provides a `getDataProvider` method which
is used then no dc general is used.

```php

public function callbackOnSubmit($dc)
{
	/** @var \DcaTools\Data\DriverManagerInterface $manager */
	$manager = $GLOBALS['container']['dcatools.driver-manager'];
	$driver	 = $manager->getDataProvider('tl_example');
	$model	 = ModelFactory::byDc($dc);

	$model->setProperty('title', 'New title');
	$driver->save($model);
}
```

### Config builder ###

If you use the driver architecture to achieve data structure independency then you often have to create filters for the
dc general. There is a ConfigBuilder shipped with, which simplifies it:

```php
<?php

public function callbackOnSubmit($dc)
{
	/** @var \DcaTools\Data\DriverManagerInterface $manager */
	$manager = $GLOBALS['container']['dcatools.driver-manager'];
    $driver	 = $manager->getDataProvider('tl_example');

    $builder = \DcaTools\Data\ConfigBuilder::create($driver)
    	->fields('id', 'title', 'category')
    	->filterEquals('pid', 2)
    	->filterGreaterThen('tstamp', strtotime('yesterday'))
    	->sorting('category', \DcGeneral\DCGE::MODEL_SORTING_DESC)
    	->sorting('title');

    foreach($builder->fetchAll() as $model) {
    	// ...
    }
}
```

### Formatter ###

Formatting labels and values used can be quit difficult because Contao does not provide an access to the formatting
methods. DcaTolls fills the gap here.

```php
<?php

$formatter = Formatter::create('tl_example');

// getting the label will return the name instead of the label if label is not set
echo $formatter->getOperationLabel('edit');
echo $formatter->getPropertyLabel('title');
echo $formatter->getGlobalOperationLabel('all', 'Alle bearbeiten'); // pass a default value

// format and/or translate the value
echo $formatter->getPropertyValue('options');

// access to language array. With trailing slash the global array will be access otherwise the sub array of the data container
echo $formatter->translate('/MSC/yes');
echo $formatter->translate('title_legend');
```

Requirements
------

* At least Contao 3.1 is required.
* Requires contao-community-alliance/event-dispatcher
* Requires contao-community-alliance/dependency-container
* Requires metamodels/dc_general dev.contao3/1.0.x-dev

Install
------

You can install DcaTools using composer by installing netzmacht/contao-dcatools
