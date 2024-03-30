#!/usr/bin/php
<?php 

$file = fopen("users.csv","r");

$values = []; 

$options = getopt("u:p:h", ["file","create_table","dry_run","help"]);

$username = $options['u'];
$password = $options['p'] ?? "";
$host = $options['h']; 


if(isset($username) && isset($password) && isset($host)) {
    echo 'success'; 
} else {
    echo fwrite(STDOUT, "Error: missing required values for db \nPlease enter the required values using the specified directives \nRun with --help flag for instructions\n");
    exit(1);
}
#$con = mysqli_connect('localhost','root','');

while (($data = fgetcsv($file)) !== FALSE)
{
    $x = [name_check($data[0]), name_check($data[1]), email_check($data[2])];
    $values[] = $x; 
}

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
