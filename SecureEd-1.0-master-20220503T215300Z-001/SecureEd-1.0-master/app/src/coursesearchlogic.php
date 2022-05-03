<?php
try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

    /*Get information from the search (post) request*/
    $courseid = $_POST['courseid'];
    $coursename = $_POST['coursename'];
    $semester = $_POST['semester'];
    $department = $_POST['department'];

    
    if($courseid=="")
    {
        $courseid="defaultvalue!";
    }
    if($coursename=="")
    {
        $coursename="defaultvalue!";
    }
    if($semester=="")
    {
        $semester="defaultvalue!";
    }
    if($department=="")
    {
        $department="defaultvalue!";
    }
// Parameterized the SQL Queries to ensure that it won't be affected by an sql injection
    // This is done with the prepared statement and bind the values of each query to it's respective
    // variable.
    $query = "SELECT Section.CRN, Course.CourseName, Section.Year, Section.Semester, User.Email, Section.Location
            FROM Section
            CROSS JOIN Course ON Section.Course = Course.Code
            INNER JOIN User ON Section.Instructor = User.UserID
            WHERE (CRN LIKE :course OR '$courseid'='defaultvalue!') AND
                    (Semester LIKE:semester OR '$semester'='defaultvalue!') AND
                    (Course LIKE:department OR '$department'='defaultvalue!') AND
                    (CourseName LIKE:coursename OR '$coursename' = 'defaultvalue!')";
    $SQLstmt = $db->prepare($query);
    $SQLstmt->bindParam(':course', $courseid, SQLITE3_TEXT);
    $SQLstmt->bindParam(':semester', $semester, SQLITE3_TEXT);    
    $SQLstmt->bindParam(':department', $department, SQLITE3_TEXT);   
    $SQLstmt->bindParam(':coursename', $coursename, SQLITE3_TEXT);
    $results = $SQLstmt->execute();
 

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $jsonArray[] = $row;
    }
    
    echo json_encode($jsonArray);

//note: since no changes happen to the database, it is not backed up on this page
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
?>