Yii2 Intercooler integration
===

Easy integration of Intercooler.js (the easy to use Ajax handler) into Yii2 framework. Provides bunch of ready to use widgets with preddefined Intercooler requirements.

> See Intercooler.js official docs on [http://intercoolerjs.org](http://intercoolerjs.org)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require dlds/yii2-intercooler
```

or add

```
"dlds/yii2-intercooler": "~2.0"
```

to the `require` section of your `composer.json` file.

## Module Base Class

Base module class **Intercooler** holds all options for Intercooler required html element attributes. Also handles registering of required js files using **IntercoolerAssets**. It is usually init through some of included widgets.

### Response Headers

Using base class static methods you are allowed to set specific intercooler headers.

#### X-IC-Redirect
To be able to redirect user to new url after intercooler request is done you have to set redirect header in server side.

```
Intercooler::doRedirect('/new-destination-url');
```

#### X-IC-Refresh
To refresh intercooler elements you can set refresh header on your server side and tell intercooler which elements should be refreshed.

```
Intercooler::doRefresh([
	'/foo/bar'
]);
```
> See what '/foo/bar' stand for in [official docs](http://intercoolerjs.org/docs.html#dependencies)

#### X-IC-Remove
To remove targetted element you have to set remove header on server side.

```
Intercooler::doRemove();
```

## Widgets

Integration comes with buch of ready made modules based on intercooler behavior.

### AjaxBlock

Helper which renders appropriate html element with required intercooler attributes.

```
AjaxBlock::begin([
	'id' => 'my-ic-widget',
	'wrapper' => 'button',
	'options' => ['class' => 'text-gray']
	'intercooler' => [
		'url' => '/my-custom-url',
		'target' => '#my-target-element',
	],
]);

// ... custom content

AjaxBlock::end();
```

> For more widget options see [AjaxBlock](https://github.com/dlds/yii2-intercooler/blob/master/src/widgets/AjaxBlock.php) class documentation.
 
### InfiniteList

Renderes Infinite ListView widget.

```
InfiniteList::widget([
	'id' => 'my-ic-infinite-list,
	// 	...
	//	standart ListView widget options
	// 	...
	'layout' => "{indicatorRefresh}<div class=\"items\">{items}{pager}</div>\n",
	'partialLayout' => "{items}{pager}\n",
	'intercooler' => [
		'url' => '/my-ic-feed-url,
		'type' => \dlds\intercooler\Intercooler::RQ_TYPE_SRC,
	],
	'pager' => [
    	'class' => InfiniteListPager::className(),
    ],
]);
```

> For more widget options see [InfiniteList](https://github.com/dlds/yii2-intercooler/blob/master/src/widgets/infinite/InfiniteList.php) class documentation.
