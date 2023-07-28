<?php
  include('ordersAndData.php');


  function signUpPage(){
    echo'
    <!-- //this is part of the <body> (start and end container) -->

    <div class="container-fluid signup">
      <div class="row no-gutter">
        <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
        <div class="col-md-8 col-lg-6 SignUp">
          <div class="login d-flex align-items-center py-5">
            <div class="container">
              <div class="row">
                <div class="col-md-9 col-lg-8 mx-auto">
                  <h3 class="login-heading mb-4">Setting your dressing room!</h3>
                  <form name="formSignup" id="formSignup" action="/account.php" onsubmit="return false" method="post">
                    <div class="form-label-group">
                      <input type="email" name="Email"  onfocusout="validateForm(this)" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
                      <label for="inputEmail">Email address</label>
                    </div>
                    <div class="form-label-group">
                      <input type="text"  name="Name"   onfocusout="validateForm(this)" id="inputName" class="form-control" placeholder="Name" required autofocus>
                      <label for="inputName">Name</label>
                    </div>
                    <div class="form-label-group">
                      <input type="text" name="Surname" onfocusout="validateForm(this)" id="inputSurname" class="form-control" placeholder="Surname" required autofocus>
                      <label for="inputSurname">Surname</label>
                    </div>
                    <div class="form-label-group">
                      <input type="text" name="Address" onfocusout="validateForm(this)" id="inputAddress" class="form-control" placeholder="Address" required autofocus>
                      <label for="inputAddress">Address</label>
                    </div>
                    <div class="form-label-group">
                      <input type="text" name="Nickname" onfocusout="validateForm(this)" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
                      <label for="inputUsername">Username</label>
                    </div>
                    <div class="form-label-group">
                      <input type="password" name="Password" onfocusout="validateForm(this)" id="inputPassword" class="form-control" placeholder="Password" required>
                      <label for="inputPassword">Password</label>
                    </div>
    
                    <div class="custom-control custom-checkbox mb-3">
                      <input type="checkbox" name="CheckBox" class="custom-control-input" id="customCheck1" required>
                      <label class="custom-control-label" for="customCheck1">Agree terms & conditions</label>
                    </div>
                    <button onclick="validateForm(this)" class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Sign Up</button>
                    
                      
                  </form>
                  <div class="text-center">
                    <a class="small" role="button" data-toggle="collapse" data-target=".dropLogin">Registered already? Sign-In!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>';
  };

  function userPage($ordersQuantity){
    
    echo'
      <div class="container mt-5">
        <div class="row">
            <div class="col-lg-4 pb-5">
                <!-- Account Sidebar-->
                <div class="author-card pb-3">
                    <div class="author-card-cover" style="background-image: url(https://www.bootdey.com/img/Content/bg1.jpg);"><a class="btn btn-style-1 btn-white btn-sm" href="#" data-toggle="tooltip" title="" data-original-title="You currently have 290 Reward points to spend"><i class="fa fa-award text-md"></i>Your current balance is: '.userProperty("","Balance").'£</a></div>
                    <div class="author-card-profile">
                        <div class="author-card-avatar"><img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="'.userProperty("","Name")." ".userProperty("","Surname").'">
                        </div>
                        <div class="author-card-details">
                            <h5 class="author-card-name text-lg">'.userProperty("","Name")." ".userProperty("","Surname").'</h5><span class="author-card-position">ID: '.userProperty("","id").'</span>
                        </div>
                    </div>
                </div>
                <div class="wizard">
                    <nav class="list-group list-group-flush">
                        <a class="list-group-item" id="buttonOrders" onclick="dataOrOrders(this)" role="button">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><i class="fe-icon-shopping-bag mr-1 text-muted"></i>
                                    <div class="d-inline-block font-weight-medium text-uppercase">Orders List</div>
                                </div><span class="badge badge-secondary">'.$ordersQuantity.'</span>
                            </div>
                        </a><a class="list-group-item active" id="buttonData" onclick="dataOrOrders(this)" role="button"><i class="fe-icon-user text-muted"></i>Profile Settings</a>
                        
                    </nav>
                </div>
            </div>
            <!-- Profile Settings-->
            <div class="col-lg-8 pb-5" id="dynamicContainer">'.
              
            userTab() //this function is declared in te "orderAndData.php" file

          .'</div>
        </div>
      </div>';
  };

















  // ----------------------------The html assebling (account main)---------------------------------
  function thePage($dataUpdated){ //dataUpdated confirmation banner is still to be done
    
    //this is the <head>
    starting();

    //determining what page body to load
    if(isset($_SESSION["user"])){
      $collection = collection("Orders");
      $orderCriteria = ["User" => userId()];
      $orderData = $collection->find($orderCriteria);
      $ordersQ = 0;
      foreach($orderData as $order){
        $ordersQ += count((array)$order["Products"]) ;
      }

      userPage($ordersQ);
    }else{ signUpPage(); }
  
    //this is the <footer>
    ending();
  }
  $thePage = function(){ thePage(false); };















  // -------------------------------ajax and requestes handling----------------------------------

  $ope = function ($method, $ajax, $request){

    //doing some DataBase operation
    $collection = collection('Users');

    if($ajax){  
      $result = 0;
      if(($method=="post")){
              //if the request has been made from a js script
        //Create a PHP array with our search criteria
        $findCriteria = [array_keys($request)[0] => array_values($request)[0]];

        //Find all of the customers that match  this criteria
        $cursor = $collection->find($findCriteria);
        
        //Output the results
        foreach ($cursor as $cust){
          $result = json_encode($cust);
        }
      }else if(($method=="get")){
        if(isset($request["buttonData"])){
          $result = userTab();   //returning the user data tabel to an ajax
        }else{
          $result = ordersTab(); //returning the orders tabel to an ajax
        }
      }
      return $result;
    }else if($method=="post"){   //if the request has been made from the html, like "forms"
      if(isset($_POST['Address'])){
        //if ther is an user loged inside coockies the just modify existing data in the database
        if(isset($_SESSION["user"])){
            //Replace customer data for this ID
            $findByID = ['_id' => new MongoDB\BSON\ObjectID(userId())];
            $newData = (array)$request;
            unset($newData['CheckBox']);
            
            $updateResult = $collection->updateOne($findByID, [ '$set' => $newData]);
            //Echo result back to user
            if($updateResult->getMatchedCount()){
                return $updateResult->getModifiedCount()? thePage("Successfully Updated!"): thePage(false);
            }else{ return header("Location: 404.php?Error=Customer%20update%20error."); }
        }else{
          //Add the new data to the database
          $data = (array)$request;
          unset($data['CheckBox']);
          $data['Balance'] = 100;
          $data['Cart'] = new stdClass();
          $insertResult = $collection->insertOne($data);
          //Echo result back to user
          if($insertResult->getInsertedCount()==1){
            //setting the cookies with user crredentials
            if(isset($_POST['Email'])){
              $_SESSION["user"] = (object) ['Email' => $_POST['Email'], 'id' => $insertResult->getInsertedId()];
            }
            return thePage("Successfully Registered!");
          }else{ return header("Location: 404.php?Error=Customer%20registration%20error."); }
        }
        
      }else if(isset($_POST['Email'])){

        $findCriteria = [ "Email" => $_POST['Email']];
        //Find all of the customers that match  this criteria
        $cursor = $collection->find($findCriteria);
        $result;
        foreach ($cursor as $cust){
          $result = $cust;
        }
        $id = ((array) $result->_id)['oid'];
        $_SESSION["user"] = (object) ['Email' => $_POST['Email'], 'id' => $id];

        return thePage("Succesfully Logged!");
      }
    }else { return header("Location: 404.php?Error=Access%20unsucsessfull%20error."); }
  };
		




  ////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////// THE MAIN ////////////////////////////////////////////// 
  ////////////////////////////////////////////////////////////////////////////////////////////////////

	//this is the ajax requests fetcher
  ajax($thePage, $ope);
?>