<?php

class File {


    public $filename;
    public $tmp_path;
    public $upload_directory = 'csv_files' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'files';

    public $errors = array();

    /*Вбудовані в пхп помилки, які можуть виникнути при звантаженні файлу */
    public $upload_errors_array = array(
        UPLOAD_ERR_OK          => "There is no errors!",
        UPLOAD_ERR_INI_SIZE    => "The uploaded file exceeds the upload max file size!",
        UPLOAD_ERR_FORM_SIZE   => "The uploaded file exceeds the MAX_FILE_SIZE directive.",
        UPLOAD_ERR_PARTIAL     => "The uploaded file was uploaded partially.",
        UPLOAD_ERR_NO_FILE     => "No file was uploaded.",
        UPLOAD_ERR_NO_TMP_DIR  => "Missing a temporary folder.",
        UPLOAD_ERR_CANT_WRITE  => "Failed to write file to disk.",
        UPLOAD_ERR_EXTENSION   => "A PHP extension stopped the file upload."
    );

    public $bd_table_for_file_data;
    public $bd_table_columns = ['nummer', 'kundengruppe', 'branche', 'anrede', 'name', 'namenzusatz', 'strabe', 'plz',
                                'ort', 'email', 'endverbraucher', 'telefon', 'zahlungsbedingung', 'steuernummer',
                                'ust_idnr', 'iban', 'name_des_ansprechpartner'];


    /**
     * @param $file - is $_FILES['uploaded_file'];
     */
    public function set_file($file) {

        // Приймаємо файл із супер глобального масиву ФАЙЛС та валідуюмо його
        if(empty($file) or !$file or !is_array($file)){
            $this->errors[] = "There was no file uploaded here!";
            return false;
        } else if ($file['error'] != 0) {
            $this->errors[] = $this->upload_errors_array[$file['error']];
            return false;
        } else if (!preg_match('#\.csv$#i', basename($file['name']))) {
            $this->errors[] = "File with '.csv' extensions accepts only!";
            return false;
        } else {
            $this->filename = basename($file['name']); // = $_FILES['uploaded_file]['name']
            $this->tmp_path = $file['tmp_name'];
        }
    }


    public function file_path(){
        return $this->upload_directory . DIRECTORY_SEPARATOR . $this->filename;
    }


    /*Метод збереження файл на сервері у вказаній в константі папці*/
    public function save(){
        if(!empty($this->errors)) {
            return false;
        }

        if(empty($this->filename) or empty($this->tmp_path)){
            $this->errors[] = 'The file was not available';
            return false;
        }

        $target_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->file_path();

        if(file_exists($target_path)){
            $this->errors[] = "The file {$this->filename} already exists";
            return false;
        }

        if(move_uploaded_file($this->tmp_path, $target_path)){
                unset($this->tmp_path);
                return true;
        } else {
            $this->errors[] = "The folder probably was not have permissions";
            return false;
        }
    }

    public function delete_file(){
        $target_path = $target_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->file_path();
        return unlink($target_path) ? true : false;
    }


    public function csv_to_array($file_path_and_name){

        $handle     = fopen($file_path_and_name, "r");

        $first_line = fgetcsv($handle, 4096, "\n"); // Зчитуємо перший рядок файлу (або "шапку" таблиці), який буде ключами в нашому ассоц. маисві

        $keys       = explode(';', $first_line[0]); // Розбиваємо рядок в масив
        $keys       = array_map("utf8_encode", $keys); // Міняємо кодировку кожного елементу масиву вбудованою функцією, щоб забрати "краказябри"

        $values     = []; // тут будуть значення, які потім обєднаються із значеннями першого рядка в ассоц. масив

        $result     = []; // тут буде кінцевий результат

        while (($line = fgetcsv($handle, 4096, "\n")) !== FALSE) {
            $values   = explode(';',$line[0]);
            $values   = array_map('utf8_encode', $values);

            $result[] = array_combine($keys, $values); // ліпимо кінцевий результат - він же ассоц. масив.
        }

        fclose($handle);

        $this->delete_file();

        // Перевіряємо чи останній елемент масивів не є пустим (трапляється якщо в кінці кожної строки csv файлу стоїть ; )
        if(empty($result[0][count($result[0])])){
            for($i=0; $i<count($result); $i++){
                array_pop($result[$i]);
            }
        }

        return $result;
    }

    public function file_data_to_db(array $file_data){
        global $database;

        $query = "INSERT INTO " . $this->bd_table_for_file_data . " (" . implode(",", $this->bd_table_columns) . ") ";
        $query .= "VALUES ";

        $tmp_values = "";

        for($i=0; $i<count($file_data); $i++){
            if(is_array($file_data[$i])) {
                if($i == (count($file_data)-1)) {
                    $tmp_values .= "(" . implode(",", array_values($file_data[$i])) . ")";
                } else {
                    $tmp_values .= "(" . implode(",", array_values($file_data[$i])) . "),";
                }
            }
        }

        $query .= $tmp_values;

        if ($database->query($query)) {
            return true;
        }

        return false;

    }

}

