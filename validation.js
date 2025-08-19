function validation() {
  let email = document.getElementById("email").value.trim();
  let password = document.getElementById("password").value.trim();

  if (email === "") {
    alert("email cannot be empty.");
    return false;
  }

  if (password.length < 8 || password.length > 12) {
    alert("Password must be between 8-12 characters.");
    return false;
  }

  return true;
}
