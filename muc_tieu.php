
<?php
$diem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $diem = trim($_POST["diem"] ?? "");

    if ($diem !== "" && is_numeric($diem)
        && $diem >= 0 && $diem <= 10) {
        $ketQua = "Điểm hợp lệ";
    } else {
        $ketQua = "Điểm không hợp lệ";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra điểm hợp lệ</title>
</head>
<body>
    <h2>Kiểm tra điểm từ 0 đến 10</h2>

    <form method="POST">
        <label for="diem">Nhập điểm:</label>
        <input
            type="number"
            id="diem"
            name="diem"
            step="any"
            required
            value="<?= htmlspecialchars($diem, ENT_QUOTES, 'UTF-8') ?>"
        >
        <button type="submit">Kiểm tra</button>
    </form>

    <?php if (isset($ketQua)): ?>
        <p><?= htmlspecialchars($ketQua, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</body>
</html>
