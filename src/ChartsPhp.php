<?php
namespace ChartsPhp;

use ChartsPhp\Charts\Chart;

class ChartsPhp
{
    const TYPE_LINE = 'line';
    const TYPE_BAR = 'bar';
    const TYPE_PIE = 'pie';
    const TYPE_DOUGHNUT = 'doughnut';
    const TYPE_POLAR_AREA = 'polarArea';
    const TYPE_RADAR = 'radar';
    const TYPE_SCATTER = 'scatter';
    const TYPE_BUBBLE = 'bubble';

    const CHART_TYPES = [self::TYPE_LINE, self::TYPE_BAR, self::TYPE_PIE, self::TYPE_DOUGHNUT, self::TYPE_POLAR_AREA, self::TYPE_RADAR, self::TYPE_SCATTER, self::TYPE_BUBBLE];

    public static function createChart(string $type = self::TYPE_LINE, array $labels = [], array $datasets = [], array $options = []): Chart
    {
        if(!in_array($type, self::CHART_TYPES))
        {
            throw new \Exception('Unknown chart type');
        }
        $route ='ChartsPhp\\Charts\\'.ucfirst($type);
        return new $route($type, $labels, $datasets, $options);
    }
}
