<?php

require_once("../../../config/constants.php");
include '../../../vendor/autoload.php';

function displayCustomProperties($nodeType, $propPerRow) {

    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $prop_LabelWidth = "2";
    $prop_ValueWidth = "2";

    switch ($propPerRow) {
        case 1:
            $prop_LabelWidth = "4";
            $prop_ValueWidth = "8";
            break;
        case 2:
            $prop_LabelWidth = "2";
            $prop_ValueWidth = "4";
            break;
        case 3:
            $prop_LabelWidth = "2";
            $prop_ValueWidth = "2";
            break;
        case 4:
            $prop_LabelWidth = "1";
            $prop_ValueWidth = "2";
            break;
        default:
            $prop_LabelWidth = "2";
            $prop_ValueWidth = "2";
            break;
    }

    $data = file_get_contents('../../../config/json_conf/itemtypes.json');
    $json = json_decode($data, true);
    $count = 0;
    foreach ($json['nodeTypes'] as $itemType) {
        if ($itemType["name"] === $nodeType) {
            if ($itemType["properties"] != null) {
                echo "<div class='form-group'>";
                foreach ($itemType["properties"] as $key => $val) {
                    if (isset($val["custom"])) {
                        if ($val["custom"]) {
                            if ($count === $propPerRow) {
                                echo "</div><div class='form-group'>";
                            }
                            $count++;
                            $propName = "";
                            if (isset($val['label'][0][$lang])) {
                                $propName = $val['label'][0][$lang];
                            } else {
                                $propName = $val['label'][0]['default'];
                            }
                            $propId = $val["name"];
                            $propPlaceHolder = $val["placeholder"];
                            $propType = $val["type"];
                            include ABSPATH . "partials/modules/customFieldProperty.php";
                        }
                    }
                }
                echo "</div>";
            }
        }
    }
    return true;
}

function displayLifecycleRoles($nodeType, $propPerRow) {

    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $prop_LabelWidth = "2";
    $prop_ValueWidth = "2";

    switch ($propPerRow) {
        case 1:
            $prop_LabelWidth = "4";
            $prop_ValueWidth = "8";
            break;
        case 2:
            $prop_LabelWidth = "2";
            $prop_ValueWidth = "4";
            break;
        case 3:
            $prop_LabelWidth = "2";
            $prop_ValueWidth = "2";
            break;
        case 4:
            $prop_LabelWidth = "1";
            $prop_ValueWidth = "2";
            break;
        default:
            $prop_LabelWidth = "2";
            $prop_ValueWidth = "2";
            break;
    }

    $data = file_get_contents('../../../config/json_conf/itemtypes.json');
    $json = json_decode($data, true);
    $count = 0;
    foreach ($json['nodeTypes'] as $itemType) {
        if ($itemType["name"] === $nodeType) {
            if ($itemType["lifecycle"] != null) {
                if ($itemType["lifecycle"]["roles"] != null) {
                    echo "<div class='form-group'>";
                    foreach ($itemType["lifecycle"]["roles"] as $key => $val) {
                        if ($count === $propPerRow) {
                            echo "</div><div class='form-group'>";
                        }
                        $count++;
                        $propName = $val["label"];
                        $propId = $val["name"];
                        $propPlaceHolder = "select user";
                        $propType = "user";
                        include ABSPATH . "partials/modules/customFieldProperty.php";
                    }
                    echo "</div>";
                }
            }
        }
    }
    return true;
}

function displayClassifications($nodeType) {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $data = file_get_contents('../../../config/json_conf/itemtypes.json');
    $json = json_decode($data, true);
    $option = "<option value='none'></option>";
    foreach ($json['nodeTypes'] as $itemType) {
        if ($itemType["name"] === $nodeType) {
            if ($itemType["classification"] != null) {
                foreach ($itemType["classification"] as $key => $val) {
                    if (isset($val['label'][0][$lang])) {
                        $option .= "<option value='" . $val['name'] . "'>" . $val['label'][0][$lang] . "</option>";
                    } else {
                        $option .= "<option value='" . $val['name'] . "'>" . $val['label'][0]['default'] . "</option>";
                    }
                }
            }
        }
    }
    return $option;
}

function getNodeTypeActions($nodeType) {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $data = file_get_contents('../../../config/json_conf/itemtypes.json');
    $json = json_decode($data, true);
    $option = "";
    $answer = array();
    $counter = 0;
    foreach ($json['nodeTypes'] as $itemType) {
        if ($itemType["name"] === $nodeType) {
            if (isset($itemType["actions"])) {
                foreach ($itemType["actions"] as $key => $val) {
                    $counter++;
                    if (isset($val['label'][$lang])) {
                        $option .= "<li><a href='#' action='actions_" . $val['method'] . "'>" . $val['label'][$lang] . "</a></li>";
                    } else {
                        $option .= "<li><a href='#' action='actions_" . $val['method'] . "'>" . $val['label']['default'] . "</a></li>";
                    }
                }
            }
        }
    }
    $answer["length"] = $counter;
    $answer["content"] = $option;
    return $answer;
}

function getNodeTypeReports($nodeType, $type="node") {
    $cypher = new EndyJasmi\Cypher(NEO4J_URL_ROOT);
    $result = $cypher->statement(
                    'MATCH  ( report:report) '
                    . 'WHERE  report.source="' . $nodeType . '" AND report.state="released" AND report.type="'.$type.'" '
                    . 'RETURN report.name as NAME, ID(report) as ID'
            )->execute();

    return $result[0];
}
