
<?php
session_start();

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;

$data = [
    "name" => "",
    "email" => "",
    "subject" => "support",
    "content" => ""
];

$subjects = [
    "support" => "Hỗ trợ kỹ thuật",
    "study" => "Học tập",
    "other" => "Khác"
];

function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Kiểm tra CSRF token
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        $errors["form"] =
            "Yêu cầu không hợp lệ. Vui lòng tải lại trang.";
    }

    // 2. Nhận dữ liệu an toàn
    foreach (["name", "email", "subject", "content"] as $key) {
        if (isset($_POST[$key]) && is_string($_POST[$key])) {
            $data[$key] = trim($_POST[$key]);
        }
    }

    // 3. Kiểm tra họ tên
    if ($data["name"] === "") {
        $errors["name"] = "Vui lòng nhập họ tên.";
    } elseif (mb_strlen($data["name"], "UTF-8") > 100) {
        $errors["name"] = "Họ tên không được quá 100 ký tự.";
    }

    // 4. Kiểm tra email
    if ($data["email"] === "") {
        $errors["email"] = "Vui lòng nhập email.";
    } elseif (
        mb_strlen($data["email"], "UTF-8") > 254 ||
        !filter_var($data["email"], FILTER_VALIDATE_EMAIL)
    ) {
        $errors["email"] = "Email không hợp lệ.";
    }

    // 5. Kiểm tra chủ đề
    if (!array_key_exists($data["subject"], $subjects)) {
        $errors["subject"] = "Chủ đề không hợp lệ.";
    }

    // 6. Kiểm tra nội dung
    $contentLength = mb_strlen($data["content"], "UTF-8");

    if ($data["content"] === "") {
        $errors["content"] = "Vui lòng nhập nội dung.";
    } elseif ($contentLength < 10 || $contentLength > 500) {
        $errors["content"] =
            "Nội dung phải từ 10 đến 500 ký tự.";
    }

    // 7. Kiểm tra upload ảnh đại diện (không bắt buộc)
    $hasImage = isset($_FILES["avatar"]) &&
        $_FILES["avatar"]["error"] !== UPLOAD_ERR_NO_FILE;

    if ($hasImage) {
        $file = $_FILES["avatar"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $errors["avatar"] = "Upload ảnh thất bại.";
        } elseif ($file["size"] > 2 * 1024 * 1024) {
            $errors["avatar"] = "Ảnh tối đa 2 MB.";
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file["tmp_name"]);

            $allowed = [
                "image/jpeg" => "jpg",
                "image/png" => "png",
                "image/webp" => "webp"
            ];

            if (!isset($allowed[$mime])) {
                $errors["avatar"] =
                    "Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.";
            }
        }
    }

    // 8. Chỉ chấp nhận khi mọi kiểm tra đều hợp lệ
    if (empty($errors)) {
        $success = true;

        // Đây là demo: chưa gửi email hoặc lưu database.
        // Ảnh cũng chưa được lưu ở ví dụ này.
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form liên hệ an toàn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5f8;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        h2 { color: #205b91; text-align: center; }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #bbb;
            border-radius: 5px;
        }

        textarea { min-height: 120px; }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            border: none;
            border-radius: 5px;
            color: white;
            background: #2475b9;
            cursor: pointer;
        }

        .error { color: #c62828; margin-top: 5px; }
        .success {
            background: #e8f5e9;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>FORM LIÊN HỆ AN TOÀN</h2>

    <?php if (isset($errors["form"])): ?>
        <p class="error"><?= e($errors["form"]) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($_SESSION["csrf_token"]) ?>"
        >

        <label for="name">Họ tên</label>
        <input id="name" name="name" maxlength="100"
               value="<?= e($data["name"]) ?>">
        <?php if (isset($errors["name"])): ?>
            <div class="error"><?= e($errors["name"]) ?></div>
        <?php endif; ?>

        <label for="email">Email</label>
        <input id="email" name="email"
               value="<?= e($data["email"]) ?>">
        <?php if (isset($errors["email"])): ?>
            <div class="error"><?= e($errors["email"]) ?></div>
        <?php endif; ?>

        <label for="subject">Chủ đề</label>
        <select id="subject" name="subject">
            <?php foreach ($subjects as $key => $label): ?>
                <option value="<?= e($key) ?>"
                    <?= $data["subject"] === $key ? "selected" : "" ?>>
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors["subject"])): ?>
            <div class="error"><?= e($errors["subject"]) ?></div>
        <?php endif; ?>

        <label for="content">Nội dung (10–500 ký tự)</label>
        <textarea id="content" name="content" maxlength="500"><?= e($data["content"]) ?></textarea>
        <?php if (isset($errors["content"])): ?>
            <div class="error"><?= e($errors["content"]) ?></div>
        <?php endif; ?>

        <label for="avatar">Ảnh đại diện (không bắt buộc, tối đa 2 MB)</label>
        <input type="file" id="avatar" name="avatar"
               accept="image/jpeg,image/png,image/webp">
        <?php if (isset($errors["avatar"])): ?>
            <div class="error"><?= e($errors["avatar"]) ?></div>
        <?php endif; ?>

        <button type="submit">Gửi liên hệ</button>
    </form>

    <?php if ($success): ?>
        <div class="success">
            <h3>Thông tin hợp lệ!</h3>
            <p>Họ tên: <?= e($data["name"]) ?></p>
            <p>Email: <?= e($data["email"]) ?></p>
            <p>Chủ đề: <?= e($subjects[$data["subject"]]) ?></p>
            <p>Nội dung: <?= e($data["content"]) ?></p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
