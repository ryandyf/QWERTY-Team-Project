<?php
try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

    if (isset($_POST['submit'])) { //checks if submit var is set
        $currentDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');//get root directory
        $uploadDirectory = "\uploads\\";

        //get info about the file
        $filename = $_FILES['file']['name'];
        $filetmp  = realpath($_FILES['file']['tmp_name']);
/* ---------------------------------------------------PATHNAME CANICOLIZATION #3------------------------------------ */

        //create the upload path with the original filename
        $uploadPath = $currentDirectory . $uploadDirectory . basename($filename);
    
        //copy file to uploads folder
        copy($filetmp, $uploadPath);
        if(!realpath($uploadPath)) {
            exit("Pathname is INVALID");
        }
/* ---------------------------------------------------END OF PATHNAME CANICOLIZATION #3----------------------------------- */
        //prepare vars to insert data into database
        $handle = fopen(($_FILES['file']['tmp_name']), "r"); //sets a read-only pointer at beginning of file
        $crn = $_POST['crn']; //grabs CRN from form
        $path = pathinfo($_FILES['file']['name']); //path info for file

        //insert data into the database if csv
 /* ---------------------------------------------------INPUT VALIDATION FOR FILE #3------------------------------------ */
    
        // **IMPORTANT** This is where we check if the file is of the type CSV, if it is not then
        // throw an Exception saying the file type is incorrect!
        if($path['extension'] == 'csv') { //check if file is .csv
            while (($data = fgetcsv($handle, 9001, ",")) !== FALSE) { //iterate through csv
                $crn = $db->escapeString($crn); //sanitize the crn
                $query = "INSERT INTO Grade VALUES ('$crn', '$data[0]', '$data[1]')";//create query for db
                $db->exec($query);
            }

            $db->backup($db, "temp", $GLOBALS['dbPath']);
            fclose($handle);
            header("Location: ../public/dashboard.php");
        }else{
            // this is where we say that file type is incorrect and throw an exception!
            exit("FILE TYPE REJECTED!");
        }
 /* ---------------------------------------------------END OF INPUT VALIDATION FOR FILE #3------------------------------------ */
    
        
    }
    else{throw new Exception("entergrades failed");}
}
catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
    var_dump($e->getTraceAsString());
    echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";

    $allVars = get_defined_vars();
    debug_zval_dump($allVars);
}