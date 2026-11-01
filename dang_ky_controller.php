
<?php

require_once __DIR__ . '/../Repositories/DangKyRepository.php';

class DangKyController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function danhSachCuaNguoiDung(): void
    {
        if (!isset($_SESSION['nguoi_dung']['id'])) {
            header('Location: index.php?trang=trang-chu');
            exit;
        }

        $nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
        $tuKhoa = trim($_GET['tu_khoa'] ?? '');

        $repositoryDangKy = new DangKyRepository($this->pdo);

// Lấy danh sách đăng ký của người dùng
$danhSachDangKy = $repositoryDangKy->layDanhSachCuaNguoiDung(
    $nguoiDungId,
    $tuKhoa
);

        
$thongBaoNhacNho = '';
$loaiThongBao = '';

$now = time();
$dsSapToi = [];
$dsSapKetThuc = [];

foreach ($danhSachDangKy as $dk) {

    // Chỉ nhắc nhở đăng ký còn hiệu lực
    if ($dk['trang_thai_dang_ky'] !== 'da_dang_ky') {
        continue;
    }

    $batDau = !empty($dk['thoi_gian_bat_dau'])
        ? strtotime($dk['thoi_gian_bat_dau'])
        : false;

    $ketThuc = !empty($dk['thoi_gian_ket_thuc'])
        ? strtotime($dk['thoi_gian_ket_thuc'])
        : false;

    if ($batDau === false) {
        continue;
    }

    // Sự kiện sắp diễn ra trong 3 ngày tới
    if (
        $now < $batDau &&
        ($batDau - $now) <= 3 * 86400
    ) {
        $dsSapToi[] = $dk['ten_su_kien'];
    }

    // Sự kiện đang diễn ra và sắp kết thúc trong 24 giờ
    if (
        $ketThuc !== false &&
        $now >= $batDau &&
        $now <= $ketThuc &&
        ($ketThuc - $now) <= 86400
    ) {
        $dsSapKetThuc[] = $dk['ten_su_kien'];
    }
}

// Ưu tiên thông báo sắp kết thúc
if (!empty($dsSapKetThuc)) {

    $thongBaoNhacNho = 'Sự kiện '
        . implode(', ', $dsSapKetThuc)
        . ' sắp kết thúc trong 24 giờ tới!';

    $loaiThongBao = 'danger';

} elseif (!empty($dsSapToi)) {

    $thongBaoNhacNho = 'Sự kiện '
        . implode(', ', $dsSapToi)
        . ' sắp diễn ra trong 3 ngày tới!';

    $loaiThongBao = 'warning';

} else {

    $thongBaoNhacNho =
        'Bạn không có sự kiện nào sắp diễn ra trong thời gian tới.';

    $loaiThongBao = 'success';
}

        // Chuyển dữ liệu sang View
        require __DIR__ . '/../Views/thanh_vien/dang_ky_cua_toi.php';
    }
}
