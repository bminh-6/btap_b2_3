
<?php

class DangKy
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy danh sách đăng ký của một thành viên.
    // Có thể tìm kiếm theo tên sự kiện.
    public function layDanhSachCuaNguoiDung(
        int $nguoiDungId,
        string $tuKhoa = ''
    ): array {
        $sql = "
            SELECT
                dk.id AS dang_ky_id,
                dk.thoi_gian_dang_ky,
                dk.trang_thai AS trang_thai_dang_ky,
                sk.id AS su_kien_id,
                sk.ten_su_kien,
                sk.dia_diem,
                sk.thoi_gian_bat_dau,
                clb.ten_cau_lac_bo
            FROM dang_ky dk
            JOIN su_kien sk ON dk.su_kien_id = sk.id
            JOIN cau_lac_bo clb
                ON sk.cau_lac_bo_id = clb.id
            WHERE dk.nguoi_dung_id = :nguoi_dung_id
              AND sk.ten_su_kien LIKE :tu_khoa
            ORDER BY dk.thoi_gian_dang_ky DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nguoi_dung_id' => $nguoiDungId,
            'tu_khoa' => '%' . trim($tuKhoa) . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy thông tin một đăng ký thuộc về người dùng.
    public function layMotDangKy(int $dangKyId, int $nguoiDungId): ?array
    {
        $sql = "
            SELECT id, nguoi_dung_id, su_kien_id,
                   thoi_gian_dang_ky, trang_thai
            FROM dang_ky
            WHERE id = :dang_ky_id
              AND nguoi_dung_id = :nguoi_dung_id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Cập nhật trạng thái đăng ký.
    public function capNhatTrangThai(
        int $dangKyId,
        int $nguoiDungId,
        string $trangThai
    ): bool {
        if (!in_array($trangThai, ['da_dang_ky', 'da_huy'], true)) {
            return false;
        }

        $sql = "
            UPDATE dang_ky
            SET trang_thai = :trang_thai
            WHERE id = :dang_ky_id
              AND nguoi_dung_id = :nguoi_dung_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'trang_thai' => $trangThai,
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        return $stmt->rowCount() > 0;
    }

    // Xóa hẳn một đăng ký thuộc về người dùng.
    public function xoaDangKy(int $dangKyId, int $nguoiDungId): bool
    {
        $sql = "
            DELETE FROM dang_ky
            WHERE id = :dang_ky_id
              AND nguoi_dung_id = :nguoi_dung_id
              AND trang_thai = 'da_huy'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        return $stmt->rowCount() > 0;
    }

    // Xóa một đăng ký đã hủy của chính thành viên.
    public function xoaDangKyDaHuy(
        int $dangKyId,
        int $nguoiDungId
    ): bool {
        $sql = "
            DELETE FROM dang_ky
            WHERE id = :dang_ky_id
              AND nguoi_dung_id = :nguoi_dung_id
              AND trang_thai = 'da_huy'
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'dang_ky_id' => $dangKyId,
            'nguoi_dung_id' => $nguoiDungId
        ]);

        return $stmt->rowCount() > 0;
    }

}
