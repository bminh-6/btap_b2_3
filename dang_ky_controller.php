<?php

require_once __DIR__ . '/../Models/dang_ky.php';

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

        $modelDangKy = new DangKy($this->pdo);

        $danhSachDangKy = $modelDangKy->layDanhSachCuaNguoiDung(
            $nguoiDungId,
            $tuKhoa
        );

        require __DIR__ . '/../Views/thanh_vien/dang_ky_cua_toi.php';
    }
}