<?php
require_once (__DIR__ . "/CRUD.class.php");

class Person Extends CRUD
{
    function __construct(){
        self::$table="persons";
        self::$pk="Id";
        parent::__construct();
    }
}
