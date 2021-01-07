<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 
	
    //Output header and navigation 
    $fileName = basename($_SERVER['PHP_SELF'], ".php");  //basename function is used to get the current file name
    outputHeader("SnakeGame");
    outputMenu($fileName); 
?>

<!-- Contents of the central aria -->
<div class="main Instructions">   <!-- This wrap that part of the page between nav bar and footer -->
    <div class="Instr Image">
        <img src="Assets/Site/game-simulation.gif">   <!-- This is the Snake simulation "gif"  -->
    </div>
    <div class="Instr Text">  <!-- This div includes the left part of the screen with text -->
        <?php
            echo "<h1>". $fileName ." page</h1>";  //here the H1 will be print
        ?>
        
        <div class="description">   <!-- This div contains an introducion -->
            <div id="intro">
                <h5>Snake is the common name for a video game concept where the player maneuvers a line which grows in length, with the line itself being a primary obstacle. The concept originated in the 1976 arcade game Blockade, and the ease of implementing Snake has led to hundreds of versions (some of which have the word snake or worm in the title) for many platforms. After a variant was preloaded on Nokia mobile phones in 1998, there was a resurgence of interest in the snake concept as it found a larger audience. There are over 300 Snake-like games for iOS alone.
                </h5>
            </div>
            <div id="controls">   <!-- This div contains the instructions -->
                <div>
                    <h5 id="contrText">The goal of this game is to make Snake grow by eating random items until its environment fulfillment. Snake direction is due to the pressing of arrow keys of your keyboard, rispectively Up, Down, left and right.
                    </<h5>
                </div>
                <img src="Assets/Site/keyboard-x.gif">
            </div>
        </div>
    </div>
</div>

<!-- END Contents of the central area -->

<?php
    //Output the footer
    footer();
    outputEndPage();
?>