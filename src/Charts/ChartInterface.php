<?php
namespace ChartsPhp\Charts;

interface ChartInterface
{
    public function addLabel(string $label);
    public function addLabels(array $labels);
    public function addDataset(array $dataset);
    public function addDatasets(array $datasets);
    public function addOptions(array $options);
    public function addWrapperHtmlAttribute(string $name, string $value);
    public function addClass(string $class);

    public function getId();
    public function getType();
    public function getLabels();
    public function getDatasets();
    public function getOptions();
    public function getData();

    public function setAspectRatio(int $value);
    public function displayLegend(bool $display = true);
    public function setChartTitle(string $title);
    public function setScaleTitle(string $scale, string $title);

    /**
     * render chart JavaScript code (require Chart.js)
     */
    public function renderScript();

    /**
     * Render HTML code with chart canvas
     */
    public function renderHtml();
}
