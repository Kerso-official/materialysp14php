<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit;
}

$dsn = "mysql:host=localhost;dbname=dm70016_materialysp14;charset=utf8mb4";
$username = "dm70016_materialysp14";
$password = "&76$3GK#G0EE";

// PDO instance, initially set to null
$pdo = null;

// Lazy initialization of the PDO object
function getPDOInstance() {
    global $pdo, $dsn, $username, $password;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
    return $pdo;
}

try {
    $pdo = getPDOInstance(); // Lazy initialize PDO

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $class = $_POST['class'];
        $content = $_POST['content'];

        $stmt = $pdo->prepare("INSERT INTO lessons (title, class, content) VALUES (:title, :class, :content)");
        $stmt->execute(['title' => $title, 'class' => $class, 'content' => $content]);

        header("Location: lessons.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
