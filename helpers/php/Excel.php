<?php

require (PATH_VENDOR.'autoload.php');
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Excel
{
    public static function styleHeadTable()
    {
        return array(
            'font' => array(
                'name' => 'Arial',
                'bold'  => true,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'C8DCFF00'],
            ),
            'borders' => array(
                'top' => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'right' => ['borderStyle' => Border::BORDER_MEDIUM],
            ),
            'alignment' => array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER,
                'vertical'  => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );
    }

}