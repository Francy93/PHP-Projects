<?php
	//starting the cookies
	session_start();


	//to get collection
	function collection($collection){
		      //Include libraries
			  require __DIR__ . '/vendor/autoload.php';
        
			  //Create instance of MongoDB client
			  $mongoClient = (new MongoDB\Client("mongodb://localhost:27017"));
			  //Select a database
			  $db = $mongoClient->MDXshop;
			  //Select a collection 
			  $collection = $db->$collection;
			  return $collection;
	}

	//filtering any possible superfluos symbol from id
	function idFilter($id){
		return preg_replace("/[^a-zA-Z0-9]+/", "", $id); 
	}

	//get formatted id as search criterion
	function idCriteria($id){
		try{
			$findCriteria = ['_id' => new MongoDB\BSON\ObjectID(idFilter($id))];
		}catch(Exception $e){
			$findCriteria = ['_id' => ""];
		}
		return $findCriteria;
	}

	//determine if en id exists
	function idExists($coll, $id){
		$id = idFilter($id);
		if(idCriteria($id) != ['_id' => ""]){
			if(!$coll){
				$collections = ["Users", "Traces", "Reviews", "Products", "Orders"];
				foreach($collections as $coll){
					$found = collection($coll)->findOne(idCriteria($id));
					if($found){ return true; }
				}
			}else{
				return $found = collection($coll)->findOne(idCriteria($id));
			}
		}
		return false;
	}

	//function to get current logged user id
	function userId(){
		
		if(isset($_SESSION["user"])){
			$collection = collection('Users');
			$findCriteria = idCriteria($_SESSION["user"]->id);
			//Find the customers that match  this criteria
			$userData = $collection->findOne($findCriteria);
			$id = isset(((array)$userData)["Email"])? $_SESSION["user"]->id: "";
		}else{ $id = ""; }

		return $id;
	}

	//function to get the id from a data
	function idGet($data){
		return ((array) $data->_id)['oid'];
	}

	function debugger(){
		//debugger
		$debug = debug_backtrace();
		$caller = array_shift($debug);
		$pathArray = explode("\\", $caller['file']);
		$fileName = end($pathArray);
		return "(". $fileName ." - line ". $caller['line'] .")";
	}

	//to get single user property
	function userProperty($id, $property){
		$userID = userId();
		if($userID || $id != ""){
			$id= $id != ""? $id: $userID;
			$collection = collection('Users');
			$findCriteria = idCriteria($id);
			//Find the customers that match  this criteria
			$userData = $collection->findOne($findCriteria);
			if(strtolower($property) == "id" || strtolower($property) == "_id"){ 
				return $userData || $userData != "null" ? $id: ""; 
			}
			return $userData? $userData->$property: 'No DataBase result! ' .debugger();
		}else{ return 'No user found '. debugger(); } // returning a message and this line number
	}
	//to get product single property
	function productProperty($id, $property){
		$collection = collection('Products');
		$findCriteria = idCriteria($id);
		//Find the customers that match  this criteria
		$productData = $collection->findOne($findCriteria);
		return $productData? $productData->$property: 'No DataBase result! ' . debugger();
	}

	//to get properties from every collection
	function dataProperty($coll, $id, $property){
		$collection = collection($coll);
			$findCriteria = idCriteria($id);
			//Find the customers that match  this criteria
			$userData = $collection->findOne($findCriteria);
			if(strtolower($property) == "id" || strtolower($property) == "_id"){ 
				return $userData || $userData != "null" ? $id: ""; 
			}
			return $userData? $userData->$property: "";
	}


	function stars($idStars){
          
		//creating the stars string
		$starsNumber = productProperty($idStars, "Stars");
		$starsNumber = is_numeric($starsNumber)? $starsNumber: dataProperty("Reviews",$idStars, "Stars");
		$starsNumber = is_numeric($starsNumber)? $starsNumber: 0;
		$stars ="";
		for ($i = 0;$i < round($starsNumber) ;$i++){
		  if($i == round($starsNumber)-1){
			$stars.= " ★ ";
			for ($j = 0;$j < 4-$i ;$j++){
			  $stars.= " ☆ ";
			}
		  }else{ $stars.= " ★ "; }
		}
		return $stars;
	  }
	  
	// listening browser requestes
	function ajax($stdPage, $operation){
		$outputArray = true; //this switcher determines if the chatched data will be gotten as an array or original type
		switch(true){
			case $_SERVER['REQUEST_METHOD'] == 'POST':
				//"echo" is the method to return back a response to the browser
				$ajax = false;
				if(isset($_POST['ajax'])){
					$_POST = (array) json_decode($_POST['ajax'], false);
					$ajax = true;
				}

				echo $operation("post", $ajax, $_POST);
				$_POST = null;
				break;
			case isset($_GET['ajax']):
				$data = $_GET['ajax'];                       //simple data
				if("\"id\"" == strtolower($data)){
					echo userId();
					break;
				}
				$dataARR = (array)json_decode($data, false); //array
				$output = $outputArray? $dataARR: $data;

				echo $operation("get", true, $output);
				$_GET  = null;
				break;
			case $_SERVER['REQUEST_METHOD'] == strtoupper("delete"):
				$json = substr($_SERVER['PATH_INFO'], 1);
				$obj = json_decode($json, false);
				$_DELETE =  (array) $obj;
				//extracted data
				if(isset($_DELETE['ajax'])){
					$data = $_DELETE['ajax']; //simple data
					$_DELETE = $outputArray? (array)$data: $data;
				}

				echo $operation("delete", true, $_DELETE);
				break;
			case $_SERVER['REQUEST_METHOD'] == 'PUT': case $_SERVER['REQUEST_METHOD'] == 'PATCH':
				//creating a variable containing the request method notation
				$method = $_SERVER['REQUEST_METHOD'] == 'PUT'? "put": "patch";
				$key = substr($_SERVER['PATH_INFO'], 1);
				$value = json_decode(file_get_contents("php://input"),false);
				$_PUT =  array($key => $value);
                //extracted data
				if(isset($_PUT['ajax'])){
					$data = $_PUT['ajax'];   //simple data
					$_PUT = $outputArray? (array)$data: $data;
				}
				
				echo $operation($method, true, $_PUT);

				break;
			default: 
				$_POST = null;

				$stdPage();
				break;
		}
	}












	//Ouputs the header for the page and opening body tag
	function outputHeader($title){
		echo '<!DOCTYPE html>
		<html lang="en">
		
		<head>
		
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<meta name="description" content="">
			<meta name="author" content="">
		
			<title>'. $title .'</title>
			<link rel="icon" type="image/png" href="Assets/favicon.png">

			<script src="js/global.js"></script>
			<script src="js/account.js"></script>
			<script src="js/cart.js"></script>
			<script src="js/product.js"></script>
			<script src="js/alerts.js"></script>
			<script src="js/shop.js"></script>
		
			<!-- Bootstrap core CSS -->
			<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
			<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		
			<!-- Custom styles for this template -->
			<link href="css/global.css"  rel="stylesheet">
			<link href="css/product.css" rel="stylesheet">
			<link href="css/userPage.css"rel="stylesheet">
			<link href="css/cart.css"    rel="stylesheet">
			<link href="css/index.css"   rel="stylesheet">
			<link href="css/sign-up.css" rel="stylesheet">
			<link href="css/orders.css"  rel="stylesheet">
		
		</head>
		<body>'."\r\n";
	}

	/* Ouputs the banner and the navigation bar
		The selected class is applied to the page that matches the page name variable */
	function outputMenu($pageName){
		$dropMenu = "";
		if(!isset($_SESSION["user"])){
			$dropMenu = 
				'<form id="formSignin" action="/account.php" name="formSignin" onsubmit="return false" class="px-4 py-3" method="post">
					<div class="form-group">
						<label for="exampleDropdownFormEmail1">Email address</label>
						<input type="email" name="Email" onfocusout="validateForm(this)" class="form-control" id="exampleDropdownFormEmail1" placeholder="email@example.com" required>
					</div>
					<div class="form-group">
						<label for="exampleDropdownFormPassword1">Password</label>
						<input type="password" name="Password" onfocusout="validateForm(this)" class="form-control" id="exampleDropdownFormPassword1" placeholder="Password" required>
					</div>
					<div class="form-check">
						<input type="checkbox" name="CheckBox" class="form-check-input" id="dropdownCheck">
						<label class="form-check-label" for="dropdownCheck">
							Remember me
						</label>
					</div>
					<button type="submit" onclick="validateForm(this)" class="btn btn-primary">Sign in</button>
				</form>
				<div class="dropdown-divider"></div>
						  <a class="dropdown-item" href="account.php">New around here? Sign up</a> ';
		}else{
			$dropMenu =
				'<span class="dropdown-item"><p class="header-dropdown">Signed-In as: </br><b>'.userProperty("",'Nickname').'</b></p></span>
				<div class="dropdown-divider"></div>
				<span class="dropdown-item"><i class="fas fa-file col-centered pr-3  text-center"></i><a href="account.php" class="header-dropdown-links">Your Settings</a></span>
				<div class="dropdown-divider"></div>
				<span class="dropdown-item"><a href=""  class="btn btn-danger" onclick="logout()">Log out</a></span>
				<div class="dropdown-divider"></div>
						  <a class="dropdown-item" href="account.php">Stay logged, go faster!</a>' ;
		}


		echo '<!-- Navigation -->
		<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark fixed-top">
			<div class="container">
				<a class="navbar-brand" href="index.php">E-Shopping</a>
				<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarResponsive">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a class="nav-link" href="index.php">Home</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="shop.php">Shop</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="account.php">Account</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="cart.php">Cart</a>
						</li>
						<li class="nav-item">
							<a class="nav-link"> </a>
						</li>
						<li class="nav-item">
							<form action="shop.php" class="input-group" method="post">
								<input type="text" class="form-control" name="title" onsubmit="find(this)" placeholder="Search for...">
								<span class="input-group-append">
								<button class="btn btn-secondary" type="submit">Go!</button>
								</span>
							</form>
						</li>
						<a class="nav-link"> </a>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<svg class="svg-inline--fa fa-user fa-w-14 fa-fw" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
									<path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path>
								</svg><!-- <i class="fas fa-user fa-fw"></i> Font Awesome fontawesome.com -->
							</a>
							<div class="dropdown-menu dropdown-menu-right dropLogin" id="dropLogin">'.
								$dropMenu
						  .'
						  
						  </div>
						</li>
					</ul>
				</div>
			</div>
		</nav>';
	}

	//this function will output the footer
	function footer(){
		echo'   <footer class="bg-dark text-white text-center text-lg-start">
		<!-- Grid container -->
		<div class="container p-4">
		<!--Grid row-->
		<div class="row">
			<!--Grid column-->
			<div class="col-lg-6 col-md-12 mb-4 mb-md-0">
			<h5 class="text-uppercase">Footer Content</h5>
	
			<p>
				Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iste atque ea quis
				molestias. Fugiat pariatur maxime quis culpa corporis vitae repudiandae aliquam
				voluptatem veniam, est atque cumque eum delectus sint!
			</p>
			</div>
			<!--Grid column-->
	
			<!--Grid column-->
			<div class="col-lg-3 col-md-6 mb-4 mb-md-0">
			<h5 class="text-uppercase">Categories</h5>
	
			<ul class="list-unstyled mb-0">
				<li>
				<a href="#!" class="text-white">Link 1</a>
				</li>
				<li>
				<a href="#!" class="text-white">Link 2</a>
				</li>
				<li>
				<a href="#!" class="text-white">Link 3</a>
				</li>
				<li>
				<a href="#!" class="text-white">Link 4</a>
				</li>
			</ul>
			</div>
			<!--Grid column-->
	
			<!--Grid column-->
			<div class="col-lg-3 col-md-6 mb-4 mb-md-0">
			<h5 class="text-uppercase mb-0">Useful links</h5>
	
			<ul class="list-unstyled">
				<li>
				<a href="account.php" class="text-white">Your Account</a>
				</li>
				<li>
				<a href="cms/cmsLogin.php" class="text-white">C.M.S.</a>
				</li>
				<li>
				<a href="shop.php" class="text-white">Shop</a>
				</li>
				<li>
				<a href="cart.php" class="text-white">Cart</a>
				</li>
			</ul>
			</div>
			<!--Grid column-->
		</div>
		<!--Grid row-->
		</div>
		<!-- Grid container -->
	
		<!-- Copyright -->
		<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
		© 2021 Copyright:
		<a class="text-white" href="https://mdbootstrap.com/">MDBootstrap.com</a>
		</div>
		<!-- Copyright -->
	</footer>

	<!-- Bootstrap core JavaScript -->
	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>';
		
	}




	//Outputs closing body tag and closing HTML tag
	function outputEndPage(){
		echo "\t\r\n". '</body>' ."\r\n".
				'</html>' ."\r\n";
	}

	//this function will output the header and navigation bar
	function starting(){
	$WebSiteName = "E-Shopping";
		outputHeader($WebSiteName);
		outputMenu($WebSiteName);
	}
	//this function will output the footer and closing page tags
	function ending(){
		footer();
		outputEndPage();
	}

?>