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
    $pdo = getPDOInstance(); // Initialize PDO only when needed

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lesson) {
            echo "Nie znaleziono lekcji";
            exit;
        }
    } else {
        echo "Nie podano ID lekcji";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $class = $_POST['class'];
        $content = $_POST['content'];

        $stmt = $pdo->prepare("UPDATE lessons SET title = :title, class = :class, content = :content WHERE id = :id");
        $stmt->execute(['title' => $title, 'class' => $class, 'content' => $content, 'id' => $id]);

        header("Location: lessons.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj lekcje</title>
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
        textarea, input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
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
    <h1>Edytuj lekcje</h1>
    <form action="edit_lesson.php?id=<?php echo htmlspecialchars($lesson['id']); ?>" method="POST" onsubmit="return submitForm()">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($lesson['id']); ?>">

        <label for="title">Tytuł lekcji:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($lesson['title']); ?>" required>

        <label for="title">Klasa:</label>
        <input type="number" id="class" name="class" value="<?php echo htmlspecialchars($lesson['class']); ?>" required>

        <label for="content">Tekst lekcji:</label>
        <div id="editor-container"></div>
        <input type="hidden" id="content" name="content">

        <button type="submit">Zapisz zmiany</button>
    </form>
    <a href="lessons.php">Powrót do lekcji</a>

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

        // Preload content
        quill.root.innerHTML = <?php echo json_encode($lesson['content']); ?>;

        // Submit form with Quill content
        function submitForm() {
            const contentInput = document.getElementById('content');
            contentInput.value = quill.root.innerHTML; // Set Quill content as hidden input value
            return true;
        }
    </script>
</body>
</html>
