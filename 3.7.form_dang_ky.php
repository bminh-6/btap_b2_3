
<?php
$errors = [];
$data = [
    "name" => "",
    "email" => "",
    "birthday" => ""
];

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Nhận dữ liệu từ form
    $data["name"] = trim($_POST["name"] ?? "");
    $data["email"] = trim($_POST["email"] ?? "");
    $data["birthday"] = trim($_POST["birthday"] ?? "");

    // Kiểm tra họ tên
    if ($data["name"] === "") {
        $errors["name"] = "Vui lòng nhập họ tên.";
    }

    // Kiểm tra email
    if ($data["email"] === "") {
        $errors["email"] = "Vui lòng nhập email.";
    } elseif (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email không đúng định dạng.";
    }

    // Kiểm tra ngày sinh
    if ($data["birthday"] === "") {
        $errors["birthday"] = "Vui lòng nhập ngày sinh.";
    } else {
        $date = DateTime::createFromFormat(
            "!Y-m-d",
            $data["birthday"]
        );

        if (
            !$date ||
            $date->format("Y-m-d") !== $data["birthday"] ||
            $date > new DateTime("today")
        ) {
            $errors["birthday"] = "Ngày sinh không hợp lệ.";
        }
    }

    // Chỉ thành công khi không có lỗi
    if (empty($errors)) {
        $success = true;
    }
}

// Hàm hiển thị dữ liệu an toàn
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký sinh viên</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 550px;
            margin: 30px auto;
            padding: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px 18px;
            background: #04AA6D;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .success {
            background: #e8f5e9;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>FORM ĐĂNG KÝ SINH VIÊN</h2>

<form method="POST">
    <label for="name">Họ tên:</label>
    <input
        type="text"
        id="name"
        name="name"
        value="<?= e($data["name"]) ?>"
    >
    <?php if (isset($errors["name"])): ?>
        <div class="error"><?= e($errors["name"]) ?></div>
    <?php endif; ?>

    <label for="email">Email:</label>
    <input
        type="text"
        id="email"
        name="email"
        value="<?= e($data["email"]) ?>"
    >
    <?php if (isset($errors["email"])): ?>
        <div class="error"><?= e($errors["email"]) ?></div>
    <?php endif; ?>

    <label for="birthday">Ngày sinh:</label>
    <input
        type="date"
        id="birthday"
        name="birthday"
        value="<?= e($data["birthday"]) ?>"
    >
    <?php if (isset($errors["birthday"])): ?>
        <div class="error"><?= e($errors["birthday"]) ?></div>
    <?php endif; ?>

    <button type="submit">Đăng ký</button>
</form>

<?php if ($success): ?>
    <div class="success">
        <h3>Đăng ký thành công!</h3>
        <p>Họ tên: <?= e($data["name"]) ?></p>
        <p>Email: <?= e($data["email"]) ?></p>
        <p>Ngày sinh: <?= e($data["birthday"]) ?></p>
    </div>
<?php endif; ?>

</body>
</html>
