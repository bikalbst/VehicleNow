<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vehiclenow";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullName = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Prepare SQL statement
    $sql = "INSERT INTO contact (FullName, Email, Message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
 
    // Bind parameters to the prepared statement
    $stmt->bind_param("sss", $fullName, $email, $message);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "<script>alert('Data inserted successfully";
        // header('Location: index.php');
    } else {
        echo "Error inserting data: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>