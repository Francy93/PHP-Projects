<?php
include('../common.php');

//generating array of strings for the title bar of the tab
function collParameters($coll){
    switch(strtolower($coll)){
        case "users":
            return ["Name", "Nickname", "Email", "Address", "Balance"];
        case "orders":
            return ["User", "Date", "Products", "Address", "Price"];
        case "products":
            return ["Title", "Category", "Description", "Src", "Price"];
    }
}

//retriving data by collection name
function dataArray($coll){
    switch(strtolower($coll)){
        case "users":
            return collection("Users")->find()->toArray();
        case "orders":
            return collection("Orders")->find()->toArray();
        case "products":
            return collection("Products")->find()->toArray();
    }
}
// generatin rows
function rows($coll){
    $result="<tbody>";
    
    foreach(dataArray($coll) as $row){
        $id = isset($row["_id"])? idGet($row):  "";
        $result .= '
            <tr id="'.$id.'" name="'.$coll.'">
                <td>
                    <span class="custom-checkbox">
                        <input type="checkbox"  name="options[]" value="1" class="checkBoxes">
                        <label for="checkbox"></label>
                    </span>
                </td>';
                foreach(collParameters($coll) as $title){
                    if(isset($row[$title])){

                        $row[$title] = $title == "User"? userProperty($row[$title], "Email"):$row[$title];
                        $row[$title] = is_object($row[$title])? count((array)$row[$title]):  $row[$title];
                    }
                    

                    $result .= '<td>';
                    $result .= isset($row[$title])? $row[$title] : 0;
                    $result .= $title == "Balance" || $title == "Price"? "£":"";
                    $result .= '</td>';
                }
                
    $result .= '<td>
                    <a role="button" onclick="fillEditModal(\''.$id.'\')" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                    <a role="button" onclick="return removeItem(this)" name="'.$id.'" alt="'.$coll.'" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                </td>
            </tr>
        ';
    }
    return $result."<tbody>";
}


function theHead($coll){
    $result =  '<thead>
                    <tr>
                        <th>
                            <span class="custom-checkbox">
                                <input type="checkbox" id="selectAll" onclick="selectAll(this)">
                                <label for="selectAll"></label>
                            </span>
                        </th>';
                        foreach(collParameters($coll) as $title){
                            $result .= "<th>$title</th>";
                        }
                        $result .= '
                        <th>Actions</th>
                    </tr>
                </thead>';
    return $result;
}


function tableBody($coll){

    return theHead($coll).rows($coll);
}


//this function print a dedicated modal for any collection
function editingModal($coll){
    $inputBox="";
    $function = $coll=="Users"? "validateEdit(this)": "sendEditedData(this)";

    foreach(collParameters($coll) as $input){
        $type = $input=="Price"||$input=="Balance"? "number":"text";
        $type = $input=="Email"&&$type=="text"? "email": $type;
        $type = $input=="Date"&&$type=="text"? "date": $type;

        $inputBox.='<div class="form-group">
                        <label>'.$input.'</label>
                        <input type="'.$type.'" placeholder="'.$input.'" id="modalEditing'.$input.'" onfocusout="'.$function.'" name="'.$input.'" class="form-control" required>
                    </div>';
    }

    $result = '
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="formEdit" id="formEdit" price="" name="formEdit" alt="'.$coll.'" onsubmit="return validateEdit(this)">
                    <div class="modal-header">						
                        <h4 class="modal-title">Data Editing</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">'.	
                        
                        $inputBox

                   .' </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <button type="button" onclick="'.$function.'" class="btn btn-info" value="Save">Save</Button>
                    </div>
                </form>
            </div>
        </div>';

    return $result;
}


// this is the mai page
function cmsPage($coll){

    $numberElemts = count(collection($coll)->find()->toArray());
echo '
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>C.M.S.</title>

        <!-- Custom styles for this template -->
        <script src="jscms.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/account.js"></script>
        <link href="csscms.css"  rel="stylesheet">


        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    </head>






    <body>
        <div class="container-xl">
            <div class="table-responsive">
                <div class="table-wrapper">
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-4">
                                <h2>Manage <b id="collectionTitle">'.$coll.'</b></h2>
                            </div>
                            <div class="col-sm-3">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Collection
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <button onclick="collection(this)" class="dropdown-item" type="button">Users</button>
                                        <button onclick="collection(this)" class="dropdown-item" type="button">Products</button>
                                        <button onclick="collection(this)" class="dropdown-item" type="button">Orders</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Product</span></a>
                                <a role="button" onclick="return removeItem(\'\')" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Delete</span></a>						
                            </div>
                        </div>
                    </div>


                    <table class="table table-striped table-hover" id="tableBody">'.
                        
                        


                        //function to print the table header

                        //function to print all the rows
                        tableBody($coll)
                        
                           


                    .'</table>


                    <div class="clearfix">
                        <div class="hint-text">Showing <b id="counter">'.$numberElemts.'</b> out of <b id="bigCounter">'.$numberElemts.'</b> entries</div>
                        <ul class="pagination">
                            <li class="page-item disabled"><a href="#">Previous</a></li>
                            <li class="page-item"><a href="#" class="page-link">1</a></li>
                            <li class="page-item"><a href="#" class="page-link">2</a></li>
                            <li class="page-item active"><a href="#" class="page-link">3</a></li>
                            <li class="page-item"><a href="#" class="page-link">4</a></li>
                            <li class="page-item"><a href="#" class="page-link">5</a></li>
                            <li class="page-item"><a href="#" class="page-link">Next</a></li>
                        </ul>
                    </div>
                </div>
            </div>        
        </div>
        <!-- Edit Modal HTML -->
        <div id="addEmployeeModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formAddProduct" onsubmit="return addData(this)">
                        <div class="modal-header">						
                            <h4 class="modal-title">Add a new Product</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">					
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="Title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" name="Category" required>
                                    <option value="Shirts">Shirts</option>
                                    <option value="Dresses">Dresses</option>
                                    <option value="Jeans">Jeans</option>
                                    <option value="Accessories">Accessories</option>
                                    <option value="Shoes">Shoes</option>
                                    <option value="Jewelry">Jewelry</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" name="Description" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Src</label>
                                <input type="text" name="Src" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" name="Price" class="form-control" required>
                            </div>						
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                            <input type="submit"  class="btn btn-success" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
       

        <!-- Edit Modal HTML -->
        <div id="editEmployeeModal" class="modal fade">'.
            


            editingModal($coll)



        .'</div>

        
        <!-- Delete Modal HTML -->
        <div id="deleteEmployeeModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form>
                        <div class="modal-header">						
                            <h4 class="modal-title">Delete data</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">					
                            <p>Are you sure you want to delete these Records?</p>
                            <p class="text-warning"><small>This action cannot be undone.</small></p>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                            <input type="button" class="btn btn-danger" onclick="return removing()" value="Delete">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html>';
}








//function which runs the page
function thePage($coll){

    cmsPage($coll);
}

//this function will start the page as User at first loading or refresh
$thePage = function(){
    if(array_key_exists("loggedEmail", $_SESSION)){
        thePage("Users"); 
    }else{
        header("Location: cmsLogin.php" );
    }
    
};








//operation to handle ajax requestes
$ope = function($method, $ajax, $request){

    switch(strtolower($method)){
        case "post":
            if(isset($request["Title"])){
                $request["Stars"] = 3;

                $insertResult = collection("Products")->insertOne($request);
                //Echo result back to user
                if($insertResult->getInsertedCount()==1){
                    return tableBody("Products");
                }else{ return ""; }
            }else if(isset($request["Nickname"])||isset($request["Email"])){
                //if the request has been made from a js script
                //Create a PHP array with our search criteria
                $result = 0;
                $findCriteria = [array_keys($request)[0] => array_values($request)[0]];
                //Find all of the customers that match  this criteria
                $cursor = collection("Users")->find($findCriteria);
                //Output the results
                foreach ($cursor as $cust){
                    $result = json_encode($cust);
                }
                return $result;
            }
          break;
  
        case "get":
            if(isset($request["coll"])){
                return tableBody($request["coll"]);
            }else if(isset($request["modal"])){
                return editingModal($request["modal"]);
            }else if(isset($request["id"])){
                $id = $request["id"];
                $coll = $request["collection"];
                $findByID = ['_id' => new MongoDB\BSON\ObjectID($id)];
                $data = (array)collection($coll)->findOne($findByID);
                return json_encode((object)$data);
            }else{ return "";}
          break;
  
        case "put":
          break;
  
        case "patch":
            if(isset($request["update"])){
                $coll = ((array)$request["update"])[0];
                $id = ((array)$request["update"])[1];
                $newData = (array)((array)$request["update"])[2];

                $findByID = ['_id' => new MongoDB\BSON\ObjectID($id)];
               
                $updateResult = collection($coll)->updateOne($findByID, [ '$set' => $newData]);
        
                if($updateResult->getMatchedCount()){
                    return $updateResult->getModifiedCount()? tableBody($coll): 2;
                }else{ return 0; }
            }
            break;
  
        case "delete":
            if(isset($request["delete"])){
                $request = (array)$request["delete"];
                $coll = $request["collection"];
                $ids = $request["id"];
                $idsReady = array();

                foreach($ids as $id){
                    $idFormatted = new MongoDB\BSON\ObjectID($id);
                    array_push($idsReady, $idFormatted);
                }
                collection($coll)->deleteMany(array('_id' => array( '$in' => $idsReady )));
                return "Successfully removed";
            }else { return ""; }
          break;
    }
};




//listening server
ajax($thePage, $ope);
?>