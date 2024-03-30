#!/usr/bin/php
<?php 

$options = getopt("u:p:h", ["file","create_table","dry_run","help"]);

$username = $options['u'] ?? "root";
$password = $options['p'] ?? "";
$host = $options['h'] ?? "localhost";  

#main(); 

input_validation(); 

function input_validation(){
    $file = fopen("users.csv","r");

    fgetcsv($file);

    while (($data = fgetcsv($file)) !== FALSE)
    {
        $name = name_check($data[0]);
        $surname = name_check($data[1]);
        $x = $data[2]; 
        $email =  email_check($x);
        if($email == '') { 
            echo fwrite(STDOUT, "Error: This is not a valid email ".$data[2]);
            die(); 
            $email = str_replace("'","''",$email); 
            $valuesArr[] = "('$name','$surname','$email'),"; 
        }
    }
    $findDuplicate = array_diff_assoc( 
        $data,  
        array_unique($data) 
    ); 

    if($findDuplicate) {
        echo fwrite(STDOUT, "Error: There are duplicate emails within the spreadsheet".$findDuplicate);
        die(1); 
    }
    return $data;  
}
function insert_values(){
    $insert_query = "INSERT INTO users (name, surname, email) VALUES";

    $data = input_validation(); 

    $insert_query .= implode($data); 

    
    $insert_query[strlen($insert_query)-1] = ';'; 
  
    $con = mysqli_connect('localhost','root','', 'task_db'); 

    if($con->connect_error) {
        die("Connection failed: ".$con->connect_error );
    }
     

     //try the query and output if success or not
     try {
        $success = $con->query($insert_query); 
        if($success) {
            echo 'Values were inserted successfully';
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

function create_table() {
    //Check DB variables are properly set
    

    //create the connection and check for error
    $con = mysqli_connect('localhost','root','', 'task_db'); 
    if($con->connect_error) {
        die("Connection failed: ".$con->connect_error );
    }
    
    //remove previous table and create new one
    $create_query = " CREATE TABLE IF NOT EXISTS users(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        surname VARCHAR(50) NOT NULL,
        email VARCHAR(128) NOT NULL UNIQUE
    );";

    $drop_query = "DROP TABLE IF EXISTS users;";
    //drop table each time for testing purposes 
    $con->query($drop_query); 

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
function variable_check(){
    if(isset($username) && isset($password) && isset($host)) {
        echo 'success'; 
        return true; 
    } else {
        echo fwrite(STDOUT, "Error: missing required values for db \nPlease enter the required values using the specified directives \nRun with --help flag for instructions\n");
        return false; 
    }
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
