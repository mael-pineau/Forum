// // Display the loader icon
// function displayLoader() {
//     var loader = document.getElementById("loader");
//     loader.style.visibility = "visible";
//     console.log("here?");
// }

function displayAlert(message) {
    if (confirm(message)) {
        displayLoader();
        return true;
    }
    else {
        return false;
    }
}

function countTrixCharacters() {
    let message = document.getElementById('x').value;

    var doc = new DOMParser().parseFromString(message, "text/html");

    message = doc.documentElement.textContent;

    if (message.length >= 20) {
        return true;
    }
    else {
        window.alert("Veuillez remplir le champ avec au moins 20 caractères");
        return false;
    }
}

// // Function called when clicking on a sort option form the dropdown menu
// function redirectToSortBy(value) {
//
//     window.location='/?sortCriteria='+value;
// }
//
// // Function called when validating a search from the search bar
// function redirectToSearchBy(value) {
//
//     window.location='/?search='+value;
// }