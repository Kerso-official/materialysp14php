<?php
session_start();

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
    $pdo = getPDOInstance(); // Initialize PDO only when needed

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputUsername = $_POST['username'];
        $inputPassword = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->execute(['username' => $inputUsername]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($inputPassword, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: admin_panel.php");
            exit;
        } else {
            echo "<h1>Nieprawidłowa nazwa użytkownika lub hasło</h1>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
