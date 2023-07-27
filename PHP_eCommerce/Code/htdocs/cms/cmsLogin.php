<?php
    include('../common.php');

    $email= filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $loginPage = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <link rel="stylesheet" type="text/css" href="cmsLogin.css">
            </head>
            <body>
                <h1> Staff Login </h1>

                <form action="cmslogin.php" method="post">
                    <div class="login">
                        <input type="text" name="email" placeholder="Email" id="email">  
                        <input type="password" name="password" placeholder="password" id="password">  
                        <input type="submit" value="Sign In">
                    </div>
                </form>
                <div class="shadow"></div>
            </body>';

    if($email=="admin" && $password=="admin") {

        $_SESSION['loggedEmail'] = $email;

        header("Location: cms.php" );

    }else if($email.$password ==""){ 
        echo $loginPage;

    }else{
        echo $loginPage;
        echo'<script> 
                alert("Wrong credentials. Try again!"); 
                window.location.replace("cmsLogin.php");
            </script>';
    }
 ?>