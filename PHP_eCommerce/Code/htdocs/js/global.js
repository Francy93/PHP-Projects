
// golobal functions
function removeAlert(){
    let alertContainer = document.getElementById("alertContainer");
    alertContainer.remove();
}
//this is a cart function to remove items
function removeSection(button){
    let id = button.getAttribute("name");
    //getting the closest element with tagname section (the product wrapper)
    const section = document.getElementById(id);

    let ope = function(response){
        if(response){
            //adjusting total value
            const itemTotal = button.closest("section").getElementsByClassName("sectionTotal")[0];
            const valueItemTotal = parseFloat(itemTotal.innerHTML);
            const generalTotal = document.getElementById("generalTotal");
            const valueGeneralTotal = parseFloat(generalTotal.innerHTML);
            let newTotal = valueGeneralTotal - valueItemTotal;
            generalTotal.innerHTML = newTotal.toFixed(2) + "£";

            //removing the div containing the item <section>
            section.remove();
        }
    }
    ajax({id: id}, ope, "delete");

    //adjusting counter value
    const counter = document.getElementById("counter");
    let value = counter.innerHTML - 1;
    counter.innerHTML = value;
}

//modal allert
function modalAlert(button, message, paiment){
    const buttonName = button.getAttribute("id");
    const body = document.getElementsByTagName("body")[0];
    const htmlContent = body.innerHTML;
    let alertContainer = document.getElementById("alertContainer");
    alertContainer = alertContainer? alertContainer.remove(): alertContainer;
    
    if(buttonName == "prodButt"){
        body.innerHTML = htmlContent + alertConfermation(message);
    }else if(buttonName == "payButt"){
        if(paiment){
            body.innerHTML = htmlContent + alertConfermation(message);
        }else{
            body.innerHTML = htmlContent + alertChoice(message, false);
        }
    }else{
        body.innerHTML = htmlContent + alertChoice(message, true);
    }
    $('#myModal').modal('show');
    $('#btnDelete').on('click', function (event) {
        $('#myModal').modal('hide');
        removeSection(button);
    });
    $("#myModal").on("hidden.bs.modal", function () {
        removeAlert();
    });
}

function logout(){
    document.cookie = "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
     sessionStorage.clear();
}

function productsSearch(form){
    const productContainer = document.getElementById("productsContainer");
    const keyword = form.getElementsByTagName("input")[0].value;
    console.log("search started");

    let opera = function(response){
        console.log("opera");
        switch(response){
            case 2: case "2":
                console.log(2);
                console.log(response);
                break;
            case 1: case "1": case true: case (response? response: false):
                console.log(1);
                console.log(response);
                productContainer.innerHTML = response;
                break;
            case false: case null: case "": case "0": case 0:
                console.log(0);
                console.log(response);
                break;
            default: console.log(response);
        }
    };
    console.log(keyword);
    const criteria = {find:{Title: keyword}};
    ajax(criteria, opera, "get", "shop.php");

    return false;
}

/**
     * Ajax technique to communicate with the server
     * @param  {Any}      data data to be sent to server
     * @param  {Function} operation a variable containing a function
     * @param  {String}   method communication method, like: "POST", "GET", "PUT", "DELETE"
     * @param  {String}   url to specify the php page to communicate with
*/
async function ajax(data, operation, method, url){

    data     = data     ? data     : true;
    operation= operation? operation: x => console.log("No operation "+x);
    method   = method   ? method   : "POST";
    url      = url      ? url      : location.pathname.substring(location.pathname.lastIndexOf("/") + 1);

    method = method.toUpperCase();
    data = JSON.stringify(data);

    //converting the data in "post" data
    function getFormData(object) {
        let formData = new FormData();
        Object.keys(object).forEach(key => formData.append(key, object[key]));
        return formData;
    }


    //handling server response
    function res(response){
        if (response.ok) {
                return response.text().then(resp => resp);
        }else{ return false; }
    }

    //performing the HOLLY ajax technique
    switch(method){
        case "POST":
            output = await  fetch( url, {
                                method: method,
                                body:   getFormData({ajax: data}),
                            }).then(response => res(response));
            break;
        case "GET":
            output = await  fetch(url+"?"
                                +"ajax"+"="
                                + data
                            ).then(response => res(response));
            break;
        case "DELETE":
            data = JSON.parse(data);
            output = await  fetch(url + '/' + JSON.stringify({ajax: data}), {
                                method: method
                            }).then(response => res(response));
            break;
        case "PUT": case "PATCH":
            output = await  fetch(url + "/" + "ajax", {
                                method: method,
                                body:   data
                            }).then(response => res(response));
            break;
        default: console.log("Wrong ajax method!");
            return; //this return avoids "operation" to be executed
    }

    //running the asycronous operation
    operation(output);
}
