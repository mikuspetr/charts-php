**ChartsPhp** is a library that makes an interface between JavaScript library **Chart.js** and **PHP**. You can draw charts directly from PHP.
## Prerequisites
- Chart.js - install Chart.js by NPM, CDN, ... [Installation instructions](https://www.chartjs.org/docs/latest/getting-started/installation.html)
- Composer - you can download from [getcomposer.org](http://getcomposer.org).

## Install ChartsPhp by composer
```
composer require mikuspetr/charts-php
```

## Exaple
```php
use ChartsPhp\ChartsPhp;

$chartType = 'bar';

// The arrays structure copies data object structure in Chart.js
$labels = ['January', 'February', 'March', 'April', 'May', 'June',];
$datasets = [
    [
        'label' => 'apples',
        'data' => [10, 12, 8, 25, 32, 20]
    ],
    [
        'label' => 'bananas',
        'data' => [11, 4, 15, 17, 10, 23]
    ]
];
$options = ['aspectRatio' => 3];


// use constructor to create new chart
$barChart = ChartsPhp::createChart($chartType, $labels, $datasets, $options);

// render HTML canvas
echo $barChart->renderHtml();

// render JavaScript that draw chart in canvas (require Chart.js)
echo $barChart->renderScript();


// use add methods to create new chart
$lineChart = ChartsPhp::createChart('line')
    ->setLabels($labels)
    ->addDatasets($datasets)
    ->setOptions(['aspectRatio' => 4, 'cubicInterpolationMode' => 'monotone']);

// render both, HTML and JavaScript
echo $lineChart;
```