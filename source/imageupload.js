// Link variables into the js file
var reqTopic = document.getElementById("reqtopic");
var reqmain = document.getElementById("reqmain");
var choose = document.getElementById("imgSelect");
var uploadImage = document.getElementById("reqImage");

// Create a function invoking an event function
function upload() {
    uploadImage.click();
}

// Event listener on automatically filling the request topic name when an image is chosen
uploadImage.addEventListener("change", function () {
    var file = this.files[0];
    if (reqTopic.value == "") {
        // Name the topic into the chosen image file name if there is none
        reqTopic.value = file.name;
    }
    // Change the text of the button once a file is selected
    choose.innerHTML = file.name;
})