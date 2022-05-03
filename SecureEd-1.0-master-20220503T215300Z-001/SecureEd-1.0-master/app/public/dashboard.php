<?php
//Access Control

session_start(); //required to bring session variables into context

if (!isset($_SESSION['email']) or (empty($_SESSION['email']))) //check that session exists and is nonempty
{
    http_response_code(403);
    die('Forbidden');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../resources/secure_app.css">
    <link rel="icon" type="image/svg" href="../resources/Header_Lock_Image.svg">
    <script async src="../resources/nav.js"></script>
    <meta charset="utf-8" />
    <?php
      if($_SESSION['acctype']===1) //admin
      { 
          //xall adminstrategy->display()
          echo "<title>Secure ED. - Admin Dashboard</title>";
      }
      else if($_SESSION['acctype']===2) //faculty
      {
          echo "<title>Secure ED. - Faculty Dashboard</title>";
      }
      else if($_SESSION['acctype']===3) //student
      {
          echo "<title>Secure ED. - Student Dashboard</title>";
      }
      ?>

</head>
<body>
  <div id="wrapper">
    <header>
	  <table class="header_table">
	    <tbody>
		  <tr>
              <td class="lock"><img src="../resources/Header_Lock_Image.svg" style="width:9vh;" alt="Header_lock"></td>
			  <td class="title"><b>Secure ED.</b></td>
              <td class="header_table_cell"></td>
		  </tr>
		</tbody>
	  </table>
    </header>

      <!--Navigation Buttons-->
      <nav>
          <button class="button_large" type="button" onclick="toLogout();">Log Out</button>
      </nav>

      <?php
      // Our ConcreteStrategyFactory which will be creating the strategies based on the user credentials
      // depending on the account type it will call the function of either admin,faculty, or student strategy
      // and that will send us to the functions.
      if($_SESSION['acctype']===1) //admin
      {
         adminStrategy(); 
      }
      else if($_SESSION['acctype']===2) //faculty
      {
        facultyStrategy();
      }
      else if($_SESSION['acctype']===3) //student
      {
          studentStrategy();
      }
// The seperate dashboard displays. Each dashboard is created depending on which strategy we are doing
// Meaning that each dasboard will be unique to the user's account type which is based off their credentials.
      function adminStrategy() {
        echo "
            <main>          
                <h1>Admin Dashboard</h1>
                <div class=horizontal_line>
                    <hr>
                </div>
                <div>
                    <button class=\"button_large\" type=\"button\" onclick=\"location.href = 'create_account.php'\">Create Account</button>
                </div>
                <br>
                <button class=\"button_large\" type=\"button\" onclick=\"location.href = 'user_search.php'\">User Search</button>
            </main>";
    }
    function facultyStrategy() {
        echo"
        <main>         
             <h1>Faculty Dashboard</h1>
             <div class=horizontal_line>
                 <hr>
             </div>
             <div>
                 <button class=\"button_large\" type=\"button\" onclick=\"location.href = 'enter_grades.php'\">Enter Grades</button>
             </div>
         </main>";
    }
    function studentStrategy() {
        echo "
           <main>        
                <h1>Student Dashboard</h1>
                <div class=horizontal_line>
                    <hr>
                </div>
                <div>
                    <button class=\"button_large\" type=\"button\" onclick=\"location.href = 'course_search.php'\">Course Search</button>
                </div>
            </main>";
    }

    ?>
  </div>
</body>
</html>

