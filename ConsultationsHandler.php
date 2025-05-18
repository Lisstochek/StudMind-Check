<?php
session_start();
require_once 'db.php';

// Перевірка, чи користувач авторизований
if (!isset($_SESSION['idZdobuvacha'])) {
    header("Location: auth.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idZdobuvacha = $_SESSION['idZdobuvacha'];
    $data = $_POST['date'];
    $chas = $_POST['time'];
    $idPsykhologa = $_POST['psychologist_id'];
    $format = $_POST['format'];

    // Перевірка чи час з психологом вже зайнятий (опціонально)
    $check = $conn->prepare("SELECT * FROM Konsultatsiyi WHERE dataKonsultatsiyi = ? AND chasKonsultatsiyi = ? AND idPsykhologa = ?");
    $check->bind_param("ssi", $data, $chas, $idPsykhologa);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Обраний час вже зайнятий. Оберіть інший.'); window.history.back();</script>";
        exit();
    }

    // Отримуємо останній idKonsultatsiyi
    $res = $conn->query("SELECT MAX(idKonsultatsiyi) AS max_id FROM Konsultatsiyi");
    $row = $res->fetch_assoc();
    $nextId = $row['max_id'] + 1;

    // Запис в БД
    $insert = $conn->prepare("INSERT INTO Konsultatsiyi (idKonsultatsiyi, idZdobuvacha, idPsykhologa, dataKonsultatsiyi, chasKonsultatsiyi, formatKonsultatsiyi) 
                              VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iiisss", $nextId, $idZdobuvacha, $idPsykhologa, $data, $chas, $format);

    if ($insert->execute()) {
        echo "<script>alert('Успішно записано на консультацію!'); window.location.href='StudMind Check.html';</script>";
    } else {
        echo "<script>alert('Сталася помилка при записі. Спробуйте ще раз.'); window.history.back();</script>";
    }

    $insert->close();
    $conn->close();
} else {
    header("Location: Consultations.php");
    exit();
}
?>