contao-dcatools
===============

This Library allow to access Contao DCA arrays providing an object oriented API. At the moment there are only methods
provided accessing and manipulating palettes configurations. Other features will follow.

Features
------

* Accessing fields, palettes, subpalettes, selectors using an api
* Manipulating data container array
* (Auto-)updating the definitions
* extending feature for every level

Requirements
------

* Only tested with Contao 3.1. Probably works with older versions as well, because no Components are required
* Requires symfony/event-dispatcher


Install
------

Using composer and install netzmacht/contao-dcatools


The API
----

### Basic usage

To access a DataContainer use the `Netzmacht\DcaTools\DcaTools` class. I use the vendor suffix to avoid issues with
the bug (no they call it feature) that the vendor namespace is removed.

DcaTools does not load the data container so far. So You have to call it in your custom module instead.

```php
$this->loadDataContainer('tl_content');

// get the data container
$objDca = Netzmacht\DcaTools\DcaTools::getDataContainer('tl_content');

// get palette and return it as string, generates palette as Contao expect them
$objDca->getPalette('default')->toString();

// generates an array of palette in the format MetaPalettes requires
$objDca->getPalette('default')->toArray();

```

### Palette manipulation

In General there are 2 ways how DcaTools work. You can switch on the *auto update*. Then every change is immediately
rendered to the data container. Without enabled auto update you have to trigger `updateDefinition` after doing your
changes. This method is provided on each level so you can update the changes as deep as you want.

```php
// switch on auato update, usually it is disabled
Netzmacht\DcaTools\DcaTools::doAutoUpdate(true);

// get an existing palette
$objPalette = $objDca->getPalette('text');

// add a new palette, name and data container are required
$objPalette = new Netzmacht\DcaTools\Palette('custom', $objDca);

// add a new palette using addPalette of DataContainer
// addPalette return instance of DataContainer, so you have to get the new palette by calling getPalette instead
$objDca->addPalette('custom');
$objPalette = $objDca->getPalette('custom');

// adding a new field
// new fields have to exist in the data container, so they have to be created there

// existing field, the 2nd parameter is the legend
$objPalette->addField('type', 'config');
$objPalette->getLegend('config')->addField('type');

// new field
$objPalette->addField($objDca->createField('new'), 'config');
$objPalette->getLegend('config')->addField($objDca->createField('new'));

```

DcaTools also provide the possibility to extend existing palettes. One Palette can inherit multiple palettes. This is
no real extending. Every changes which happens to the parent after being used for extending, are not set by the
child palette.

```php

$objParent = $objDca->getPalette('text');

$objNew = new Palette('new', $objDca);
$objNew->extend($objParent);

// manipulate palette, if possible the instance itself is given back so it possible to create such path calls
$objNew->removeLegend('invisible')->removeField('guests');

// this does not affect $objNew
$objParent->removePalette('expert');

// store new palette in the data container array, required if auto update is disabled
$objNew->updateDefinition();

```

### Accessing fields

Fields are used to each palette/subpalette/legend entry and are used for the data container fields as well. Technically
each of them are a unqiue object. This is required so a field can know it's parent. Every level provides methods for
accessing fields. Palettes do this as well even though they do not even have fields. Palette will try to find the fields
in the assigned palettes.

Fields can only be created in the the datacontainer so far. Trying to get a field which does not exists will throw an
`\RuntimeException`.

```php

// get all fields which are in a palette
$arrFields = $objPalette->getFields();

// get all fields which are in a legend
$arrFields = $objPalette->getLegend('expert')->getFields();

// move field to new position
$objExpertLegend = $objPalette->getLegend('expert');
$objTypeLegend = $objPalette->getLegend('type');

// move guest field behind cssID
$objExpertLegend->getField('guest')->appendAfter('cssID');

// move cssID before guest
$objExpertLegend->getField('cssID')->appendBefore('guest');

// move to a new parent
$objExpertLegend->getField('guest')->appendTo($objTypeLegend);

$objField = $objExpertLegend->getField('guest');
$objExpertLegend->removeField('guest');
$objTypeLegend->addField($objField);

```

### Active fields

One goal of the development of DcaTools was to know which fields are active using a current data record. So it come
with the feature of listing to a data record. It does not detect palette switching at the moment, only sub palette
adding.

DcaTools provides support for `Model`, `Model\Collection`, `Database\Result` of Contao and also supports the
`ModelInterface` of the DC_General.

```php

// register the data record
$objRecord = \Database::getInstance()->prepare('SELECT * FROM tl_content WHERE id=?')->limit(1)->execute('12');

$objDca->setRecord($objRecord);

// get all fields which are shown in the palette, subpalettes are included if activated
$arrFields = $objPalette->getActiveFields();

// get all subpalettes which are activated
$arrSubPalettes = $objPalette->getActiveSubPaletes();

// get subpalette triggered by a field
$objField = $objPalette->getField('addImage');

if($objField->isSelector() && $objField->hasActiveSubPalette())
{
	$objSubPalette = $objPalette->getField('addImage')->getActiveSubPalette();
	$arrFields = array_keys($objSubPalette->getFields());
}

```

### And a lot more

Read the source to understand all possibilities of DcaTools.