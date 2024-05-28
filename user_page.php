<?php

@include 'config.php';

session_start();

// check if the user is logged in
if (!isset($_SESSION['user_name']) && !isset($_SESSION['google_loggedin'])) {
    // if not, redirect to the login page
    header('location:login_form.php');
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Page</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link rel="stylesheet" type="text/css" href="source/styleMain.css">
   <link rel="stylesheet" type="text/css" href="source/userStyle.css">

</head>
<body>
   <?php include('template/header.php'); ?>   

   <main class="main-container">
      <div class="profile-card mx-auto my-5">
         <div class="card">
            <div class="card-body">
               <div class="profile-head d-flex">
                  <h1 class="text-capitalize fw-light">Hello <?php echo $username ?>!</h1>
                  <a href="logout.php" class="btn btn-outline-primary ms-auto py-3 text-uppercase">logout</a>
               </div>
               <h4 class="my-4 fw-light">We welcome you to the community hub for helping each other!</h4>
               <p class="mb-2 fw-bolder"> Before requesting, please follow our guidelines:</p>
               <ul class="list-group list-group-flush">
                  <li class="list-group-item fw-bolder">1. Be respectful: we have admins looking through the requests</li>
                  <li class="list-group-item fw-bolder">2. Be patient for response: not everyone is active/willing to take on the task</li>
                  <li class="list-group-item fw-bolder">3. No inappropriate requests: admins will not tolerate any inappropriate requests</li>
                  <li class="list-group-item fw-bolder">4. No Illegal requests: depending on where the request is will have to be monitored by the admins</li>
                  <li class="list-group-item fw-bolder">5. Ban on failure to following the guidelines</li>
               </ul>
               <h4 class="text-center mt-3 fw-light"> Great, now you can look through the requests in the <a href="dashmain.php" class="btn btn-outline-primary">dashboard</a>
                or <a href="upload.php" class="btn btn-outline-primary">submit one</a> </h4>
            </div>
         </div>
      </div>
   </main>

   <?php include('template/footer.php'); ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>
</body>
</html>