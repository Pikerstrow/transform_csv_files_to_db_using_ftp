<hr>
<div class="panel panel-primary">
    <div class="panel-heading">CONNECTION TO REMOTE DB</div>
    <div class="panel-body">
        <form method="post" action="" id="db_connection_form">
            <table style="width:100%" id="db_data_table">
                <tr>
                    <td>IP/URL:</td>
                    <td>
                        <div class="error_div_db_data" id="ip_span"></div>
                        <input type="text" name="ip_address" required="required" class="database_form_input"
                               value="<?php echo isset($_SESSION['db_ip_address']) ? $_SESSION['db_ip_address'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>DB USER:</td>
                    <td>
                        <div class="error_div_db_data" id="db_user_span"></div>
                        <input type="text" name="db_user" required="required" class="database_form_input"
                               value="<?php echo isset($_SESSION['db_user']) ? $_SESSION['db_user'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>DB PASS.:</td>
                    <td>
                        <div class="input-group">
                            <input type="password" name="db_password" class="database_form_input"
                                   value="<?php echo isset($_SESSION['db_password']) ? $_SESSION['db_password'] : ''; ?>">
                            <div class="input-group-btn">
                                <button style="padding: 1px 7px;" class="btn btn-primary" id="switch_pass_db"><i class="far fa-eye-slash"></i></button>
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

                                    $('#switch_pass_db').click(function(e){
                                        e.preventDefault();
                                            var hide = '<i class="far fa-eye-slash"></i>';
                                            var show = '<i class="far fa-eye"></i>';
                                            var inp_pass = $("input[name='db_password']");

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
                    <td>DB PORT:</td>
                    <td><input type="text" name="db_port" class="database_form_input"
                               value="<?php echo isset($_SESSION['db_port']) ? $_SESSION['db_port'] : ''; ?>"></td>
                </tr>
                <tr>
                    <td>DB NAME:</td>
                    <td>
                        <div class="error_div_db_data" id="db_name_span"></div>
                        <input type="text" name="db_name" required="required" class="database_form_input"
                               value="<?php echo isset($_SESSION['db_name']) ? $_SESSION['db_name'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>PREFIX:</td>
                    <td>
                        <div class="error_div_db_data" id="db_table_prefix"></div>
                        <input type="text" name="db_table_prefix" required="required" class="database_form_input"
                               value="<?php echo isset($_SESSION['db_table_prefix']) ? $_SESSION['db_table_prefix'] : ''; ?>">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="panel-footer" style="font-weight: bold;">
        <div class="row">
            <div class="col-xs-6" style="padding-right:0; width:120px;">
                <p style="margin-bottom:0;"><b>Test result: </b></p>
            </div>
            <div class="col-xs-6 text-left" id="connection_response" style="padding-left:0">
                <?php echo isset($db_connection_result) ? $db_connection_result : '' ?>
            </div>
        </div>
    </div>
</div>
<button style="width:100%;" id="check_connection" class="btn btn-primary btn-block">Test remote DB connection</button>



<?php if(isset($_SESSION['table_name']) and Database::check_data_exist($_SESSION['table_name'])): ?>

<div style="margin-top:20px;display:none;" id="upl_to_rem_db_butt_div" class="text-center">
    <img src="images/wait.gif" id="preloader" style="display: none;">
    <button style="width:100%;" id="upload_to_rem_db" class="btn btn-primary btn-block">Upload data to the remote db</button>
</div>
<!--<div style="margin-top:15px;" id="upl_to_rem_db_res" class="text-center">-->
<!---->
<!--</div>-->

<?php endif; ?>

<script>
    /*Destroying current connection on change database data in inputs*/
    $(document).ready(function () {
        $('.database_form_input').keyup(function () {
            if ($('#connection_response').text() == 'Connection is ok' || $('#connection_response').text().replace(/\s/g, '') == 'Connectionisok') {
                $.get("./includes/ajax_db_connection.php?break_connection=true", function (data) {
                    $('#connection_response').html(data);
                });
            } else if ($('#connection_response').text().indexOf("Wrong settings!") > 0) {
                $.get("./includes/ajax_db_connection.php?break_connection=true", function (data) {
                    $('#connection_response').html(data);
                });
            }
        });
    });
</script>


<script>
    /*Activating and disabling buttons "connect to db" and 'file upload'*/
    $(document).ready(function () {

        function monitoringConnection() {
            var connectionStatus = $('#connection_response').text();

            if (connectionStatus !== 'Connection is ok') {
                $('#check_connection').prop('disabled', false);
                $('#check_connection').attr('title', '');
                $('#upl_to_rem_db_butt_divr').css('display', 'none');
            }
            if (connectionStatus.replace(/\s/g, '') !== 'Connectionisok') {
                $('#check_connection').prop('disabled', false);
                $('#check_connection').attr('title', '');
                $('#upl_to_rem_db_butt_div').css('display', 'none');
            } else {
                $('#check_connection').prop('disabled', true);
                $('#check_connection').attr('title', 'Connection is set! For creating another one, please erase data from inputs above.');
                $('#upl_to_rem_db_butt_div').css('display', 'block');
            }
        }

        setInterval(function () {
            monitoringConnection();
        }, 500);


    });
</script>
<br>