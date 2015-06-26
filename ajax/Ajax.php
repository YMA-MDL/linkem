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

$data = file_get_contents('../config/config.json');
$reportStore = '../VAULT/reports/';
$json = json_decode($data, true);
$actionDetail = "";


include '../vendor/autoload.php';

try {
    $queryStart = microtime(true);
    $cypher = new EndyJasmi\Cypher(NEO4J_URL_ROOT);
    $userID = $_SESSION['uid'];
    switch ($action) {
        // =========================== Nodes Management ===============================//
        case "node_add":
            header("Content-type: application/json");
            //retreive nodeType
            $nodeType = getFromPost("type", "");
            $uniqueId = getFromPost("uniqueId", "");

            //create query
            $result = $cypher->statement(
                            'START user=node(' . $userID . ') '
                            . ' CREATE  (newNode:linkem_' . $nodeType . '{uniqueId:"' . $uniqueId . '"}),'
                            . ' user-[:created]->newNode, '
                            . ' user-[:canRead]->newNode, '
                            . ' user-[:canUpdate]->newNode, '
                            . ' user-[:canDelete]->newNode  '
                            . ' RETURN id(newNode) as ID '
                    )->execute();

            // prepare result Array
            $data = array();

            //define log action
            $actionDetail = $action . ";";

            // build return array
            $wsAnswer["result"] = "success";
            $wsAnswer["content"] = ["id" => $result[0][0]["ID"]];
            echo json_encode($wsAnswer);
            break;

        case "node_update":
            header("Content-type: application/json");
            //retreive nodeType
            $propName = getFromPost("propName", "");
            $propValue = getFromPost("propValue", "");
            $uniqueId = getFromPost("uniqueId", "");

            //create query
            $query = 'MATCH (n{uniqueId:"' . $uniqueId . '"}) '
                    . ' SET  n.' . $propName . '="' . $propValue . '" '
                    . ' RETURN id(n) as ID ';
            $result = $cypher->statement($query)->execute();
            $wsAnswer["query"] = $query;
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "node_delete":
            header("Content-type: application/json");
            $uniqueId = getFromPost("uniqueId", "");

            $query = 'MATCH (n{uniqueId:"' . $uniqueId . '"})-[rel]-(other) '
                    . ' DELETE  rel,n ';
            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        // =========================== Links Management ===============================//
        case "link_add":
            header("Content-type: application/json");
            //retreive nodeType
            $uniqueIdSource = getFromPost("uniqueIdSource", "");
            $uniqueIdTarget = getFromPost("uniqueIdTarget", "");
            $linkType = getFromPost("linkType", "");
            $uniqueId = getFromPost("uniqueId", "");

            //create query
            $result = $cypher->statement(
                            'MATCH (source{uniqueId:"' . $uniqueIdSource . '"}),(target{uniqueId:"' . $uniqueIdTarget . '"}) '
                            . ' CREATE  source-[edge:' . $linkType . '{uniqueId:"' . $uniqueId . '"}]->target '
                            . ' RETURN id(edge) as ID '
                    )->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "link_update":
            header("Content-type: application/json");
            //retreive nodeType
            $propName = getFromPost("propName", "");
            $propValue = getFromPost("propValue", "");
            $uniqueId = getFromPost("uniqueId", "");

            //create query
            $query = 'MATCH a-[n{uniqueId:"' . $uniqueId . '"}]-b '
                    . ' SET  n.' . $propName . '="' . $propValue . '" '
                    . ' RETURN id(n) as ID ';
            $result = $cypher->statement($query)->execute();
            $wsAnswer["query"] = $query;
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "link_delete":
            header("Content-type: application/json");
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        // ====================== Node Template Management ===========================//
        case "templates_load":
            header("Content-type: application/json");

            $queryNodes = 'START user=node(' . $userID . ') '
                    . ' MATCH  user-[:own]->(nodeTemplate:linkem_nodeTemplate) '
                    . ' OPTIONAL MATCH  user-[:belongsTo]-(:linkemAppOrg)-[:own]->(nodeTemplate) '
                    . ' RETURN nodeTemplate,Id(nodeTemplate) as ID ';
            $resultNodes = $cypher->statement($queryNodes)->execute();
            $queryEdges = 'START user=node(' . $userID . ') '
                    . ' MATCH  user-[:own]->(edgeTemplate:linkem_edgeTemplate) '
                    . ' OPTIONAL MATCH  user-[:belongsTo]-(:linkemAppOrg)-[:own]->(edgeTemplate) '
                    . ' RETURN edgeTemplate,Id(edgeTemplate) as ID ';
            $resultEdges = $cypher->statement($queryEdges)->execute();

            $answerNode = array();
            foreach ($resultNodes[0] as $resNodes) {
                $resNodes['nodeTemplate']['_id'] = $resNodes['ID'];
                array_push($answerNode, $resNodes['nodeTemplate']);
            }
            $answerEdge = array();
            foreach ($resultEdges[0] as $resEdges) {
                $resEdges['edgeTemplate']['_id'] = $resEdges['ID'];
                array_push($answerEdge, $resEdges['edgeTemplate']);
            }

            $wsAnswer["content_nodes"] = $answerNode;
            $wsAnswer["content_edges"] = $answerEdge;
            $wsAnswer["query_nodes"] = $queryNodes;
            $wsAnswer["query_edges"] = $queryEdges;
            $wsAnswer["result"] = "success";
            $wsAnswer["userid"] = $userID;
            echo json_encode($wsAnswer);
            break;
        case "node_template_add":
            header("Content-type: application/json");
            $nodeTemplateName = getFromPost("name", "");
            $shareToOrg = getFromPost("share", "");
            //create query
            if ($shareToOrg) {
                $query = 'START user=node(' . $userID . ') '
                        . ' OPTIONAL MATCH user-[:belongsTo]-(org:linkemAppOrg) '
                        . ' CREATE  (nodeTemplate:linkem_nodeTemplate{_name:"' . $nodeTemplateName . '", _label:"_name", _shared:true}) , '
                        . ' user-[:own]->nodeTemplate '
                        . ' RETURN id(nodeTemplate) as ID ';
            } else {
                $query = 'START user=node(' . $userID . ') '
                        . ' CREATE  (nodeTemplate:linkem_nodeTemplate{_name:"' . $nodeTemplateName . '", _label:"_name", _shared:false}) , '
                        . ' user-[:own]->nodeTemplate, '
                        . ' RETURN id(nodeTemplate) as ID ';
            }
            $result = $cypher->statement($query)->execute();
            $wsAnswer["query"] = $query;
            $wsAnswer["id"] = $result[0]["ID"];


            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "node_template_update":
            header("Content-type: application/json");
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "node_template_delete":
            header("Content-type: application/json");
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "node_template_addProperty":
            header("Content-type: application/json");
            $propName = getFromPost("propertyName", "");
            $nodeId = getFromPost("nodeId", "");
            $query = 'START template=node(' . $nodeId . ') '
                    . ' SET  template.' . $propName . '="text"  '
                    . ' RETURN id(template) as ID ';

            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;

        case "node_template_deleteProperty":
            header("Content-type: application/json");
            $propName = getFromPost("propertyName", "");
            $nodeId = getFromPost("nodeId", "");
            $query = 'START template=node(' . $nodeId . ') '
                    . ' REMOVE  template.' . $propName
                    . ' RETURN id(template) as ID ';

            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "node_template_updateImage":
            header("Content-type: application/json");
            $imageUrl = getFromPost("imageUrl", "");
            $nodeId = getFromPost("nodeId", "");
            $query = 'START template=node(' . $nodeId . ') '
                    . ' SET  template._image="' . $imageUrl . '"  '
                    . ' RETURN id(template) as ID ';

            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;

        case "node_template_updateProperty":
            header("Content-type: application/json");
            $propName = getFromPost("propertyName", "");
            $propValue = getFromPost("propertyValue", "");
            $nodeId = getFromPost("nodeId", "");
            $query = 'START template=node(' . $nodeId . ') '
                    . ' SET  template.' . $propName . '="' . $propValue . '"  '
                    . ' RETURN id(template) as ID ';

            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        // ====================== Node Template Management ===========================//

        case "edge_template_add":
            header("Content-type: application/json");
            $nodeTemplateName = getFromPost("name", "");
            $shareToOrg = getFromPost("share", true);
            //create query
            if ($shareToOrg) {
                $query = 'START user=node(' . $userID . ') '
                        . ' OPTIONAL MATCH user-[:belongsTo]-(org:linkemAppOrg) '
                        . ' CREATE  (edgeTemplate:linkem_edgeTemplate{_name:"' . $nodeTemplateName . '", _label:"_name", _shared:true}) , '
                        . ' user-[:own]->edgeTemplate '
                        . ' RETURN id(edgeTemplate) as ID ';
            } else {
                $query = 'START user=node(' . $userID . ') '
                        . ' CREATE  (edgeTemplate:linkem_edgeTemplate{_name:"' . $nodeTemplateName . '", _label:"_name", _shared:false}) , '
                        . ' user-[:own]->edgeTemplate, '
                        . ' RETURN id(edgeTemplate) as ID ';
            }
            $result = $cypher->statement($query)->execute();
            $wsAnswer["query"] = $query;
            $wsAnswer["id"] = $result[0]["ID"];


            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "edge_template_update":
            header("Content-type: application/json");
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "edge_template_updateProperty":
            header("Content-type: application/json");
            $propName = getFromPost("propertyName", "");
            $propValue = getFromPost("propertyValue", "");
            $nodeId = getFromPost("nodeId", "");
            $query = 'START template=node(' . $nodeId . ') '
                    . ' SET  template.' . $propName . '="' . $propValue . '"  '
                    . ' RETURN id(template) as ID ';

            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "edge_template_delete":
            header("Content-type: application/json");
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "edge_template_addProperty":
            header("Content-type: application/json");
            $propName = getFromPost("propertyName", "");
            $nodeId = getFromPost("nodeId", "");
            $query = 'START template=node(' . $nodeId . ') '
                    . ' SET  template.' . $propName . '="text"  '
                    . ' RETURN id(template) as ID ';

            $result = $cypher->statement($query)->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        // =============================== Default ===========================//
        case "getRelationships":
            header("Content-type: application/json");
            $nodeId = getFromPost("nodeId", "");
            $direction = getFromPost("direction", "");
            $query = "";
            $log = "";
            $wsAnswer["nodes"] = [];
            $wsAnswer["edges"] = [];

            if ($direction === "both") {
                $nodeRef = "node";
                $query = 'START user=node(' . $userID . ') '
                        . ' MATCH  user-[:canRead]->(nend{uniqueId:"' . $nodeId . '"})<-[edge]-(node) '
                        . ' RETURN node,labels(node) as Labels,edge,nend, type(edge) as TypeR ';
                $result = $cypher->statement($query)->execute();

                foreach ($result[0] as $res) {
                    if ($res["Labels"][0] !== "User") {
                        $res[$nodeRef]["type"] = explode("_", $res["Labels"][0])[1];
                        array_push($wsAnswer["nodes"], $res[$nodeRef]);
                        if (!(is_null($res["edge"]))) {
                            $edge = array();
                            $edge["data"] = $res["edge"];
                            $edge["type"] = $res["TypeR"];
                            $edge["uniqueId"] = $res["edge"]["uniqueId"];
                            $edge["source"] = $res["node"]["uniqueId"];
                            $edge["target"] = $res["nend"]["uniqueId"];
                            array_push($wsAnswer["edges"], $edge);
                        }
                    }
                }
                $nodeRef = "nend";
                $query = 'START user=node(' . $userID . ') '
                        . ' MATCH  user-[:canRead]->(node{uniqueId:"' . $nodeId . '"})-[edge]->(nend) '
                        . ' RETURN node,labels(nend) as Labels,edge,nend, type(edge) as TypeR ';
                $result = $cypher->statement($query)->execute();

                foreach ($result[0] as $res) {
                    if ($res["Labels"][0] !== "User") {
                        $res[$nodeRef]["type"] = explode("_", $res["Labels"][0])[1];
                        array_push($wsAnswer["nodes"], $res[$nodeRef]);
                        if (!(is_null($res["edge"]))) {
                            $edge = array();
                            $edge["data"] = $res["edge"];
                            $edge["type"] = $res["TypeR"];
                            $edge["uniqueId"] = $res["edge"]["uniqueId"];
                            $edge["source"] = $res["node"]["uniqueId"];
                            $edge["target"] = $res["nend"]["uniqueId"];
                            array_push($wsAnswer["edges"], $edge);
                        }
                    }
                }
            } else {
                if ($direction === "up") {
                    $nodeRef = "node";
                    $query = 'START user=node(' . $userID . ') '
                            . ' MATCH  user-[:canRead]->(nend{uniqueId:"' . $nodeId . '"})<-[edge]-(node) '
                            . ' RETURN node,labels(node) as Labels,edge,nend, type(edge) as TypeR ';
                } else {
                    $nodeRef = "nend";
                    $query = 'START user=node(' . $userID . ') '
                            . ' MATCH  user-[:canRead]->(node{uniqueId:"' . $nodeId . '"})-[edge]->(nend) '
                            . ' RETURN node,labels(nend) as Labels,edge,nend, type(edge) as TypeR ';
                }

                $result = $cypher->statement($query)->execute();

                foreach ($result[0] as $res) {
                    if ($res["Labels"][0] !== "User") {
                        $res[$nodeRef]["type"] = explode("_", $res["Labels"][0])[1];
                        array_push($wsAnswer["nodes"], $res[$nodeRef]);
                        if (!(is_null($res["edge"]))) {
                            $edge = array();
                            $edge["data"] = $res["edge"];
                            $edge["type"] = $res["TypeR"];
                            $edge["uniqueId"] = $res["edge"]["uniqueId"];
                            $edge["source"] = $res["node"]["uniqueId"];
                            $edge["target"] = $res["nend"]["uniqueId"];
                            array_push($wsAnswer["edges"], $edge);
                        }
                    }
                }
            }
            $wsAnswer["query"] = $query;
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;

        case "nodeTypeQuery":
            header("Content-type: application/json");
            $nodeType = getFromPost("nodeType", "");
            //create query
            $query = 'START user=node(' . $userID . ') '
                    . ' MATCH  user-[:canRead]->(node:linkem_' . $nodeType . ') '
                    . ' OPTIONAL MATCH  node-[edge]->(nend:linkem_' . $nodeType . ') '
                    . ' RETURN node,labels(node) as Labels,edge,nend, type(edge) as TypeR ';
            $result = $cypher->statement($query)->execute();
            $log = "";
            $wsAnswer["nodes"] = [];
            $wsAnswer["edges"] = [];
            foreach ($result[0] as $res) {
                $res["node"]["type"] = explode("_", $res["Labels"][0])[1];
                array_push($wsAnswer["nodes"], $res["node"]);
                if (!(is_null($res["edge"]))) {
                    $edge = array();
                    $edge["data"] = $res["edge"];
                    $edge["type"] = $res["TypeR"];
                    $edge["uniqueId"] = $res["edge"]["uniqueId"];
                    $edge["source"] = $res["node"]["uniqueId"];
                    $edge["target"] = $res["nend"]["uniqueId"];
                    array_push($wsAnswer["edges"], $edge);
                }
            }
            $wsAnswer["query"] = $query;
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);

            break;

        case "getItemElements":
            header("Content-type: application/json");
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "getViews":
            header("Content-type: application/json");

            //create query
            $result = $cypher->statement(
                            'START user=node(' . $userID . ') '
                            . ' MATCH  user-[:created]->(views:linkem_tech_view) '
                            . ' RETURN id(views) as ID, views.uniqueId as UID, views.query as QUERY, views.name as NAME '
                    )->execute();

            $wsAnswer["content"] = array();

            foreach ($result[0] as $res) {
                $view = array();
                $view['id'] = $res['ID'];
                $view['uid'] = $res['UID'];
                $view['query'] = $res['QUERY'];
                $view['name'] = $res['NAME'];
                array_push($wsAnswer["content"], $view);
            }
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "createView":
            header("Content-type: application/json");
            $viewId = getFromPost("viewId", "");

            //create query
            $result = $cypher->statement(
                            'START user=node(' . $userID . ') '
                            . ' CREATE  (newNode:linkem_tech_view{uniqueId:"' . $viewId . '"}),'
                            . ' user-[:created]->newNode '
                            . ' RETURN id(newNode) as ID '
                    )->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "updateViewName":
            header("Content-type: application/json");
            $viewId = getFromPost("viewId", "");
            $viewName = getFromPost("viewName", "");

            //create query
            $result = $cypher->statement(
                            'START view=node(' . $viewId . ') '
                            . ' SET  view.name="' . $viewName . '" '
                            . ' RETURN id(view) as ID '
                    )->execute();

            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;
        case "saveViewState":
            header("Content-type: application/json");
            $viewStateEls = getFromPost("viewState", "");
            $viewId = getFromPost("viewId", "");
            $query = "";
            $i = 0;
            if (count($viewStateEls) > 0) {
                if (array_key_exists("nodes", $viewStateEls)) {
                    foreach ($viewStateEls["nodes"] as $node) {
                        if ($i != 0) {
                            $query .= ", ";
                        } else {
                            $query = "MATCH ";
                        }
                        $query .= " (n$i{uniqueId:'$node'}) ";
                        $i++;
                    }
                }
                if (array_key_exists("edges", $viewStateEls)) {
                    foreach ($viewStateEls["edges"] as $edge) {
                        $query .= ", (start$i)-[n$i{uniqueId:'$edge'}]->(end$i) ";
                        $i++;
                    }
                }
                for ($j = 0; $j < $i; $j++) {
                    if ($j != 0) {
                        $query .= ", ";
                    } else {
                        $query .= "RETURN ";
                    }
                    $query .= "n$j";
                    $j++;
                }
            }

            //create query
            $result = $cypher->statement(
                            'MATCH (view{uniqueId:"' . $viewId . '"}) '
                            . ' SET  view.query = "' . $query . '" '
                            . ' RETURN view '
                    )->execute();

            $wsAnswer["content"] = $query;
            $wsAnswer["result"] = "success";
            echo json_encode($wsAnswer);
            break;

        case "getCypherGraph":
            header("Content-type: application/json");

            $cypherString = getFromPost("cypherString", "");
            $graphnodes = array();
            $graphedges = array();

            if (strpos($cypherString, 'set') !== false) {
                echo 'true';
            }
            if (strpos($cypherString, 'create') !== false) {
                echo 'true';
            }

            $wsAnswer["result"] = "success";
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

    $queryStop = microtime(true);
    $queryTime = $queryStop - $queryStart;
    $queryTimeLog = $actionDetail . ";" . microtime(true) . ";" . $queryStart . ";" . $queryStop . ";" . $queryTime . "\n";
    error_log($queryTimeLog, 3, getcwd() . '/Neo4jQueryLog.csv');
} catch (Exception $e) {
    echo "<dbError><message>Internal error, please try again later. <message><detail>$e</detail></dbError>";
}

exit;
