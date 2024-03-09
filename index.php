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

echo "Connected successfully";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardName = $_POST['cardName'];
    $firstName = htmlspecialchars($_POST['firstName']); 
    $lastName = htmlspecialchars($_POST['lastName']);
    $rollNo = htmlspecialchars($_POST['rollNo']);
    $BOJ = $_POST['BOJ'];

    // Check if the rollno already exists
    $checkQuery = "SELECT * FROM users WHERE rollno = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $rollNo);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo "Roll number already exists. Choose a different roll number.";
    } else {
        // Proceed with the insert query using prepared statement
        $insertQuery = "INSERT INTO users (rollno, firstname, lastname, course, batch) VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("sssss", $rollNo, $firstName, $lastName, $cardName, $BOJ);

        if ($insertStmt->execute()) {
            echo "Success";
            header("Location:suc.html");
        } else {
            echo "Error: " . $insertStmt->error;
        }

        $insertStmt->close();
    }

    $checkStmt->close();
}

$conn->close();
?>
