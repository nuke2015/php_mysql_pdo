<?php
require_once (__DIR__ . '/DBpdo.class.php');
class CRUD
{
    protected static $table;
    protected static $pk;

    private static $db;
    
    public static $variables;

    public function __construct($data = array()) {
        if (!self::$db) {
            
            //静态化,只加载一次;
            $config = parse_ini_file('settings.ini.php');
            self::$db = new DBpdo($config);
        }
        self::$variables = $data;
    }
    
    public function __set($name, $value) {
        if (strtolower($name) === self::$pk) {
            self::$variables[self::$pk] = $value;
        } else {
            self::$variables[$name] = $value;
        }
    }
    
    public function __get($name) {
        if (is_array(self::$variables)) {
            if (array_key_exists($name, self::$variables)) {
                return self::$variables[$name];
            }
        }
        
        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }
    
    public static function save($id = "0") {
        self::$variables[self::$pk] = (empty(self::$variables[self::$pk])) ? $id : self::$variables[self::$pk];
        
        $fieldsvals = '';
        $columns = array_keys(self::$variables);
        
        foreach ($columns as $column) {
            if ($column !== self::$pk) $fieldsvals.= $column . " = :" . $column . ",";
        }
        
        $fieldsvals = substr_replace($fieldsvals, '', -1);
        
        if (count($columns) > 1) {
            $sql = "UPDATE " . self::$table . " SET " . $fieldsvals . " WHERE " . self::$pk . "= :" . self::$pk;
            return self::$db->query($sql, self::$variables);
        }
    }
    
    public static function create() {
        $bindings = self::$variables;
        
        if (!empty($bindings)) {
            $fields = array_keys($bindings);
            $fieldsvals = array(implode(",", $fields), ":" . implode(",:", $fields));
            $sql = "INSERT INTO " . self::$table . " (" . $fieldsvals[0] . ") VALUES (" . $fieldsvals[1] . ");";
        } else {
            $sql = "INSERT INTO " . self::$table . " () VALUES ();";
        }
        return self::$db->query($sql, $bindings);
    }
    
    public static function delete($id = "") {
        $id = (empty(self::$variables[self::$pk])) ? $id : self::$variables[self::$pk];
        
        if (!empty($id)) {
            $sql = "DELETE FROM " . self::$table . " WHERE " . self::$pk . "= :" . self::$pk . " LIMIT 1";
            return self::$db->query($sql, array(self::$pk => $id));
        }
    }
    
    public static function find($id = "") {
        $id = (empty(self::$variables[self::$pk])) ? $id : self::$variables[self::$pk];
        
        if (!empty($id)) {
            $sql = "SELECT * FROM " . self::$table . " WHERE " . self::$pk . "= :" . self::$pk . " LIMIT 1";
            self::$variables = self::$db->row($sql, array(self::$pk => $id));
        }
        return self::$variables;
    }
    
    public static function all() {
        return self::$db->query("SELECT * FROM " . self::$table);
    }

    public static function select($fields,$where,$limit,$order){
        
    }
    
    public static function min($field) {
        if ($field) return self::$db->single("SELECT min(" . $field . ")" . " FROM " . self::$table);
    }
    
    public static function max($field) {
        if ($field) return self::$db->single("SELECT max(" . $field . ")" . " FROM " . self::$table);
    }
    
    public static function avg($field) {
        if ($field) return self::$db->single("SELECT avg(" . $field . ")" . " FROM " . self::$table);
    }
    
    public static function sum($field) {
        if ($field) return self::$db->single("SELECT sum(" . $field . ")" . " FROM " . self::$table);
    }
    
    public static function count($field) {
        if ($field) return self::$db->single("SELECT count(" . $field . ")" . " FROM " . self::$table);
    }
    
    //语句查询;
    public static function query($sql) {
        return self::$db->query($sql);
    }
    
}
