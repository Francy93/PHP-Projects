//----------------------------- logOut by removing the cookies and sessionStorage-------------------------
function logOut(name) {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    sessionStorage.clear();
    location.reload();
} //this function will be called like this: logOut("PHPSESSID");

//this checks if the user is logged in
function checkLogin(){
    if ("user" in sessionStorage){
        document.getElementById("LogInOut").innerHTML='LogOut';
    }else {document.getElementById("LogInOut").innerHTML='LogIn';}
}

//set user page with personal datas iside
function setUser(){
    if ("user" in sessionStorage){
        let userId = JSON.parse(sessionStorage.getItem("user"));
        let userData = JSON.parse(localStorage.getItem(userId["eMail"]));
        let bestScore = "score" in  userData? Object.values(userData.score)[0] : "No scores!";
        document.querySelector('.data h2').innerHTML = userData["Name"]+"'s best score: "+bestScore;
        setEdits(userData);
    }
}

//Edit user personal data
function setEdits(data){
    let objEdits = new Object;                  // ready but not used yet
    let elements = document.querySelectorAll('.group > input');
    for (let input of elements){
        if (data == null){                      //if true set the object "objEdits" with all the user data
            objEdits[input.name] = input.value; // ready but not used yet
        }else{
            input.value = data[input.name];     //setting user page with user data
        }
    }
}