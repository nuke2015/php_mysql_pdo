<?php

// Require the person class file
require ("Person.class.php");

// Instantiate the person class
$person = new Person();

// Create new person
$person->Firstname = "Kona";
$person->Age = "20";
$person->Sex = "F";
$creation = $person->Create();
dump("person create");
dump($creation);

// Update Person Info
$person->id = "4";
$person->Age = "32";
$saved = $person->Save();
dump("person save");
dump($saved);

// Find person
$person->id = "4";
$person->Find();
dump("person find");
dump($person);
dump("preson Firstname");
dump($person->Firstname);

// Delete person
$person->id = "17";
$delete = $person->Delete();
dump("person delete");
dump($delete);

// Get all persons
$persons = $person->all();
dump("person all");
dump($persons);

dump("person max");
dump($person->max("age"));
dump("person min");
dump($person->min("age"));
dump("person sum");
dump($person->sum("age"));
dump("person avg");
dump($person->avg("age"));
dump("person count");
dump($person->count("age"));

dump("preson update");
$ps = new person();
if ($persons) {
    $data = $persons[0];
    $data['Firstname']="Firstname".time();
    dump($data);
    if ($data) {
        foreach ($data as $k => $v) $ps->$k = $v;
    }
}
dump($ps->save());

dump("person query");
$sql="SELECT * FROM `persons` WHERE `Id` > '0' ORDER BY `Id` DESC LIMIT 3;";
dump($sql);
$tmp=$person->query($sql);
dump($tmp);
exit;

function dump($v) {
    echo '<pre>';
    print_r($v);
    echo '</pre>';
}
