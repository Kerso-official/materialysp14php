<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
            color: #333;
        }

        .buttons {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .buttons button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        .buttons button:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <h1>Witaj w panelu administratora</h1>
    <div class="buttons">
        <button onclick="redir('./lessons.php')">Wyświetl wszystkie lekcje</button>
        <button onclick="redir('./add_lesson.php')">Dodaj lekcje</button>
    </div>
    <a href="logout.php">Wyloguj</a>

    <script>
        function redir(string) {
            window.location.href = string;
        }
    </script>
</body>
</html>
