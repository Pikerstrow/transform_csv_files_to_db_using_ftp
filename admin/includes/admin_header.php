<?php include('../classes/config.php'); ?>
<?php require_once("../classes/init.php"); ?>

<?php
if(!isset($_SESSION['user'])){
    exit(header('Location:../login.php'));
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Work with CSV files!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Rancho" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="css/main.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap/css/bootstrap-theme.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <script src="jquery/jquery-3.3.1.min.js" ></script>
    <script src="js/main.js" ></script>
</head>
<body>