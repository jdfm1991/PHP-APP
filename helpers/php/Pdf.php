<?php


class Pdf {

    private $j;
    private $width = array();

    public function __construct()
    {
        $j = 0;
    }

    public function addWidthInArray($num)
    {
        $GLOBALS['width'][$GLOBALS['j']] = $num;
        $GLOBALS['j'] = $GLOBALS['j'] + 1;
        return $num;
    }

    public function getWidth()
    {
        return $GLOBALS['width'];
    }

}
