<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Core/ket_noi_csdl.php';

// ============ KIỂM TRA ĐĂNG NHẬP ============
if (!isset($_SESSION['nguoi_dung']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để thực hiện.']);
    exit;
}

$nguoiDungId = (int) $_SESSION['nguoi_dung']['id'];
$action = $_POST['action'] ?? 'dang_ky';
$suKienId = (int) ($_POST['su_kien_id'] ?? 0);

// ============ XỬ LÝ ĐĂNG KÝ ============
if ($action === 'dang_ky') {
    if ($suKienId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sự kiện không hợp lệ.']);
        exit;
    }

    // Lấy thông tin sự kiện
    $stmt = $pdo->prepare("SELECT * FROM su_kien WHERE id = ?");
    $stmt->execute([$suKienId]);
    $suKien = $stmt->fetch();

    if (!$suKien) {
        echo json_encode(['status' => 'error', 'message' => 'Sự kiện không tồn tại.']);
        exit;
    }

    // --- KIỂM TRA CÁC QUY TẮC ---
    $now = date('Y-m-d H:i:s');

    // Quy tắc 1: Sự kiện phải đang 'cong_khai'
    if ($suKien['trang_thai'] !== 'cong_khai') {
        echo json_encode(['status' => 'error', 'message' => 'Sự kiện chưa mở hoặc đã đóng đăng ký.']);
        exit;
    }

    // Quy tắc 2: Phải đăng ký TRƯỚC khi sự kiện bắt đầu
    if ($now >= $suKien['thoi_gian_bat_dau']) {
        echo json_encode(['status' => 'error', 'message' => 'Sự kiện đã bắt đầu, không thể đăng ký.']);
        exit;
    }

    // Quy tắc 3: Đếm số lượng đã đăng ký
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS so_luong 
        FROM dang_ky 
        WHERE su_kien_id = ? AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
    ");
    $stmt->execute([$suKienId]);
    $ketQua = $stmt->fetch();
    $soLuongHienTai = (int) $ketQua['so_luong'];

    if ($soLuongHienTai >= $suKien['so_luong_toi_da']) {
        echo json_encode(['status' => 'error', 'message' => 'Sự kiện đã hết chỗ.']);
        exit;
    }

    // Quy tắc 4: Chưa đăng ký trùng
    $stmt = $pdo->prepare("
        SELECT id FROM dang_ky 
        WHERE nguoi_dung_id = ? AND su_kien_id = ? 
        AND trang_thai IN ('da_dang_ky', 'da_tham_gia')
    ");
    $stmt->execute([$nguoiDungId, $suKienId]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn đã đăng ký sự kiện này rồi.']);
        exit;
    }

    // --- THỰC HIỆN ĐĂNG KÝ ---
    try {
        $pdo->beginTransaction();

        // Nếu user đã từng hủy (trang_thai = 'da_huy'), cập nhật lại
        $stmt = $pdo->prepare("
            SELECT id FROM dang_ky 
            WHERE nguoi_dung_id = ? AND su_kien_id = ? AND trang_thai = 'da_huy'
        ");
        $stmt->execute([$nguoiDungId, $suKienId]);
        $daHuy = $stmt->fetch();

        if ($daHuy) {
            $stmt = $pdo->prepare("
                UPDATE dang_ky 
                SET trang_thai = 'da_dang_ky', thoi_gian_dang_ky = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$daHuy['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO dang_ky (nguoi_dung_id, su_kien_id, thoi_gian_dang_ky, trang_thai) 
                VALUES (?, ?, NOW(), 'da_dang_ky')
            ");
            $stmt->execute([$nguoiDungId, $suKienId]);
        }

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Đăng ký sự kiện thành công!',
            'so_luong_con_lai' => $suKien['so_luong_toi_da'] - $soLuongHienTai - 1
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
}
// ============ XỬ LÝ HỦY ĐĂNG KÝ ============
elseif ($action === 'huy_dang_ky') {
    $dangKyId = (int) ($_POST['dang_ky_id'] ?? 0);

    if ($dangKyId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Đăng ký không hợp lệ.']);
        exit;
    }

    // 1. Lấy thông tin đăng ký + kiểm tra quyền sở hữu
    $sql = "SELECT dk.*, sk.thoi_gian_bat_dau 
            FROM dang_ky dk 
            JOIN su_kien sk ON dk.su_kien_id = sk.id 
            WHERE dk.id = ? AND dk.nguoi_dung_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dangKyId, $nguoiDungId]);
    $dangKy = $stmt->fetch();

    if (!$dangKy) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đăng ký hoặc bạn không có quyền hủy.']);
        exit;
    }

    // 2. Kiểm tra trạng thái
    if ($dangKy['trang_thai'] !== 'da_dang_ky') {
        echo json_encode(['status' => 'error', 'message' => 'Đăng ký này không thể hủy (đã hủy hoặc đã tham gia).']);
        exit;
    }

    // 3. Kiểm tra hạn hủy (phải hủy trước 24h)
    $now = time();
    $thoiGianBatDau = strtotime($dangKy['thoi_gian_bat_dau']);
    $soGiayConLai = $thoiGianBatDau - $now;

    if ($soGiayConLai < 86400) { // 24h = 86400 giây
        echo json_encode(['status' => 'error', 'message' => 'Đã quá hạn hủy đăng ký (phải hủy trước 24 giờ so với thời gian bắt đầu).']);
        exit;
    }

    // 4. Thực hiện hủy
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
    UPDATE dang_ky
    SET trang_thai = 'da_huy'
    WHERE id = ?
      AND nguoi_dung_id = ?
      AND trang_thai = 'da_dang_ky'
");
$stmt->execute([$dangKyId, $nguoiDungId]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Hủy đăng ký thành công!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
}
// ============ HÀNH ĐỘNG KHÔNG HỢP LỆ ============

elseif ($action === 'xoa_dang_ky') {
    $dangKyId = (int) ($_POST['dang_ky_id'] ?? 0);

    if ($dangKyId <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Mã đăng ký không hợp lệ.'
        ]);
        exit;
    }

    require_once __DIR__ . '/../../app/Models/dang_ky.php';

    try {
        $modelDangKy = new DangKy($pdo);

        $daXoa = $modelDangKy->xoaDangKyDaHuy(
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
    } catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            'status' => 'error',
            'message' => 'Không thể xóa đăng ký lúc này.'
        ]);
    }
}

?>