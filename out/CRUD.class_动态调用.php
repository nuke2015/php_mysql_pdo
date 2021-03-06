<?php
require_once (__DIR__ . '/DBpdo.class.php');
class CRUD
{
    
    private static $db;
    
    public $variables;
    
    public function __construct($data = array()) {
        if (!self::$db) {
            
            //静态化,只加载一次;
            $config = parse_ini_file('settings.ini.php');
            self::$db = new DBpdo($config);
        }
        $this->variables = $data;
    }
    
    public function __set($name, $value) {
        if (strtolower($name) === $this->pk) {
            $this->variables[$this->pk] = $value;
        } else {
            $this->variables[$name] = $value;
        }
    }
    
    public function __get($name) {
        if (is_array($this->variables)) {
            if (array_key_exists($name, $this->variables)) {
                return $this->variables[$name];
            }
        }
        
        $trace = debug_backtrace();
        trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }
    
    public function save($id = "0") {
        $this->variables[$this->pk] = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
        
        $fieldsvals = '';
        $columns = array_keys($this->variables);
        
        foreach ($columns as $column) {
            if ($column !== $this->pk) $fieldsvals.= $column . " = :" . $column . ",";
        }
        
        $fieldsvals = substr_replace($fieldsvals, '', -1);
        
        if (count($columns) > 1) {
            $sql = "UPDATE " . $this->table . " SET " . $fieldsvals . " WHERE " . $this->pk . "= :" . $this->pk;
            return self::$db->query($sql, $this->variables);
        }
    }
    
    public function create() {
        $bindings = $this->variables;
        
        if (!empty($bindings)) {
            $fields = array_keys($bindings);
            $fieldsvals = array(implode(",", $fields), ":" . implode(",:", $fields));
            $sql = "INSERT INTO " . $this->table . " (" . $fieldsvals[0] . ") VALUES (" . $fieldsvals[1] . ")";
        } else {
            $sql = "INSERT INTO " . $this->table . " () VALUES ()";
        }
        
        return self::$db->query($sql, $bindings);
    }
    
    public function delete($id = "") {
        $id = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
        
        if (!empty($id)) {
            $sql = "DELETE FROM " . $this->table . " WHERE " . $this->pk . "= :" . $this->pk . " LIMIT 1";
            return self::$db->query($sql, array($this->pk => $id));
        }
    }
    
    public function find($id = "") {
        $id = (empty($this->variables[$this->pk])) ? $id : $this->variables[$this->pk];
        
        if (!empty($id)) {
            $sql = "SELECT * FROM " . $this->table . " WHERE " . $this->pk . "= :" . $this->pk . " LIMIT 1";
            $this->variables = self::$db->row($sql, array($this->pk => $id));
        }
    }
    
    public function all() {
        return self::$db->query("SELECT * FROM " . $this->table);
    }

    public function select($fields,$where,$limit,$order){
        
    }
    
    public function min($field) {
        if ($field) return self::$db->single("SELECT min(" . $field . ")" . " FROM " . $this->table);
    }
    
    public function max($field) {
        if ($field) return self::$db->single("SELECT max(" . $field . ")" . " FROM " . $this->table);
    }
    
    public function avg($field) {
        if ($field) return self::$db->single("SELECT avg(" . $field . ")" . " FROM " . $this->table);
    }
    
    public function sum($field) {
        if ($field) return self::$db->single("SELECT sum(" . $field . ")" . " FROM " . $this->table);
    }
    
    public function count($field) {
        if ($field) return self::$db->single("SELECT count(" . $field . ")" . " FROM " . $this->table);
    }
    
    //语句查询;
    public function query($sql) {
        return self::$db->query($sql);
    }
    
}
