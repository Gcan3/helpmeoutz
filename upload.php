<?php 

@include 'config.php';

session_start();

// create a function to get the user's IP address
function get_ip_address(){
    // look through the possible headers that could contain the IP address
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        // check if the key exists in the server variable, 
        if (array_key_exists($key, $_SERVER) === true){
            // look through the IP addresses in the header
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // for safety
                // check if the IP address is valid
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    // return the user's valid IP address
                    return $ip;
                }
            }
        }
    }
}

// check if the user is logged in
if (!isset($_SESSION['user_name']) && !isset($_SESSION['google_loggedin'])) {
    // if not, redirect to the login page
    header('location:index.php');
    exit;
} else {
    // if the user is logged in, get the user's name
    if(isset($_SESSION['google_loggedin'])){
        // if user is logged in with Google, get the Google ID name
        $google_loggedin = $_SESSION['google_loggedin'];
        $username = $_SESSION['google_name'];
        $email = $_SESSION['google_email'];
    } else {
        // otherwise, get the database username
        $username = $_SESSION['user_name'];
        $email = $_SESSION['user_email'];
    }
}

if(isset($_POST["submit"])){
    // Linking request variables to the php file
    $req_topic = $_POST["reqtopic"];
    $req_main = $_POST["reqmain"];
    $req_user = $username; // Get the original poster's name
    $ip = get_ip_address(); // Get the IP address of the poster

    // Uploading image process
    $upload_directory = "upload_req/"; // Storage for uploaded image
    $req_img = $upload_directory.$_FILES["reqImage"]["name"]; // Link the stored file into the php code
    $upload_directory.$_FILES["reqImage"]["name"]; // Store the file and name of the file
    $upload_file = $upload_directory.basename($_FILES["reqImage"]["name"]); // Link the submitted file from the storage
    $img_type = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION)); // Find the type of file the submitted file is
    $img_size = $_FILES["reqImage"]["size"]; // Finding the size of the submitted file
    $valid_img = 0;

    if(file_exists($upload_file)){
        // If there's a file already (or missing) execute the error command
        echo"<script> alert('Please upload a file') </script>";
        $valid_img = 0;
    } else {
        if ($img_size !== false){
            if ($img_type == 'jpg' || $img_type == 'png' || $img_type == 'jpeg' || $img_type == 'gif') {
                // Fully validate the file if its filetype is an image and it isn't size 0
                $valid_img = 1;
            } else {
                // Otherwise execute an error
                echo"<script> alert('Image files accepted only (JPG, PNG, JPEG, GIF)') </script>";
            }
        } else {
            // If Image size is 0 then execute error command
            echo"<script> alert('The photo is empty (or have a 0 size). Please change the Image') </script>";
            $valid_img = 0;
        }
    }

    // converting user ip address into valid location for the database using geoplugin api
    $loc = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip"));
    $country = $loc["geoplugin_countryName"];
    $city = $loc["geoplugin_city"];

    // If the image is not valid, execute an error
    if($valid_img == 0){
        echo"<script> alert('Your image can't upload. Please try again') </script>";
    } else {
        // If the image is valid, proceed to upload the image and the request to the database
        if($req_topic != "" && $req_main != "" && $req_user != ""){
            // If the user is available, proceed to upload the request to the database
            move_uploaded_file($_FILES["reqImage"]["tmp_name"], $upload_file);
            // SQL command for inserting the request to the database
            $sql_insert = "INSERT INTO user_request(request_user,request_topic, request_main, request_image, request_country, request_city, request_email) VALUES ('$req_user','$req_topic','$req_main','$req_img', '$country', '$city', '$email')";
            // If the request is successfully uploaded, send a success message
            if($conn->query($sql_insert) === TRUE){
                echo"<script>alert('Your request is submitted to the main dashboard!')</script>";
            }
        } else {
            // If the user is not available, send an error
            echo"<script>alert('User not available')</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="source/styleMain.css">
    <link rel="stylesheet" type="text/css" href="source/uploadstyle.css">
</head>
<body>
    <?php include('template/header.php'); ?>

    <main class="main-container">
        <div class="form-container text-center">
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <label for="request" class="text-center my-3 text-capitalize">Create a request to the community!</label>
                <input type="text" name="reqtopic" id="reqtopic" placeholder="Put Your Request Topic" class="reqtopic rounded p-2" required>
                <input type="file" name="reqImage" id="reqImage" require hidden>
                <button id="imgSelect" onclick="upload();" class="picbtn btn my-3">Insert Image (Required)</button>
                <textarea cols="20" rows="10" name="reqmain" id="reqmain" placeholder="Put Your Full Request Here" class="rounded mb-3 p-2" required></textarea>
                <input type="submit" value="Submit Request" name="submit" class="submitbtn btn my-1">
            </form>
        </div>
    </main>

    <?php include('template/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="source/imageupload.js"></script>
</body>
</html>