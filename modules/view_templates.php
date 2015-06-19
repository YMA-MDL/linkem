<?php
$data = file_get_contents('config/config.json');
$json = json_decode($data, true);
?>
<h2> Nodes and Links Templates </h2>
<div class="panel panel-default ">
    <div class="panel-heading">Templates</div>
    <div class="panel-body">
        <div class="col-md-6">
            <label>Nodes</label>
            <select class="form-control" id="nodeTemplateSelection">
                <option selected></option>
                <?php
                foreach ($json['nodeTypes'] as $key => $value) {
                    echo "<option>" . $value['name'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Edges</label>
            <select class="form-control" id="edgeTemplateSelection">
                <option selected></option>
                <?php
                foreach ($json['edgeTypes'] as $key => $value) {
                    echo "<option>" . $value['name'] . "</option>";
                }
                ?>
            </select>
        </div>



    </div>
    <div class="panel-group" id="templateEltSettings" role="tablist" aria-multiselectable="true">
        <div class="panel panel-info">
            <div class="panel-heading"  role="tab" id="templateEltSettings_properties">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#templateEltSettings" href="#collapseEltSettings_properties" aria-expanded="true" aria-controls="collapseEltSettings_properties">
                        Properties
                    </a>
                </h4>
            </div>
            <div id="collapseEltSettings_properties" class="panel-collapse collapse " role="tabpanel" aria-labelledby="templateEltSettings_properties">
                <div class="panel-body">

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

                </div>
            </div>
        </div>
    </div>
</div>