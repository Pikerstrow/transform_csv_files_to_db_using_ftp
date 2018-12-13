<?php

require_once("Database.php");

class DbObject {

    protected function properties() {
        $properties = array();

        foreach (static::$db_table_fields as $db_field) {
            if (property_exists($this, $db_field)) {
                $properties[$db_field] = $this->$db_field;
            }
        }

        return $properties;
    }

    public function save(){
        global $database;

        $properties = $this->properties();

        $query = "INSERT INTO " . static::$db_table . " (" . implode(",", array_keys($properties)) . ") ";
        $query .= "VALUES ('" . implode("','", array_values($properties)) . "')";

        if ($database->query($query)) {
            return true;
        }

        return false;
    }

    public static function find_using_query($query) {
        global $database;
        $result = $database->query($query);

        $array_of_objects = array();

        while ($row = mysqli_fetch_array($result)) {
            $array_of_objects[] = static::instatiation($row);
        }
        return $array_of_objects;
    }


    public static function instatiation($data) {
        $calling_class = get_called_class();

        $obj = new $calling_class;

        foreach ($data as $property => $value) {
            if ($obj->has_property($property)) {
                $obj->$property = $value;
            }
        }
        return $obj;
    }

    private function has_property($property) {
        $all_obj_properties = get_object_vars($this);
        return (array_key_exists($property, $all_obj_properties)) ? true : false;
    }
}