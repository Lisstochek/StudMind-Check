<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['idZdobuvacha'])) {
  header("Location: ZapStan.php");
  exit();
}

$error = '';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $parol = $_POST['parol'];

    $stmt = $conn->prepare("SELECT idZdobuvacha, imya, prizvishche, email, parol FROM Zdobuvach_osviti WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($idZdobuvacha, $imya, $prizvishche, $email_from_db, $hashed_password);
        $stmt->fetch();

        if (password_verify($parol, $hashed_password)) {
            $_SESSION['idZdobuvacha'] = $idZdobuvacha;
            $_SESSION['imya'] = $imya;
            $_SESSION['prizvishche'] = $prizvishche;
            $_SESSION['email'] = $email_from_db;

            header("Location: ZapStan.php");
            exit();
        } else {
            $error = "Невірний пароль.";
        }
    } else {
        $error = "Користувача з таким email не знайдено.";
    }
    $stmt->close();
}

if (isset($_POST['register'])) {
    $imya = $_POST['imya'];
    $prizvishche = $_POST['prizvishche'];
    $email = $_POST['email'];
    $parol = password_hash($_POST['parol'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Zdobuvach_osviti (imya, prizvishche, email, parol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $imya, $prizvishche, $email, $parol);

    if ($stmt->execute()) {
        $_SESSION['idZdobuvacha'] = $stmt->insert_id;
        $_SESSION['imya'] = $imya;
        $_SESSION['prizvishche'] = $prizvishche;
        $_SESSION['email'] = $email;

        header("Location: ZapStan.php");
        exit();
    } else {
        $error = "Помилка реєстрації.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>StudMind Check</title>
  <link rel="icon" href="images/logo.png" type="image/x-icon"/>
  <link rel="stylesheet" href="css/styleAuth.css" />
  <link href="https://fonts.googleapis.com/css2?family=Lemonada&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="auth-container">
    <div class="logo-title">
      <img src="images/logo.png" alt="Логотип" class="logo" />
      <h1 class="title">StudMind Check</h1>
    </div>

    <?php if (!empty($error)): ?>
      <p style="color: red; text-align:center; font-weight: bold;"><?= $error ?></p>
    <?php endif; ?>

    <div class="auth-form" id="login-form">
      <h2>Авторизація</h2>
      <form method="POST">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="parol" placeholder="Пароль" required />
        <button type="submit" name="login">Увійти</button>
      </form>
      <p>Немає акаунту? <a href="#" onclick="toggleForm('register')">Зареєструватись</a></p>
    </div>

    <div class="auth-form hidden" id="register-form">
      <h2>Реєстрація</h2>
      <form method="POST">
        <input type="text" name="imya" placeholder="Ім’я" required />
        <input type="text" name="prizvishche" placeholder="Прізвище" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="parol" placeholder="Пароль" required />
        <button type="submit" name="register">Зареєструватись</button>
      </form>
      <p>В вас вже є акаунт? <a href="#" onclick="toggleForm('login')">Авторизуватись</a></p>
    </div>
  </div>

  <script src="js/authScript.js"></script>
</body>
</html>