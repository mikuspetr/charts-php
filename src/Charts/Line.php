<?php
namespace ChartsPhp\Charts;
class Line extends Chart
{
    public function setCubicInterpolationMode(string $value = 'linear')
    {
        $this->options['cubicInterpolationMode'] = $value;
        return $this;
    }
}
