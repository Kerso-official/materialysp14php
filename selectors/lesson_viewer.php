<?php
session_start();

$dsn = "mysql:host=localhost;dbname=dm70016_materialysp14;charset=utf8mb4";
$username = "dm70016_materialysp14";
$password = "&76$3GK#G0EE";

// Establish PDO connection immediately
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Get class parameter from URL
$class = $_GET['redirectclass'] ?? null;
if ($class === null) {
    echo '<h1 id="blink_text">Nie podano klasy w URL</h1>';
    exit;
}

// Check if it's a valid integer (positive or negative)
if (filter_var($class, FILTER_VALIDATE_INT)) {
    $class = (int) $class;  // Safely cast to integer
} else {
    echo '<h1 id="blink_text">Wybrano nieprawidłową klasę</h1>';
    exit;
}

// Check if the class number is greater than 8
if ($class > 8 || $class === 0) {
    echo '<h1 id="blink_text">Wybrano nieprawidłową klasę</h1>';
    exit;
}

// Check if lesson_id is provided
if (isset($_GET['lesson_id'])) {
    $lessonId = $_GET['lesson_id'];
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = :id");
    $stmt->execute(['id' => $lessonId]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lesson) {
        echo "<h1>Nie znaleziono lekcji.</h1>";
        exit;
    }
} else {
    echo "<h1>Nie podano ID lekcji.</h1>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lesson['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 800px;
            margin: auto;
        }
        .lesson-content {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
        }
        h1 {
            margin-bottom: 20px;
        }
        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #007bff;
        }
        a.back-link:hover {
            text-decoration: underline;
        }

        #blink_text {  
            animation-name:blink;
            animation-duration:2s;
            animation-timing-function:ease-in;
            animation-iteration-count:infinite;
        }
  
        @keyframes blink {
            0% {color:red;}
            50% {color:white;}
            100% {color:red;}
        }
    </style>
</head>
<body>
    <a href="class_selector.php?klasa=<?=$class ?>" class="back-link">← Powrót do lekcji (klasa <?=$class ?>)</a>
    <h1>Temat: <?php echo htmlspecialchars($lesson['title']); ?></h1>
    <div class="lesson-content">
        <?php echo $lesson['content']; // Render the content as HTML ?>
    </div>
</body>
</html>
