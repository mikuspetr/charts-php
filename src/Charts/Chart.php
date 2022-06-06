<?php
namespace ChartsPhp\Charts;

abstract class Chart implements ChartInterface
{
    const MULTICOLOR_DATASET_TYPES = ['pie', 'doughnut', 'polarArea'];
    const COLORS = [
        ['border' => 'rgba(255, 99, 132, 1)', 'background' => 'rgba(255, 99, 132, 0.8)',],
        ['border' => 'rgba(54, 162, 235, 1)', 'background' => 'rgba(54, 162, 235, 0.8)',],
        ['border' => 'rgba(255, 206, 86, 1)', 'background' => 'rgba(255, 206, 86, 0.8)',],
        ['border' => 'rgba(75, 192, 192, 1)', 'background' => 'rgba(75, 192, 192, 0.8)',],
        ['border' => 'rgba(153, 102, 255, 1)', 'background' => 'rgba(153, 102, 255, 0.8)',],
        ['border' => 'rgba(255, 159, 64, 1)', 'background' => 'rgba(255, 159, 64, 0.8)',],
        ['border' => 'rgba(163, 12, 213, 1)', 'background' => 'rgba(163, 12, 213, 0.8)',],
        ['border' => 'rgba(13, 102, 182, 1)', 'background' => 'rgba(13, 102, 182, 0.8)',],
        ['border' => 'rgba(183, 122, 25, 1)', 'background' => 'rgba(183, 122, 25, 0.8)',],
        ['border' => 'rgba(13, 102, 105, 1)', 'background' => 'rgba(13, 102, 105, 0.8)',],
        ['border' => 'rgba(53, 128, 125, 1)', 'background' => 'rgba(53, 128, 125, 0.8)',]
    ];
    const DISPLAY_WARNINGS = true;

    protected $type;
    protected $labels = [];
    protected $datasets = [];
    protected $options;
    protected static $instancesCounter = 0;
    protected $id;
    protected $wrapperHtmlAttributes = [];
    protected $colors = self::COLORS;
    protected $warnings = [];
    protected $displayWarnings;

    public function __construct(string $type, array $labels = [], array $datasets = [], array $options = [], bool $displayWarnings = self::DISPLAY_WARNINGS)
    {
        $this->type = $type;
        self::$instancesCounter++;
        $this->id = $this->type.'Chart'.self::$instancesCounter;
        $this->labels = $labels;
        $this->options = $options;
        $this->addDatasets($datasets);
        $this->displayWarnings = $displayWarnings;
    }

    protected function validateDataset(array &$dataset)
    {
        $this->validateData($dataset);
        $this->validateColors($dataset);
    }

    protected function validateColors(array &$dataset)
    {
        $color = $this->getNextColor();
        $dataset['borderColor'] = $dataset['borderColor'] ?? $color['border'];
        $dataset['backgroundColor'] = $dataset['backgroundColor'] ?? $color['background'];
    }

    protected function validateData($dataset)
    {
        if(!isset($dataset['data'][0]))
        {
            throw new \Exception('data array is missing in dataset');
        }
    }

    public function setLabels(array $labels)
    {
        $this->labels = $labels;
        return $this;
    }

    public function addDataset(array $dataset)
    {
        $this->validateDataset($dataset);
        array_push($this->datasets, $dataset);
        return $this;
    }

    public function addDatasets(array $datasets)
    {
        foreach($datasets as $dataset)
        {
            $this->addDataset($dataset);
        }
        return $this;
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function addWrapperHtmlAttribute(string $name, string $value)
    {
        $this->wrapperHtmlAttributes[$name] = $value;
        return $this;
    }
    public function addClass(string $class)
    {
        $this->addWrapperHtmlAttribute('class', $class);
        return $this;
    }
    public function disableWarnings(): void
    {
        $this->displayWarnings = false;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getLabels()
    {
        return $this->labels;
    }
    public function getDatasets()
    {
        return $this->datasets;
    }
    public function getOptions()
    {
        return $this->options !== [] ? $this->options : null;
    }
    public function getData()
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'data' => [
                'labels' => $this->getLabels(),
                'datasets' => $this->getDatasets(),
            ],
            'options' => $this->getOptions(),
        ];
    }

    protected function getNextColor()
    {
        if($this->colors === [])
        {
            $this->colors = self::COLORS;
        }
        return array_shift($this->colors);
    }

    public function setOption(string $name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }

    public function setAspectRatio(int $value)
    {
        $this->options['aspectRatio'] = $value;
        return $this;
    }
    public function displayLegend(bool $display = true)
    {
        $this->options['plugins']['legend']['display'] = $display;
        return $this;
    }
    public function setChartTitle(string $title)
    {
        $this->options['plugins']['title'] = [
            'display' => true,
            'text' => $title
        ];
        return $this;
    }
    public function setScaleTitle(string $scale, string $title)
    {
        $this->options['scales'][$scale] = [
            'display' => true,
            'title' => [
                'display' => true,
                'text' => $title
            ]
        ];
        return $this;
    }

    public function addWarning(string $message)
    {
        array_push($this->warnings, $message);
        return $this;
    }

    protected function validateBeforeRender()
    {
        $maxDatasetItems = 0;
        foreach($this->datasets as $dataset)
        {
            $maxDatasetItems = $maxDatasetItems < count($dataset['data']) ? count($dataset['data']) : $maxDatasetItems;
        }
        if($maxDatasetItems > count($this->labels)) {
            $this->addWarning('Missing '.($maxDatasetItems - count($this->labels)).' labels. Your char has only '.count($this->labels).' labels for '.$maxDatasetItems. ' data values. Set the correct number of labels to display all data!');
        }
        if ($maxDatasetItems < count($this->labels)) {
            $this->addWarning('Missing ' . (count($this->labels) - $maxDatasetItems) . ' data values in chart datasets. Your char has maximal ' . $maxDatasetItems . ' values in datasets, but ' . count($this->labels) . ' labels.');
        }
    }

    public function renderScript()
    {
        return "<script type='text/javascript'>
        const ctx".$this->getId()." = document.getElementById('".$this->getId(). "').getContext('2d');
        try{
        const ".$this->getId()." = new Chart(ctx".$this->getId().", {
            type: '". $this->getType() ."',
            data: ". json_encode($this->getData()['data'])  .",
            options: ". json_encode($this->getOptions()) ."
        });
        }
        catch(err){
            document.getElementById('".$this->getId()."').before(err+'. Install Chart.js to your project, follow installation instructions https://www.chartjs.org/docs/latest/getting-started/installation.html');
        }
        </script>";
    }

    public function renderHtml()
    {
        $attributes = '';
        $this->validateBeforeRender();
        foreach($this->wrapperHtmlAttributes as $name => $value)
        {
            $attributes .= $name .'="'.$value.'" ';
        }
        $html = '<div ' . $attributes . '>';
        if($this->displayWarnings && count($this->warnings))
        {
            foreach($this->warnings as $warning)
            {
                $html .= '<div style="color: red; padding: 5px 10px;">'.$warning.'</div>';
            }
        }
        $html .= '<canvas id="'.$this->id.'"></canvas></div>';
        return $html;
    }

    public function __toString()
    {
        return $this->renderHtml() . PHP_EOL . $this->renderScript();
    }
}
