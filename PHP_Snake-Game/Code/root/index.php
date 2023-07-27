<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 
	
    //Output header and navigation 
    outputHeader("SnakeGame");
    outputMenu("Home");
?>

<!-- Contents of the central aria -->

<main class="main Home">
    <div id="canWrap">        <!-- This div will wrap the canvas -->
        <div id="myCanvas" >
        <script>
            (()=>{  //self calling function (fatArrow) determines the autostart of the game during the page loading
                var game = new Phaser.Game("82%", "88%", Phaser.AUTO, 'myCanvas',null,false);
                game.state.add  ('Begin'   ,Intro);
                game.state.add  ('TheGame' ,GameCode);
                game.state.add  ('GameOver',End);
                game.state.start('Begin');
            })();
        </script> <!--configuring Phaser engine-->
        </div> <!-- This div will show the game content -->
    </div>
</main>
<!-- END Contents of the central aria -->

<?php
    //Output the footer
    footer();
    outputEndPage();
?>