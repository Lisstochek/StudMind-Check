<?php
session_start();
require_once 'db.php';

$idZdobuvacha = $_SESSION['idZdobuvacha'] ?? null;

// Обробка форми
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['mood_text']) && $idZdobuvacha) {
    $stan = $_POST['mood_text'];
    $prichyny = isset($_POST['factors']) ? implode(', ', $_POST['factors']) : '';
    $data = date('Y-m-d');

    $sql = "INSERT INTO Stan (idZdobuvacha, dataZapysy, stan, prichina) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $idZdobuvacha, $data, $stan, $prichyny);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Стан успішно збережено!";
    header("Location: ZapStan.php");
    exit();
}

// Завантаження даних для графіка
$moodData = [];
$dateData = [];
$showWarning = false;

if ($idZdobuvacha) {
    $query = "SELECT dataZapysy, stan FROM Stan WHERE idZdobuvacha = ? ORDER BY dataZapysy";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idZdobuvacha);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $dateData[] = $row['dataZapysy'];
        $moodData[] = $row['stan'];
    }
    $stmt->close();

    if (count($moodData) >= 1) {
        $threshold = 45;
        $badMoodCount = 0;
        $today = new DateTime();

        for ($i = count($moodData) - 1; $i >= 0; $i--) {
            $date = DateTime::createFromFormat('Y-m-d', $dateData[$i]);
            $interval = $today->diff($date)->days;

            if ($interval <= 7 && intval($moodData[$i]) < $threshold) {
                $badMoodCount++;
            }
        }

        $showWarning = $badMoodCount >= 3;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Запис стану</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" id="favicon" href="images/logo.png" type="image/x-icon"/>
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            text-align: center;
        }
    </style>
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

<main>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <h2>Як ви почуваєтеся?</h2>
    <form method="POST">
        <input type="range" id="mood-slider" min="1" max="100" value="50">
        <input type="hidden" name="mood_text" id="mood-text">

        <div class="slider-labels">
            <span class="bad">Дуже погано</span>
            <span class="neutral">Нейтрально</span>
            <span class="good">Чудово</span>
        </div>

        <h2>Що зараз найбільше впливає на вас?</h2>
        <div class="checkbox-container">
            <?php
            $factors = ["Навчання", "Поточні події", "Родина", "Погода", "Друзі", "Спільнота", "Здоров'я", "Ідентичність", "Інформація", "Хобі"];
            foreach ($factors as $factor) {
                echo "<label><input type='checkbox' name='factors[]' value='$factor'> $factor</label>";
            }
            ?>
        </div>
        <button class="record-btn" type="submit">Записати</button>
    </form>

    <h2>Графік стану</h2>
    <canvas id="moodChart"></canvas>

    <?php if ($idZdobuvacha && $showWarning): ?>
    <div class="warning-box">
        Здається, останнім часом ваш психічний стан не надто добрий.
        <a href="Consultations.php" class="highlight-link">Не бажаєте записатися на консультацію?</a>
    </div>
<?php endif; ?>

</main>

<script>
    window.moodChartLabels = <?= json_encode($dateData) ?>;
    window.moodChartData = <?= json_encode($moodData) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/stanScript.js"></script>
</body>
</html>