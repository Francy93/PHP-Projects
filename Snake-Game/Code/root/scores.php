<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 
	
    //Output header and navigation 
    $fileName = basename($_SERVER['PHP_SELF'], ".php");  //basename function is used to get the current file name
    outputHeader("SnakeGame");
    outputMenu($fileName); 
?>

<!-- Contents of the central aria -->
<div class="main Scores">   <!-- This wrap that part of the page between nav bar and footer -->
    <?php
    echo "<h1>". $fileName ." page</h1>";   //here the H1 will be print
    ?>
    <div class="container" id="rankingTable">      <!-- This div will wrap the table -->
        <table>
            <thead>
                <tr>
                    <th>NikName</th>
                    <th>Date</th>
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
    window.addEventListener("DOMContentLoaded", setTabVar());
</script>

<!-- END Contents of the central aria -->

<?php
    //Output the footer
    footer();
    outputEndPage();
?>