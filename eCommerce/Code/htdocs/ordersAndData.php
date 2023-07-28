<?php
include('common.php');

function UserTab(){

    $result = '<form class="row formEdit" id="formEdit" name="formEdit" method="POST">
        <div class="col-md-6">
            <div class="form-group">
                <label for="account-fn">First Name</label>
                <input class="form-control" type="text" name="Name" onfocusout="validateForm(this)" id="Name" value="'.userProperty("","Name").'" required="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="account-ln">Last Name</label>
                <input class="form-control" type="text" name="Surname" onfocusout="validateForm(this)" id="Surname" value="'.userProperty("","Surname").'" required="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="account-email">E-mail Address</label>
                <input class="form-control" type="email" name="Email" onfocusout="validateForm(this)" id="Email" value="'.userProperty("","Email").'" required="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="account-phone">Username</label>
                <input class="form-control" type="text" name="Nickname" onfocusout="validateForm(this)" id="Nickname" value="'.userProperty("","Nickname").'" >
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="account-pass">Address</label>
                <input class="form-control" type="text" name="Address" onfocusout="validateForm(this)" id="Address" value="'.userProperty("","Address").'">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="account-confirm-pass">New Password</label>
                <input class="form-control" type="password" name="Password" onfocusout="validateForm(this)" id="Password" value="'.userProperty("","Password").'">
            </div>
        </div>
        <div class="col-12">
            <hr class="mt-2 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="custom-control custom-checkox d-block">
                    <input class="custom-control-input" type="checkbox" id="subscribe_me">
                    <label class="custom-control-label" for="subscribe_me">Subscribe me to Newsletter</label>
                </div>
                <button class="btn btn-style-1 btn-primary" type="button" onclick="validateForm(this)" data-toast="" data-toast-position="topRight" data-toast-type="success" data-toast-icon="fe-icon-check-circle" data-toast-title="Success!" data-toast-message="Your profile updated successfuly.">Update Profile</button>
            </div>
        </div>
    </form>';

    return $result;
}

function ordersTab(){
    $list="";
    $collOrders = collection("Orders");
    $collProducts = collection("Products");
    $ordersCriteria = ['User' => userId()];
    $orders = $collOrders->find($ordersCriteria);
    $OrdersArray = array();
    $ordersAmount = 0;
    foreach ($orders as $order){
        $date = $order['Date'];
        $address = $order['Address'];
        foreach ($order['Products'] as $productID => $quantity){
            $productCriteria = ['_id' => new MongoDB\BSON\ObjectID($productID)];
            $product = $collProducts->findOne($productCriteria);
            $ordersAmount++;
            
            $list .='<tr>
                    <td>'.$ordersAmount.'</td>
                    <td><a href="product.php?id='.$productID.'"><img src="'.$product->Src.'" class="avatar" alt="Image"> '.$product->Title.'</a></td>
                    <td>'.$quantity.'</td>
                    <td>'.$date.'</td>                        
                    <td><span class="status text-success">&bull;</span>'.$address.'</td>
                    <td>'.$product->Price*$quantity.'£</td>
                    <td><a href="product.php?id='.$productID.'" class="view" title="View Details" data-toggle="tooltip"><i class="material-icons">&#xE5C8;</i></a></td>
                </tr>';
        }
    }


        

$tab = '  
    <div class="ordersContainer">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-4">
                            <h2>Order <b>Details</b></h2>
                        </div>
                        <div class="col-sm-8">						
                            <a href="#" class="btn btn-primary"><i class="material-icons">&#xE863;</i> <span>Refresh List</span></a>
                            <a href="#" class="btn btn-secondary"><i class="material-icons">&#xE24D;</i> <span>Export to Excel</span></a>
                        </div>
                    </div>
                </div>
                <div class="table-filter">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="show-entries">
                                <span>Show</span>
                                <select class="form-control">
                                    <option>5</option>
                                    <option>10</option>
                                    <option>15</option>
                                    <option>20</option>
                                </select>
                                <span class="filter-icon"><i class="fa fa-filter"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            <div class="filter-group">
                                <label>Name</label>
                                <input type="text" class="form-control">
                            </div>
                            
                            <div class="filter-group">
                                <label>Status</label>
                                <select class="form-control">
                                    <option>Any</option>
                                    <option>Delivered</option>
                                    <option>Shipped</option>
                                    <option>Pending</option>
                                    <option>Cancelled</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Order Date</th>						
                            <th>Address</th>						
                            <th>Net Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$list.'
                        
                    </tbody>
                </table>
                <div class="clearfix">
                    <div class="hint-text">Showing <b>5</b> out of <b>25</b> entries</div>
                    <ul class="pagination">
                        <li class="page-item disabled"><a href="#">Previous</a></li>
                        <li class="page-item active"><a href="#" class="page-link">1</a></li>
                        <li class="page-item"><a href="#" class="page-link">2</a></li>
                        <li class="page-item"><a href="#" class="page-link">3</a></li>
                        <li class="page-item"><a href="#" class="page-link">4</a></li>
                        <li class="page-item"><a href="#" class="page-link">5</a></li>
                        <li class="page-item"><a href="#" class="page-link">6</a></li>
                        <li class="page-item"><a href="#" class="page-link">7</a></li>
                        <li class="page-item"><a href="#" class="page-link">Next</a></li>
                    </ul>
                </div>
            </div>
        </div>  
    </div>';

    return $tab;
}
?>