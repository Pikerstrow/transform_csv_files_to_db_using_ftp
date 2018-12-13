<hr>
<div class="panel panel-primary">
    <div class="panel-heading">CONNECTION TO FTP</div>
    <div class="panel-body">
        <form method="post" action="" id="ftp_connection_form">
            <table id="db_data_table" style="width:100%">
                <tr>
                    <td>HOST:</td>
                    <td>
                        <div class="error_div_db_data" id="ftp_host"></div>
                        <input type="text" name="ftp_host" required="required" class="ftp_form_input"
                               value="<?php echo isset($_SESSION['ftp_host']) ? $_SESSION['ftp_host'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>USER:</td>
                    <td>
                        <div class="error_div_db_data" id="ftp_user"></div>
                        <input type="text" name="ftp_user" required="required" class="ftp_form_input"
                               value="<?php echo isset($_SESSION['ftp_user']) ? $_SESSION['ftp_user'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>PASSWORD:</td>
                    <td>
                        <div class="error_div_db_data" id="ftp_password"></div>
                        <div class="input-group">
                            <input type="password" name="ftp_password" class="ftp_form_input"
                                   value="<?php echo isset($_SESSION['ftp_password']) ? $_SESSION['ftp_password'] : ''; ?>">
                            <div class="input-group-btn">
                                <button style="padding: 1px 7px;" class="btn btn-primary" id="switch_pass_ftp"><i class="far fa-eye-slash"></i></button>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function(){

                                $('#db_data_table input').bind('keypress', function(e) {
                                    if(e.keyCode==13){
                                        e.preventDefault();
                                        return false;
                                    }
                                });

                                $('#switch_pass_ftp').click(function(e){
                                    e.preventDefault();

                                        var hide = '<i class="far fa-eye-slash"></i>';
                                        var show = '<i class="far fa-eye"></i>';
                                        var inp_pass = $("input[name='ftp_password']");

                                        if($(this).html() == hide){
                                            $(this).html(show);
                                            $(inp_pass).prop('type', 'text');
                                        } else {
                                            $(this).html(hide);
                                            $(inp_pass).prop('type', 'password');
                                        }

                                });
                            });
                        </script>
                    </td>
                </tr>
                <tr>
                    <td>PORT:</td>
                    <td><input type="text" name="ftp_port" class="ftp_form_input" value="21" disabled></td>
                </tr>
            </table>

    </div>
    <div class="panel-footer" style="font-weight: bold;">
        <div class="row">
            <div class="col-xs-6" style="padding-right:0">
                <p style="margin-bottom:0;"><b>Connection status: </b></p>
            </div>
            <div class="col-xs-6 text-left" id="connection_response_ftp" style="padding-left:0">
                <?php echo isset($ftp_connection_result) ? $ftp_connection_result : '' ?>
            </div>
        </div>
    </div>

</div>
<button type="submit" style="width:100%;" name="check_ftp_connection" id="check_ftp_connection" class="btn btn-primary btn-block">Connect and get files / update file list</button>
</form>
<br>

<!-- For displaying list of files from FTP -->
<?php if(isset($_SESSION['ftp_host']) and isset($_SESSION['ftp_user']) and isset($_SESSION['ftp_password'])): ?>
    <div id="ftp_file_upload_form_div">
        <form action="" method="post" enctype="multipart/form-data" id="ftp_file_upload_form">

            <p class="hint-text">Please, choose the table in your <b>local</b> Database to which file data will be transferred.</p>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-database"></i></span>
                    <select class="form-control" name="db_table_name" id="db_table_name" required="required">
                        <option value='file_data'>file_data</option>
                    </select>
                </div>
            </div>


            <p class="hint-text">Select file from the list, which you're going to transform and move to database.
            </p>
            <span class="file_upload_success_span" style="color:<?php echo (isset($file->errors) and $file->errors != []) ? 'red' : 'green'; ?>;"><?php echo isset($file_ftp_message) ? $file_ftp_message : ''; ?></span>
            <div class="form-group">
                <div class="input-group">
                            <span class="input-group-addon">
                                 <i class="fas fa-paperclip"></i>
                            </span>
                    <select class="form-control" name="ftp_file">
                        <option value="" disabled selected>Choose file...</option>
                        <?php
                        $ftp_file_list = $_SESSION['ftp_list_of_files'];
                        foreach($ftp_file_list as $value){
                            echo "<option value='{$value}'>{$value}</option>";
                        }
                        ?>
                    </select>
                    <div class="input-group-btn">
                        <button name="ftp_file_upload" class="btn btn-primary my-btn-success" type="submit" id="file_upload_button">
                            <i class="fas fa-upload fa-lg"></i>
                        </button>
                    </div>
                </div>
            </div>


        </form> <!-- For displaying list of files from FTP -->

        <script>
            /*Destroying current connection on change database data in inputs*/
            $(document).ready(function(){
                $('.ftp_form_input').keyup(function(){

                    if($('#connection_response_ftp').text() == 'Connection is ok' || $('#connection_response_ftp').text().replace(/\s/g, '') == 'Connectionisok'){
                        $.get("./includes/ajax_ftp_connection.php?break_ftp_connection=true", function (data) {
                            $('#connection_response_ftp').html(data);
                        });
                    } else if ($('#connection_response_ftp').text().indexOf("Wrong settings!") > 0) {
                        $.get("./includes/ajax_ftp_connection.php?break_ftp_connection=true", function (data) {
                            $('#connection_response_ftp').html(data);
                        });
                    }

                });
            });
        </script>
    </div>
<?php endif; ?>
<script>
    /*Activating and disabling buttons "connect to ftp" and 'file upload'*/
    $(document).ready(function () {

        function monitoringConnectionFtp() {
            var connectionStatus = $('#connection_response_ftp').text();

            if (connectionStatus !== 'Connection is ok') {
                $('#ftp_file_upload_form_div').css('display', 'none');
            }
            if (connectionStatus.replace(/\s/g, '') !== 'Connectionisok') {
                $('#ftp_file_upload_form_div').css('display', 'none');
            } else {
                $('#ftp_file_upload_form_div').css('display', 'block');
            }
        }

        setInterval(function () {
            monitoringConnectionFtp();
        }, 500);


    });
</script>