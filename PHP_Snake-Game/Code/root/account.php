<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 
    session_start();
	
    //Output header and navigation 
    $fileName = basename($_SERVER['PHP_SELF'], ".php");  //basename function is used to get the current file name
    outputHeader("SnakeGame");
    outputMenu($fileName); 
?>

<?php
    if (null != $_POST['eMail'] || null != $_SESSION["user"]){
        $logged = $_POST['eMail'];
        if(null == $_SESSION["user"]){
            $_SESSION["user"] = $logged;
        }
        $logged = $_SESSION["user"];
        userPage();
    }else { signPage(); }
?>

<?php
    function signPage(){

        echo '
                                            <!-- Contents of the central aria -->
            <div class="main Account">      <!-- This wrap that part og the page between nav bar and footer -->

                <div class="sign In">       <!-- This contains all the SignIn frame -->
                    <h2>SignIn</h2>   
                    <form name= "formSignin" id="formSignin" action="/account.php" onsubmit="return validateForm(this)" method="post">    <!-- A form is used to submit data -->
                        <div class = "wrapper in"> 
                            <div class="group">
                                <input type="text" name="eMail" onfocusout="validateForm(this)" required="required"/><span class="highlight"></span><span class="bar"></span>
                                <label>Your eMail</label>
                            </div>
                            <br><br>  
                            <div class="group">
                                <input type="password" name="Password" onfocusout="validateForm(this)" required="required"/><span class="highlight"></span><span class="bar"></span>
                                <label>Your Password</label>
                            </div>     
                        </div>
                        <div class="buttonwrap">
                            <button type="submit" form="formSignin" class="button bIn" value="Sign-In"> </button>
                        </div>
                    </form> 
                </div>

                <div class="sign Up">      <!-- This contains all the SignUp frame -->
                    <h2>SignUp</h2>
                    <form name="formSignup" id="formSignup" action="/account.php" onsubmit="return validateForm(this)" method="post">
                        <div class = "wrapper up">

                            <div class = "column left">
                                '; echo  inputb($GLOBALS['columnLeft']);  echo'   <!-- generate the first data colum form-->
                            </div>

                            <div class = "column right">
                                '; echo  inputb($GLOBALS['columnRight']); echo'  <!-- generate the second data colum form-->
                            </div>

                        </div>
                            <div class="buttonwrap">
                                <button type="submit" form="formSignup" class="button bUp" value="Sign-In"> </button>
                            </div>
                    </form> 
                </div>
            </div>
        ';
    }
?>
        <!-- END Contents of the central aria -->

<?php
    function userPage(){
        echo'
        <div class="main User">
            <div class="data">
                <h2>User Name</h2>
                <form name="formEdit" id="formEdit" action="/account.php" onsubmit="return validateForm(this)" method="post">
                    <div class = "wrapper ed">

                        <div class = "column left">
                            ';echo inputb($GLOBALS['columnLeft']);  echo'  <!-- generate the first data colum form-->
                        </div>

                        <div class = "column right">
                            ';echo inputb($GLOBALS['columnRight']); echo'  <!-- generate the second data colum form-->
                        </div>

                    </div>
                        <div class="buttonwrap">
                            <button type="submit" form="formEdit" id="Edit" class="button bIn" value="Update"> </button>
                        </div>
                </form>    
            </div>

            <div class="container" id="accountTable">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>  
                    <tbody>
                </table>
            </div>
        </div>
        <script> 
            window.addEventListener("DOMContentLoaded", setUser() );
            window.addEventListener("DOMContentLoaded", setTabVar() );
        </script>
        ';
    }
?>

<?php
    //Output the footer
    footer();
    outputEndPage();
?>