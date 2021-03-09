<?php


class Pdf_helper {

    private $j;
    private $width = array();

    public function __construct()
    {
        $this->j = 0;
    }

    public function addWidthInArray($num)
    {
        $this->width[$this->j] = $num;
        $this->j = $this->j + 1;
        return $num;
    }

    public function getWidth()
    {
        return $this->width;
    }

}
