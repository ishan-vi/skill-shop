
function createAccount() {
  var fname = document.getElementById("signup-firstname").value;
  var lname = document.getElementById("signup-lastname").value;
  var email = document.getElementById("signup-email").value;
  var password = document.getElementById("signup-password").value;
  var cpassword = document.getElementById("signup-confirm").value;
  var accountTypeSeller = document.getElementById("account_type_seller");
  var accountTypeBuyer = document.getElementById("account_type_buyer");
  var termsCondition = document.getElementById("terms_condition");
  var signupErrorMessage = document.getElementById("signup-message");
  signupErrorMessage.classList.remove("hidden");

  if (!fname || !lname || !email || !password || !cpassword) {
    signupErrorMessage.innerHTML = "All fields are required";
  } else if (password.length < 8) {
    signupErrorMessage.innerHTML = "Password must be at least 8 characters";
  } else if (password != cpassword) {
    signupErrorMessage.innerHTML = "Password must be same";
  } else if (!accountTypeSeller.checked || !accountTypeBuyer.checked) {
    signupErrorMessage.innerHTML = "Please select account type";
  } else if (!termsCondition.checked) {
    signupErrorMessage.innerHTML =
      "Please read and check I agree to the terms & condition";
  } else {
    signupErrorMessage.classList.add("hidden");

    var form = new FormData();
    form.append("fname", fname);
    form.append("lname", lname);
    form.append("email", email);
    form.append("password", password);
    form.append("pass_confirm", cpassword);
    form.append(
      "account_type",
      accountTypeSeller.checked
        ? accountTypeSeller.value
        : accountTypeBuyer.value,
    );
    form.append("termsConditions", termsCondition.checked);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
      if (r.readyState == 4) {
        signupErrorMessage.classList.remove("hidden");
        if (r.status == 200) {
          if (r.responseText == "success") {
            signupErrorMessage.classList.remove("text-red-500");
            signupErrorMessage.classList.add("text-green-500");
            signupErrorMessage.innerHTML = "Registration Successfull";
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          }else{
            signupErrorMessage.innerHTML = r.responseText;
          }
        }
      } else {
        signupErrorMessage.innerHTML = "Request failed!", r.responseText;
      }
    };
    r.open("POST", "process/createAccountProcess.php", true);
    r.send(form);
  }
}
