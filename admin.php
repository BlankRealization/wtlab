<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coursereg";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Perform SQL query to check admin credentials
    $sql = "SELECT * FROM AdminData WHERE username = '$username' AND pass = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Authentication successful
        echo "Login successful. Redirecting to admin dashboard...";
        // You can redirect to the admin dashboard page here using header() function
        header("Location: dashboard.php");
    } else {
        // Authentication failed
        echo "Invalid username or password. Please try again.";
    }
}

$conn->close();
?>
