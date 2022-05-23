// File used for transition and effect when a user is on the Sign page

const sign_in_button = document.querySelector("#sign-in-button");
const sign_up_button = document.querySelector("#sign-up-button");
const container = document.querySelector(".container");

// Transitions left to right
sign_in_button.addEventListener("click", () => {
    container.classList.remove("sign-up-mode");
});

sign_up_button.addEventListener("click", () => {
    container.classList.add("sign-up-mode");
});
//
// // Display the loader icon
// function displayLoader() {
//     var loader = document.getElementById("loader");
//     loader.style.visibility = "visible";
//     console.log("here?");
// }