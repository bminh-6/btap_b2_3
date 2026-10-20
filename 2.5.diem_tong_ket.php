
<?php
function finalScore(float $cc, float $mid, float $final): float {
    return $cc * 0.10 + $mid * 0.30 + $final * 0.60;
}

$cc = "";
$mid = "";
$final = "";
$thongBao = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cc = trim($_POST["cc"] ?? "");
    $mid = trim($_POST["mid"] ?? "");
    $final = trim($_POST["final"] ?? "");

    if (
        $cc === "" || $mid === "" || $final === "" ||
        !is_numeric($cc) || !is_numeric($mid) || !is_numeric($final) ||
        $cc < 0 || $cc > 10 ||
        $mid < 0 || $mid > 10 ||
        $final < 0 || $final > 10
    ) {
        $thongBao = "Vui lòng nhập đủ 3 điểm hợp lệ từ 0 đến 10.";
    } else {
        $tongKet = finalScore((float)$cc, (float)$mid, (float)$final);

        if ($tongKet >= 8) {
            $xepLoai = "Giỏi";
        } elseif ($tongKet >= 6.5) {
            $xepLoai = "Khá";
        } elseif ($tongKet >= 5) {
            $xepLoai = "Trung bình";
        } else {
            $xepLoai = "Chưa đạt";
        }

        $thongBao = "Điểm tổng kết: " . number_format($tongKet, 2)
                  ;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tính điểm tổng kết</title>
</head>
<body>
    <h2>TÍNH ĐIỂM TỔNG KẾT</h2>

    <form method="POST">
        <p>
            <label>Điểm chuyên cần (10%):</label>
            <input type="number" name="cc" min="0" max="10"
                   step="any" required
                   value="<?= htmlspecialchars($cc, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label>Điểm giữa kỳ (30%):</label>
            <input type="number" name="mid" min="0" max="10"
                   step="any" required
                   value="<?= htmlspecialchars($mid, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <p>
            <label>Điểm cuối kỳ (60%):</label>
            <input type="number" name="final" min="0" max="10"
                   step="any" required
                   value="<?= htmlspecialchars($final, ENT_QUOTES, 'UTF-8') ?>">
        </p>

        <button type="submit">Tính điểm</button>
    </form>

    <?php if ($thongBao !== ""): ?>
        <h3><?= htmlspecialchars($thongBao, ENT_QUOTES, 'UTF-8') ?></h3>
    <?php endif; ?>
</body>
</html>
