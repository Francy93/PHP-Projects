<?php
    //Include the PHP functions to be used on the page 
    include('common.php'); 

    function emptyCart(){
        return '
                    
            <div class="card">
                <div class="card-header">
                    <h5>Cart</h5>
                </div>
                <div class="card-body cart">
                    <div class="col-sm-12 empty-cart-cls text-center"> <img src="https://i.imgur.com/dCdflKN.png" width="130" height="130" class="img-fluid mb-4 mr-3">
                        <h3><strong>Your Cart is Empty</strong></h3>
                        <h4>Add something to make me happy :)</h4> <a href="shop.php" class="btn btn-primary cart-btn-transform m-3" data-abc="true">continue shopping</a>
                    </div>
                </div>
            </div>
    
        ';
    }

    function cartCicle($array){
        $itemsAmount = count($array);
        $summ = 0;
        $discountPercentage = 30;
        echo'<div class="ibox-title">
            <span class="pull-right">(<strong id="counter">'.$itemsAmount.'</strong>) items</span>
            <h5>Items in your cart</h5>
        </div>
        <div id="fullempty">';

        if(!$itemsAmount){
            echo emptyCart();
        }else{

            foreach($array as $id => $quantity){
                $discount = (float)productProperty($id,"Price") - (float)productProperty($id,"Price")/100*$discountPercentage . '£';
                $discount = number_format((float)$discount, 2, '.', '');
                $discountQ = (float)$discount*$quantity;
                $discountQ = number_format((float)$discountQ, 2, '.', '');
                $summ += (float)$discountQ;
                
                echo'                
                    <section class="ibox-content" id="'.$id.'">
                        <div class="table-responsive">
                            <table class="table shoping-cart-table">
                                <tbody>
                                <tr>
                                    <td width="90">
                                        <div class="cart-product-imitation" >
                                            <a href="product.php?id='.$id.'">
                                                <img class="card-img cart" src="'.productProperty($id,"Src").'">
                                            </a>
                                        </div>
                                    </td>
                                    <td class="desc">
                                        <h3 class="cartH3">
                                        <a href="product.php?id='.$id.'" class="text-navy">
                                        '.productProperty($id,"Title").'
                                        </a>
                                        </h3 class="cartH3">
                                        <p class="small">'.productProperty($id,"Description").'</p>
                                        <dl class="small m-b-none">
                                            <dt>Category</dt>
                                            <dd>'.productProperty($id,"Category").'</dd>
                                        </dl>

                                        <div class="m-t-sm">
                                            <a href="#" class="text-muted"><i class="fa fa-gift"></i> Add gift package</a>
                                            |
                                            <a role="button" onclick="deleteItem(this)" name="'.$id.'" class="text-muted removeButton"><i class="fa fa-trash"></i> Remove item</a>
                                        </div>
                                    </td>

                                    <td  class="price" name="'.$id.'" value="'.$discount.'">
                                        '.$discount.'£
                                        <s class="small text-muted">'.productProperty($id,"Price").'</s>
                                    </td>
                                    <td width="65">
                                        <input type="text" name="'.$id.'" onkeyup="priceUpdate(this)" class="form-control quantity" placeholder="'.$quantity.'" value = "" maxlength = "1">
                                    </td>
                                    <td>
                                        <h4 class="sectionTotal" name="'.$id.'">
                                            '.$discountQ.'£
                                        </h4>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>';
            }
        }
        return number_format((float)$summ, 2, '.', '');
    }







?>
<?php
    // ----------------------------The html assebling (account main)---------------------------------


    





    //determining if the cart list comes from guest(cookies) of user(DataBase)
    $cartItems = array();
    if(userId()){
        $cartItems = (array)userProperty("", "Cart");
        
    }else if(isset($_SESSION["Cart"])){
        $cartItems = (array)$_SESSION["Cart"];
        
    }else{ $cartItems = array(); }









    function cartBody(){
        // ----------------------------The body---------------------------------
        

        //this is part of the <body> (start container)
        echo '<div class="cartContainer">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- start items container -->
        <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">    
            <!-- start row -->
            <div class="row">';



        //this is part of the <body>  (start items column container)
        echo'            <!-- start col-md-9 -->
        <div class="col-md-9">
            <div class="ibox">';

        //this is part of the <body>  (items column container)
        $total = cartCicle($GLOBALS['cartItems']);

        //this is part of the <body>  (end items column container)
        echo'</div>
                <div class="ibox-content">
                    <button onclick="checkOut(this)" id="payButt" class="btn btn-primary pull-right"><i class="fa fa fa-shopping-cart"></i> Checkout</button>
                    <a href="shop.php" class="btn btn-white"><i class="fa fa-arrow-left"></i> Continue shopping</a>
                </div>
            </div>
            </div>';


        //this is part of the <body>  (checkout)
        echo'<div class="col-md-3 cart">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Cart Summary</h5>
                </div>
                <div class="ibox-content">
                    <span>
                        Total
                    </span>
                    <h2 class="font-bold" id="generalTotal">
                        '.$total.'£'.'
                    </h2>

                    <hr>
                    <span class="text-muted small">
                        *For United States, France and Germany applicable sales tax will be applied
                    </span>
                    <div class="m-t-sm">
                        <div class="btn-group">
                        <a role="button" onclick="checkOut(this)" id="payButt" class="btn btn-primary btn-sm"><i class="fa fa-shopping-cart"></i> Checkout</a>
                        <a href="#" class="btn btn-white btn-sm"> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Support</h5>
                </div>
                <div class="ibox-content text-center">
                    <h3 class="cartH3"><i class="fa fa-phone"></i> +43 100 783 001</h3 class="cartH3">
                    <span class="small">
                        Please contact with us if you have any questions. We are avalible 24h.
                    </span>
                </div>
            </div>

        </div>';



        //this is part of the <body> (end container)
        echo'        </div>
        <!-- end row -->
        </div>
        </div>
        <!-- end items container -->

        </div>
        <!-- end cart container -->';

    }









    // ----------------------------The html assebling (account main)---------------------------------
    function thePage(){
    
        //this is the <head>
        starting();
    
        //this is the <body>
        cartBody();
      
        //this is the <footer>
        ending();
    }
    $thePage = function(){ thePage(); };












        // -------------------------------ajax and requestes handling----------------------------------

    $ope = function ($method, $ajax, $request){
        $findByID = ['_id' => new MongoDB\BSON\ObjectID(userId())];
        $discount = 30;
        switch(strtolower($method)){
            case "post":
                break;
    
            case "get":
                break;
    
            case "put":
                //purchasing a cart and setting a order
                if(isset($request["buy"])){
                    $total = 0;
                    $balance = userProperty("", "Balance");
                    $cart = (array)userProperty("", "Cart");
                    foreach($cart as $idProduct => $quantity){
                        $price = dataProperty("Products", $idProduct, "Price");
                        $discount = (float)$price - (float)$price/100*$discount;
                        $total += number_format((float)($discount * $quantity), 2, '.', '');
                    }
                    if($total <= $balance){
                        $dataOrder = [  "User"     => userId(), 
                                        "Price"    => $total, 
                                        "Date"     => date("d/m/Y"), 
                                        "Address"  => userProperty("", "Address"),
                                        "Products" => (object)$cart
                        ];
                        $insertResult = collection("Orders")->insertOne($dataOrder);
                        if($insertResult->getInsertedCount()==1){
                            $newBalance = $balance - $price;
                            $newData = ["Balance" => $newBalance];
                            $updateResult = collection("Users")->updateOne($findByID, [ '$set' => $newData]);
                            //returning response
                            if($updateResult->getMatchedCount()){
                                return $updateResult->getModifiedCount()? emptyCart(): 2;
                            }else{ return ""; }
                        }else{ return ""; }

                    }else{ return ""; }

                }else{ return ""; }
                break;
    
            case "patch":
                //adding a new product to the cart
                if(isset($request["num"])){
                    $idProduct = $request["id"];
                    $newQuantity = $request["num"];
                    $cart = userProperty("", "Cart")? (array)userProperty("", "Cart"): null;
                    $cart[$idProduct] = $newQuantity;
                    //updating
                    $newCart = ["Cart" => (object)$cart];
                    $updateResult = collection("Users")->updateOne($findByID, [ '$set' => $newCart]);
                    //returning response
                    if($updateResult->getMatchedCount()){
                        return $updateResult->getModifiedCount()? "Successfully updated": 2;
                    }else{ return ""; }

                }else{ return ""; }
                break;
    
            case "delete":
                //deleting a product from the cart
                if(isset($request["id"])){
                    $idProduct = $request["id"];
                    $cart = userProperty("", "Cart")? (array)userProperty("", "Cart"): null;
                    unset($cart[$idProduct]);
                    //updating
                    $newCart = ["Cart" => (object)$cart];
                    $updateResult = collection("Users")->updateOne($findByID, [ '$set' => $newCart]);
                    //returning response
                    if($updateResult->getMatchedCount()){
                        return $updateResult->getModifiedCount()? "Successfully Removed": 2;
                    }else{ return ""; }

                }else{ return ""; }
                break;
        }
    };
    
    
      
    
    
    
      ////////////////////////////////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////// THE MAIN ////////////////////////////////////////////// 
      ////////////////////////////////////////////////////////////////////////////////////////////////////
    
        //this is the ajax requests fetcher
      ajax($thePage, $ope);
?>