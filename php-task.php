#!/usr/bin/php
<?php 


$values = []; 

$options = getopt("u:p:h", ["file","create_table","dry_run","help"]);

$username = $options['u'];
$password = $options['p'] ?? "";
$host = $options['h']; 


function main(){
    $file = fopen("users.csv","r");
 
    while (($data = fgetcsv($file)) !== FALSE)
    {
        $x = [name_check($data[0]), name_check($data[1]), email_check($data[2])];
        $values[] = $x; 
    }
    
}

function create_table() {
    //Check DB variables are properly set
    if(isset($username) && isset($password) && isset($host)) {
        echo 'success'; 
    } else {
        echo fwrite(STDOUT, "Error: missing required values for db \nPlease enter the required values using the specified directives \nRun with --help flag for instructions\n");
        exit(1);
    }

    //create the connection and check for error
    $con = mysqli_connect('localhost','root','', 'task_db'); 
    if($con->connect_error) {
        die("Connection failed: ".$con->connect_error );
    }
    
    //remove previous table and create new one
    $create_query = "DROP TABLE IF EXISTS users;
    CREATE TABLE IF NOT EXISTS users(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        surname VARCHAR(50) NOT NULL,
        email VARCHAR(128) NOT NULL UNIQUE
    );";

    //try the query and output if success or not
    try {
        $success = $con->query($create_query); 
        if($success) {
            echo 'Table users was created successfully';
        } else {
            echo "Error creating table: ". $con->error; 
        }
    } //catch unforeseen errors
    catch (Exception $e) {
        echo "Error : ". $e; 
    }

    //close the connection
    $con->close(); 
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
