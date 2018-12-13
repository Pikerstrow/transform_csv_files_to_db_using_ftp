<?php

include_once("../../classes/init.php");

/*Підключення до віддаленої бази через аякс*/
if(isset($_POST['ip_address']) and isset($_POST['db_user']) and isset($_POST['db_name'])){

    $db_ip_address = $_POST['ip_address'];
    $db_user       = $_POST['db_user'];
    $db_password   = !empty($_POST['db_password']) ? $_POST['db_password'] : '';
    $db_name       = $_POST['db_name'];
    $db_port       = !empty($_POST['db_port']) ? $_POST['db_port'] : 3306;
    $db_tab_prefix = !empty($_POST['db_table_prefix']) ? $_POST['db_table_prefix'] : '';

    @$remote_database = new DatabaseRemote($db_ip_address, $db_user, $db_password, $db_name, $db_port, $db_tab_prefix);

    $remote_database->test_connection_after_creating();

}



/*РОзриваємо зєднання із віддаленою БД (аякс з головної сторінки адмінки) на зміну значень в полях для настройки підключення*/
if(isset($_GET['break_connection']) and isset($_SESSION['db_ip_address']) and isset($_SESSION['db_user']) and isset($_SESSION['db_name'])){
    if(break_connection()){
        echo "<span style='color:red;'>Wrong settings!</span>";
    }
}

/*Дані для які використовувалися для підключення до віддаленї бд збергіаються в сесії.
Дана функція використуується для очищення сесії від даних, у випадку, якщо користувач змінює налаштування необхідні для підключення до віддаленої бд
*/
function break_connection(){
    global $remote_database;

    if(isset($remote_database) and !empty($remote_database)) {
        unset($remote_database);
    }

    if(isset($_SESSION['db_ip_address']) and isset($_SESSION['db_user']) and isset($_SESSION['db_name'])){

        unset($_SESSION['db_user']);
        unset($_SESSION['db_password']);
        unset($_SESSION['db_name']);
        unset($_SESSION['db_port']);
        unset($_SESSION['db_ip_address']);
        unset($_SESSION['table_name']);
        unset($_SESSION['db_table_prefix']);

        return true;
    }

    return false;
}