'use strict';

//this function switches the account page from user data to user orders
function dataOrOrders(element){
    const dynamicContainer = document.querySelector('#dynamicContainer');
    const buttonData = document.querySelector('#buttonData');
    const buttonOrders = document.querySelector('#buttonOrders');
    const className = "list-group-item";
    const buttonName = element.getAttribute("id");
    const data = new Object;
    data[buttonName] = true;

    function operation(response){
        dynamicContainer.innerHTML = response;
        if(buttonName=="buttonData"){
            buttonData.setAttribute("class", className+" active");
            buttonOrders.setAttribute("class", className);
        }else{
            buttonOrders.setAttribute("class", className+" active");
            buttonData.setAttribute("class", className);
        }
    }

    ajax(data, operation, "get", "account.php");
}



/**
* here will be checked and validate any type of data entered into the forms
* @param  {Object}  element DOM element (form or input)
* @return {Boolean} Boolean which determines the form submission
*/
async function validateForm(element){
    if(element.tagName != "BUTTON"){ //if just a focus-out of the inputBox
            const validate = new Validation(element?element: form);
            let result = validate.inputs();
            return result;
    }else {                          //if the button as been pressed
        /* const elemForm = document.getElementsByName(form)[0]; */
        const elemForm = element.form;
        const validate = new Validation(elemForm);
        let TorF = await validate.inputs();
        TorF? elemForm.submit(): console.log("void button");
        
        return false; 
    }
}

// Validation Form
class Validation {
    constructor(element){    
        //-------------------------------local variables----------------------------------------
        let elObj       = new Object;   //form data object
        let parent      = element.name; //form name
        let currentUser = "";
        let kList;                      //list of data Object keys

        //setting the currentUser variable when "user" key exists inside the session storage
        if(sessionStorage.getItem("user")){
            currentUser = JSON.parse(sessionStorage.getItem("user"))["Email"];
            console.log(currentUser);
        }

        //--------------------setting the kList, elObj and parent variable---------------------
        if(element.tagName === 'INPUT'){ //the previous intruction was && element.type === 'text') {
            kList = [element.getAttribute('name')];
            elObj[element.getAttribute('name')] = element.value;
            parent = element.form.name; //the previous intruction was .parentNode.name;
        }else {
            const formEntries = new FormData(element).entries();
            elObj = Object.assign(...Array.from(formEntries, ([name, value]) => ({[name]: value})));
            kList = Object.keys(elObj);
        }

        /* making variales visible outsite the contructor*/
        this.elObj       = elObj;
        this.kList       = kList;
        this.parent      = parent;
        this.currentUser = currentUser;

        this.label       ="";
        this.Tof         = true;
        this.element     = element;
        this.oneElement  = this.element;
    }

    //--------------------------cicling all the kList element(keys)---------------------------
    /**
     * Operating with each input box
     * @return  {Boolean} Boolean which determines the form submission
     */
    async inputs(){
        const elObj       = this.elObj;       //Object containing user/form data el-Obj:  el= elements, Obj= Object
        const kList       = this.kList;       //list of Keys from the form
        const parent      = this.parent;      //getting form name
        const currentUser = this.currentUser; //gettin current user if any from sessionStorage
        let ToF           = this.Tof;         //Boolean which determines the form submission

        const server = parent=="formEdit"? "": "account.php";
        
        console.log(kList);
        console.log(elObj);
        for(let i=0 ; i<kList.length ; i++){
            const key = kList[i];
            let value = elObj[key];
            if (kList.length !== 1){
                this.oneElement = this.element[key];
            }
            this.label = this.oneElement.parentNode.getElementsByTagName("LABEL")[0]; //getting the label //getting the label

            switch (key){
                case "Name": case "Surname": case "Country":
                    //3 cases format validation
                    if (/^([a-z]+\s?)*$/gi.test(value)){
                        ToF = ToF?!(value==""): false;
                        this.#styleSet(true, ""); //settin the input box style
                    }else{ 
                        ToF = false;
                        this.#styleSet(ToF, "Just letters!"); //settin the input box style
                    }
                    break;
                case "Email":
                    //Email format validation
                    if (/^([\w\.\-]+\@([\w\-]+\.)+([a-z0-9]{2,4}))?$/gi.test(value)){
                        ToF = ToF?!(value==""): false;
                        //if the ToF is flase already then keep it false otherwise true
                        this.#styleSet(true, ""); //setting the input box style
                        //local storage value to lowerCase
                        value = value.toLowerCase();
                        //making sure the value get sent in lowerCase
                        let inputElem = document.getElementsByName(parent)[0][key];
                        inputElem.innerText = value;
                        inputElem.value = value;

                        //operating server response
                        let local = "";
                        let operation = function(response) {local = JSON.parse(response).Email;};
                        let data = new Object;
                        data[key] = value;
                        await ajax(data, operation,"", server);
                        local = local? local.toLowerCase(): null;
                        
                        //if the element comes from the FormSignIn
                        if(parent == "formSignin" && value !=""){
                            if(value != local){
                                ToF = false;
                                this.#styleSet(ToF, "Not registered!"); //settin the input box style
                            }else {this.elObj[key] = value}; //overwriting a elObj value
                        }else if (value == local && (parent == "formSignup" || 
                                  (parent == "formEdit" && value != currentUser))){
                            console.log(parent+ " "+currentUser+" "+value);
                            ToF = false;
                            this.#styleSet(ToF, "Already registered!"); //settin the input box style
                        }else {this.elObj[key] = value}; //overwriting a elObj value
                    }else{ 
                        ToF = false;
                        this.#styleSet(ToF, "Wrong format!"); //settin the input box style
                    }
                    break;
                case "Nickname":
                    //nikname format validation
                    if (/^(\w+[.|-]*)*$/gi.test(value)){
                        ToF = ToF?!(value==""): false;
                        this.#styleSet(true, ""); //settin the input box style
                        //-------------------------searching Nikname existence-----------------------
                        let currentOwnsNik=false;
                        //if the current user exists in loclStorage then get his nikname
                        
                        //operating server response
                        let opeOwn = function(response) {
                            currentOwnsNik = value == JSON.parse(response)[key]? true: false;
                        };
                        let data = {Email: currentUser};
                        await ajax(data, opeOwn, "", server);

                        let NickIsInLocal = false;
                        
                        let opeNick = function(response) {
                            console.log(response);
                            NickIsInLocal = value == JSON.parse(response)[key]? true: false;
                        };
                        data = new Object;
                        data[key] = value;
                        await ajax(data, opeNick, "", server);
                        //if the nikname exist already in the localStorage
                        if (NickIsInLocal && 
                                (parent == "formSignup" || (parent == "formEdit" && !currentOwnsNik)) ){
                            ToF = false;
                            this.#styleSet(ToF, "Is taken!"); //settin the input box style
                        }//-------------------------------end of searching-----------------------------
                    }else{ 
                        ToF = false;
                        this.#styleSet(ToF, "Wrong format!"); //settin the input box style
                    }
                    break;
                case "Password":
                    //password format validation
                    if (/^([\w|\W]{8,})?$/gi.test(value)){
                        ToF = ToF?!(value==""): false;
                        this.#styleSet(true, ""); //settin the input box style
                        //if the element comes from FormSignIn and elements of the form are just 2
                        if(parent == "formSignin"){
                            let localPass = false;
                            let opePass = function(response) {
                                console.log(response);
                                localPass = value == JSON.parse(response)[key]? true: false;
                            };
                            let email = elObj['Email']? elObj['Email']: false;
                            let data = {Email: email}; //potential bag

                            email? await ajax(data, opePass, "", server): localPass=true;

                            if(!localPass){
                                ToF = false;
                                this.#styleSet(ToF, "Wrong Password!"); //settin the input box style
                            }
                        }
                    }else{ 
                        ToF = false;
                        this.#styleSet(ToF, "At least 8!"); //settin the input box style
                    }
                    break;
                case "Repeat-Pass":
                    if (value === elObj["Password"]){
                        ToF = ToF?!(value==""): false;
                        this.#styleSet(true, ""); //settin the input box style
                    }else if(kList.length >2 ){ 
                        ToF = false;
                        this.#styleSet(ToF, "No match!"); //settin the input box style
                    }
                    break;
            }
            console.log(key + " =   " + value);
            console.log(ToF);
        }
        let checkBox = function(){if(elObj.CheckBox) return elObj.CheckBox=="on"? true: false;};
        console.log("checkBox status: "+checkBox());

        console.log(ToF);
        this.ToF = checkBox()||parent=="formSignin"||parent=="formEdit"? ToF: false;
        console.log(ToF);
        return this.#validated();
    }

    // this private function will store data and allow form to be submitted
    #validated(){
        let elObj       = this.elObj;
        let parent      = this.parent;
        let currentUser = this.currentUser;

        if(this.ToF && this.kList.length > 1){
            //marging new scores from (tesmpScore) withold ones from user data and store them into elObj.score
            /*The previous function included this line to chek cart even if not logged: elObj.score = this.#ScoresMerge(elObj, currentUser); */

            if(parent == "formSignup"){
                //puttanella che modifica e trigghera il form di merda
                localStorage.setItem(elObj["Email"], JSON.stringify(elObj));    //save 7 data to local storage; (elObj)
            }else if(parent == "formEdit"){
                currentUser = JSON.parse(sessionStorage.getItem("user"))["Email"];
                console.log(currentUser);
                localStorage.removeItem(currentUser);
                localStorage.setItem(elObj["Email"], JSON.stringify(elObj));   //save 7 data to local storage; (elObj)
            }
            elObj = { Email : elObj["Email"], Password : elObj["Password"] };
            sessionStorage.setItem("user", JSON.stringify(elObj));  //save 2 data to session storage; (elObj)
        }
        return this.ToF;
    }







   #ScoresMerge(elObj, currentUser){
        //getting current user data
        let accessingUser = elObj.Email;
        let accessingUserData = accessingUser in localStorage ? JSON.parse(localStorage[accessingUser]):new Object;
        let currentUserData = currentUser in localStorage ? JSON.parse(localStorage[currentUser]):new Object;
        let today = new Date().toLocaleString(); //getting the registration date
        let firstRegistr = new Object;
        let newScores = new Object;
        firstRegistr[today] = 0;
        
        //put into newScores possible "tempScore" data if any
        if (!("user" in sessionStorage)){
            newScores = "tempScore" in sessionStorage? JSON.parse(sessionStorage.tempScore):firstRegistr;
        } 
        //adding the score/s to the user data
        //updating the exiating score property with new data
        if(currentUser in localStorage && "score" in currentUserData){
            Object.assign(newScores, currentUserData.score);
        }else if(accessingUser in localStorage && "score" in accessingUserData){
            Object.assign(newScores, accessingUserData.score);
            if(accessingUser in localStorage && "tempScore" in sessionStorage){
                //Storing the data directly into the localStorage
                accessingUserData.score = this.sorting(newScores);
                console.log(accessingUserData);
                localStorage[accessingUser] = JSON.stringify(accessingUserData);
            }
        }
        newScores = this.sorting(newScores);
        sessionStorage.removeItem("tempScore");
        return newScores;
    }

    //-----------------------setting the wrong fields style in html--------------------------
    /**
     * Styling the text of the dom input box
     * @param  {Boolean} WoR Determine the displayin of the "wrong" message
     * @param  {String}  message Message Text
     */
    #styleSet(WoR, message){
        if(WoR){                            //setting style back to normal
            this.oneElement.style.color = "";
            this.label.innerHTML        = this.oneElement.name;
            this.label.style.color      = "";
        }else{                              //setting style to wrong
            this.label.innerHTML        = message;
            this.label.style.color      = "red";
            this.oneElement.style.color = "red";
        }
    } //this function will be called inside the "swith"
    
    sorting(data){
        //this is the sorting algorithm which sort the second column data in a decreasing way
        return Object.fromEntries(Object.entries(data).sort(([,a],[,b]) => b-a));
    }
}