
<?php require_once("includes/admin_header.php"); ?>
<?php require_once("includes/admin_navigation.php"); ?>

<?php
/*If we have data about ftp and remote db connections in sessions it means that connection settings is fine and connection is ok*/
if(isset($_SESSION['ftp_host']) and isset($_SESSION['ftp_user']) and isset($_SESSION['ftp_password'])){
    $ftp_connection_result = "<span style='color:darkgreen'>Connection is ok</span>";
}

if(isset($_SESSION['db_user']) and isset($_SESSION['db_ip_address']) and isset($_SESSION['db_name'])){
    $db_connection_result = "<span style='color:darkgreen'>Connection is ok</span>";
}

?>

<?php
/*TESTING FTP CONNECTION */
if(isset($_POST['check_ftp_connection'])){
    $host = trim($_POST['ftp_host']);
    $port = isset($_POST['ftp_port']) ? trim($_POST['ftp_port']) : 21;
    $user = trim($_POST['ftp_user']);
    $password = trim($_POST['ftp_password']);

    $ftp = new Ftp($host, $port, $user, $password);

    if($ftp->connect()){
        if($ftp->login_and_get_file_list()){
            $ftp->filter_files();
            $ftp_connection_result = "<span style='color:darkgreen'>Connection is ok</span>";
        }
    } else {
        $ftp_connection_result = "<span style='color:darkred'>Wrong settings!</span>";
    }

}


/*UPLOADING AND TRANSFORMING FILE FROM FTP*/
if(isset($_POST['ftp_file_upload'])){

    $host = $_SESSION['ftp_host'];
    $port = isset($_SESSION['ftp_port']) ? $_SESSION['ftp_port'] : 21;
    $user = $_SESSION['ftp_user'];
    $password = $_SESSION['ftp_password'];

    $ftp = new Ftp($host, $port, $user, $password);

    if($ftp->connect()){
        if($ftp->login_and_get_file_list()){
            $ftp_connection_result = "<span style='color:darkgreen'>Connection is ok</span>";

            $local_file = "files/file_to_transform.csv";
            $server_file = $_POST['ftp_file'];

            if (ftp_get($ftp->connection, $local_file, $server_file, FTP_ASCII)) {

                $file = new File();
                $file->filename = 'file_to_transform.csv';
                $file->bd_table_for_file_data = $_SESSION['table_name'] = $_POST['db_table_name'];

                if (file_exists($local_file)) {
                    $result = $file->csv_to_array($local_file);
                }

                // Зберігаємо дані в локальні ДБ
                if($file->file_data_to_db($result)){
                    $file_ftp_message = "The file was transformed and moved to database successfully!";
                }

            } else {
                $file_ftp_message = "An error occurred. The file wasn't downloaded and transformed!";
            }

        }
    } else {
        $ftp_connection_result = "<span style='color:darkred'>Worng settings!</span>";
    }




}


/*UPLOADING FILE FROM LOCAL PC*/
if (isset($_POST['file_upload'])) {

    $file = new File();
    $file->bd_table_for_file_data = $_SESSION['table_name'] = $_POST['db_table_name'];

    $file->set_file($_FILES['file']);

    if ($file->save()) {
        $file_message = "The file was uploaded successfully!";
    } else {
        $file_message = join("<br>", $file->errors);
    }

    $file_path_and_name = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $file->file_path();

    if (file_exists($file_path_and_name)) {
        $result = $file->csv_to_array($file_path_and_name);
    }

    // Зберігаємо дані в локальні ДБ
    if($file->file_data_to_db($result)){
        $success_massage = "The file was transformed and moved to database successfully!";
    }
}


?>

<!-- modal show array window -->
<?php include('includes/show_array_modal.php'); ?>
<!-- end of modal show array window -->

<!-- Main Content -->
<section id="main-content">
    <div class="content">
        <div class="row">
            <!-- LOCAL PC FILE UPLOAD -->
            <div class="col-sm-12 col-xs-12 col-md-4">
                <h4 style="font-size:17px">UPLOAD FILE FROM LOCAL PC</h4>
                <hr>
                <form action="" method="post" enctype="multipart/form-data">
                    <p class="hint-text">Please, choose the table in your <b>local</b> Database to which file data will be transferred.</p>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-database"></i></span>
                            <select class="form-control" name="db_table_name" id="db_table_name" required="required">
                                <option value='file_data'>file_data</option>
                            </select>
                        </div>
                    </div>

                    <p class="hint-text">Choose file for uploading from your PC.
                        <b>Pay attention,</b> the file must have 'csv' extension. The file would be transformed
                        into an associative array.
                    </p>
                    <span class="file_upload_success_span" style="color:<?php echo (isset($file->errors) and $file->errors != []) ? 'red' : 'green'; ?>;"><?php echo isset($file_message) ? $file_message : ''; ?></span>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                 <i class="fas fa-paperclip"></i>
                            </span>
                            <input style="height:auto;" type="file" name="file" class="form-control">
                            <div class="input-group-btn">
                                <button name="file_upload" class="btn btn-primary my-btn-success" type="submit" id="file_upload_button">
                                    <i class="fas fa-upload fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

                <?php if (isset($success_massage)): ?>
                    <span style="color:green" class="success-transf-msg"><?php echo $success_massage; ?></span>
                    <div class="panel panel-primary" id="array_info_div">
                        <div class="panel-heading">Received array information:</div>
                        <div class="panel-body">
                            <p><b>Array elements quantity:</b> <?php echo count($result); ?></p>
                            <p><i><b>Note:</b> Each element is an another associative array, which consists of <?php echo count($result[0]); ?>
                                    elements</i></p>
                        </div>
                        <div class="panel-footer text-center panel-footer-show-arr"><a href="#" data-toggle="modal" data-target="#show_array" class="show-arr-a">Show array</a></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($success_massage)): ?>
                <div class="form-group">
                    <div style="width:100%;" class="input-group">
                        <button style="width:100%;" class="btn btn-danger btn-block" id="hide_button">HIDE ARRAY INFO</button>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <script>
                $(document).ready(function(){
                    $('#hide_button').click(function(e){
                        e.preventDefault();
                        $('.success-transf-msg').text('');
                        $('.file_upload_success_span').text('');
                        $('#array_info_div').css('display', 'none');
                        $(this).css('display', 'none');
                    });
                });
            </script> <!--END OF LOCAL PC FILE UPLOAD -->


            <!-- FTP FILE UPLOAD -->
            <div class="col-sm-12 col-md-4 col-xs-12">
                <h4 style="font-size:17px">UPLOAD FILE FROM REM. SERV. VIA FTP</h4>
                <?php include_once "includes/ftp_html.php"; ?>
            </div> <!-- FTP FILE UPLOAD -->

            <!-- CONNECTION TO REMOTE DB -->
            <div class="col-sm-12 col-md-4 col-xs-12">
                <h4 style="font-size:17px">CONNECT TO REMOTE DB</h4>
                <?php include_once "includes/remote_db_html.php"; ?>
            </div> <!-- CONNECTION TO REMOTE DB -->

        </div>
    </div>
</section>
<!-- End of Main Content section -->
<footer>
    <div class="copyright">
        <p>Created by <span class="panel_author">Oleksandr Mishchuk</span></p>
    </div>
</footer>
<script src="jquery/jquery-3.3.1.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
