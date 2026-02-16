//Togle Sign In & Sign Up Forms
function togleForms(){
    var signinForm = document.getElementById("signin-form");
    var signupForm = document.getElementById("signup-form");

    signinForm.classList.toggle("hidden");
    signinForm.classList.toggle("active");

    signupForm.classList.toggle("hidden");
    signupForm.classList.toggle("active");
}

//Togle Password & Text in password inputs
function toglePassword( inputId,btn){
    var input = document.getElementById(inputId);
    if(input.type == "password"){
        input.type = "text";
        btn.innerHTML = "🔓"
    }else{
        input.type = "password";
        btn.innerHTML = "🔒";
    }

}