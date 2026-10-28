
<?php
require_once __DIR__ . '/../app/Core/xac_thuc.php';

require_once __DIR__ . '/../app/Controllers/su_kien_controller.php';
require_once __DIR__ . '/../app/Controllers/cau_lac_bo_controller.php';
require_once __DIR__ . '/../app/Controllers/dang_ky_controller.php';

// Khôi phục đăng nhập từ cookie nếu người dùng đã chọn ghi nhớ.
nguoiDungHienTai();

$controller = new SuKienController($pdo);

if (!isset($_GET['trang'])) {
    header('Location: /Nhom-4-LTW/public/dang_nhap.php');
    exit;
}

$trang = $_GET['trang'];

switch ($trang) {
    case 'trang-chu':
        $controller->trangChu();
        break;

    case 'su-kien':
        $controller->danhSach();
        break;

    case 'chi-tiet':
        $controller->chiTiet();
        break;

    case 'cau-lac-bo':
        $controllerCLB = new CauLacBoThanhVienController($pdo);
        $controllerCLB->danhSach();
        break;

    case 'dang-ky-cua-toi':
        $controllerDK = new DangKyController($pdo);
        $controllerDK->danhSachCuaNguoiDung();
        break;

    default:
        $controller->trangChu();
        break;
}
