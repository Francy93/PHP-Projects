//this functio  will keep the cart update while changing quantity values
function priceUpdate(inputBox){
    const id = inputBox.getAttribute("name");
    const section = inputBox.closest("section");
    const sectionTotal = section.getElementsByClassName("sectionTotal")[0];
    const generalTotal = document.getElementById("generalTotal");
    const quantityBox = section.getElementsByClassName("quantity")[0];
    const price = section.getElementsByClassName("price")[0].getAttribute("value");
    const quantity = quantityBox.value;
    const currentTotal = generalTotal.getAttribute("value");
    let total = price*quantity? price*quantity: price;
    
    quantityBox.value = "";
    quantityBox.innerHTML = "";
    quantityBox.setAttribute("placeholder", quantity);

    total = total.toFixed(2);
    sectionTotal.value = total;
    sectionTotal.innerHTML = total+"£";

    let collettiveTotal = 0;
    const sections = document.getElementsByClassName("sectionTotal");
    for(let item of sections){
        collettiveTotal += parseFloat(item.innerHTML);
    }
    collettiveTotal = collettiveTotal.toFixed(2);
    generalTotal.value = collettiveTotal;
    generalTotal.innerHTML = collettiveTotal+"£";


    //server update
    ajax({id: id, num: quantity}, "", "patch");
}

// this function will delete just the choosen element from the cart
function deleteItem(button){
    const message = "Do you really want to delete these records? This process cannot be undone.";
    modalAlert(button, message);
}

//this function will remove every item in the cart
async function deleteCart(response){
    const container = document.getElementById("fullempty");
    const buttonsArray = document.getElementsByClassName("removeButton");
    for(button of buttonsArray){
        await removeSection(button);
        console.log("removed ");
        
    }
    container.innerHTML = response;
}

//this function will send the order and carryout the payment
function checkOut(button){
    

    let operation = async function(response){
        const message = "Awsome, you purchased Successfully your products! :)";
        console.log(response);
        if(response && response != "0"){
            await modalAlert(button, message, response);
            deleteCart(response);
            console.log("cart deleted");
            
            console.log("cart replaced");
        }else{
            const message = "Somenthing went wrong while processing your payment. Your card might not have enough founds!</br>:(";
            modalAlert(button, message, false);
        }

    }
    //telling the server to purchase this cart
    ajax({buy: true}, operation, "put");
}