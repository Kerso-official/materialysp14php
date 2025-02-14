<?php
session_start();
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

    // Fetch lessons from the database
    $stmt = $pdo->query("SELECT * FROM lessons ORDER BY created_at DESC");
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lekcje</title>
</head>
<body>
    <h1>Wszystkie lekcje</h1>
    <a href="add_lesson.php">Dodaj nową lekcje</a>
    <ul>
        <?php foreach ($lessons as $lesson): ?>
            <li>
                <h2><?php echo htmlspecialchars($lesson['title']); ?>, klasa: <?php echo htmlspecialchars($lesson['class']); ?></h2>
                <div><?php echo $lesson['content']; // Render content as HTML ?></div>
                <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>">Edytuj</a> |
                <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>" onclick="return confirm('Czy jesteś pewny?');">Usuń</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
