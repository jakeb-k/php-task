#!/usr/bin/php
<?php 

$file = fopen("users.csv","r");


while (($data = fgetcsv($file)) !== FALSE)
{
    echo "name: " . name_check($data[0])."\n";
}

function name_check($name) {
    $name = preg_replace('/\PL/u', '', $name); 
    $name = strtolower($name);
    $name = ucfirst($name); 

    return $name; 
}
?>
