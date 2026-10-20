
<?php
$diem = "";
$xepLoai = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diem = trim($_POST["diem"] ?? "");

    if ($diem === "" || !is_numeric($diem) || $diem < 0 || $diem > 10) {
        $xepLoai = "Điểm không hợp lệ! Hãy nhập điểm từ 0 đến 10.";
    } elseif ($diem >= 8) {
        $xepLoai = "Xếp loại: Giỏi";
    } elseif ($diem >= 6.5) {
        $xepLoai = "Xếp loại: Khá";
    } elseif ($diem >= 5) {
        $xepLoai = "Xếp loại: Trung bình";
    } else {
        $xepLoai = "Xếp loại: Chưa đạt";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra xếp loại</title>
</head>
<body>
    <h2>CHƯƠNG TRÌNH XẾP LOẠI ĐIỂM</h2>

    <form method="POST">
        <label>Nhập điểm (0 - 10):</label>
        <input
            type="number"
            name="diem"
            min="0"
            max="10"
            step="any"
            value="<?= htmlspecialchars($diem, ENT_QUOTES, 'UTF-8') ?>"
            required
        >
        <button type="submit">Kiểm tra</button>
    </form>

    <?php if ($xepLoai !== ""): ?>
        <h3>
            <?= htmlspecialchars($xepLoai, ENT_QUOTES, 'UTF-8') ?>
        </h3>
    <?php endif; ?>
</body>
</html>
