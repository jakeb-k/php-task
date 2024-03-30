#!/usr/bin/php
<?php 

//set potential CLI flag directives
$options = getopt("u:p:h:n", ["file:","create_table","dry_run","help"]);

//determine the file path first
$fileName = $options['file'] ?? "users.csv"; 

//if a help flag was set, run help function and stop script
if(isset($options['help'])) {
    help();
    die(0); 
}
//if dry run flag was set, validate the input (dry_run) and stop script
if(isset($options['dry_run'])) {
    input_validation();
    die(0); 
}
//if create_table flag was set, simply create the table and stop script
if(isset($options['create_table'])) {
    create_table();
    die(0); 
}

//run query functions if no flags, or ones that are above are not used. 
create_table(); 
insert_values();  

/**
 * Help function
 * Run the formatted echo, that has all flag directive info (copied from test document)
 */
function help(){
    
    echo "
    • --file [csv file name] - this is the name of the CSV to be parsed
    • --create_table - this will cause the MySQL users table to be built (and no further action will be taken)
    • --dry_run - this will be used with the --file directive in case we want to run the script but not insert 
    into the DB. All other functions will be executed, but the database won't be altered
    • -u - MySQL username
    • -p - MySQL password
    • -h - MySQL host
    • -n - MySQL schema name
    • --help - which will output the above list of directives with details.\n"; 
}

/**
 * Calls validation functions and assigns csv values to workable array
 * @returns valuesArr - An array to be imploded and added to insert query after being validated and sanitized
 */
function input_validation(){
    global $fileName; 
  
    $file = fopen($fileName,"r");

    //skip header row 
    fgetcsv($file);

    while (($data = fgetcsv($file)) !== FALSE)
    {
        $name = name_check($data[0]);
        $surname = name_check($data[1]);
        $x = $data[2]; 
        $email =  email_check($x);
        if($email == '') { 
            echo fwrite(STDOUT, "\nError: This is not a valid email ".$data[2]);
            die(1); 
        } else {
            $email = str_replace("'","''",$email); 
            $valuesArr[] = "('$name','$surname','$email'),"; 
        }
    }

    //duplicate check to ensure emails are unique 
    $findDuplicate = array_diff_assoc( 
        $valuesArr,  
        array_unique($valuesArr) 
    ); 

    if($findDuplicate) {
        $findDuplicate = implode(',',$findDuplicate); 
        echo fwrite(STDOUT, "\nError: There is a duplicate entry within the spreadsheet ".$findDuplicate);
        die(1); 
    }
    return $valuesArr;  
}

/**
 * inserts the validated and sanitized values into the db and handles any errors
 */
function insert_values(){
    //define initial query
    $insert_query = "INSERT INTO users (name, surname, email) VALUES";

    //validate the data inputted from csv and assign to a var
    $data = input_validation(); 

    //data is formatted to allow for implode and then append to create the insert query
    $insert_query .= implode($data); 

    //removes the final , with a ; to ensure syntatically correct query
    $insert_query[strlen($insert_query)-1] = ';'; 
  
    //create the connection and end the script if theres an error
    $con = mysqli_connect('localhost','root','', 'task_db'); 

    if($con->connect_error) {
        die("Connection failed: ".$con->connect_error );
    }

    //try the query and output if success or not
    try {
        $success = $con->query($insert_query); 
        if($success) {
            echo "\nValues were inserted successfully";
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
/**
 * Creates the sql table and handles any errors
 */
function create_table() {
    //global options to allow scope to reach into sql connection
    global $options; 

    //if there was variables set, get them. use coalesence operator to set likely variables
    $username = $options['u'] ?? "root";
    $password = $options['p'] ?? "";
    $host = $options['h'] ?? "localhost";  
    $db_name = $options['n'] ?? "task_db";  
    
    //create the connection and report any error whilst ending script
    $con = mysqli_connect($host, $username, $password, $db_name); 
    if($con->connect_error) {
        die("Connection failed: ".$con->connect_error );
    }
    
    //define the table with sql create query
    $create_query = " CREATE TABLE IF NOT EXISTS users(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        surname VARCHAR(50) NOT NULL,
        email VARCHAR(128) NOT NULL UNIQUE
    );";

    //drop table each time for testing/rebuild purposes 
    $drop_query = "DROP TABLE IF EXISTS users;";
    $con->query($drop_query); 

    //try the query and output if success or not. catches errors
    try {
        $success = $con->query($create_query); 
        if($success) {
            echo "\nTable users was created successfully";
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

/**
 * Removes invalid characters and capitlizes on the first letter
 * @param name - Name or Surname to be validated/formatted correctly
 */
function name_check($name) {
    $name = preg_replace('/\PL/u', '', $name); 
    $name = strtolower($name);
    $name = ucfirst($name); 
    return $name; 
}

/**
 * Removes invalid characters, whitespace, or hidden characters. Formats in lowercase
 * @param email - email to be validated/formatted correctly
 */
function email_check($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL); 
    $email = strtolower($email); 
    return $email; 
}

?>
