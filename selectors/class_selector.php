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

    $cacheKey = "sections_class_$class";
    $sections = apcu_fetch($cacheKey);

    if ($sections === false) {
        $stmt = $pdo->prepare("
            SELECT s.id AS section_id, s.title AS section_title, s.description AS section_description, 
                   l.id AS lesson_id, l.title AS lesson_title
            FROM sections s
            LEFT JOIN lessons l ON s.id = l.section_id
            WHERE l.class = :class
            ORDER BY s.id, l.id
        ");
        $stmt->execute(['class' => $class]);
        $sections = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        apcu_store($cacheKey, $sections, 600);
    }
    ?>
    <title>KLASA <?= htmlspecialchars($class) ?></title>
</head>
<body>
    <div class="content">
        <h1 class="title">KLASA <?= htmlspecialchars($class) ?></h1>
        
        <a href="../" style="color: aqua;">← Powrót do strony głównej</a>

        <?php if ($sections): ?>
            <?php foreach ($sections as $section_id => $lessons): ?>
                <div class="dzial">
                    <div class="dzialH"><?= htmlspecialchars($lessons[0]['section_title']) ?></div>
                    <p><?= htmlspecialchars($lessons[0]['section_description']) ?></p>
                    <div class="dzialD">
                        <?php foreach ($lessons as $index => $lesson): ?>
                            <div class="numery">
                                <div class="numer"><?= ($index + 1) ?>.</div>
                            </div>
                            <div class="lekcje">
                                <div class="lekcja">
                                    <a href="lesson_viewer.php?lesson_id=<?= urlencode($lesson['lesson_id']) ?>&redirectclass=<?= urlencode($class) ?>">
                                        <?= htmlspecialchars($lesson['lesson_title']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
