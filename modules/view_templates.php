<?php
$data = file_get_contents('config/config.json');
$json = json_decode($data, true);
?>
<h2> Nodes and Links Templates </h2>
<div class="panel panel-default " id="panelTemplates">
    <div class="panel-heading">Templates</div>
    <div class="panel-body">
        <div class="col-md-8">
            <label>Nodes</label>
            <select class="form-control" id="nodeTemplateSelection">
                <option selected id="emptyOption"></option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Add</label>
            <button class="btn btn-success btn-block" id="addNodeTemplate"><span class="glyphicon glyphicon-plus-sign"></span></button>
        </div>
        <div class="col-md-8">
            <label>Edges</label>
            <select class="form-control" id="edgeTemplateSelection">
                <option selected id="emptyOption"></option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Add</label>
            <button class="btn btn-success btn-block" id="addEdgeTemplate"><span class="glyphicon glyphicon-plus-sign"></span></button>
        </div>



    </div>
    <div class="panel-group" id="templateEltSettings" role="tablist" aria-multiselectable="true">
        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateEltSettings_definition">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateEltSettings" href="#collapseEltSettings_definition" aria-expanded="true" aria-controls="collapseEltSettings_definition">
                        Definition
                    </a>
                </h4>
            </div>
            <div id="collapseEltSettings_definition" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateEltSettings_definition">
                <div class="panel-body">
                    <label>Name</label>
                    <input type="text" class="form-control" id="TemplateName" />

                    <label>Label</label>
                    <select class="form-control nodeTemplatePropList" id="nodeLabelSelection">
                        <option selected id="emptyOption"></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateEltSettings_properties">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateEltSettings" href="#collapseEltSettings_properties" aria-expanded="true" aria-controls="collapseEltSettings_properties">
                        Properties
                    </a>
                </h4>
            </div>
            <div id="collapseEltSettings_properties" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateEltSettings_properties">
                <div class="panel-body" id="TemplateProperties">

                </div>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateEltSettings_Image">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateEltSettings" href="#collapseEltSettings_Image" aria-expanded="true" aria-controls="collapseEltSettings_Image">
                        Image
                    </a>
                </h4>
            </div>
            <div id="collapseEltSettings_Image" class="panel-collapse collapse" role="tabpanel" aria-labelledby="templateEltSettings_Image">
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
</div>