
var loadFile = function (event) {
    var image = document.getElementById("output-profil");
    image.src = URL.createObjectURL(event.target.files[0]);
};

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