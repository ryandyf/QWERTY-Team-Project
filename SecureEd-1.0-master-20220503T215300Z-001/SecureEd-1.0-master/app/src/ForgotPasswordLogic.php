<?php
try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

//Variables and Email gained from user entry------------------
    
    $email = strtolower($_POST['email']);
    $SecQuestion = "";
    $emailMatch = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z-]+(\.[a-z-]+)*(\.[a-z]{3})$/i";
    $works = preg_match($emailMatch,$email);
    if($email==null)
    {exit("input did not exist");}

    if($works){
        echo "Valid Format";
    }else{
        exit("INVALID EMAIL FORMAT ");
    }

//checks if given email exists-------------
    $query = "SELECT COUNT(*) as count FROM User WHERE Email ='$email'";
    $count = $db->querySingle($query);

    if ($count == 0) {
//Invalid Email
        header("Location: ../public/ForgotPassword.php?emailcheck=fail");
    } else {
        $filename = "../resources/tmp.txt";
        $file = fopen($filename, "w+");
        fwrite($file, $email);
        $query = "SELECT SQuestion FROM User WHERE Email ='$email'";
        $SecQuestion = $db->querySingle($query);

        global $jsonArray;
        $jsonArray[0] = $email;
        $jsonArray[1] = $SecQuestion;

        echo json_encode($jsonArray);

        header("Location:../public/ForgotPasswordSecQ.php");
    }
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







