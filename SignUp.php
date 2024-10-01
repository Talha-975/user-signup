<?php
session_start(); // Start the session

$servername = 'localhost';
$username = 'root';
$password = '';
$db_name = 'user_data';

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS user_info (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL
)";
$conn->query($sql);

// Sign up Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $confirm_password = $_POST['confirm_password'];
    $phone_number = $_POST['phone_number'];
    $profile_picture = $_FILES['profile_picture'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];

    // Input Validation
    $error = array();
    if (empty($firstname)) {
        $error['firstname'] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $firstname)) {
        $error['firstname'] = "Invalid first name";
    }
    if (empty($lastname)) {
        $error['lastname'] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $lastname)) {
        $error['lastname'] = "Invalid last name";
    }
    if (empty($email)) {
        $error['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Invalid email";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM user_info WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error['email'] = "This email is already registered.";
        }
    }
    if (empty($_POST['password'])) {
        $error['password'] = "Password is required";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $_POST['password'])) {
        $error['password'] = "Password Format: At least 8 characters, uppercase/lowercase letters, symbols, and numeric digits.";
    }
    if (empty($confirm_password)) {
        $error['confirm_password'] = "Confirm password is required";
    } elseif ($_POST['password'] != $_POST['confirm_password']) {
        $error['confirm_password'] = "Passwords do not match";
    }
    if (empty($phone_number)) {
        $error['phone_number'] = "Phone number is required";
    } elseif (!preg_match("/^\+[0-9]{1,3} [0-9]{3} [0-9]{7}$/", $phone_number)) {
        $error['phone_number'] = "Invalid format! Valid Format: +92 300 1234567";
    }
    if (empty($address)) {
        $error['address'] = "Address is required";
    }
    if (empty($city)) {
        $error['city'] = "City is required";
    }
    if (empty($country)) {
        $error['country'] = "Country is required";
    }

    if (count($error) == 0) {
        // Inserting the form into database
        $stmt = $conn->prepare("INSERT INTO user_info (firstname, lastname, email, password, phone_number, profile_picture, address, city, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $firstname, $lastname, $email, $password, $phone_number, $profile_picture['name'], $address, $city, $country);
        if ($stmt->execute()) {
            // Upload profile picture
            $target_dir = ""; // Ensure this directory exists and is writable
            $target_file = $target_dir . basename($profile_picture['name']);
            move_uploaded_file($profile_picture['tmp_name'], $target_file);
            $_SESSION['profile_picture'] = $target_file;
            header("Location: Login Page.php");
            exit;
        } else {
            $error['general'] = "Error occurred during registration";
        }
    }
}

// Close connection
$conn->close();
?>


<style>
    form {
        width: 50%;
        margin: 70px auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 10px lightgrey;
    }

    input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #838383;
        border-radius: 5px;
    }

    button[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #159e00;
        color: white;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

        button[type="submit"]:hover {
        background-color: #17B200;
    }

    .error {
        color: red;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .profile-picture {
        width: 150px; /* adjust the size as needed */
        height: 150px;
        border-radius: 50%; /* makes the container circular */
        overflow: hidden; /* hides excess image parts */
        cursor: pointer;
    }

    .profile-picture img {
        width: 40%;
        height: 40%;
        object-fit: cover; /* scales the image to cover the container */
    }
</style>


<!-- Sign Up Form (html) -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <h2 style="text-align: center; text-decoration:underline;">Sign Up/Register Yourself</h2>
    <br><br>

    <label class="profile-picture">
    <?php if (!empty($target_file)) { ?>
        <img src="<?php echo $target_file; ?>" alt="Profile Picture" id="placeholder-image" style="width: 100px; height: 100px; border-radius: 50%; margin-left: 250px;">
    <?php } else { ?>
        <img src="placeholder.png" alt="Placeholder Image" id="placeholder-image" style="width: 100px; height: 100px; border-radius: 50%; margin-left: 275px;">
    <?php } ?>
    <input type="file" name="profile_picture" accept="image/*" onchange="document.getElementById('placeholder-image').src = window.URL.createObjectURL(this.files[0])" style="display: none;">
</label> <br><br><br><br>

    <input type="text" name="firstname" placeholder="First Name" value="<?php echo $_POST['firstname'] ?? ''; ?>" >
    <?php if (isset($error['firstname'])) echo '<span class="error">' . $error['firstname'] . '</span>' ?>
    <br><br>
    <input type="text" name="lastname" placeholder="Last Name" value="<?php echo $_POST['lastname'] ?? ''; ?>" >
    <?php if (isset($error['lastname'])) echo '<span class="error">' . $error['lastname'] . '</span>' ?>
    <br><br>
    <input type="email" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?? ''; ?>" >
    <?php if (isset($error['email'])) echo '<span class="error">' . $error['email'] . '</span>' ?>
    <br><br>
    <input type="password" name="password" placeholder="Password" >
    <?php if (isset($error['password'])) echo '<span class="error">' . $error['password'] . '</span>' ?>
    <br><br>
    <input type="password" name="confirm_password" placeholder="Confirm Password" >
    <?php if (isset($error['confirm_password'])) echo '<span class="error">' . $error['confirm_password'] . '</span>' ?>
    <br><br>
    <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo $_POST['phone_number'] ?? ''; ?>" >
    <?php if (isset($error['phone_number'])) echo '<span class="error">' . $error['phone_number'] . '</span>' ?>
    <br><br>
    <input type="text" name="address" placeholder="Address" value="<?php echo $_POST['address'] ?? ''; ?>" >
    <?php if (isset($error['address'])) echo '<span class="error">' . $error['address'] . '</span>' ?>
    <br><br>
    <input type="text" name="city" placeholder="City" value="<?php echo $_POST['city'] ?? ''; ?>" >
    <?php if (isset($error['city'])) echo '<span class="error">' . $error['city'] . '</span>' ?>
    <br><br>
    <input type="text" name="country" placeholder="Country" value="<?php echo $_POST['country'] ?? ''; ?>" >
    <?php if (isset($error['country'])) echo '<span class="error">' . $error['country'] . '</span>' ?>
    <br><br>
    <button type="submit">Sign Up / Register</button>

    <?php if (isset($error['general'])) echo '<span class="error">' . $error['general'] . '</span>' ?>
</form>