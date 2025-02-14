<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style-csel.css">
    <?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dsn = "mysql:host=" . $_ENV['MYSQL_HOST'] . ";dbname=" . $_ENV['MYSQL_DB'] . ";charset=utf8mb4";
$username = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASS'];

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

$class = $_GET['klasa'] ?? null;
if ($class === null || !filter_var($class, FILTER_VALIDATE_INT) || $class <= 0 || $class > 8) {
    echo '<h1 id="blink_text">Wybrano nieprawidłową klasę</h1>';
    exit;
}

$cacheKey = "lessons_class_$class";
$lessons = apcu_fetch($cacheKey);

if ($lessons === false) {
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE class = :class");
    $stmt->execute(['class' => $class]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    apcu_store($cacheKey, $lessons, 600);
}
?>
    <title>KLASA <?= htmlspecialchars($class) ?></title>
</head>
<body>
    <div class="content">
        <h1 class="title">KLASA <?= htmlspecialchars($class) ?></h1>
        
        <a href="../" style="color: aqua;">← Powrót do strony głównej</a>

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
    <!-- Biblioteka Particles JS -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <!-- Skrypt aplikacji -->
    <script src="../app.js"></script>
</body>
</html>