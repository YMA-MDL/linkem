
<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#edit" aria-controls="defaulttView" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></li>
    <li role="presentation"><a href="#configview" aria-controls="profile" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a></li>
    <li role="presentation"><a href="#export" aria-controls="profile" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-export" aria-hidden="true"></span></a></li>
    <li role="presentation"><a href="#access" aria-controls="profile" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-user" aria-hidden="true"></span></a></li>
    <li role="presentation"><a href="#templates" aria-controls="profile" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-th" aria-hidden="true"></span></a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="edit">
    <?php include 'modules/view_edit.php' ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="configview">
    <?php include 'modules/view_config.php' ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="export">
    <?php include 'modules/view_export.php' ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="access">
    <?php include 'modules/view_access.php' ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="templates">
    <?php include 'modules/view_templates.php' ?>
    </div>
  </div>

</div>


<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#nodeproperties" aria-controls="defaulttView" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span></a></li>
    <li role="presentation" ><a href="#nodeaccess" aria-controls="defaulttView" role="tab" data-toggle="tab"> <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="nodeproperties">
    <?php include 'modules/node_properties.php' ?>
   </div>
    <div role="tabpanel" class="tab-pane" id="nodeaccess">
    <?php include 'modules/node_access.php' ?>
   </div>
  </div>

</div>