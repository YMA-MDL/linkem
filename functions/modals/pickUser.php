<div class="modal fade" id="UserSelectionModal-<?php echo $nodeId ?>" parentid ="<?php echo $nodeId ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">User selection</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed" id="nodeList" width="100%">
                        <thead>
                            <?php
                            echo getNodeTypeColumns("User");
                            ?>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">            
                    <button type="button" class="btn btn-warning" id="UserSelectionModalReset" >Reset</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                    <button type="button" class="btn btn-primary" id="UserSelectionModalAdd" >Confirm</button>
                </div>
            </div>
        </div>
    </div>