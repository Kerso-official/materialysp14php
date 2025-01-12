<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit;
}

// Database connection details
$dsn = "mysql:host=localhost;dbname=dm70016_materialysp14;charset=utf8mb4";
$username = "dm70016_materialysp14";
$password = "&76$3GK#G0EE";

try {
    // Establish PDO connection
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if 'id' parameter is set in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Prepare DELETE query
        $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Redirect after deletion
        header("Location: lessons.php?message=Lesson deleted successfully");
        exit;
    } else {
        // Redirect if no 'id' is provided
        header("Location: lessons.php?message=No lesson ID provided");
        exit;
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
    exit;
}
?>
