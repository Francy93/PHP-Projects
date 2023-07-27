<?php
    //Include the PHP functions to be used on the page 
    include('common.php');
?>
<?php
    function indexHeaderSlides($title, $description, $src, $index){
        $active = '';
        $outcome = '';

        if($index == 0){
            $active = 'active';
            $outcome = '<div class="carousel-inner" role="listbox">';
        }
        $outcome .= '<!-- Slide' . ++$index . '- Set the background image for this slide in the line below -->
                    <div class="carousel-item ' . $active . '" style="background-image: url(\'' . $src . '\')">
                        <div class="carousel-caption d-none d-md-block">
                        <h3>' . $title . '</h3>
                        <p>' . $description . '</p>
                        </div>
                    </div>';

        return $outcome;
    }
    //carousel showcase
    function indexHeader($array){
        
        $cicles = count($array);
        $headerStart = '
            <header>
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">';
        $headerEnd = '        
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </header>';
        $carouselIndicators;
        $slides ="";

        //creating the carousel indicators
        for($i=0; $i<$cicles ;$i++){
            if($i==0){
                $carouselIndicators = '<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>';
            }else{ $carouselIndicators .= '<li data-target="#carouselExampleIndicators" data-slide-to="'.$i.'"></li>'; }
            //creating the string containing the Slides
            $slides .= indexHeaderSlides($array[$i][0], $array[$i][1], $array[$i][2], $i);
        }
        $carouselIndicators .= '</ol>';
        $slides .= '</div>' ; 

        //--------THE OUTPUT---------
        //printing the whole carousel
        echo $headerStart . $carouselIndicators . $slides . $headerEnd;
    }
    
    function categoryCicle($rowsArray){
        echo '<h1 class="my-4">Categories</h1>';
        for($i=0; $i<count($rowsArray) ;$i++){
            echo '    <!-- Marketing Icons Section -->
            <div class="row">';
            for($j=0; $j<count($rowsArray[$i]) ;$j++){
                echo'<div class="col-lg-4 mb-4 cat">
                <div class="card h-100">
                  <a href="'.$rowsArray[$i][$j][1].'">
                    <img class="card-img cat" src="'.$rowsArray[$i][$j][2].'" alt="'.$rowsArray[$i][$j][0].'">
                    <h4 class="catText">'.$rowsArray[$i][$j][0].'</h1>
                  </a>
                </div>
              </div>';
            }
            echo '</div>
            <!-- /.row -->';
        }
    }

    function showcaseCicle($rowsArray){
        echo '    <!-- Showcase Section -->';
        for($i=0; $i<count($rowsArray) ;$i++){
            echo '    <h2 class="indexH2">'.$rowsArray[$i][0][0].'</h2 class="indexH2">
            <div class="row">';
            for($j=0; $j<count($rowsArray[$i]) ;$j++){
                echo'      <div class="col-lg-4 col-sm-6 portfolio-item">
                <div class="card h-100">
                  <a href="'.$rowsArray[$i][$j][4].'"><img class="card-img-top" src="'.$rowsArray[$i][$j][5].'" alt="'.$rowsArray[$i][$j][3].'"></a>
                  <div class="card-body index">
                    <h4 class="card-title">
                      <a href="'.$rowsArray[$i][$j][4].'">'.$rowsArray[$i][$j][1].'</a>
                    </h4>
                    <p class="card-text">'.$rowsArray[$i][$j][3].'</p>
                    <p class="card-text">⭐⭐⭐⭐✰</p>
                    <hr>
                    <p class="card-text">'.$rowsArray[$i][$j][2].'</p>
                    <a href="'.$rowsArray[$i][$j][4].'" class="btn btn-primary">ADD TO CART</a>
                    <a href="'.$rowsArray[$i][$j][4].'" class="btn btn-secondary">DETAILS</a>
                  </div>
                </div>
              </div>';
            }
            echo '</div>
            <!-- /.row -->';
        }
    }

    function rows($array, $level){
        $rowsArray = array();
        $tempArray = array();

        $counter = 0;
        for($i=0; $i<count($array) ;$i++){
            $counter++;
            $tempArray[] = $array[$i];
            //making rows by 3
            if($counter >= 3 || $i == count($array)-1){
                $counter = 0;
                $rowsArray[] = $tempArray;
                $tempArray = array();
            }
        }
        switch($level){
            case 0: categoryCicle($rowsArray);
                break;
            case 1: showcaseCicle($rowsArray);
                break;
            default: echo'<script> console.log(System error! Wrong level entered..); </script>';
                break;
        }
        
    }
?>
<?php
    // ----------------------------The html assebling (index main)---------------------------------
$thePage = function(){
    


    //array containing the carousel data
    $slides = array (
        array('First Slide' ,'Description one'  ,'Assets/carousel/11.jpg'),
        array('Second Slide','Description two'  ,'Assets/carousel/22.jpg'),
        array('Third Slide' ,'Description three','Assets/carousel/33.jpg')
    );
    //array containing categories data
    $categories = array (
        array('Dresses'    ,'shop.php?category=Dresses' ,'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/new/img(1).jpg'),
        array('Shirts'     ,'shop.php?category=Shirts'  ,'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/new/img(2).jpg'),
        array('Jeans'      ,'shop.php?category=Jeans'   ,'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/new/img(3).jpg'),
        array('Shoes'      ,'shop.php?category=Shoes'   ,'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/new/img(4).jpg'),
        array('Accessories','shop.php?category=Accessories' ,'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/new/img(5).jpg'),
        array('Jewelry'    ,'shop.php?category=Jewelry' ,'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/new/img(6).jpg')
    );
    //array containing bestselleres and NewProduct data
    $showcases = array (
        array('Bestsellers','Blue denim shirt'  , '200.00£', 'Dresses'    , 'product.php?id=602872dd47043023e67bdff0', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/12.jpg'),
        array('Bestsellers','Red hoodie'        , '180.00£', 'Shirts'     , 'product.php?id=6004ca5fae9ac6362438b4ab', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/13.jpg'),
        array('Bestsellers','Grey sweater'      , '160.00£', 'Jeans'      , 'product.php?id=602fa13a84180000c0001571', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/14.jpg'),
        array('New Product','Black denim jacket', '140.00£', 'Shoes'      , 'product.php?id=602fa10284180000c0001570', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/15.jpg'),
        array('New Product','Grey sweater'      , '120.00£', 'Accessories', 'product.php?id=602fa13a84180000c0001571', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/14.jpg'),
        array('New Product','Blue denim shirt'  , '100.00£', 'Jewelry'    , 'product.php?id=602872dd47043023e67bdff0', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/12.jpg')
    );







    //this is the <head>
    starting();

    
    //this is part of the <body>
    indexHeader($slides);

    //this is part of the <body> (start containre)
    echo'<!-- Page Content -->
    <div class="container">';

    //this is part of the <body>  (categories)
    rows($categories, 0);
    //this is part of the <body>  (Bestsellers and New Product)
    rows($showcases  , 1);

    //this is part of the <body> (end containre)
    echo'</div>
    <!-- /.container -->';


    //this is the <footer>
    ending();
};
?>
<?php
    // ----------------------------ajax handling---------------------------------
    $ope = function ($method, $ajax, $request){

      //doing some DataBase operation
      $res = $ajax?"ajax":"browser";

      return 'This is the returned value of an operation of "'. 
      strtoupper($method) .'" due to an ' .$res. ' request: "'.
       array_keys($request)[0].' :: '.array_values($request)[0].'"';
  };


  ajax($thePage, $ope);
?>