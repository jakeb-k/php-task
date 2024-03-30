#!/usr/bin/php
<?php 

$file = fopen("users.csv","r");


while (($data = fgetcsv($file)) !== FALSE)
{
    echo "email: " . $data[2]."\n";
}

?>
