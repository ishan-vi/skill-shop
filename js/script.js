// Toggle Between Sign In & Sign Up Forms
function toggleForms() {
  var signinForm = document.getElementById("signin-form");
  var signupForm = document.getElementById("signup-form");

  signinForm.classList.toggle("hidden");
  signinForm.classList.toggle("active");

  signupForm.classList.toggle("hidden");
  signupForm.classList.toggle("active");
}

//Togle Password & Text in password inputs
function togglePassword(inputId, btn) {
  var input = document.getElementById(inputId);
  if (input.type == "password") {
    input.type = "text";
    btn.innerHTML = "🔓";
  } else {
    input.type = "password";
    btn.innerHTML = "🔒";
  }
}
