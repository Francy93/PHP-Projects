

//function to get and display a different collection
function collection(button){
	let coll = button.innerHTML;
	coll = coll == "Customers"? "Users": coll;
	document.getElementById("collectionTitle").innerHTML = coll;

	let opeCards = function(response){
		const container = document.getElementById("tableBody");
		container.innerHTML = response;

	}
	const opeModal = function(response){
		const container = document.getElementById("editEmployeeModal");
		container.innerHTML = response;
	}

	ajax({coll:  coll}, opeCards, "get");
	ajax({modal: coll}, opeModal, "get");
}

//this function add a new product
function addData(element){
	const container = document.getElementById("tableBody");
	const formEntries = new FormData(element).entries();
    let elObj = Object.assign(...Array.from(formEntries, ([name, value]) => ({[name]: value})));
	
	const ope = function(response){
		container.innerHTML = response;
	}

	ajax(elObj, ope, "post");
	$('#addEmployeeModal').modal('hide');
	return false;
}

//sending edited data to server
function sendEditedData(button){
	const id = button.form.getAttribute("price");
	const coll = button.form.getAttribute("alt");
	const formEntries = new FormData(button.closest("form")).entries();
	formData = Object.assign(...Array.from(formEntries, ([name, value]) => ({[name]: value})));

	const ope = function(response){
		if(!response){
			console.log("Error wihile updating");
		}else if(response === 2 || response === "2"){
			console.log("No data changed "+response);
			$('#editEmployeeModal').modal('hide');
	    }else{  
			const container = document.getElementById("tableBody");
			console.log("Successfully Updated");
			container.innerHTML = response;
			$('#editEmployeeModal').modal('hide');
		}
	}

	ajax({update: [coll, id, formData]}, ope, "patch");
}

//this function is ment to validate the users data
async function validateEdit(element){
	const form = element.form;
	const email = form.querySelector('input[name="Email"]').value;

	let user = { Email : email };
    sessionStorage.setItem("user", JSON.stringify(user));  //save 2 data to session storage; (elObj)

	if(element.tagName != "BUTTON"){ //if just a focus-out of the inputBox
		const validate = new Validation(element?element: form);
		validate.inputs();
		
	}else {                          //if the button as been pressed

		const elemForm = element.form;
		const validate = new Validation(elemForm);
		let TorF = await validate.inputs();

		//if editing form is validated do this:
		if(TorF){
			sendEditedData(element);
		}else{ alert("Wrong data Entered"); }
	}
	sessionStorage.removeItem('user');
	return false;
}

//this function will fill the edit form with the selected card data
function fillEditModal(id){
	const card = document.getElementById(id);
	const coll = card.getAttribute("name");
	const formEdit = document.getElementById("formEdit");
	formEdit.setAttribute("price", id);

	const ope = function(response){
		response = JSON.parse(response);
		const CardData = Array.from(card.childNodes);
		CardData.pop();CardData.pop();CardData.shift();CardData.shift();
		
		for(property of CardData){
			let value = property.innerHTML;
			value = value.slice(-1)=="£"? parseFloat(value).toFixed(2): value;
			const key = Object.keys(response).find(key => response[key] == value);

			const mFormPropery = document.getElementById("modalEditing"+key);
			mFormPropery? mFormPropery.value = value: null;
			mFormPropery? mFormPropery.innerHTML = value: null;

		}
		$('#editEmployeeModal').modal('show');
	}

	ajax({id: id, collection: coll}, ope, "get");
}



let cards = [];
let ids = [];
function removing(){
    //getting the closest element with tagname section (the product wrapper)
	console.log(ids);
	const collection = document.getElementById(ids[0]).getAttribute("name");
	console.log(collection);

	let ope = function(response){
        if(response){
			console.log(response);
			for(card of cards){
				card.remove();
				//adjusting counter value
				const counter = document.getElementById("counter");
				counter.innerHTML = counter.innerHTML - 1;
			}
			$('#deleteEmployeeModal').modal('hide');
		}
	}
	ajax({delete: {collection: collection, id: ids}}, ope,"delete");

	return false;
}


//this function remove many items
function removeItem(button){

	const boxes = document.getElementsByClassName("checkBoxes");
	cards = [];
	ids = [];

	if(button){
		ids.push(button.getAttribute("name"));
		cards.push(button.closest("tr"));	
	}else{
		for(box of boxes){
			if(box.checked){
				let id = box.closest("tr").getAttribute("id");
				let card = box.closest("tr");
				cards.push(card);
				ids.push(id);
			}
		}
	}

	console.log(ids);
	if(ids.length){
		$('#deleteEmployeeModal').modal('show');

	}else{ alert("No rows selected"); }

	return false;
}

//function to select every checkbox
function selectAll(mainBox){
	const status = mainBox.checked;
	console.log("started");
	const boxes = document.getElementsByClassName("checkBoxes");
	for(box of boxes){
		console.log("selected");
		box.checked = status;
	}
}
