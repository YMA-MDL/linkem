<?php
$data = file_get_contents('config/config.json');
$json = json_decode($data, true);
?>
<h2> Nodes and Links Templates </h2>
<div class="panel panel-default " id="panelTemplates">
    <div class="panel-heading">Templates</div>
    <div class="panel-body">
            <h4>nodes</h4>
        <div class="row">
            <div class="col-md-7">
                <select class="form-control" id="nodeTemplateSelection">
                    <option selected id="emptyOption"></option>
                </select>
            </div>
            <div class="col-md-5">
                <button class="btn btn-success " id="addNodeTemplate"><span class="glyphicon glyphicon-plus-sign"></span></button>
                <button class="btn btn-danger " id="delNodeTemplate"><span class="glyphicon glyphicon-remove-circle"></span></button>
            </div>
        </div>
            <h4>edges</h4>
        <div class="row">
            <div class="col-md-7">
                <select class="form-control" id="edgeTemplateSelection">
                    <option selected id="emptyOption"></option>
                </select>
            </div>
            <div class="col-md-5">
                <button class="btn btn-success " id="addEdgeTemplate"><span class="glyphicon glyphicon-plus-sign"></span></button>
                <button class="btn btn-danger " id="delEdgeTemplate"><span class="glyphicon glyphicon-remove-circle"></span></button>
            </div>
        </div>



    </div>
    <div class="panel-group" id="templateNodeSettings" role="tablist" aria-multiselectable="true">
        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateNodeSettings_definition">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateNodeSettings" href="#collapseNodeSettings_definition" aria-expanded="true" aria-controls="collapseNodeSettings_definition">
                        Definition
                    </a>
                </h4>
            </div>
            <div id="collapseNodeSettings_definition" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateNodeSettings_definition">
                <div class="panel-body">
                    <label>Name</label>
                    <input type="text" class="form-control" id="nodeTemplateName" />

                    <label>Label</label>
                    <select class="form-control nodeTemplatePropList" id="nodeLabelSelection" multiple="multiple">
                        <option selected id="emptyOption"></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateNodeSettings_properties">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateNodeSettings" href="#collapseNodeSettings_properties" aria-expanded="true" aria-controls="collapseNodeSettings_properties">
                        Properties
                    </a>
                </h4>
            </div>
            <div id="collapseNodeSettings_properties" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateNodeSettings_properties">
                <div class="panel-body TemplateProperties" >

                </div>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateNodeSettings_Image">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateNodeSettings" href="#collapseNodeSettings_Image" aria-expanded="true" aria-controls="collapseNodeSettings_Image">
                        Image
                    </a>
                </h4>
            </div>
            <div id="collapseNodeSettings_Image" class="panel-collapse collapse" role="tabpanel" aria-labelledby="templateNodeSettings_Image">
                <div class="panel-body">
                    <div class="col-xs-12 col-md-12">
                        <a href="#" class="thumbnail">
                            <img src="./images/nodeTypes/noimage128.png" id="nodeTemplateImage" src alt="...">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-group hidden" id="templateEdgeSettings" role="tablist" aria-multiselectable="true">
        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateEdgeSettings_definition">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateEdgeSettings" href="#collapseEdgeSettings_definition" aria-expanded="true" aria-controls="collapseEdgeSettings_definition">
                        Definition
                    </a>
                </h4>
            </div>
            <div id="collapseEdgeSettings_definition" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateEdgeSettings_definition">
                <div class="panel-body">
                    <label>Name</label>
                    <input type="text" class="form-control" id="edgeTemplateName" />
                    <label>Source</label>
                    <select class="form-control nodeTemplatePropListWithEmpty" id="edgeTemplateSource" multiple="multiple">
                        <option selected id="emptyOption"></option>
                    </select>
                    <label>Target</label>
                    <select class="form-control nodeTemplatePropListWithEmpty" id="edgeTemplateTarget" multiple="multiple">
                        <option selected id="emptyOption"></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateEdgeSettings_properties">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateEdgeSettings" href="#collapseEdgeSettings_properties" aria-expanded="true" aria-controls="collapseEdgeSettings_properties">
                        Properties
                    </a>
                </h4>
            </div>
            <div id="collapseEdgeSettings_properties" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateEdgeSettings_properties">
                <div class="panel-body TemplateProperties" >

                </div>
            </div>
        </div>
    </div>
</div>
