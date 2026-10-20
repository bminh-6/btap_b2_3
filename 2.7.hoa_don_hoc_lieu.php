
<?php
$tenTaiLieu = "";
$soLuong = "";
$donGia = "";
$thongBao = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tenTaiLieu = trim($_POST["tenTaiLieu"] ?? "");
    $soLuong = trim($_POST["soLuong"] ?? "");
    $donGia = trim($_POST["donGia"] ?? "");

    if (
        $tenTaiLieu === "" ||
        $soLuong === "" || !is_numeric($soLuong) ||
        $donGia === "" || !is_numeric($donGia) ||
        $soLuong <= 0 || $donGia < 0
    ) {
        $thongBao = "Vui lòng nhập tên tài liệu, số lượng lớn hơn 0 và đơn giá hợp lệ.";
    } else {
        $thanhTien = (float)$soLuong * (float)$donGia;
        $thongBao = $tenTaiLieu . ": " .
            number_format($thanhTien, 0, ",", ".") . " đ";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tính tiền tài liệu</title>
</head>
<body>
    <form method="POST">
        <h2>HÓA ĐƠN HỌC LIỆU</h2>
        <input
            type="text"
            name="tenTaiLieu"
            placeholder="Tên tài liệu"
            required
            value="<?= htmlspecialchars($tenTaiLieu, ENT_QUOTES, 'UTF-8') ?>"
        >

        <input
            type="number"
            name="soLuong"
            placeholder="Số lượng"
            min="1"
            step="1"
            required
            value="<?= htmlspecialchars($soLuong, ENT_QUOTES, 'UTF-8') ?>"
        >

        <input
            type="number"
            name="donGia"
            placeholder="Đơn giá"
            min="0"
            step="any"
            required
            value="<?= htmlspecialchars($donGia, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit">Tính</button>
    </form>

    <?php if ($thongBao !== ""): ?>
        <p><?= htmlspecialchars($thongBao, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</body>
</html>
