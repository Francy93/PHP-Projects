<?php

$columnLeft = array("Name"=>"text", 
"Date"=>"date", 
"eMail"=>"text", 
"Password"=>"password");

$columnRight= array("Surname"=>"text", 
"Country"=>"text", 
"Nikname"=>"text", 
"Repeat-Pass"=>"password");


//This function will be called from the account page to generate the form
function inputb($arr){
    foreach($arr as $label => $type) {
        echo'<div class="group">
                <input type="'.$type.'" name="'.$label.'"  onfocusout="validateForm(this)" required="required"/><span class="highlight"></span><span class="bar"></span>
                <label>'.($label == "Date" ? '' : $label ).'</label>
            </div>';
        }
}

//Ouputs the header for the page and opening body tag
function outputHeader($title){
    echo '<!DOCTYPE html>'."\r\n\r\n".
         '<html>'."\r\n".
         "\t". '<head>'."\r\n".
         "\t\t".   '<title>' . $title . '</title>'."\r\n".
         "\t\t".   '<!-- Link to external style sheet -->'."\r\n".
         "\t\t".   '<link rel="icon" href="Assets/Site/logo.png" type="image/gif" sizes="16x16">'."\r\n".
         "\t\t".   '<link rel="stylesheet" type="text/css" href="CSS/global.css">'."\r\n".   //This instruction will load all the css files
         "\t\t".   '<script src="JS/Game/GameEngine/phaser.min.js"></script>'.
         "\t\t".   '<script src="JS/Game/start.js"></script>'.
         "\t\t".   '<script src="JS/Game/game.js"></script>'.
         "\t\t".   '<script src="JS/Game/gameOver.js"></script>'.
         "\t\t".   '<script src="JS/Game/Components/snake.js"></script>'.
         "\t\t".   '<script src="JS/Game/Components/controller.js"></script>'.
         "\t\t".   '<script src="JS/Game/Components/eyes.js"></script>'.
         "\t\t".   '<script src="JS/Game/Components/particles.js"></script>'.
         "\t\t".   '<script src="JS/Game/Components/gGlobals.js"></script>'.
         "\t\t".   '<script src="JS/Site/sGlobals.js"></script>'.
         "\t\t".   '<script src="JS/Site/account.js"></script>'.
         "\t\t".   '<script src="JS/Site/tables.js"></script>'.
         "\t". '</head>'."\r\n\r\n".
         "\t". '<body onload="checkLogin()">'."\r\n".
         "\t\t". '<img id="background" src="Assets/Site/background.gif">'."\r\n";
}

/* Ouputs the banner and the navigation bar
    The selected class is applied to the page that matches the page name variable */
function outputMenu($pageName){
    //Output banner and first part of navigation
    echo "\t\t".'<nav class="navigation">'."\r\n".
         "\t\t\t".   '<div class="Sides"><a id="logo" href="index.php">
                            <img onclick="index.php" src="Assets/Site/logo22.png"></a>
                    </div> 
                    <div>'."\r\n";
    
    //Array of pages to link to
    $AssocArray = array("home"=>"index.php", 
                        "account"=>"account.php", 
                        "scores"=>"scores.php", 
                        "instructions"=>"instructions.php");
    
    //Output navigation
    foreach($AssocArray as $page => $link) {
        echo "\t\t\t".'<a id="menBut"';
        
        if($page == $pageName){
            echo 'class="selected" ';
        }
        echo 'href="' . $link . '">' . $page . '</a>'."\r\n";
    }
    
    echo "\t\t".'</div> <div class="Sides"> <a id="LogInOut" onclick=logOut("PHPSESSID") href="account.php" >'.
                                                
                                            '</a>
                        </div>
            </nav>';
}
function footer(){
    echo "\t\t".'<footer role="contentinfo">'."\r\n".
         "\t\t\t".   '<div id="foot1"><p>Middlesex University <br> game webSite project. </p></div>'."\r\n".
         "\t\t\t".   '<div id="foot2"><p>WebDesign by <em>Francesco Arrabito</em></p></div>'."\r\n".
         "\t\t\t".   '<div id="foot3"></div>'."\r\n".
         "\t\t".'</footer>'."\r\n";
}

//Outputs closing body tag and closing HTML tag
function outputEndPage(){
    echo "\t". '</body>' ."\r\n".
         '</html>' ."\r\n";
}
?>