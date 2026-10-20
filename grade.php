
<?php
// Hàm kiểm tra điểm hợp lệ
function isValidScore(string $score): bool {
    return $score !== ""
        && is_numeric($score)
        && (float)$score >= 0
        && (float)$score <= 10;
}

// Hàm tính điểm tổng kết
function finalScore(float $cc, float $mid, float $final): float {
    return $cc * 0.10 + $mid * 0.30 + $final * 0.60;
}

$name = "";
$cc = "";
$mid = "";
$final = "";
$error = "";
$result = null;
$xepLoai = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $cc = trim($_POST["cc"] ?? "");
    $mid = trim($_POST["mid"] ?? "");
    $final = trim($_POST["final"] ?? "");

    // Kiểm tra họ tên
    if ($name === "") {
        $error = "Vui lòng nhập họ tên.";
    } elseif (
        !isValidScore($cc) ||
        !isValidScore($mid) ||
        !isValidScore($final)
    ) {
        $error = "Điểm phải là số nằm trong khoảng từ 0 đến 10.";
    } else {
        // Tính điểm tổng kết
        $result = finalScore(
            (float)$cc,
            (float)$mid,
            (float)$final
        );

        // Xếp loại từ cao xuống thấp
        if ($result >= 8) {
            $xepLoai = "Giỏi";
        } elseif ($result >= 6.5) {
            $xepLoai = "Khá";
        } elseif ($result >= 5) {
            $xepLoai = "Trung bình";
        } else {
            $xepLoai = "Chưa đạt";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Máy tính điểm học phần</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 9px;
            margin-top: 5px;
        }

        button {
            margin-top: 18px;
            padding: 10px 20px;
            background: #d9c639;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-top: 15px;
        }

        .result {
            background: #f8f8f8;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>MÁY TÍNH ĐIỂM HỌC PHẦN</h2>

<form method="POST">
    <label for="name">Họ tên sinh viên:</label>
    <input
        type="text"
        id="name"
        name="name"
        required
        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="cc">Điểm chuyên cần (10%):</label>
    <input
        type="number"
        id="cc"
        name="cc"
        min="0"
        max="10"
        step="any"
        required
        value="<?= htmlspecialchars($cc, ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="mid">Điểm giữa kỳ (30%):</label>
    <input
        type="number"
        id="mid"
        name="mid"
        min="0"
        max="10"
        step="any"
        required
        value="<?= htmlspecialchars($mid, ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="final">Điểm cuối kỳ (60%):</label>
    <input
        type="number"
        id="final"
        name="final"
        min="0"
        max="10"
        step="any"
        required
        value="<?= htmlspecialchars($final, ENT_QUOTES, 'UTF-8') ?>"
    >

    <button type="submit">Tính điểm</button>
</form>

<?php if ($error !== ""): ?>
    <p class="error">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<?php if ($result !== null): ?>
    <div class="result">
        <h3>KẾT QUẢ</h3>
        <p>Họ tên:
            <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>Điểm tổng kết: <strong><?= number_format($result, 2) ?></strong></p>
        <p>Xếp loại: <strong><?= htmlspecialchars($xepLoai, ENT_QUOTES, 'UTF-8') ?></strong></p>
    </div>
<?php endif; ?>

</body>
</html>
