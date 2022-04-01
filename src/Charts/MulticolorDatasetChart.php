<?php
namespace ChartsPhp\Charts;
class MulticolorDatasetChart extends Chart
{
    protected function validateColors(array &$dataset)
    {
        if(!isset($dataset['backgroundColor']) || !is_array($dataset['backgroundColor']))
        {
            $colors = array_map(function($c){
                return $c['background'];
            }, self::COLORS );
            $dataset['backgroundColor'] = $colors;
        }
    }
}
