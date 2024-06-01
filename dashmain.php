<?php

@include 'config.php';

session_start();

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

    // setting the default value for the take request button
    $own_request = true;

    // The codes below are for the user view mode toggling

    // Set default view mode
    $view_mode = isset($_SESSION['view_mode']) ? $_SESSION['view_mode'] : "all";

    // Update view mode if toggle button is clicked
    if (isset($_POST['view_toggle'])) {
        // switch to the opposite view mode
        $view_mode = ($_POST['view_toggle'] == "all") ? "all" : "user";
        //  save the view mode in the session
        $_SESSION['view_mode'] = $view_mode;
        // reload the page to apply the new view mode
        header('location: dashmain.php');
        exit;
    }

    if (isset($_POST['delete_request'])) {
        if (isset($_POST['request_id']) && !empty($_POST['request_id'])) {
            $request_id = $_POST['request_id'];
            $sql_delete = "DELETE FROM user_request WHERE request_id = $request_id AND request_user = '$username'";
            $conn->query($sql_delete);
            // reload the page to reflect the deletion
            header('location: dashmain.php');
            exit;
        } else {
            echo "Error: request_id is not set or empty";
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
    <!-- Main Container -->
    <main class="main-container row p-0 m-0">
        <!-- Requests Container -->
        <div class="request-container col-md-8 mt-4">
            <!-- Toggle Button between user requests and all requests -->
            <form method="post" class="d-flex align-items-center justify-content-around">
                <h3 class="mx-3 my-3"> Community Requests </h3>
                <button type="submit" name="view_toggle" value="<?php echo ($view_mode == "all") ? "user" : "all"; ?>" class="btn btn-outline-light view-toggle-btn <?php echo ($view_mode == "all") ? "all-mode" : "user-mode"; ?>">
                   Toggle List: <?php echo ($view_mode == "all") ? "Your Requests" : "All Requests"; ?>
                </button>
            </form>
            <?php
                // SQL query based on view mode
                if ($view_mode == "all") {
                    // Display all requests
                    $sql_take = "SELECT * FROM user_request ORDER BY request_id DESC";
                } else {
                    // Display only the user's requests
                    $sql_take = "SELECT * FROM user_request WHERE request_user = '$username' ORDER BY request_id DESC";
                }
                $result = $conn->query($sql_take);

                // Display the following requests information
                if($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()) {
                        $req_id = $row['request_id'];
                        $req_user = $row['request_user'];
                        $topic = $row['request_topic'];
                        $main = $row['request_main'];
                        $image = $row['request_image']; // Adjust path if image is stored in a subdirectory
                        $country = $row['request_country'];
                        $city = $row['request_city'];
                        $req_email = $row['request_email'];

                        // Hide button if it's the user's own request
                        $own_request = ($email !== $req_email); 
            ?>
            <div class="card mt-3 mx-3">
                <div class="row g-0">
                    <div class="col-md-4 d-flex align-items-center">
                        <img src="<?php echo $image ?>" class="card-img-top w-100 align-middle img-fluid" alt="IMAGE">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h3 class="card-title"> <?php echo $topic ?></h3><!--Request title-->
                            <h5 class="req-poster">  <?php echo $req_user ?> </h5><!--User-->
                            <p class="card-text">  <?php echo $main ?> </p><!--Request body-->
                            <p class="req-time">  </p><!--Request time-->
                            <p class="req-location"> <b>Location:</b> <?php echo $city ?>, <?php echo $country ?> </p><!--Request location-->
                            <!-- Display button if the posted request is not from the user to avoid confusion -->
                            <?php if ($own_request) : ?>
                                <a href="mailto: <?php echo $req_email ?>" target="_blank" class="btn btn-primary">Take on Request</a>
                            <?php endif; ?>
                            <?php if (!$own_request) : ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $req_id; ?>">
                                    Delete Request
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="deleteModal<?php echo $req_id; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this request? This is permanent and cannot be undone. You can make a new request if needed.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form method="post">
                                    <input type="hidden" name="request_id" value="<?php echo $req_id; ?>">
                                    <button type="submit" name="delete_request" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    }// While loop ends
                } else {
                    echo '<div class="card mt-3 mx-3">
                                <div class="card-body">
                                    <p class="card-text">' . (($view_mode == "all") ? "No requests found." : "You have no requests.") . '</p>
                                </div>
                            </div>';
                }
                // Update view mode if toggle button is submitted (assuming a button with name="view_toggle")
                if (isset($_POST['view_toggle'])) {
                    $view_mode = ($_POST['view_toggle'] == "all") ? "all" : "user";
                    $_SESSION['view_mode'] = $view_mode;
                }
            ?>
        </div>
        <div class="sidebar col-md-4 mt-4">
            <div class="card my-2">
                <div class="card-header">
                    Welcome, <?php echo $username; ?>!
                </div>
                <div class="card-body">
                    Today's date and time: <br><span id="datetime" class="fs-5"></span>
                </div>
            </div>
            <div class="card my-2">
                <div class="card-header">
                    Community articles!
                </div>
                <div class="card-body">
                    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="card" style="width: 22rem;">
                                    <div class="card-body">
                                        <h5 class="card-title">Meet Carl: best dog for excuses</h5>
                                        <p class="card-text">Some people around Windrower City believe that Carl, an irish pup, always have problems...</p>
                                        <a href="#" class="btn btn-primary">Read More</a>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="card" style="width: 22rem;">
                                    <div class="card-body">
                                        <h5 class="card-title">New Moon suspected in Mars</h5>
                                        <p class="card-text">Citizens in Dalewood tends to look out their window after hearing shocking news...</p>
                                        <a href="#" class="btn btn-primary">Read More</a>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="card" style="width: 22rem;">
                                    <div class="card-body">
                                        <h5 class="card-title">Mayor of Rappiron recognizes HelpMeOutz</h5>
                                        <p class="card-text">The popcorn was ready for consumption as we check out a documentary about the app's use in Rappiron.</p>
                                        <a href="#" class="btn btn-primary">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('template/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="source/time.js"></script>
</body>
</html>
