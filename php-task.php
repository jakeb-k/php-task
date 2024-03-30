#!/usr/bin/php
<?php 

$file = fopen("users.csv","r");

$values = []; 

while (($data = fgetcsv($file)) !== FALSE)
{
    $email = $data[2]; 

    $x = [name_check($data[0]), name_check($data[1]), email_check($data[2])];
    $values[] = $x; 
}
print_r($values); 

function name_check($name) {
    $name = preg_replace('/\PL/u', '', $name); 
    $name = strtolower($name);
    $name = ucfirst($name); 
    return $name; 
}

function email_check($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL); 
    return $email; 
}
?>
