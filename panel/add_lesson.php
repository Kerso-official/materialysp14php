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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj lekcje</title>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 800px;
            margin: auto;
        }
        #editor-container {
            height: 300px;
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Dodaj nową lekcje</h1>
    <form action="save_lesson.php" method="POST" onsubmit="return submitForm()">
        <label for="title">Tytuł lekcji:</label>
        <input type="text" id="title" name="title" required>

        <label for="title">Klasa:</label>
        <input type="number" id="class" name="class" required>

        <label for="content">Tekst lekcji:</label>
        <div id="editor-container"></div>
        <input type="hidden" id="content" name="content">

        <button type="submit">Dodaj lekcje</button>
    </form>
    <a href="lessons.php">Wyświetl wszystkie lekcje</a>

    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        // Initialize Quill editor
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Wpisz tutaj zawartość lekcji...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image']
                ]
            }
        });

        // Submit form with Quill content
        function submitForm() {
            const contentInput = document.getElementById('content');
            contentInput.value = quill.root.innerHTML; // Set Quill content as hidden input value
            return true;
        }
    </script>
</body>
</html>
