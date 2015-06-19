<?php

if (!defined('ABSPATH')) {
    die("ABSPATH undefined. Should not happen");
}

require_once(ABSPATH . "vendor/phpexcel/PHPExcel.php");

/* Export each itemInstance to Excel
   This method is recursive and starts with the deepest children, moving up to the root elements.
 */
function exportItemInstance($sheet, $itemInstance, $column, $listElems, &$line) {
    $lineStart   = $line;
    $nbCols      = count($itemInstance->properties->property);
    $colChildIdx = $column + $nbCols;

    foreach ($itemInstance->ItemInstance as $ItemInstanceChild) {
        exportItemInstance($sheet, $ItemInstanceChild, $colChildIdx, $listElems, $line);
        $line++;
    }

    /* If there was more than one children we go back a line before exporting current item */
    if ($lineStart != $line) {
       $line--;
    }
    foreach ($itemInstance->properties->property as $property) {
        $columnExcel = PHPExcel_Cell::stringFromColumnIndex($column);
        $cell = "$columnExcel$lineStart";
        if ($lineStart != $line) {
            $sheet->mergeCells("$cell:$columnExcel$line");
        }

        $cellValue = (string) $property;
        switch($property['format']) {
            case "URL":
                $sheet->setCellValue($cell, $cellValue);
                $sheet->getCell($cell)->getHyperlink()->setUrl($cellValue);
                break;

            case "list":
                $realValue = ("" == $cellValue) ? "" : $listElems[$cellValue];
                $sheet->getCell($cell)->setValueExplicit($realValue, PHPExcel_Cell_DataType::TYPE_STRING);
                break;

            case "date":
            case "text":
                $sheet->getCell($cell)->setValueExplicit($cellValue, PHPExcel_Cell_DataType::TYPE_STRING);
                break;

            case "image":
                $tokens = explode(":::", $cellValue);
                if(2 == count($tokens)) {
                    $sheet->setCellValue($cell, $tokens[0]);
                    $sheet->getCell($cell)->getHyperlink()->setUrl($tokens[0]);
                }
                break;

            case "file":
                $tokens = explode(":::", $cellValue);
                if(2 == count($tokens)) {
                    $sheet->setCellValue($cell, $tokens[1]);
                    $sheet->getCell($cell)->getHyperlink()->setUrl($tokens[0]);
                }
                break;

            default:
                $sheet->setCellValue($cell, $cellValue);
                break;
        }

        $column++;
    }
}

function excelExport($userInfo, $docInfo, $version, $definition, $content) {
    $excel = new PHPExcel();

    $excel->getProperties()
            ->setCreator($userInfo['displayName'])
            ->setLastModifiedBy($userInfo['displayName'])
            ->setTitle($docInfo['title'])
            ->setSubject("Excel export from mysimplegrid")
            ->setDescription("Excel export from mysimplegrid")
            ->setKeywords("mysimplegrid export excel")
            ->setCategory("mysimplegrid");

    $excel->setActiveSheetIndex(0);
    $sheet = $excel->getActiveSheet();

    $sheet->setTitle($docInfo['title']);
    $sheet->getDefaultColumnDimension()->setWidth(16);
    $sheet->getDefaultRowDimension()->setRowHeight(14);


    /* Export the definition in the first three lines of the grid */
    $xml      = simplexml_load_string($definition);
    $itemType = $xml->ItemType;
    $colIdx   = 0;
    foreach ($xml->xpath("//ItemType") as $itemType) {
        $colExcel      = PHPExcel_Cell::stringFromColumnIndex($colIdx);
        $colExcelStart = $colExcel;
        $sheet->setCellValue("{$colExcel}1", $itemType['typename']);
        foreach ($itemType->properties->property as $property) {
            $sheet->setCellValue("{$colExcel}2", $property);
            $sheet->setCellValue("{$colExcel}3", $property['format']);

            $colExcelEnd = PHPExcel_Cell::stringFromColumnIndex($colIdx);
            $colIdx++;
            $colExcel    = PHPExcel_Cell::stringFromColumnIndex($colIdx);
        }

        if ($colExcelStart != $colExcelEnd) {
            $sheet->mergeCells("{$colExcelStart}1:{$colExcelEnd}1");
        }
    }

    /* Apply styles to the header of the grid */
    $sheet->getStyle("A1:{$colExcelEnd}3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $styleArray = array('font' => array('bold' => true),
                        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'FFFF00'))); // Yellow
    $sheet->getStyle("A1:{$colExcelEnd}1")->applyFromArray($styleArray);

    $styleArray = array('font' => array('italic' => true),
                        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'C1DEFA'))); // Light blue
    $sheet->getStyle("A2:{$colExcelEnd}2")->applyFromArray($styleArray);

    $styleArray = array('font' => array('italic' => true, 'size' => '9'),
                        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E0E0E0'))); // Grey
    $sheet->getStyle("A3:{$colExcelEnd}3")->applyFromArray($styleArray);

    $sheet->freezePane("A4"); /* Header lines are now "sticky" */

    /* Retrieve each listElem nodes if any for the properties of format "list" */
    $listElems = array();
    foreach ($xml->xpath("//listElem") as $listElem) {
        $id = (string) $listElem["id"];
        $listElems[$id] = (string) $listElem;
    }

    /* Export the grid content */
    $xml  = simplexml_load_string($content);
    $line = 3;
    foreach ($xml->ItemInstance as $itemInstance) {
        $line++;
        exportItemInstance($sheet, $itemInstance, 0, $listElems, $line);
    }

    /* Center vertically and add border to the whole grid */
    $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
    $sheet->getStyle("A1:$colExcelEnd{$line}")->applyFromArray($styleArray);
    $sheet->getStyle("A1:$colExcelEnd{$line}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $date        = date("Ymd");
    $versionInfo = ""; /* By default no version info in the filename */
    if ("" != $version) {
        $versionInfo = "-v$version";
    }
    $filename = "$date-mysimplegrid$versionInfo.xls";
    /* Output the Excel file to the user */
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $writer->save('php://output');

    return true;
}

?>