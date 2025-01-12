<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style-csel.css">
<?php
// Singleton dla połączenia z bazą danych
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $dsn = "mysql:host=localhost;dbname=dm70016_materialysp14;charset=utf8mb4";
        $username = "dm70016_materialysp14";
        $password = "&76$3GK#G0EE";

        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}

// Weryfikacja parametru klasy
$class = $_GET['klasa'] ?? null;
if ($class === null || !filter_var($class, FILTER_VALIDATE_INT) || $class <= 0 || $class > 8) {
    echo '<h1 id="blink_text">Wybrano nieprawidłową klasę</h1>';
    exit;
}

// Połączenie z bazą danych przez Singleton
$pdo = Database::getInstance();

// Buforowanie zapytań
$cacheKey = "lessons_class_$class";
$lessons = apcu_fetch($cacheKey);

if ($lessons === false) {
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE class = :class");
    $stmt->execute(['class' => $class]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Zapis do cache na 10 minut
    apcu_store($cacheKey, $lessons, 600);
}
?>
    <title>KLASA <?= htmlspecialchars($class) ?></title>
</head>
<body>
    <a href="../" style="color: aqua;">← Powrót do strony głównej</a>
    <div class="content">
        <h1 class="title">KLASA <?= htmlspecialchars($class) ?></h1>
        
        <?php if ($lessons): ?>
            <div class="dzial">
                <div class="dzialH"> TEST </div>
                <div class="dzialC">
                    <?php foreach ($lessons as $index => $lesson): ?>
                        <div class="numery">
                            <div class="numer"><?= ($index + 1) ?>.</div>
                        </div>
                        <div class="lekcje">
                            <div class="lekcja">
                                <a href="lesson_viewer.php?lesson_id=<?= urlencode($lesson['id']) ?>&redirectclass=<?= urlencode($class) ?>">
                                    <?= htmlspecialchars($lesson['title']) ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p>Nie znaleziono lekcji w tej klasie...</p>
        <?php endif; ?>
    </div>
</body>
</html>
