
<?php
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
        $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Nhận và giữ lại dữ liệu đã nhập
    $data["name"] = trim($_POST["name"] ?? "");
    $data["email"] = trim($_POST["email"] ?? "");
    $data["subject"] = $_POST["subject"] ?? "";
    $data["content"] = trim($_POST["content"] ?? "");

    // 2. Kiểm tra họ tên
    if ($data["name"] === "") {
        $errors["name"] = "Vui lòng nhập họ tên.";
    }

    // 3. Kiểm tra email
    if ($data["email"] === "") {
        $errors["email"] = "Vui lòng nhập email.";
    } elseif (!filter_var(
        $data["email"],
        FILTER_VALIDATE_EMAIL
    )) {
        $errors["email"] = "Email không đúng định dạng.";
    }

    // 4. Kiểm tra chủ đề
    if (!array_key_exists($data["subject"], $subjects)) {
        $errors["subject"] = "Vui lòng chọn chủ đề hợp lệ.";
    }

    // 5. Kiểm tra nội dung
    if ($data["content"] === "") {
        $errors["content"] = "Vui lòng nhập nội dung.";
    } elseif (
        mb_strlen($data["content"], "UTF-8") < 10 ||
        mb_strlen($data["content"], "UTF-8") > 500
    ) {
        $errors["content"] =
            "Nội dung phải từ 10 đến 500 ký tự.";
    }

    // 6. Kiểm tra ảnh đại diện (không bắt buộc)
    $hasImage = isset($_FILES["avatar"]) &&
                $_FILES["avatar"]["error"] !== UPLOAD_ERR_NO_FILE;

    if ($hasImage) {
        if ($_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {
            $errors["avatar"] = "Upload ảnh thất bại.";
        } elseif ($_FILES["avatar"]["size"] > 2 * 1024 * 1024) {
            $errors["avatar"] = "Ảnh không được vượt quá 2 MB.";
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES["avatar"]["tmp_name"]);

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

    // 7. Chỉ xử lý thành công nếu không có lỗi
    if (empty($errors)) {
        // Demo: chưa gửi email hay lưu vào database
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form liên hệ</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 560px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 12px #0001;
        }

        h2 {
            text-align: center;
            color: #205b91;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #2475b9;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .error {
            color: #d21f32;
            font-size: 14px;
            margin-top: 5px;
        }

        .success {
            background: #e6f7e9;
            color: #17652b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Liên hệ</h2>
    <p>Vui lòng nhập đầy đủ thông tin bên dưới.</p>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Họ tên</label>
        <input
            id="name"
            name="name"
            value="<?= e($data["name"]) ?>"
        >
        <?php if (isset($errors["name"])): ?>
            <div class="error"><?= e($errors["name"]) ?></div>
        <?php endif; ?>

        <label for="email">Email</label>
        <input
            id="email"
            name="email"
            value="<?= e($data["email"]) ?>"
        >
        <?php if (isset($errors["email"])): ?>
            <div class="error"><?= e($errors["email"]) ?></div>
        <?php endif; ?>

        <label for="subject">Chủ đề</label>
        <select id="subject" name="subject">
            <?php foreach ($subjects as $key => $label): ?>
                <option
                    value="<?= e($key) ?>"
                    <?= $data["subject"] === $key ? "selected" : "" ?>
                >
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors["subject"])): ?>
            <div class="error"><?= e($errors["subject"]) ?></div>
        <?php endif; ?>

        <label for="content">Nội dung</label>
        <textarea id="content" name="content"><?= e($data["content"]) ?></textarea>
        <?php if (isset($errors["content"])): ?>
            <div class="error"><?= e($errors["content"]) ?></div>
        <?php endif; ?>

        <label for="avatar">Ảnh đại diện (không bắt buộc)</label>
        <input
            type="file"
            id="avatar"
            name="avatar"
            accept="image/jpeg,image/png,image/webp"
        >
        <?php if (isset($errors["avatar"])): ?>
            <div class="error"><?= e($errors["avatar"]) ?></div>
        <?php endif; ?>

        <button type="submit">Gửi liên hệ</button>
    </form>

    <?php if ($success): ?>
        <div class="success">
            <h3>Gửi liên hệ thành công!</h3>
            <p>Họ tên: <?= e($data["name"]) ?></p>
            <p>Email: <?= e($data["email"]) ?></p>
            <p>Chủ đề: <?= e($subjects[$data["subject"]]) ?></p>
            <p>Nội dung: <?= e($data["content"]) ?></p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
