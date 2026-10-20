
<?php
$message = "";
$imagePath = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Kiểm tra 1: file có được tải lên thành công không
    if (!isset($_FILES["avatar"]) ||
        $_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {

        $message = "Upload thất bại. Vui lòng chọn ảnh và thử lại.";
    } else {
        $file = $_FILES["avatar"];

        // Kiểm tra 2: kích thước file, tối đa 2 MB
        $maxSize = 2 * 1024 * 1024;

        if ($file["size"] > $maxSize) {
            $message = "Ảnh không được vượt quá 2 MB.";
        } else {
            // Kiểm tra 3: MIME type thực tế của file
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file["tmp_name"]);

            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/gif"  => "gif",
                "image/webp" => "webp"
            ];

            if (!isset($allowedTypes[$mimeType])) {
                $message = "Chỉ chấp nhận ảnh JPG, PNG, GIF hoặc WEBP.";
            } else {
                // Tạo tên file mới, không dùng tên gốc của người dùng
                $newName = bin2hex(random_bytes(16))
                         . "." . $allowedTypes[$mimeType];

                // Lưu bên ngoài thư mục htdocs để không truy cập trực tiếp
                $uploadDir = "C:/xampp/uploads/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destination = $uploadDir . $newName;

                if (move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )) {
                    $message = "Upload ảnh thành công!";
                    $imagePath = $destination;
                } else {
                    $message = "Không thể lưu ảnh lên máy chủ.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload ảnh an toàn</title>
</head>
<body>
    <h2>UPLOAD ẢNH ĐẠI DIỆN</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Chọn ảnh đại diện:</label>
        <input
            type="file"
            name="avatar"
            accept="image/jpeg,image/png,image/gif,image/webp"
            required
        >

        <br><br>
        <button type="submit">Tải ảnh lên</button>
    </form>

    <?php if ($message !== ""): ?>
        <p>
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <?php if ($imagePath !== ""): ?>
        <p>Ảnh đã lưu tại máy chủ:</p>
        <code>
            <?= htmlspecialchars($imagePath, ENT_QUOTES, "UTF-8") ?>
        </code>
    <?php endif; ?>
</body>
</html>
