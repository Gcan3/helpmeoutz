<?php

// Include connection and suppress any warnings
@include 'config.php';

if(isset($_POST['submit'])){
   // Link the user input into the php code while hashing the passwords
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, $_POST['password']);
   $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

   // Search MySQL database on existing email data
   $select = " SELECT * FROM user_form WHERE email = '$email'";
   // Load the database from the query
   $result = mysqli_query($conn, $select);

   // If conditional to act if email already exists
   if(mysqli_num_rows($result) > 0) {
      // Enter error string into the array
      $error[] = 'email already exists';

   } else {
      // Another If conditional for when the passwords do not match
      if($pass != $cpass){
         // Error array gets modified
         $error[] = 'passwords do not match';
      } elseif (!preg_match("#[0-9]+#", $pass)){
         $error[] = 'password must contain at least one number';
      } elseif (!preg_match("#[A-Z]+#", $pass)){
         $error[] = 'password must contain at least one capital letter';
      } elseif ($pass != $cpass){
         $error[] = 'passwords do not match';
      } else {
         // Hash the password
         $hashpass = md5($pass);
         // Inserting MySql data 
         $insert = "INSERT INTO user_form(name, email, password) VALUES('$name','$email','$hashpass')";
         // Load the action from the query
         mysqli_query($conn, $insert);
         // Redirect the page to the login page in succession
         header('location:index.php');
      }
   }

};


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Helpmeoutz Sign In</title>

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
         <h4 class="text-uppercase my-2">become one with the community</h4>
         <!-- Error Text -->
         <?php
            if(isset($error)){
               foreach($error as $error){
                  echo '<span class="errorTxt mx-auto my-2 p-1 text-capitalize rounded">'.$error.'</span>';
               };
            };
         ?>
         <!-- Form inputs -->
         <input type="text" name="name" required placeholder="Enter Username" class="py-2 px-3 my-2 rounded">
         <input type="email" name="email" required placeholder="Enter Email" class="py-2 px-3 my-2 rounded">
         <input type="password" name="password" required placeholder="Enter Password" class="py-2 px-3 my-2 rounded">
         <input type="password" name="cpassword" required placeholder="Confirm Password" class="py-2 px-3 my-2 rounded">
         <!-- Button -->
         <input type="submit" name="submit" value="register now" class="form-btn text-capitalize py-1 my-2 rounded">
         <!-- Alternate Links -->
         <p class="mt-3">Already have an account? <a href="index.php" class="text-capitalize">login now</a> or <a href="googleAPI.php" class="text-capitalize">login with google</a></p>
      </form>
      <!-- Bootstrap Scripts -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   </div>

</body>
</html>