<?php
namespace ChartsPhp\Charts;
class Bubble extends Scatter
{
    protected function throwException()
    {
        throw new \Exception('data array in dataset must contain arrays with numeric values! ( [5, 10, 8] or arrays with keys x,y,r [x => 5, y => 10, r => 8] )');
    }
}
