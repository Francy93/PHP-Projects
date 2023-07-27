<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 
    
    

    // ----------------------------The DataBase response (product)---------------------------------
    function reviews($idProduct){
      $collection = collection('Reviews');
      
      $findCriteria = [
        "Product" => $idProduct
      ];
      //Find all of the customers that match  this criteria
      $reviewsArray = $collection->find($findCriteria);
      
      return $reviewsArray;
    }

    //this function make array to generate suggestions from traces
    function suggestions(){

      $findCriteria = ["User" => userId()];
      $Traces = collection("Traces")->findOne($findCriteria);

      //if bobth user is logged and user has traced data
      if(userId() && $Traces){

       
        //Find all of the customers that match  this criteria
        
        $idProducts = (array)$Traces["Products"];
            arsort($idProducts);
        //specifying a maximum
        $idProducts = array_slice(array_keys($idProducts), 0, 6);

        $notRelevant = collection("Products")->find()->toArray();
        $length = count($idProducts);
        //cicling to fill possible remining white spaces
        for($i = 0; $i < (6 - $length); $i++){
          array_push($idProducts, idGet(((array)$notRelevant)[$i]));
        }
      }else{
        $data = array_slice(collection("Products")->find()->toArray(),0,6);
        $idProducts = [];
        foreach( $data as $card){
          array_push($idProducts, idGet($card));
        }

      }
      return $idProducts;

    }
    



    //this function will cicle all the reviews and cards of tracing carousel
    function sectionCicles($array, $section, $id){
        $itemsAmount = count(array_keys((array)$array)); 
        $start;
        $middle = '';
        $end;

        if($section == 0){
            $start = '    <div class="card card-outline-secondary my-4">
            <div class="card-header">
              Product Reviews
            </div>
            <div class="card-body">';
            $end = '     <a href="#" class="btn btn-success">Leave a Review</a>
          </div>
        </div>
        <!-- /.card -->';
        }else if($section == 1){
            $start = '    <!-- Our Customers -->
            <h2>Products you might like</h2>
            <div class="row">';
            $end = '    </div>
            <!-- /.row -->';
        }


        foreach ($array as $comment){
            if($section == 0){
              if($itemsAmount){
                $middle = '
                <div class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                  <h5 class="mb-4">Leave Comment</h5>
                  <div class="mb-4">
                    <span class="star-rating">
                      <a href="#"><i class="icofont-ui-rating icofont-2x"></i></a>
                      <a href="#"><i class="icofont-ui-rating icofont-2x"></i></a>
                      <a href="#"><i class="icofont-ui-rating icofont-2x"></i></a>
                      <a href="#"><i class="icofont-ui-rating icofont-2x"></i></a>
                      <a href="#"><i class="icofont-ui-rating icofont-2x"></i></a>
                    </span>
                  </div>
                  <form>
                    <div class="form-group">
                      <label>Your Comment</label>
                      <textarea class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                      <button class="btn btn-primary btn-sm" type="button"> Submit Comment </button>
                    </div>
                  </form>
                </div>';
              }else{
                
                $middle .= 
                '<div class="row justify-content-between">
                  <div class="mx-5 col-sm-auto">
                    <small class="text-muted">Posted by '.userProperty($comment['User'], "Nickname").' on '.$comment['Date'].'</small>
                
                    <p class="text-warning">'.stars(idGet($comment)).'</p>
                  </div>
                  <div class="mx-5 text-center col-sm-6">
                    <p class="text-break">'.$comment['Comment'].'</p>
                  </div>
                </div>
                <hr>';
              }
            }else if($section == 1){
                $middle .= '      <div class="col-lg-2 col-sm-4 mb-4">
                <a href=product.php?id="'.$comment.'">
                    <img class="img-fluid" src="'.productProperty($comment, "Src").'" alt="">
                </a>
              </div>';
            }
        }

        echo $start . $middle . $end;
    }













    

  function productBody($id){
    //getting the reviews array from the DataBase
    $reviews = reviews($id);
    $stars = stars($id);
	  $suggestions = suggestions();
    $StarsNum = is_numeric(productProperty($id, "Stars"))? productProperty($id, "Stars"): 0;

      // ----------------------------The body---------------------------------
    

      //this is part of the <body> (start container)
      echo'  <!-- Page Content -->
      <div class="container">';

      //this is part of the <body>  (product)
      echo'    <!-- Heading Row -->
      <div class="row align-items-center my-5">
        <div class="col-lg-7">
          <img class="img-fluid rounded mb-4 mb-lg-0" src="'. productProperty($id, "Src") .'" style="max-height: 95vh">
        </div>
        <!-- /.col-lg-8 -->
        <div class="col-lg-5">
          <div class="card-body">
            <h3 class="card-title">'. productProperty($id, "Title") .'</h3>
            <h4>'. productProperty($id, "Price") .'£</h4>
            <p class="card-text">'. productProperty($id, "Description") .'</p>
            <span class="text-warning">'. $stars .'</span>'.
            number_format($StarsNum, 1, '.', '').' stars
          </div>
          <hr>
          <a class="btn btn-primary" role="button" onclick="cartAdd(this)" id="prodButt" name="'.$id.'" >ADD TO CART</a>
        </div>
        <!-- /.col-md-4 -->
      </div>
      <!-- /.row -->';
      
      //this is part of the <body>  (warning)
      echo'    <!-- Call to Action Well -->
      <div class="card text-white bg-secondary my-5 py-4 text-center">
        <div class="card-body">
          <p class="text-white m-0">WARNING! Products from foreign countries may apply additional costs and extended shipping times..</p>
        </div>
      </div>';

      //this is part of the <body>  (reviews)
      sectionCicles($reviews, 0, $id);


      //this is part of the <body>  (suggestions)
      sectionCicles($suggestions, 1, $id);


      //this is part of the <body> (end container)
      echo'  </div>
      <!-- /.container -->';
  }












// ----------------------------The html assebling (account main)---------------------------------
  function thePage(){
    //if the page has been called with a correct "get" request and the id exists in the DB
    $validator = $_GET["id"] && idExists("Products", $_GET["id"]);
    //determining what page body to load
    if($validator){

      //this is the <head>
      starting();

      //this is the <body>
      $id = $_GET["id"];
      //removing any possible superfluos symbol from id
      $id = preg_replace("/[^a-zA-Z0-9]+/", "", $id); 
      productBody($id);

      //this is the <footer>
      echo '<script> window.onload = function(){ loadingTraces("'.$id.'");} </script>';
      ending();
      

    }else{ header("Location: 404.php?Error=No DataBase response.</br>I am sorry :'("); }
  }


  $thePage = function(){ thePage(); };















  // -------------------------------ajax and requestes handling----------------------------------

  $ope = function ($method, $ajax, $request){

    switch(strtolower($method)){
      case "post":
        if(isset($request["trace"]) && userId() != "" && idExists("Products", $request["trace"])){
          $idProduct = $request["trace"];
          $findCriteria = ["User" => userId()];
          $traces = collection("Traces")->findOne($findCriteria);
          $idTraces = "";
          //creating a new one if it does not exists
          if(!$traces){
            $data = ["User" => userId(), "Products" => (object)[$idProduct => 1]];
            $insertResult = collection("Traces")->insertOne($data);
            $idTraces = $insertResult->getInsertedId();
            $findByID = idCriteria($idTraces);
            $traces = collection("Traces")->findOne($findByID);
          }
          //extracting the products array
          $products = (array)$traces["Products"];
          //updating the products array
          if(isset($products[$idProduct])){
            $products[$idProduct] = $products[$idProduct] +1;
          }else{
            $products[$idProduct] = 1;
          }
          //replacing the products array into the traces data
          $traces["Products"] = $products;
          //updating the new data
          $findById = ["User" => userId()];
          $updateResult = collection("Traces")->updateOne($findById, ['$set' => $traces], ['upsert'=>true]);
        //returning response
          if($updateResult->getMatchedCount()){
            return $updateResult->getModifiedCount()? "Succesfully Uploaded": 2;
          }else{ return $updateResult; }
        }
        break;

      case "get":
        break;

      case "put":
        break;

      case "patch":
        $findByID = idCriteria(userId());
        $oldCart = (array)userProperty("", "Cart");
        if(!isset($oldCart[$request["id"]])){
          $oldCart[$request["id"]] = 1;
        }
        $newCart = ["Cart" => (object)$oldCart];
        $updateResult = collection("Users")->updateOne($findByID, [ '$set' => $newCart]);

        if($updateResult->getMatchedCount()){
          return $updateResult->getModifiedCount()? "Successfully Added": 2;
        }else{ return 0; }
        break;

      case "delete":
        break;
    }
  };


  



  ////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////// THE MAIN ////////////////////////////////////////////// 
  ////////////////////////////////////////////////////////////////////////////////////////////////////

	//this is the ajax requests fetcher
  ajax($thePage, $ope);
?>