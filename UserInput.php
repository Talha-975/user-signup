<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: Login Page.php");
    exit;
}

// Retrieve user information from session
$user = $_SESSION['user'];
?>

    <style>
        .profile-container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px lightgrey;
            background-color: #fff;
        }
        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover; /* scales the image to cover the container */
        }
        .user-info {
            margin-top: 20px;
        }
        .profile-info p {
            font-size: 16px;
            margin: 5px 0;
        }
    </style>

    <div class="profile-container">
        <h2 style="text-align: center; text-decoration: underline;">User Information</h2>
        <div class="profile-picture">
            <?php if (!empty($user['profile_picture'])) { ?>
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <?php } else { ?>
                <img src="placeholder.png" alt="Placeholder Image">
            <?php } ?>
        <div class="user-info">
            <p><b>First Name:</b> <?php echo htmlspecialchars($user['firstname']); ?></p>
            <p><b>Last Name:</b> <?php echo htmlspecialchars($user['lastname']); ?></p>
            <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><b>Phone Number:</b> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p><b>Address:</b> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><b>City:</b> <?php echo htmlspecialchars($user['city']); ?></p>
            <p><b>Country:</b> <?php echo htmlspecialchars($user['country']); ?></p>
        </div>
    </div>