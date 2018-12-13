<?php
include_once("../../classes/init.php");


if(isset($_GET['send_data']) and $_GET['send_data'] == 'true'){
    //echo "<span style='color:darkgreen'>IT'S WORKING!!!!</span>";

    /*SELECT CLIENT GROUPS FROM LOCAL DB ABD STORE THEN INTO ARRAY $client_groups*/
    $query = "SELECT DISTINCT kundengruppe FROM `file_data`";
    $result = $database->query($query);

    $client_groups = [];

    while ($row = mysqli_fetch_array($result)) {
        $client_groups[] = $row['kundengruppe'];
    }

    /*SELECT NECESSARY INFO FROM REMOTE DB*/
    //1. Create remote db connection
    $db_ip_address = $_SESSION['db_ip_address'];
    $db_user       = $_SESSION['db_user'];
    $db_password   = !empty($_SESSION['db_password']) ? $_SESSION['db_password'] : '';
    $db_name       = $_SESSION['db_name'];
    $db_port       = !empty($_SESSION['db_port']) ? $_SESSION['db_port'] : 3306;
    $db_tab_prefix = !empty($_SESSION['db_table_prefix']) ? $_SESSION['db_table_prefix'] : 'ps_';

    @$remote_database = new DatabaseRemote($db_ip_address, $db_user, $db_password, $db_name, $db_port, $db_tab_prefix);

    //2. Select data
    //2.1. Select shop id
    $query = "SELECT id_shop FROM " . $db_tab_prefix . "shop WHERE active = 1";
    $result = $remote_database->query($query);
    $row = $result->fetch_row();
    $shop_id = $row[0];

    //2.2. Select lang id
    $query = "SELECT LA.id_lang FROM " . $db_tab_prefix . "lang AS LA JOIN " . $db_tab_prefix . "lang_shop AS LS ON (LA.id_lang = LS.id_lang) WHERE LA.active = 1 AND LS.id_shop = {$shop_id}";
    $result = $remote_database->query($query);
    $lang_id = [];
    while ($row = mysqli_fetch_array($result)) {
        $lang_id[] = $row['id_lang'];
    }

    /*TRANSFERRING DATA TO REMOTE DB*/

    foreach($client_groups as $cl_group){

        //1.
        $query = "INSERT INTO " . $db_tab_prefix . "group (reduction, price_display_method, show_prices, date_add, date_upd) VALUES (0,1,1,NOW(),NOW())";
        $remote_database->connection->query($query);
        $id_group = $remote_database->connection->insert_id;

        //2.
        $query = "INSERT INTO " . $db_tab_prefix . "group_shop (id_group, id_shop) VALUES ($id_group, $shop_id)";
        if(!$remote_database->query($query)) {
            echo "<span style='color:red;'>Operation failed!</span>";
            die;
        }


        foreach($lang_id as $lng_id){
            $sql = "INSERT INTO " . $db_tab_prefix . "group_lang (id_group, id_lang, name) VALUES ($id_group, $lng_id, '{$cl_group}')";
            if(!$remote_database->query($sql)){
                echo "<span style='color:red;'>Operation failed!</span>";
                die;
            }
        }

    }



    //2


    /*SELECTING MIN ID LANG*/
    $query = "SELECT min(id_lang) FROM " . $db_tab_prefix . "lang WHERE active = 1";
    $result = $remote_database->query($query);
    if(!$result){
        echo "<span style='color:red;'>Operation failed!</span>";
        die;
    }
    $row = $result->fetch_row();
    $min_id_lang = $row[0];


    /*SELECTING ALL DATA FROM LOCAL DB*/
    $query = "SELECT * FROM file_data";
    $result = $database->query($query);
    if(!$result){
        echo "<span style='color:red;'>Operation failed!</span>";
        die;
    }

    while ($row = mysqli_fetch_array($result)) {

        /*SELECTING ID GROUP*/
        $query = "SELECT id_group FROM " . $db_tab_prefix . "group_lang WHERE id_lang = $min_id_lang AND name = '" . $row['kundengruppe'] . "'";
        $res_id = $remote_database->query($query);
        if(!$res_id){
            echo "<span style='color:red;'>Operation failed! </span>";
            die;
        }
        $row_id = $res_id->fetch_row();
        $id_group = $row_id[0];


        /*INSERTING PS_CUSTOMER*/
        // Available max length in db for this field is 32 symbols...
        if(mb_strlen($row['name']) > 32){
            $row['name'] = substr($row['name'], 0, 32);
        }


        $query = "INSERT INTO " . $db_tab_prefix . "customer (id_shop, id_gender, id_default_group, id_lang, firstname, lastname, email, ";
        $query .= "passwd, active, date_add, date_upd) VALUES ($shop_id, 1,	$id_group, $min_id_lang, '" . $row['name'] . "', ";
        $query .= "'" . $row['name'] . "', '" . $row['email'] . "',	0, 1, NOW(), NOW())";

        if(!$remote_database->connection->query($query)){
            echo "<span style='color:red;'>Operation failed! </span>";
            die;
        }
        $new_customer_id = $remote_database->connection->insert_id;


        /*INSERTING INTO PS_CUSTOMER_GROUP*/
        $query = "INSERT INTO " . $db_tab_prefix . "customer_group (id_customer, id_group) VALUES ($new_customer_id, $id_group)";
        if(!$remote_database->query($query)){
            echo "<span style='color:red;'>Operation failed!</span>";
            die;
        }

        /*SELECTING ID COUNTRY*/
        $query = "SELECT id_country	FROM " . $db_tab_prefix . "country	WHERE iso_code = 'DE'";
        $res_id_country = $remote_database->query($query);
        if(!$res_id_country){
            echo "<span style='color:red;'>Operation failed!</span>";
            die;
        }
        $row_id_country = $res_id_country->fetch_row();
        $id_country = $row_id_country[0];


        /*INSERTING REST OF FIELDS FROM LOCAL DB TO PS_ADDRESS*/
        $other_fields = "Nummer: " . $row['nummer'] . "\n".
                        "Zahlungsbedingung: " . $row['zahlungsbedingung'] . "\n".
                        "Name des ansprechpartner: " . $row['name_des_ansprechpartner'];

        $name_with_addr =  $row['name'] . " Address";

        // Available max length in db for this field is 32 symbols...
        if(strlen($name_with_addr) > 32){
            $name_with_addr = substr($name_with_addr, 0, 32);
        }

        // Available max length in db for this field is 16 symbols...
        if(strlen($row['steuernummer']) > 16){
            $row['steuernummer'] = str_replace(' ', '', $row['steuernummer']);
        }

        // Available max length in db for this field is 32 symbols...
        if(mb_strlen($row['namenzusatz']) > 32){
            $row['namenzusatz'] = substr($row['namenzusatz'], 0, 32);
        }

        // Available max length in db for this field is 128 symbols...
        if(mb_strlen($row['strabe']) > 128){
            $row['strabe'] = substr($row['strabe'], 0, 128);
        }

        // Available max length in db for this field is 12 symbols...
        if(mb_strlen($row['plz']) > 12){
            $row['plz'] = substr($row['plz'], 0, 12);
        }

        // Available max length in db for this field is 64 symbols...
        if(mb_strlen($row['ort']) > 64){
            $row['ort'] = substr($row['ort'], 0, 64);
        }

        // Available max length in db for this field is 32 symbols...
        if(mb_strlen($row['telefon']) > 32){
            $row['telefon'] = substr($row['telefon'], 0, 32);
        }

        // Available max length in db for this field is 32 symbols...
        if(mb_strlen($row['ust_idnr']) > 32){
            $row['ust_idnr'] = substr($row['ust_idnr'], 0, 32);
        }

        // Available max length in db for this field is 16 symbols...
        if(mb_strlen($row['steuernummer']) > 16){
            $row['steuernummer'] = substr($row['steuernummer'], 0, 16);
        }


        $query = "INSERT INTO " . $db_tab_prefix . "address (id_country, id_state, id_customer,	alias, company,	lastname, ";
        $query .= "firstname, address1, postcode, city,	phone, vat_number, dni,	date_add, date_upd,	other) ";
        $query .= " VALUES ($id_country, 0,	$new_customer_id, '" . $name_with_addr . "', '" . $row['name'] . "', '" . $row['name'] . "', ";
        $query .= "'" . $row['namenzusatz'] . "', '" . $row['strabe'] . "', '" . $row['plz'] . "', '" . $row['ort'] . "', '" . $row['telefon'] . "', ";
	     $query .= "'" . $row['ust_idnr'] . "', '" . $row['steuernummer'] . "', NOW(), NOW(), '{$other_fields}')";

        if(!$remote_database->query($query)){
            echo "<span style='color:red;'>Operation failed!</span>";
            die;
        }

    }


    /*DELETING CURRENT DATA FROM LOCAL DB*/
    $query_del = "TRUNCATE TABLE `file_data`";

    if(!$database->query($query_del)){
        echo "<span style='color:red;'>Operation failed!</span>";
        die;
    }


   echo "<span style='color:green;'>Data was transferred successfully!</span>";


}