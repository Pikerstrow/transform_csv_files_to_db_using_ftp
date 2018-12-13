
<div style="z-index:11000;" class="modal fade" id="show_array">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Result array:</h4>
            </div>
            <div class="modal-body">
                <div class="col-xs-12">
                    <div class="row">
                        <?php
                        echo "<pre>";
                            print_r($result);
                        echo "</pre>";
                        ?>
                    </div>
                </div><!--col-md-9 -->
            </div><!--Modal Body-->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->