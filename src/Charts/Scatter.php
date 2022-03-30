<?php
namespace ChartsPhp\Charts;
class Scatter extends Chart
{
    protected function validateData($dataset)
    {
        parent::validateData($dataset);
        $validated = array_filter($dataset['data'], function($item){
            $numeric = array_filter($item, function($value){
                return is_numeric($value);
            });
            return count($numeric) === count($item) && is_array($item) &&  (isset($item['x']) || isset($item[0])) && (isset($item['y']) || isset($item[1]));
        });

        if(count($dataset['data']) !== count($validated))
        {
            static::throwException();
        }
    }

    protected function throwException()
    {
        throw new \Exception('data array in dataset must contain arrays with numeric values! ( [5, 10] or arrays with keys x,y [x=>5, y=>10] )');
    }
}
