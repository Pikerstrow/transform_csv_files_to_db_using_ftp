<?php

include_once("../../classes/init.php");


/*CHECKING FTP CONNECTION*/

/*Розриваємо зєднання із FTP (аякс з головної сторінки адмінки) на зміну значень в полях для настройки підключення*/
if(isset($_GET['break_ftp_connection']) and isset($_SESSION['ftp_host']) and isset($_SESSION['ftp_user']) and isset($_SESSION['ftp_password'])){
    if(break_connection_ftp()){
        echo "<span style='color:red;'>Wrong settings!</span>";
    }
}

/*Дані для які використовувалися для підключення до FTP збергіаються в сесії.
Дана функція використуується для очищення сесії від даних, у випадку, якщо користувач змінює налаштування необхідні для підключення до FTP
*/
function break_connection_ftp(){
    if(isset($ftp) and !empty($ftp)) {
        unset($ftp);
    }
    if(isset($_SESSION['ftp_host']) and isset($_SESSION['ftp_user']) and isset($_SESSION['ftp_password'])){
        unset($_SESSION['ftp_host']);
        unset($_SESSION['ftp_user']);
        unset($_SESSION['ftp_password']);
        unset($_SESSION['ftp_list_of_files']);
        unset($_SESSION['table_name']);

        return true;
    }
    return false;
}