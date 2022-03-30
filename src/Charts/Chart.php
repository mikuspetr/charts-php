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

    protected $type;
    protected $labels = [];
    public $datasets = [];
    protected $options;
    protected static $instancesCounter = 0;
    protected $id;
    protected $wrapperHtmlAttributes = [];

    protected $colors = self::COLORS;

    public function __construct(string $type, array $labels = [], array $datasets = [], array $options = [])
    {
        $this->type = $type;
        self::$instancesCounter++;
        $this->id = $this->type.'Chart'.self::$instancesCounter;

        $this->type = $type;
        $this->labels = $labels;
        $this->options = $options;
        $this->addDatasets($datasets);
    }

    public function validateDatasets()
    {
        if($this->datasets != []) {
            foreach($this->datasets as $dataset)
            {
                $this->validateDataset($dataset);
                $validated[] = $dataset;
            }
            $this->datasets = $validated;
        }
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

    public function addLabel(string $label)
    {
        array_push($this->labels, $label);
        return $this;
    }
    public function addLabels(array $labels)
    {
        $this->labels = array_merge($this->labels, $labels);
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

    public function addOptions(array $options)
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

    public function renderScript()
    {
        return "<script type='text/javascript'>
        try{
        const ctx".$this->getId()." = document.getElementById('".$this->getId()."').getContext('2d');
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
        foreach($this->wrapperHtmlAttributes as $name => $value)
        {
            $attributes .= $name .'="'.$value.'" ';
        }
        return '<div '.$attributes.'><canvas id="'.$this->id.'"></canvas></div>';
    }

    public function __toString()
    {
        return $this->renderHtml();
    }
}
