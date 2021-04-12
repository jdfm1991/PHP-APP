<?php


class Kpimarca
{
    private $arr;

    /**
     * Kpimarcas constructor.
     * @param $arr
     */
    public function __construct($marcasKpi)
    {
        foreach ($marcasKpi as $marca)
            $this->arr[$marca] = 0;

        return $this->arr;
    }


    public function get_totalKpiMarcas ()
    {
        return $this->arr;
    }

    public function set_acumKpiMarcas ($marcasArr)
    {
        foreach ($marcasArr as $marca => $value)
            $this->arr[$marca] += $value;
    }

}