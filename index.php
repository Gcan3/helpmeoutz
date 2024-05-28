<?php

// Include connection and suppress any warnings
@include 'config.php';

// Begin database session
session_start();

if(isset($_POST['submit'])){
   // Link the user input into the php code while hashing the passwords
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);

   // Search MySQL database on existing email data
   $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass'";
   // Load the database from the query
   $result = mysqli_query($conn, $select);

   // If conditional to act if the exact email and password exists in the database (successful login)
   if(mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_array($result);
      // Acquire the user name of the logged in person
      $_SESSION['user_name'] = $row['name'];
      $_SESSION['user_email'] = $row['email'];
      // Redirect the user to the main page
      header('location:user_page.php');

   } else {
      // Otherwise, store the error message into the array for display
      $error[] = 'incorrect email or password!';
   }

};
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Helpmeoutz Login</title>
   <!-- Bootstrap styles -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
   integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
   crossorigin="anonymous">
   <!-- Custom Styles  -->
   <link rel="stylesheet" href="source/formstyle.css">

</head>
<body class="change-bg">
   
<div class="form-container d-flex text-center p-4 pb-4">
   <form class="text-center" action="" method="post">
      <!-- Logo -->
      <div class="text-center">
         <img class="logo img-fluid my-1"
            src="photosource/helpmeoutz-high-resolution-logo-transparent.png" alt="helpmetoutzlogo">
      </div>
      <!-- Slogan -->
      <h4 class="text-uppercase my-2">welcome the the helping hand of the community</h4>
      <!-- Error text -->
      <?php
         if(isset($error)){
            foreach($error as $error){
               echo '<span class="errorTxt mx-auto my-2 p-1 text-capitalize rounded">'.$error.'</span>';
            };
         };
      ?>
      <!-- Form inputs -->
      <input type="email" name="email" required placeholder="Enter your email" class="py-2 px-3 my-2 rounded">
      <input type="password" name="password" required placeholder="Enter your password" class="py-2 px-3 my-2 rounded">
      <!-- Button -->
      <input type="submit" name="submit" value="login now" class="form-btn text-capitalize py-1 my-2 rounded">
      <!-- Alternative Links -->
      <p class="mt-3">Don't have an account? <a href="register_form.php" class="text-capitalize">register now</a> or <a href="googleAPI.php" class="text-capitalize">sign in with google</a></p>
   </form>
</div>

</body>
</html>