<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 





    function notFound(){
        return "page not found!";
    }



    function filtering($array){
        echo '        <div class="col-lg-3">
        <h1 class="my-4">Filter by Category</h1>
        <div class="list-group">';

        foreach($array as $catButton => $status){
            echo'<a role="button" onclick="catFilter(this)" class="list-group-item '.$status.'">'.$catButton.'</a>';
        }

        echo '</div>
        </div>
        <!-- /.col-lg-3 -->';
    }

    function resultsCicle($rowsArray){

        $result = '';
        for($i=0; $i < count($rowsArray) && $i <6; $i++){
            $result .= '    <!-- Start or middle of the Row -->';
            foreach($rowsArray[$i] as $item){
                $idProduct = ((array)$item['_id'])['oid'];
                $result .= '<div name="'.$item['Title'].'" rates="'.$item['Stars'].'" price="'.$item['Price'].'" id="'.$idProduct.'" class="col-lg-4 col-sm-6 portfolio-item">
                <div class="card h-100">
                    <a href="product.php?id='.$idProduct.'">
                      <img class="card-img-top" src="'.$item['Src'].'" alt="'.$item['Title'].'">
                    </a>
                    <div class="card-body shop">
                      <h4 class="card-title">
                          <a href="product.php?id='.$idProduct.'">'.$item['Title'].'</a>
                      </h4>
                      <p class="card-text" name="'.$item['Price'].'" alt="'.$item['Stars'].'">'.$item['Price'].'£ '.stars($idProduct).'</p>
                    </div>
                </div>
              </div>';
            }
        }
        return $result;
    }

    function rows($array){

        if ($array == "standard"){
            $collection = collection('Products');
            $result = $collection->find();
            $array = $result->toArray();

        }else if(isset($array['category'])){
            $collection = collection('Products');
            $criteria = ['Category' => $array['category']];
            $result = $collection->find($criteria);
            $array = $result->toArray();
        }
        if(is_array($array)){
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
            return resultsCicle($rowsArray);
        }else{ return notFound(); }
    }













    // ----------------------------The DataBase data (shop main)---------------------------------


    //array for categories filtering
    $categoryFilter = [
        'Dresses'    => '',
        'Shirts'     => '',
        'Jeans'      => '',
        'Shoes'      => '',
        'Accessories'=> '',
        'Jewelry'    => ''
    ];

    $products = array (
        array('Blue denim shirt'   ,'200.00£' ,'product.php', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/12.jpg'),
        array('Red hoodie'         ,'200.00£' ,'product.php', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/13.jpg'),
        array('Grey sweater'       ,'200.00£' ,'product.php', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/14.jpg'),
        array('Black denim jacket' ,'200.00£' ,'product.php', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/15.jpg'),
        array('Grey sweater'       ,'200.00£' ,'product.php', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/14.jpg'),
        array('Blue denim shirt'   ,'200.00£' ,'product.php', 'https://mdbootstrap.com/img/Photos/Horizontal/E-commerce/Vertical/12.jpg')
    );





    function shopBody($dataArray){
        if(isset($dataArray['category'])){
            $cat = $dataArray['category'];
            $GLOBALS['categoryFilter'][$cat] = "active";
        }
        //this is part of the <body> (start container)
        echo '<!-- Page Content -->
        <div class="container">
        <!-- Row -->
        <div class="row">';

        //this is part of the <body>  (categories filter)
        filtering($GLOBALS['categoryFilter']);

        //this is part of the <body>
        echo '<div class="col-lg-9">';
        echo '          <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-4 mb-3">Shop
            <small>browse your desires</small>
        </h1>';
        echo '          <!-- Search Widget -->
        <div class="card mb-4">
            <h5 class="card-header">Search</h5>
            <div class="card-body">
                <div class="table-filter">
                    <div class="row">
                        <div class="col-sm-7">
                            <form class="input-group formsearch" onsubmit="return productsSearch(this)">
                                <input type="text" class="form-control" placeholder="Search for...">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">Go!</button>
                                </span>
                            </form>
                                
                            
                        </div>
                        <div class="col-sm-3-right filter-group">
                            <label>Sort</label>
                            <select class="form-control" onclick="sort(this)">
                                <option>Name</option>
                                <option>Price</option>
                                <option>Rates</option>
                            </select>
                        </div>
                            
                    </div>
                </div>
                
            </div>
        </div>
        <!-- END Search Widget -->
        <!-- starting row -->
        <div class="row" id="productsContainer">';
        
        //this is part of the <body>  (result list)
        echo rows($dataArray);

        echo'       <!-- /.row -->
        </div>        <!-- Pagination -->
        <ul class="pagination justify-content-center">
            <li class="page-item">
            <a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
            </li>
            <li class="page-item">
            <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item">
            <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
            <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
            </li>
        </ul>';

        //this is part of the <body> (end container)
        echo '        </div>
                <!-- /.col-lg-9 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->';
    }











    // ----------------------------The html assebling (shop main)---------------------------------
    
    function thePage($dataArray){
        
        //this is the <head>
        starting();

        if(isset($_GET['category'])){
            //this is the <body>
            shopBody($_GET);
        }else{ shopBody($dataArray); }

        //this is the <footer>
        ending();
    }
    
    $thePage = function(){ thePage("standard"); };









    // ----------------------------ajax handling---------------------------------
    $ope = function ($method, $ajax, $request){

        switch(strtolower($method)){
            case "post":
                if(isset($request["title"])){
                    $regex = new \MongoDB\BSON\Regex(preg_quote($request["title"]),"i");
                    $findCriteria = [ "Title" => $regex ];
                    $cursor = collection("Products")->find($findCriteria);
                    
                    return thePage($cursor->toArray());
                }else{ return 0;}
            break;
    
            case "get":
                if(isset($request["find"])){
                    $property = array_keys((array)$request["find"])[0];
                    $value  = array_values((array)$request["find"])[0];
                    $regex = new \MongoDB\BSON\Regex(preg_quote($value),"i");
                    $findCriteria = [ $property => $regex ];
                    $cursor = collection("Products")->find($findCriteria);
                    
                    return rows($cursor->toArray());
                }else{ return 0; }
            break;

            case "put":
            break;
    
            case "patch":
            break;
    
            case "delete":
            break;
        }
    };

    ajax($thePage, $ope);
?>