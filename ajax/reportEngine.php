<?php

// for production purpose
// error_reporting(E_ERROR);
session_start();
require_once("../config/constants.php");
require_once("../config/constantsSpecific.php");

require_once(ABSPATH . "functions/tools.php");
require_once(ABSPATH . "functions/permissions.php");

$action = getFromPost("action", "");
define("NL", "\n");

$data = file_get_contents('../config/json_conf/itemtypes.json');
$reportStore = '../VAULT/reports/';
$json = json_decode($data, true);

class excelProp {

    public $name;
    public $backgroundColor;
    public $parent;
    public $parentQueryRef;
    public $backID;

}

$alphas = range('A', 'Z');

include '../vendor/autoload.php';
include '../vendor/PHPExcel/PHPExcel.php';
$wsAnswer = array();
try {
    $cypher = new EndyJasmi\Cypher(NEO4J_URL_ROOT);
    switch ($action) {
        case "BuildExcelNodeReport":
            header("Content-type: application/json");
            $wsAnswer['time'] = new DateTime('now');
            // retrieve the targetId
            // retrieve the report
            $reportId = getFromPost("reportId", "");
            $parentId = getFromPost("parentId", "");
            $outputType = getFromPost("outputType", "excel");
            $reportDefinition = json_decode(file_get_contents($reportStore . "report_" . $reportId . ".json"));

            // build the query
            $parentNode = "";
            $outputColsArray = [];
            $colindex = 0;
            // get the starting node
            foreach ($reportDefinition->nodes as $node) {
                if ($node->isMain) {
                    $wsAnswer["mainNode"] = $node->name;
                    $parentNode = $node->name;

                    // build columns array
                    foreach ($node->Properties as $property) {
                        $colindex++;
                        $excelProp = new excelProp();
                        $excelProp->parentQueryRef = "sn";
                        $excelProp->name = $property->name;
                        $excelProp->parent = $node->name;
                        $outputColsArray[$colindex] = $excelProp;
                    }
                }
            }

            // get users list
            
            
            
            $CypherQueryMATCH = "MATCH (sn:$parentNode) ";
            $CypherQueryRESULT = "RETURN sn as sn, ID(sn) as IDsn";

            $parentRef = "sn";
            $fullCounter = 0;
            $depth = 0;
            $index = 0;
            $ExcelNodesColNb = [];

            function createReportQuery($reportDefinition, $parentNode, $parentRef, &$CypherQueryMATCH, &$CypherQueryRESULT, &$fullCounter, &$depth, &$index, &$outputColsArray, &$colindex, &$ExcelNodesColNb) {

                $depth++;
                if ($depth < 10) {
                    foreach ($reportDefinition->links as $link) {
                        if ($link->parentItem == $parentNode) {

                            $index++;
                            $fullCounter++;
                            $CypherQueryMATCH .= "OPTIONAL MATCH $parentRef-[]-(cn$index$fullCounter:$link->childItem) ";
                            $CypherQueryRESULT .= ",cn$index$fullCounter as cn$index$fullCounter, ID(cn$index$fullCounter) as IDcn$index$fullCounter";

                            foreach ($reportDefinition->nodes as $node) {
                                if ($node->name == $link->childItem) {
                                    // build columns array
                                    $nbcol = 0;
                                    foreach ($node->Properties as $property) {
                                        $colindex++;
                                        $excelProp = new excelProp();
                                        $excelProp->parentQueryRef = "cn$index$fullCounter";
                                        $excelProp->name = $property->name;
                                        $excelProp->type = $property->type;
                                        $excelProp->parent = $node->name;
                                        $outputColsArray[$colindex] = $excelProp;
                                        $nbcol++;
                                    }
                                    $ExcelNodesColNb[$depth] = $nbcol;
                                }
                            }
                            // handle deeper items
                            createReportQuery($reportDefinition, $link->childItem, "cn$index$fullCounter", $CypherQueryMATCH, $CypherQueryRESULT, $fullCounter, $depth, $index, $outputColsArray, $colindex, $ExcelNodesColNb);
                        }
                    }
                }
            }

            createReportQuery($reportDefinition, $parentNode, $parentRef, $CypherQueryMATCH, $CypherQueryRESULT, $fullCounter, $depth, $index, $outputColsArray, $colindex, $ExcelNodesColNb);
            $CypherQuery = $CypherQueryMATCH . $CypherQueryRESULT;

            // send back the answer

            $wsAnswer["result"] = "success";
            $wsAnswer["cypher"] = $CypherQuery;
            $wsAnswer["reportDefintion"] = $reportDefinition;

            $result = $cypher->statement($CypherQuery)->execute();


            $objPHPExcel = new PHPExcel();

            $col = 0;
            $line = 1;
            $previousCellValue = "";
            $lastmerge = 0;

            // build headers
            foreach ($outputColsArray as $prop) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $line, $prop->parent);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $line + 1, $prop->name);
                if (($previousCellValue != $prop->parent) && ($col > 0)) {
                    $objPHPExcel->getActiveSheet()->mergeCells($alphas[$lastmerge] . '1:' . $alphas[$col - 1] . '1');
                    $lastmerge = $col;
                }
                $previousCellValue = $prop->parent;
                $col ++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells($alphas[$lastmerge] . '1:' . $alphas[$col - 1] . '1');
            
            // build report result
            $line = 3;
            foreach ($result[0] as $res) {
                $col = 0;
                foreach ($outputColsArray as &$prop) {
                    if (isset($res[$prop->parentQueryRef][$prop->name])) {
                        // convert value by type
                        $convertedOutput = "";
                        switch ($prop->parentQueryRef){
                            case "state":
                                $convertedOutput = $res[$prop->parentQueryRef][$prop->name];
                                break;
                            case "date":
                                $convertedOutput = $res[$prop->parentQueryRef][$prop->name];
                                break;
                            case "user":
                                $convertedOutput = $res[$prop->parentQueryRef][$prop->name];
                                break;
                            default:
                                $convertedOutput = $res[$prop->parentQueryRef][$prop->name];
                                break;
                        }
                        
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $line, $res[$prop->parentQueryRef][$prop->name]);
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $line, "");
                    }
                    if (($res["ID" . $prop->parentQueryRef] == $prop->backID) && ($res["ID" . $prop->parentQueryRef] != null)) {

                        $objPHPExcel->getActiveSheet()->mergeCells($alphas[$col] . ($line - 1) . ':' . $alphas[$col] . ($line ));
                    }
                    $prop->backID = $res["ID" . $prop->parentQueryRef];
                    $col ++;
                }
                $line++;
            }

            $reportName= date("ymd").$wsAnswer["mainNode"].'_'.$reportId;
            
            $objWriterExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriterExcel->save("../VAULT/reports/generated/$reportName.xlsx");

            $objWriterHTML = new PHPExcel_Writer_HTML($objPHPExcel);
            $objWriterHTML->save("../VAULT/reports/generated/$reportName.htm");


            $wsAnswer['reportName'] = $reportName ;
            $wsAnswer["result"] = "success";
            $wsAnswer["parentId"] = $parentId;
            $wsAnswer["url"] = "/VAULT/reports/generated/$reportName.xlsx";

            echo json_encode($wsAnswer);
            break;
        default:
            header("Content-type: application/json");
            $wsAnswer['time'] = new DateTime('now');
            $wsAnswer["result"] = "error";
            $wsAnswer["message"] = "The action '$action' is not supported";
            echo json_encode($wsAnswer);
            break;
    }
} catch (Exception $e) {
    echo "<dbError><message>Internal error, please try again later. <message><detail>$e</detail></dbError>";
}