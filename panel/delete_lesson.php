<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dsn = "mysql:host=" . $_ENV['MYSQL_HOST'] . ";dbname=" . $_ENV['MYSQL_DB'] . ";charset=utf8mb4";
$username = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASS'];

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
