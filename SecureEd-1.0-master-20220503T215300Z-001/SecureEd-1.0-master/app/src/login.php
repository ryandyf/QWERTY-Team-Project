<?php
try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

    /*Get information from the post request*/
    $myusername = $_POST['username'];
    $mypassword = $_POST['password'];

    /* ---------------------------------------------------INPUT VALIDATION # 1------------------------------------ */

     // These are the regular expressions I'm using to see if the email is in the format of user@domain.com
    // for example, if it contains a number or does not contain an @ it will not match this format. When it comes
    // to pasword it must contain a Capital, a Number, and must be between 8-16 characters long and the acceptable characters are
    // a-z, A-Z, 0-9
    $UserMatch = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z-]+(\.[a-z-]+)*(\.[a-z]{3})$/i"; 
    $PassMatch = "/^(?=.*[0-9])(?=.*[A-Z]).{8,16}$/";

    // This is where I use the preg_match function in which i compare the email and password given by the user
    // and check it against the regular expressions if it matches it's in the valid format, if it doesn't an 
    // exception will be thrown
    $works = preg_match($UserMatch,$myusername);
    $works2 = preg_match($PassMatch,$mypassword);
// Example: Scienceguy@gmail.com : Valid | ScienceGuy35 : Invalid no @domain.com | ScienceGuy@gmail : Invalid no .com and etc..
    if($works){
        echo "";
    }else{
        exit("USERNAME OR PASSWORD HAS INCORRRECT FORMAT");
    }

// Example: Password1: Valid | password1 : Invalid no capital | thiscurrentpasswordisverylong: Invalid too long and etc..
    if($works2){
        echo "";
    }else{
        exit(" USERNAME OR PASSWORD HAS INCORRRECT FORMAT ");
    }

 /* ---------------------------------------------------END OF INPUT VALIDATION #1------------------------------------ */
 
    //convert password to 80 byte hash using ripemd256 before comparing
    $hashpassword = hash('ripemd256', $mypassword);

    if($myusername==null)
    {exit("input did not exist");}


    
    $myusername = strtolower($myusername); //makes username noncase-sensitive
    global $acctype;

 /* ---------------------------------------------------SQL MITIGATION #2------------------------------------ */
    
    //query for count
    // Parameterized the SQL Queries to ensure that it won't be affected by an sql injection
    // This is done with the prepared statement and bind the values of each query to it's respective
    // variable like username to $myusername and Password1 to $mypassword
    $query ="SELECT COUNT(*) as count FROM User WHERE Email=:username AND (Password=:Password1 OR Password=:hashPassword)";
    $SQLstmt =  $db->prepare($query);
    $SQLstmt->bindValue(':username', $myusername, SQLITE3_TEXT);
    $SQLstmt->bindValue(':Password1', $mypassword, SQLITE3_TEXT);    
    $SQLstmt->bindValue(':hashPassword', $hashpassword, SQLITE3_TEXT); 
    $count = $SQLstmt->execute();

    //query for the row(s)
    // Parameterized the SQL Queries to ensure that it won't be affected by an sql injection
    // This is done with the prepared statement and bind the values of each query to it's respective
    // variable like username to $myusername and Password1 to $mypassword
    $query ="SELECT * FROM User WHERE Email=:username AND (Password=:Password1 OR Password=:hashPassword)";
    $SQLstmt2 =  $db->prepare($query);
    $SQLstmt2->bindValue(':username', $myusername, SQLITE3_TEXT);
    $SQLstmt2->bindValue(':Password1', $mypassword, SQLITE3_TEXT);    
    $SQLstmt2->bindValue(':hashPassword', $hashpassword, SQLITE3_TEXT);       
    $results = $SQLstmt2->execute();

    

    if ($results !== false) //query failed check
    {
        if (($userinfo = $results->fetchArray()) !== (null || false)) //checks if rows exist
        {
            // users or user found
            $error = false;

            $acctype = $userinfo[2];
        } else {
            // user was not found
            $error = true;

        }
    } else {
        //query failed
        $error = true;

    }

    //determine if an account that met the credentials was found
    if ($count >= 1 && !$error) {
        //login success

        if (isset($_SESSION)) {
            //a session already existed
            session_destroy();
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        } else {
            //a session did not exist
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        }
        //redirect
        header("Location: ../public/dashboard.php");
    }else {
        //login fail
        header("Location: ../public/index.php?login=fail");
    }
//note: since the database is not changed, it is not backed up
}
 /* ---------------------------------------------------END OF SQL MITIGATION #2------------------------------------ */
    
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

