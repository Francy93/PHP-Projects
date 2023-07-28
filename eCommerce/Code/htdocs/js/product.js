async function cartAdd(button){
    const productID = button.getAttribute("name");
    let userId = "";

    const getUserId = function(id){ userId = id; }
    const addToCart = function(response){
        if(response){
            /* const message = "Your booking has been confirmed. Check your email for detials."; */
            message = response==2? "It was already in your Cart :)": "Item Successfully Added to your Cart :)";
            modalAlert(button, message);
            console.log("modal alert called");
        }else{ alert("Error, not added"); }
    }
    await ajax("id", getUserId, "get");

    if(userId){
        await ajax({id:productID}, addToCart, "patch", "product.php");
    }else{ console.log("user not registered")}
}

//this function will send the browser the page visited by the user (Tracing)
function loadingTraces(id){

    ajax({trace: id},"","post", "product.php");
}