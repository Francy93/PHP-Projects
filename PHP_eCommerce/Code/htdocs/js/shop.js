
//thi will make the filter by category buttons working
function catFilter(button){
    const className = "list-group-item";
    const buttonsArray = button.closest("div").childNodes;
    for(btn of buttonsArray){
        btn.setAttribute("class", className); 
    }
    button.setAttribute("class", className+" active");

    const productContainer = document.getElementById("productsContainer");

    const ope = function(response){
        productContainer.innerHTML = response;
    }

    const request = {find:{Category: button.innerHTML}}
    ajax(request, ope, "get", "shop.php");
}



// this function allow the sorting of the element of the page
var oldMethod = ["", true];
var wrongClick = true;
function sort(method){
    if(!wrongClick){
        wrongClick = true;
        method = method.value;
        const productContainer = document.getElementById("productsContainer");
        const cards = productContainer.children;
        counter = oldMethod[0] == method && oldMethod[1];
        oldMethod = [method, counter?false:true];
        method = method.toLowerCase();
        let ope;

        switch(method){
            case "name":
                ope = function([,a], [,b], bool){ 
                    a = a.getAttribute(method);
                    b = b.getAttribute(method);
                    console.log(bool);
                    return (bool?a>b:a<b)?1:-1; 
                };
                break;
            case "rates": case "price":
                ope = function([,a], [,b], bool){
                    a = a.getAttribute(method);
                    b = b.getAttribute(method);
                    console.log(bool);
                    return bool? a-b: b-a; 
                };
                break;
            default: console.log("no sorting recognizded");
                break;
        }
        //this line below will sorte the array
        let sorted = Object.entries(cards).sort((a, b) => ope(a, b, counter));

        productContainer.innerHTML = "";
        for(card of sorted){
            //console.log(card[1]);
            productContainer.appendChild(card[1]);
        }
    } else { wrongClick = false; }
}