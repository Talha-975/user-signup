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

// Login Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Input Validation
    $error = array();
    if (empty($email)) {
        $error['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Invalid email";
    }

    if (empty($password)) {
        $error['password'] = "Password is required";
    }

    if (count($error) == 0) {
        $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) { 
                // Store user info in session
                $_SESSION['user'] = $user;
                header("Location: UserInput.php");
                exit;
            } else {
                $error['password'] = "Incorrect password";
            }
        } else {
            $error['email'] = "User not found";
        }
    }
}

// Close connection
$conn->close();
?>


<style>
    form {
        width: 30%;
        margin: 170px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow:0 0 10px lightgrey;
    }

    input[type="email"], input[type="password"] {
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
        font-size: 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #17B200;
    }

    .error {
        color: red;
        font-size: 16px;
        margin-bottom: 20px;
    }
</style>


<!-- Login form -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h2 style="text-align: center; text-decoration:underline;">Login</h2>
    <br><br>
    <input type="email" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?? ''; ?>" >
    <?php if (isset($error['email'])) echo '<span class="error">' . $error['email'] . '</span>' ?>
    <br><br>
    <input type="password" name="password" placeholder="Password" value="<?php echo isset($error['password']) ? '' : $_POST['password'] ?? ''; ?>" >
    <?php if (isset($error['password'])) echo '<span class="error">' . $error['password'] . '</span>' ?>
    <br><br>
    <button type="submit">Login</button>
<form>