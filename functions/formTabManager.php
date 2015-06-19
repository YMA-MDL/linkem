<?php

echo "<script>var nodeId = " . $_GET['nodeId'] . ";</script>";

function getNodeTypeColumns($nodeType) {
    $output = "";
    $data = file_get_contents('../../../config/json_conf/itemtypes.json');
    $json = json_decode($data, true);
    foreach ($json['nodeTypes'] as $itemType) {
        if ($itemType["name"] === $nodeType) {
            if ($itemType["gridColumnsHeader"] != null) {
                foreach ($itemType["gridColumnsHeader"] as $key => $val) {
                    $output .= '<th  groupRef="' . $val["name"] . '" colspan="' . $val["width"] . '" class="headerTh">' . $val["name"] . ' </th>';
                }
                $output .="</tr><tr class='active'>";
            }
            // build relationship properties array
            $relPropertiesTable = array();
            foreach ($itemType["properties"] as $key => $val) {
                array_push($relPropertiesTable, $val["name"]);
            }
            // if column is part of the relationship then make it an input.
            foreach ($itemType["gridColumns"] as $key => $val) {
                if (array_search($val["name"], $relPropertiesTable) !== false) {
                    $outputAddition = '<th relProp="true" propertyRef="' . $val["name"] . '" propertyType="' . $val["type"] . '" >' . $val["label"] . '</th>';
                } else {
                    $outputAddition = '<th relProp="false" propertyRef="' . $val["name"] . '" propertyType="' . $val["type"] . '" >' . $val["label"] . '</th>';
                }
                $output .= $outputAddition;
            }
        }
    }
    return $output;
}

function createTab($nodeId, $tabName, $tabLabel, $activeOnStartup, $tabContentType, $lockLinked) {
    $output = [];

    $class = "";
    if ($activeOnStartup) {
        $class = "active";
    }
    if ($lockLinked) {
        $class .= "locklinked";
    }
    $output["tab-head"] = "<li class'" . $class . "'><a href='#" . $tabName . "-" . $nodeId . "' role='tab' data-toggle='tab'  data-i18n = 'relationships." . $tabLabel . "' ></a></li>";

    switch ($tabContentType) {
        case 'classificationProperties':
            $output["tab-content"] = "<div class='typeSpecificContent tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "</div>";
            break;
        case 'fileAttachement':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "<div id='" . $tabName . "ListActionButtons '>";
            $output["tab-content"] .= "<div class='btn-group' >";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-success btn-sm lockSensitive' id='add" . $tabName . "'><span data-i18n = 'relationships.buttons.add' />  <span class='glyphicon glyphicon-cloud-upload'></span></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-danger btn-sm selectionLinked lockSensitive' id='deleteFile" . $tabName . "' disabled><span data-i18n = 'relationships.buttons.delete' /> <span class='glyphicon glyphicon-trash'></span></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-primary btn-sm selectionLinked ' id='openFile" . $tabName . "' disabled><span data-i18n = 'relationships.buttons.open' /> <span class='glyphicon glyphicon-cloud-download'></span></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-primary btn-primary btn-sm selectionLinked lockSensitive' id='updateFile" . $tabName . "' disabled><span data-i18n = 'relationships.buttons.update' /> <span class='glyphicon glyphicon-refresh'></span></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-info btn-primary btn-sm selectionLinked ' id='versionsFile" . $tabName . "' disabled><span data-i18n = 'relationships.buttons.versions' /> <span class='glyphicon glyphicon-tags'></span></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-info btn-primary btn-sm selectionLinked ' id='previewFile" . $tabName . "' disabled><span data-i18n = 'relationships.buttons.preview' /> <span class='glyphicon glyphicon-eye-open'></span></button>";
            $output["tab-content"] .= "</div>";
            $output["tab-content"] .= "</div>";
            $output["tab-content"] .= "<table class='table table-condensed table-bordered " . $tabName . "List' id='" . $tabName . "List-" . $nodeId . "'>";
            $output["tab-content"] .= "<thead>";
            $output["tab-content"] .= getNodeTypeColumns($tabName);
            $output["tab-content"] .= "</thead>";
            $output["tab-content"] .= "<tbody> </tbody>";
            $output["tab-content"] .= "</table>";
            $output["tab-content"] .= "</div>";
            break;
        case 'discussion':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= " <div class='btn-group' >";
            $output["tab-content"] .= "  <button type='button' class='btn btn-default btn-success btn-sm' id='addDiscussion'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.newDiscussion' /></span></button>";
            $output["tab-content"] .= " </div>";
            $output["tab-content"] .= " <div class='mediaPanel-" . $nodeId . " initPanel'>";
            $output["tab-content"] .= "</div>";
            $output["tab-content"] .= "</div>";
            break;
        case 'structure':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "' parentId='" . $nodeId . "'>";
            $output["tab-content"] .= "<table class='table table-condensed table-bordered BOMStructureTree " . $tabName . "List' id='" . $tabName . "List-" . $nodeId . "'>";
            $output["tab-content"] .= "<thead>";
            $output["tab-content"] .= getNodeTypeColumns($tabName);
            $output["tab-content"] .= "</thead>";
            $output["tab-content"] .= "<tbody> </tbody>";
            $output["tab-content"] .= "</table>";
            $output["tab-content"] .= "</div>";
            break;
        case 'whereUsed':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "' parentId='" . $nodeId . "'>";
            $output["tab-content"] .= "<table class='table table-condensed table-bordered BOMStructureTree " . $tabName . "List' id='" . $tabName . "List-" . $nodeId . "'>";
            $output["tab-content"] .= "<thead>";
            $output["tab-content"] .= getNodeTypeColumns($tabName);
            $output["tab-content"] .= "</thead>";
            $output["tab-content"] .= "<tbody> </tbody>";
            $output["tab-content"] .= "</table>";
            $output["tab-content"] .= "</div>";
            break;
        case 'relatedObject':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "<div id='" . $tabName . "ListActionButtons'>";
            $output["tab-content"] .= "<div class='btn-group' >";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-success btn-sm lockSensitive' id='create" . $tabName . "'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.create' /></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-success btn-sm lockSensitive' id='attach" . $tabName . "'><span class='glyphicon glyphicon-log-in' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.attach' /></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-primary btn-sm selectionLinked lockSensitive' id='detach" . $tabName . "' disabled><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.unlink' /></button>";
            if ($tabName == "supplierReference") {
                $output["tab-content"] .= "<button type='button' class='btn btn-default btn-primary btn-sm selectionLinked ' id='partFinder' ><span class='glyphicon glyphicon-search' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.partfinder' /></button>";
            }
            $output["tab-content"] .= "</div></div>";
            $output["tab-content"] .= "<table class='table table-condensed table-bordered " . $tabName . "List' id='" . $tabName . "List-" . $nodeId . "' >";
            $output["tab-content"] .= "<thead>";
            $output["tab-content"] .= getNodeTypeColumns($tabName);
            $output["tab-content"] .= "</thead> <tbody/></table></div>";
            break;
        case 'relatedAffectedItem':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "<div id='" . $tabName . "ListActionButtons'>";
            $output["tab-content"] .= "<div class='btn-group' >";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-success btn-sm lockSensitive' id='attach" . $tabName . "'><span class='glyphicon glyphicon-log-in' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.attach' /></button>";
            $output["tab-content"] .= "<button type='button' class='btn btn-default btn-primary btn-sm selectionLinked lockSensitive' id='detach" . $tabName . "' disabled><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span> <span data-i18n = 'relationships.buttons.unlink' /></button>";
//            $output["tab-content"] .= "<button type='button' class='btn btn-primary btn-primary btn-sm selectionLinked ' id='openNode" . $tabName . "' disabled><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span> open</button>
            $output["tab-content"] .= "</div></div>";
            $output["tab-content"] .= "<table class='table table-condensed table-bordered " . $tabName . "List' id='" . $tabName . "List-" . $nodeId . "' >";
            $output["tab-content"] .= "<thead>";
            $output["tab-content"] .= getNodeTypeColumns($tabName);
            $output["tab-content"] .= "</thead> <tbody/></table></div>";
            break;
        case 'reverseRelationship':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "<div class='spaceForTableFeatureAlignment'/>";
            $output["tab-content"] .= "<table class='table table-condensed table-bordered " . $tabName . "List' id='" . $tabName . "List-" . $nodeId . "' >";
            $output["tab-content"] .= "<thead>";
            $output["tab-content"] .= getNodeTypeColumns(substr($tabName, 7));
            $output["tab-content"] .= "</thead><tbody/></table></div>";
            break;
        case 'custom':
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "</div>";
            break;
        case 'reportDataModelFactory':
            $data = file_get_contents('../../../config/json_conf/itemtypes.json');
            $json = json_decode($data, true);
            $index = 1;
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= " <div id='ReportGraph-" . $nodeId . "' class=' col-md-8'>"
                    . "<h4><span class='label label-primary tobeTitle'>Report Graph</span></h4>";
            $output["tab-content"].= "<select id='nodeTypeSelector'>";
            foreach ($json['nodeTypes'] as $itemType) {
                $output["tab-content"].= "<option value='" . $itemType["name"] . "'>" . $itemType["name"] . "</option>";
            }
            $output["tab-content"] .= "</select>"
                    . "<div class='btn-group' role='group' aria-label='...'>"
                    . "<button type='button' class='btn btn-primary btn-xs' id='addReportNode-" . $nodeId . "' disabled><span data-i18n = 'relationships.buttons.add' /></button>"
                    . "<button type='button' class='btn btn-primary btn-xs' id='attachReportNode-" . $nodeId . "' disabled><span data-i18n = 'relationships.buttons.attach' /></button>"
                    . "<button type='button' class='btn btn-primary btn-xs' id='deleteReportNode-" . $nodeId . "' disabled><span data-i18n = 'relationships.buttons.delete' /></button>"
                    . "<button type='button' class='btn btn-success btn-xs' id='fitReport-" . $nodeId . "'>fit</button>"
                    . "</div>"
                    . "<div class='reportGraph' id='REPORT" . $nodeId . "'><img src='images/reload_256.png' class='reportGraphReload center-block' /></div>"
                    . "<h4><span class='label label-primary tobeTitle'>Report Table</span></h4>"
                    . "<div class='reportTable'/><button type='button' id='refreshTable' class='btn btn-primary'>refresh</button><div id='reportTablePreviewDetail' ><table class='table table-condensed table-bordered'><thead><tr></tr></thead><tbody><tr></tr></tbody></table></div></div>";
            $output["tab-content"] .= " <div id='ReportNodeDetail-" . $nodeId . "'  class='col-md-4'><h4><span class='label label-info'>Report Node configuration</span></h4><div class='nodeDetailContent' /></div>";

            $output["tab-content"] .= "</div>";
            break;
        case 'ECOimpactMatrix':
            $output["tab-content"] = "  <div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "     <div class='row'>";
            $output["tab-content"] .= "         <div id='ECOchangeStart-" . $nodeId . "' class='ECOImpactList col-md-5'>"
                    . "                             <h4><span class='label label-primary asisTitle'>AsIs situation</span></h4>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-success btn-xs addNodeToXCO lockSensitive' id='add" . $tabName . "'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> add </button>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-danger btn-xs deleteNodeFromXCO lockSensitive' id='detachECOAffectedItems'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span> delete </button>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-default btn-xs refreshXCO' id='refreshECOAffectedItems'><span class='glyphicon glyphicon-refresh' aria-hidden='true'></span> refresh </button>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-default btn-xs refreshXCO fullScreenizer' id='FullScreenECOAffectedItems'><span class='glyphicon glyphicon-resize-full' aria-hidden='true'></span></button>";
            $output["tab-content"] .= "             <div class='graph'/></div>";
            $output["tab-content"] .= "             <div id='ECOchangeFinish-" . $nodeId . "' class='ECOImpactList col-md-4'><h4><span class='label label-warning tobeTitle'>ToBe situation</span></h4>";
            
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-default btn-xs refreshXCO fullScreenizer' id='FullScreenECOchangeFinish'><span class='glyphicon glyphicon-resize-full' aria-hidden='true'></span></button><div class='graph'/></div>";
            $output["tab-content"] .= "             <div id='ECOselectionDetail-" . $nodeId . "' selectedNode='' class='ECOImpactList ECOSelectionDetail col-md-3'><h4><span class='label label-info'>Item Information</span></h4>"
                    . "                                 <div class='nodeInfo'>"
                    . "                                     <div class='dropdown changeOptions'>";
            $output["tab-content"] .= '                         <button class="btn btn-block btn-default lockSensitive dropdown-toggle disabled" type="button" id="ECOChangeActionSelection-' . $nodeId . '" data-toggle="dropdown" aria-expanded="true">';
            $output["tab-content"] .= '                         Change Action<span class="caret"></span></button>
                                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" chgAction="change">Change</a></li>
                                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" chgAction="nochange">No Change</a></li>
                                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" chgAction="review">Review</a></li>
                                                                </ul>
                                                            </div>
                                                            <div>
                                                                <form>
                                                                    <div class="form-group">
                                                                        <label class="col-md-4" style="float:left;">maj.</label>
                                                                        <label class="col-md-4" style="float:left;">min.</label>
                                                                        <label class="col-md-4" style="float:left;">iter.</label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="input-group spinner col-md-4" style="float:left;">
                                                                            <input type="text" class="form-control versionSpinner majRevSpinner" value="0" disabled>
                                                                            <div class="input-group-btn-vertical ">
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-up"></i></div>
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-down"></i></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group spinner col-md-4" style="float:left;">
                                                                            <input type="text" class="form-control versionSpinner minRevSpinner" value="0" disabled>
                                                                            <div class="input-group-btn-vertical">
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-up"></i></div>
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-down"></i></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group spinner col-md-4" style="float:left;">
                                                                            <input type="text" class="form-control versionSpinner iterationSpinner" value="0" disabled>
                                                                            <div class="input-group-btn-vertical">
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-up"></i></div>
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-down"></i></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <div class="form-group">
                                                                    <label style="margin-top:10px;" >Change description</label>
                                                                    <textarea class="form-control" id="ecoNodeChangeDescription" rows="8" disabled></textarea>
                                                                </div> 
                                                                <!-- <div class="form-group">
                                                                    <label>Node Owner</label>
                                                                    <input type="text" class="form-control" id="nodeOwner" disabled>
                                                                </div> 
                                                                <div class="form-group">
                                                                    <label>Node User</label>
                                                                    <input type="text" class="form-control" id="nodeUser" disabled>
                                                                </div> 
                                                                 <div class="form-group">
                                                                    <button class="btn btn-block btn-success disabled" type="button" id="SAVEnodeChangeInfo" >save</button>
                                                                </div>-->
                                                            </div>
                                                        </form> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
            break;
        case 'FCOimpactMatrix':
            $output["tab-content"] = "  <div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "     <div class='row'>";
            $output["tab-content"] .= "         <div id='FCOchangeStart-" . $nodeId . "' class='FCOImpactList col-md-5'>"
                    . "                             <h4><span class='label label-primary asisTitle'>AsIs situation</span></h4>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-success btn-xs addNodeToXCO lockSensitive' id='add" . $tabName . "'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> add </button>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-danger btn-xs deleteNodeFromXCO lockSensitive' id='detachFCOAffectedItems'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span> delete </button>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-default btn-xs refreshXCO' id='refreshFCOAffectedItems'><span class='glyphicon glyphicon-refresh' aria-hidden='true'></span> refresh </button>";
            $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-default btn-xs refreshXCO fullScreenizer' id='FullScreenFCOAffectedItems'><span class='glyphicon glyphicon-resize-full' aria-hidden='true'></span></button>";
            $output["tab-content"] .= "             <div class='graph'/></div>";
            $output["tab-content"] .= "             <div id='FCOchangeFinish-" . $nodeId . "' class='FCOImpactList col-md-4'><h4><span class='label label-warning tobeTitle'>ToBe situation</span></h4>";
             $output["tab-content"] .= "             <button type='button' class='btn btn-default btn-default btn-xs refreshXCO fullScreenizer' id='FullScreenECOchangeFinish'><span class='glyphicon glyphicon-resize-full' aria-hidden='true'></span></button><div class='graph'/></div>";
           
            $output["tab-content"] .= "             <div id='FCOselectionDetail-" . $nodeId . "' selectedNode='' class='FCOImpactList FCOSelectionDetail col-md-3'><h4><span class='label label-info'>Item Information</span></h4>"
                    . "                                 <div class='nodeInfo'>"
                    . "                                     <div class='dropdown changeOptions'>";
            $output["tab-content"] .= '                         <button class="btn btn-block btn-default lockSensitive dropdown-toggle disabled" type="button" id="FCOChangeActionSelection-' . $nodeId . '" data-toggle="dropdown" aria-expanded="true">';
            $output["tab-content"] .= '                         Change Action<span class="caret"></span></button>
                                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" chgAction="change">Change</a></li>
                                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" chgAction="nochange">No Change</a></li>
                                                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" chgAction="review">Review</a></li>
                                                                </ul>
                                                            </div>
                                                            <div>
                                                                <form>
                                                                    <div class="form-group">
                                                                        <label class="col-md-4" style="float:left;">maj.</label>
                                                                        <label class="col-md-4" style="float:left;">min.</label>
                                                                        <label class="col-md-4" style="float:left;">iter.</label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="input-group spinner col-md-4" style="float:left;">
                                                                            <input type="text" class="form-control versionSpinner majRevSpinner" value="0" disabled>
                                                                            <div class="input-group-btn-vertical ">
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-up"></i></div>
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-down"></i></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group spinner col-md-4" style="float:left;">
                                                                            <input type="text" class="form-control versionSpinner minRevSpinner" value="0" disabled>
                                                                            <div class="input-group-btn-vertical">
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-up"></i></div>
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-down"></i></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group spinner col-md-4" style="float:left;">
                                                                            <input type="text" class="form-control versionSpinner iterationSpinner" value="0" disabled>
                                                                            <div class="input-group-btn-vertical">
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-up"></i></div>
                                                                                <div class="btn btn-default disabled"><i class="glyphicon glyphicon-chevron-down"></i></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <div class="form-group">
                                                                    <label style="margin-top:10px;" >Change description</label>
                                                                    <textarea class="form-control" id="fcoNodeChangeDescription" rows="8" disabled></textarea>
                                                                </div> 
                                                                <!-- <div class="form-group">
                                                                    <label>Node Owner</label>
                                                                    <input type="text" class="form-control" id="nodeOwner" disabled>
                                                                </div> 
                                                                <div class="form-group">
                                                                    <label>Node User</label>
                                                                    <input type="text" class="form-control" id="nodeUser" disabled>
                                                                </div> 
                                                                 <div class="form-group">
                                                                    <button class="btn btn-block btn-success disabled" type="button" id="SAVEnodeChangeInfo" >save</button>
                                                                </div>-->
                                                            </div>
                                                        </form> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
            break;
        default:
            $output["tab-content"] = "<div class='tab-pane " . $class . "' id='" . $tabName . "-" . $nodeId . "'>";
            $output["tab-content"] .= "</div>";
            break;
    }

    return $output;
}

?>
