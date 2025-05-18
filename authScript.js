function toggleForm(type) {
  document.getElementById("login-form").classList.toggle("hidden", type === "register");
  document.getElementById("register-form").classList.toggle("hidden", type === "login");
}