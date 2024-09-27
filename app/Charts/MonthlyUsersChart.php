<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class MonthlyUsersChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build($xdata, $datasets, $title): \ArielMejiaDev\LarapexCharts\lineChart
    {
       

        return $this->chart->lineChart()
          //  ->setTitle($title)
            ->setStroke(1)
            ->setHeight(400)
            ->setWidth(500)
            ->setGrid(false, '#3F51B5', 0.04)
            ->setToolbar(true, false)
         //   ->setSubtitle('Observability plot')
            ->setDataset($datasets)
            ->setXAxis(['categories' => $xdata, 'type' => 'datetime']
        );
    }
}
