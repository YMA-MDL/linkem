<?php
$data = file_get_contents('config/config.json');
$json = json_decode($data, true);
?>

<h2>Content Management</h2>
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

    <!-- NODE OPENING PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading"  role="tab" id="headingOpenNode">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOpenNode" aria-expanded="true" aria-controls="collapseOpenNode">
                    Add Nodes to the view
                </a>
            </h4>
        </div>
        <div id="collapseOpenNode" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOpenNode">
            <div class="panel-body">
                <div class="col-md-8">
                    <select class="form-control nodeTemplateList" id="OpenNodeTypeSelection">
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block disabled" id="searchNodesByType">Search</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NODE OPENING PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading"  role="tab" id="headingOpenEdges">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOpenEdges" aria-expanded="true" aria-controls="collapseOpenNode">
                    Browse Relationships
                </a>
            </h4>
        </div>
        <div id="collapseOpenEdges" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOpenEdges">
            <div class="panel-body">
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block disabled expendEdges" id="expendEdgesUpAndDown"> <span class="glyphicon glyphicon-resize-vertical"></span></button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block disabled expendEdges" id="expendEdgesUp"> <span class="glyphicon glyphicon-arrow-up"></span></button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block disabled expendEdges" id="expendEdgesDown"> <span class="glyphicon glyphicon-arrow-down"></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- NODE CREATION PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading"  role="tab" id="headingCreateNode">
            <h4 class="panel-title">
                <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#collapseCreateNode" aria-expanded="true" aria-controls="collapseCreateNode">
                    Create Nodes
                </a>
            </h4>
        </div>
        <div id="collapseCreateNode" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingCreateNode">
            <div class="panel-body">
                <div class="col-md-8">
                    <select class="form-control nodeTemplateList"  id="newItemTypeSelection">

                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block disabled" id="createItemOnView">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- RELATIONSHIP CREATION PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingCreateRel">
            <h4 class="panel-title">
                <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#collapseCreateRel" aria-expanded="true" aria-controls="collapseCreateRel">
                    Create/Edit Relationship
                </a>
            </h4>
        </div>
        <div id="collapseCreateRel" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingCreateRel">
            <div class="panel-body">

                <div class="col-md-6">
                    <label>Source Item</label>
                    <input class="form-control notyping nodeSelection " id="sourceItemSelection" type="text" />
                </div>
                <div class="col-md-6">
                    <label>Target Item</label>
                    <input class="form-control notyping nodeSelection" id="targetItemSelection" type="text" />
                </div>
                <div class="col-md-8">
                    <label>Relationship type</label>
                    <select class="form-control" id="relationshipNameList"></select>
                </div>
                <div class="col-md-4">
                    <label>Link</label>
                    <button class="btn btn-primary btn-block disabled" id="relationshipAdd">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>


