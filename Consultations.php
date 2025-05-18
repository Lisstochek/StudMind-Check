<?php
session_start();
require_once 'db.php';

// Перевірка авторизації
if (!isset($_SESSION['idZdobuvacha'])) {
    header("Location: auth.php");
    exit();
}

// Отримати дані користувача
$idZdobuvacha = $_SESSION['idZdobuvacha'];
$userQuery = $conn->prepare("SELECT imya, prizvishche, email FROM Zdobuvach_osviti WHERE idZdobuvacha = ?");
$userQuery->bind_param("i", $idZdobuvacha);
$userQuery->execute();
$userQuery->bind_result($imya, $prizvishche, $email);
$userQuery->fetch();
$userQuery->close();

// Отримати список психологів
$psychologists = [];
$result = $conn->query("SELECT idPsykhologa, CONCAT(imyaPsykhologa, ' ', prizvPsykhologa) AS name FROM Psykhology");
while ($row = $result->fetch_assoc()) {
    $psychologists[] = $row;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Запис на консультацію</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="images/logo.png" type="image/x-icon"/>
</head>
<body>
<header>
    <div class="logo-title">
        <img src="images/logo.png" alt="Логотип" class="logo">
        <h1>StudMind Check</h1>
        <div class="auth-container">
            <a href="auth.php" title="Профіль">
                <img src="images/auth.png" alt="Профіль" class="auth-icon">
            </a>
            <a href="logout.php" title="Вийти">
                <img src="images/logout.png" alt="Вийти" class="auth-icon">
            </a>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="StudMind Check.html">Головна</a></li>
            <li><a href="ZapStan.php">Запис стану</a></li>
            <li><a href="Psychologist.html">Психологи</a></li>
            <li><a href="Consultations.php">Запис на консультації</a></li>
        </ul>
    </nav>
</header>

<main class="consultation-container">
    <h2>Запис на консультацію</h2>
    <form method="POST" action="ConsultationsHandler.php" class="consultation-form">
        <label>Ім’я та прізвище:</label>
        <input type="text" value="<?= htmlspecialchars($imya . ' ' . $prizvishche) ?>" readonly>

        <label for="date">Дата консультації:</label>
        <input type="date" name="date" required>

        <label for="time">Час консультації:</label>
        <input type="time" name="time" required>

        <label for="psychologist_id">Оберіть психолога:</label>
        <select name="psychologist_id" required>
            <option value="">-- Оберіть --</option>
            <?php foreach ($psychologists as $psych): ?>
                <option value="<?= $psych['idPsykhologa'] ?>"><?= htmlspecialchars($psych['name']) ?></option>
            <?php endforeach; ?>
        </select>

        
        <label>Формат консультації:</label>
        <div class="format-options">
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="format" value="online" checked>
                    <span>Онлайн</span>
                </label>
                <label class="radio-label">
                    <input type="radio" name="format" value="offline">
                    <span>Офлайн</span>
                </label>
            </div>
        </div>

        <label>Email:</label>
        <input type="email" value="<?= htmlspecialchars($email) ?>" readonly>

        <button type="submit">Записатись</button>
    </form>
</main>
</body>
</html>