
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';
require_once __DIR__ . '/../../app/Repositories/DangKyRepository.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['nguoi_dung']['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bạn cần đăng nhập để thực hiện.'
    ]);
    exit;
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$action = $_POST['action'] ?? 'dang_ky';
$suKienId = (int) ($_POST['su_kien_id'] ?? 0);

$repository = new DangKyRepository($pdo);

// Xử lý đăng ký sự kiện
if ($action === 'dang_ky') {
    if ($suKienId <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sự kiện không hợp lệ.'
        ]);
        exit;
    }

    $suKien = $repository->layThongTinSuKien($suKienId);

    if (!$suKien) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sự kiện không tồn tại.'
        ]);
        exit;
    }

    $now = date('Y-m-d H:i:s');

    if ($suKien['trang_thai'] !== 'cong_khai') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sự kiện chưa mở hoặc đã đóng đăng ký.'
        ]);
        exit;
    }

    if ($now >= $suKien['thoi_gian_bat_dau']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sự kiện đã bắt đầu, không thể đăng ký.'
        ]);
        exit;
    }

    $soLuongHienTai = $repository->demSoLuongDangKy($suKienId);

    if ($soLuongHienTai >= $suKien['so_luong_toi_da']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sự kiện đã hết chỗ.'
        ]);
        exit;
    }

    if ($repository->daDangKySuKien($nguoiDungId, $suKienId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Bạn đã đăng ký sự kiện này rồi.'
        ]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $daHuy = $repository->layDangKyDaHuy(
            $nguoiDungId,
            $suKienId
        );

        if ($daHuy) {
            $thanhCong = $repository->capNhatTrangThai(
                (int) $daHuy['id'],
                $nguoiDungId,
                'da_dang_ky'
            );
        } else {
            $thanhCong = $repository->themDangKy(
                $nguoiDungId,
                $suKienId
            );
        }

        if (!$thanhCong) {
            throw new Exception('Không thể thực hiện đăng ký.');
        }

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Đăng ký sự kiện thành công!',
            'so_luong_con_lai' =>
                (int) $suKien['so_luong_toi_da'] - $soLuongHienTai - 1
        ]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log('Lỗi đăng ký sự kiện: ' . $e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message' => 'Lỗi hệ thống khi đăng ký sự kiện.'
        ]);
    }
}

// Xử lý hủy đăng ký
elseif ($action === 'huy_dang_ky') {
    $dangKyId = (int) ($_POST['dang_ky_id'] ?? 0);

    if ($dangKyId <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Đăng ký không hợp lệ.'
        ]);
        exit;
    }

    $dangKy = $repository->layMotDangKy(
        $dangKyId,
        $nguoiDungId
    );

    if (!$dangKy) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy đăng ký hoặc bạn không có quyền hủy.'
        ]);
        exit;
    }

    if ($dangKy['trang_thai'] !== 'da_dang_ky') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Đăng ký này không thể hủy (đã hủy hoặc đã tham gia).'
        ]);
        exit;
    }

    // Lấy thời gian bắt đầu sự kiện
    $suKien = $repository->layThongTinSuKien(
        (int) $dangKy['su_kien_id']
    );

    if (!$suKien) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy sự kiện.'
        ]);
        exit;
    }

    $soGiayConLai =
        strtotime($suKien['thoi_gian_bat_dau']) - time();

    if ($soGiayConLai < 86400) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Đã quá hạn hủy đăng ký (phải hủy trước 24 giờ so với thời gian bắt đầu).'
        ]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $thanhCong = $repository->capNhatTrangThai(
            $dangKyId,
            $nguoiDungId,
            'da_huy'
        );

        if (!$thanhCong) {
            throw new Exception('Không thể hủy đăng ký.');
        }

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Hủy đăng ký thành công!'
        ]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log('Lỗi hủy đăng ký: ' . $e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message' => 'Lỗi hệ thống khi hủy đăng ký.'
        ]);
    }
}

// Xử lý xóa đăng ký đã hủy
elseif ($action === 'xoa_dang_ky') {
    $dangKyId = (int) ($_POST['dang_ky_id'] ?? 0);

    if ($dangKyId <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Mã đăng ký không hợp lệ.'
        ]);
        exit;
    }

    try {
        $daXoa = $repository->xoaDangKyDaHuy(
            $dangKyId,
            $nguoiDungId
        );

        if ($daXoa) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Đã xóa đăng ký.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Không tìm thấy đăng ký hoặc đăng ký chưa được hủy.'
            ]);
        }
    } catch (Throwable $e) {
        error_log('Lỗi xóa đăng ký: ' . $e->getMessage());

        http_response_code(500);

        echo json_encode([
            'status' => 'error',
            'message' => 'Không thể xóa đăng ký lúc này.'
        ]);
    }
}

// Hành động không hợp lệ
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Hành động không hợp lệ.'
    ]);
}
