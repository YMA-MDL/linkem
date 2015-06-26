<?php
$data = file_get_contents('config/config.json');
$json = json_decode($data, true);
?>
<h2>Views</h2>
<div class="panel-group" id="accordionManageViews" role="tablist" aria-multiselectable="true">

        <div class="panel panel-default">
        <div class="panel-heading"  role="tab" id="headingNodetypeListing">
            <h4 class="panel-title">
                <a data-toggle="collapse" class="collapsed" data-parent="#accordionManageViews" href="#collapseNodetypeListing" aria-expanded="true" aria-controls="collapseNodetypeListing">
                    nodetype listing
                </a>
            </h4>
        </div>
        <div id="collapseNodetypeListing" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingNodetypeListing">
            <div class="panel-body">
                <div class="col-md-6">
                    <select class="form-control nodeTemplateList" id="openNodeTypeListView">
                       
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-block " id="loadNodesByType" title="add to a new view"><span class="glyphicon glyphicon-list-alt"></span></button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success btn-block " id="addNodesByType" title="add to active view"><span class="glyphicon glyphicon-plus-sign"></span></button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- NODE OPENING PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading"  role="tab" id="headingSavedViews">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordionManageViews" href="#collapseSavedViews" aria-expanded="true" aria-controls="collapseSavedViews">
                    Saved Views
                </a>
            </h4>
        </div>
        <div id="collapseSavedViews" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingSavedViews">
            <div class="panel-body">
                <div class="col-md-6">
                    <select class="form-control " id="savedViewsList">
                       
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary btn-block " id="loadSavedView" title="add to a new view"><span class="glyphicon glyphicon-folder-open"></span></button>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-danger btn-block " id="deleteSavedView" title="add to active view"><span class="glyphicon glyphicon-trash"></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- NODE OPENING PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading"  role="tab" id="headingSharedViews">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordionManageViews" href="#collapseSharedViews" aria-expanded="true" aria-controls="collapseSharedViews">
                    Shared Views
                </a>
            </h4>
        </div>
        <div id="collapseSharedViews" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingSharedViews">
            <div class="panel-body"></div>
        </div>
    </div>


    <!-- RELATIONSHIP CREATION PANEL -->
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingCustomQuery">
            <h4 class="panel-title">
                <a data-toggle="collapse" class="collapsed" data-parent="#accordionManageViews" href="#collapseCustomQuery" aria-expanded="true" aria-controls="collapseCustomQuery">
                    Custom Query
                </a>
            </h4>
        </div>
        <div id="collapseCustomQuery" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingCustomQuery">
            <div class="panel-body">
                <h4>Cypher Query</h4>
                <textarea class="form-control" rows="4" ></textarea>
                <button class="btn btn-primary btn-block " id="CypherRunButton">Run</button>

            </div>
        </div>
    </div>
</div>


