
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nguoiDung = $_SESSION['nguoi_dung'] ?? null;

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function dinhDangNgayGio($value): string
{
    if (empty($value)) {
        return 'Chưa cập nhật';
    }

    $timestamp = strtotime($value);

    return $timestamp !== false
        ? date('d/m/Y H:i', $timestamp)
        : 'Chưa cập nhật';
}

$danhSachDangKy = $danhSachDangKy ?? [];
$tuKhoa = $tuKhoa ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sự kiện đã đăng ký</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --green: #075b49;
            --green-dark: #064638;
            --green-light: #e0f1e6;
            --cream: #f7f5ec;
            --white: #fffef9;
            --text: #263b34;
            --muted: #718078;
            --border: #e8e5d9;
            --red: #c94747;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: var(--cream);
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        /* Thanh điều hướng */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .nav-inner {
            width: 100%;
            min-height: 65px;
            padding: 10px 3%;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--green);
            font-size: 17px;
            font-weight: 800;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 18px;
            flex-wrap: wrap;
        }

        .nav-links > a {
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .nav-links > a:hover,
        .nav-links > a.active {
            color: var(--green);
        }

        .member-greeting {
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .member-logout-form {
            display: inline-flex;
            margin: 0;
        }

        /* Nút đăng xuất màu xanh */
        .member-logout {
            padding: 8px 14px;
            border: 1px solid var(--green) !important;
            border-radius: 7px;
            background: var(--green) !important;
            color: #fff !important;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
        }

        .member-logout:hover {
            background: var(--green-dark) !important;
            border-color: var(--green-dark) !important;
            color: #fff !important;
        }

        .member-register-link {
            padding: 8px 13px;
            border-radius: 7px;
            background: var(--green);
            color: #fff !important;
        }

        .member-register-link:hover {
            background: var(--green-dark);
        }

        /* Nội dung */
        .container {
            width: min(1200px, calc(100% - 40px));
            margin: 28px auto;
        }

        .page-heading {
            margin-bottom: 20px;
        }

        h1 {
            margin: 0 0 8px;
            color: var(--green-dark);
            font-size: clamp(24px, 4vw, 30px);
            line-height: 1.3;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
        }

        .summary {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .summary-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 10px;
            background: var(--green-light);
            font-size: 23px;
        }

        .summary-label {
            color: var(--muted);
            font-size: 13px;
        }

        .summary-number {
            color: var(--green);
            font-size: 23px;
            font-weight: 800;
        }

        .table-heading {
            padding-bottom: 14px;
        }

        .table-heading h2 {
            margin: 0 0 4px;
            color: var(--green-dark);
            font-size: 18px;
        }

        .table-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
        }

        .search-form input {
            flex: 1;
            min-width: 200px;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--white);
            font-size: 14px;
            outline-color: var(--green);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border: 0;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
            white-space: nowrap;
        }

        .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .btn-primary {
            padding: 11px 18px;
            background: var(--green);
            color: #fff;
            font-size: 13px;
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--green-dark);
        }

        .btn-cancel {
            background: var(--red);
            color: #fff;
        }

        .btn-cancel:hover:not(:disabled) {
            background: #b92e2e;
        }

        .btn-delete {
            background: #64748b;
            color: #fff;
        }

        .btn-delete:hover:not(:disabled) {
            background: #475569;
        }

        .btn-secondary {
            background: #eeeee5;
            color: #34483f;
        }

        .btn-secondary:hover {
            background: #e1e2d7;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        thead {
            background: #f0efe6;
        }

        th {
            padding: 13px 14px;
            color: #53645c;
            font-size: 12px;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 14px;
            border-top: 1px solid #efeee7;
            font-size: 13px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #faf9f4;
        }

        .event-name {
            min-width: 180px;
            font-weight: 700;
        }

        .club-name {
            min-width: 150px;
        }

        .muted {
            color: var(--muted);
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status.da-dang-ky {
            color: var(--green);
            background: var(--green-light);
        }

        .status.da-huy {
            color: #b63b3b;
            background: #fde8e5;
        }

        .status.da-tham-gia {
            color: #245e9c;
            background: #e1efff;
        }

        .empty {
            padding: 50px 20px;
            text-align: center;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .empty-icon {
            margin-bottom: 12px;
            font-size: 46px;
        }

        .empty h3 {
            margin: 0 0 8px;
            color: var(--green-dark);
            font-size: 20px;
        }

        .empty p {
            margin: 0 0 22px;
            color: var(--muted);
        }

        .message {
            display: none;
            padding: 13px 16px;
            margin-bottom: 18px;
            border-radius: 9px;
            font-size: 14px;
        }

        .message.success {
            display: block;
            color: #17623e;
            background: #e7f7ef;
            border: 1px solid #c3ead5;
        }

        .message.error {
            display: block;
            color: #a82f2f;
            background: #fff0f0;
            border: 1px solid #f3cccc;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            padding: 20px;
            background: rgba(21, 35, 30, .48);
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            width: min(400px, 100%);
            padding: 26px;
            border-radius: 14px;
            background: var(--white);
            box-shadow: 0 15px 50px rgba(0, 0, 0, .2);
        }

        .modal-box h3 {
            margin: 0 0 10px;
            color: var(--green-dark);
            font-size: 19px;
        }

        .modal-box p {
            margin-bottom: 22px;
            color: var(--muted);
            font-size: 14px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .footer {
            padding: 25px 16px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 750px) {
            .nav-inner {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
                padding: 14px 16px;
            }

            .nav-links {
                justify-content: flex-start;
                gap: 12px;
            }

            .container {
                width: calc(100% - 24px);
                margin-top: 22px;
            }

            .search-form input {
                min-width: 100%;
            }

            .modal-box {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<header class="navbar">
    <div class="nav-inner">
        <a class="brand"
           href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
            <span class="brand-icon">✦</span>
            Sự kiện sinh viên
        </a>

        <nav class="nav-links">
            <a href="/Nhom-4-LTW/public/index.php?trang=trang-chu">
                Trang chủ
            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=su-kien">
                Sự kiện
            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo">
                Câu lạc bộ
            </a>

            <?php if ($nguoiDung): ?>
                <a class="active"
                   href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi">
                    Đăng ký của tôi
                </a>

                <span class="member-greeting">
                    Xin chào, <?= e($nguoiDung['ho_ten'] ?? 'Thành viên') ?>
                </span>

                <form method="POST"
                      action="/Nhom-4-LTW/app/Auth/dang_xuat.php"
                      class="member-logout-form">
                    <button type="submit" class="member-logout">
                        Đăng xuất
                    </button>
                </form>
            <?php else: ?>
                <a href="/Nhom-4-LTW/public/dang_nhap.php">
                    Đăng nhập
                </a>

                <a class="member-register-link"
                   href="/Nhom-4-LTW/public/thanh_vien/dang_ky.php">
                    Đăng ký
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
    <div class="page-heading">
        <h1>Sự kiện đã đăng ký</h1>
        <p class="subtitle">
            Theo dõi các sự kiện bạn đã đăng ký và quản lý đăng ký của mình.
        </p>
    </div>

    <div id="thongBao" class="message" role="status" aria-live="polite"></div>

    <section class="summary">
        <div class="summary-icon">📅</div>
        <div>
            <div class="summary-label">Tổng số lượt đăng ký</div>
            <div class="summary-number"><?= count($danhSachDangKy) ?></div>
        </div>
    </section>

    <section class="table-card">
        <div class="table-heading">
            <h2>Danh sách đăng ký của tôi</h2>
            <p>Thông tin và trạng thái đăng ký sự kiện của bạn.</p>

            <form method="GET"
                  action="/Nhom-4-LTW/public/index.php"
                  class="search-form">

                <input type="hidden"
                       name="trang"
                       value="dang-ky-cua-toi">

                <input
                    type="text"
                    name="tu_khoa"
                    value="<?= e($tuKhoa) ?>"
                    maxlength="100"
                    placeholder="Nhập tên sự kiện cần tìm..."
                    aria-label="Tìm kiếm theo tên sự kiện">

                <button type="submit" class="btn btn-primary">
                    Tìm kiếm
                </button>

                <?php if (!empty($tuKhoa)): ?>
                    <a class="btn btn-secondary"
                       href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi">
                        Xóa tìm kiếm
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($danhSachDangKy)): ?>
            <div class="empty">
                <div class="empty-icon">📭</div>
                <h3>Bạn chưa đăng ký sự kiện nào</h3>
                <p>Khám phá các sự kiện thú vị và đăng ký tham gia ngay nhé!</p>

                <a class="btn btn-primary"
                   href="/Nhom-4-LTW/public/index.php?trang=su-kien">
                    Khám phá sự kiện
                </a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tên sự kiện</th>
                            <th>Câu lạc bộ</th>
                            <th>Địa điểm</th>
                            <th>Thời gian bắt đầu</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($danhSachDangKy as $dk): ?>
                        <?php
                        $trangThai = $dk['trang_thai_dang_ky'] ?? '';

                        $thongTinTrangThai = [
                            'da_dang_ky' => [
                                'class' => 'da-dang-ky',
                                'text' => 'Đã đăng ký'
                            ],
                            'da_huy' => [
                                'class' => 'da-huy',
                                'text' => 'Đã hủy'
                            ],
                            'da_tham_gia' => [
                                'class' => 'da-tham-gia',
                                'text' => 'Đã tham gia'
                            ]
                        ];

                        $trangThaiHienThi = $thongTinTrangThai[$trangThai]
                            ?? ['class' => '', 'text' => $trangThai];

                        $idDangKy = (int) ($dk['dang_ky_id'] ?? 0);
                        ?>

                        <tr id="row-<?= $idDangKy ?>">
                            <td class="event-name">
                                <?= e($dk['ten_su_kien'] ?? '') ?>
                            </td>

                            <td class="club-name">
                                <?= e($dk['ten_cau_lac_bo'] ?? '') ?>
                            </td>

                            <td><?= e($dk['dia_diem'] ?? '') ?></td>

                            <td>
                                <?= e(dinhDangNgayGio($dk['thoi_gian_bat_dau'] ?? null)) ?>
                            </td>

                            <td>
                                <?= e(dinhDangNgayGio($dk['thoi_gian_dang_ky'] ?? null)) ?>
                            </td>

                            <td class="td-trang-thai">
                                <span class="status <?= e($trangThaiHienThi['class']) ?>">
                                    <?= e($trangThaiHienThi['text']) ?>
                                </span>
                            </td>

                            <td class="td-hanh-dong">
                                <?php if ($trangThai === 'da_dang_ky'): ?>
                                    <button
                                        type="button"
                                        class="btn btn-cancel nut-huy"
                                        data-id="<?= $idDangKy ?>"
                                        data-ten="<?= e($dk['ten_su_kien'] ?? '') ?>">
                                        Hủy đăng ký
                                    </button>
                                <?php elseif ($trangThai === 'da_huy'): ?>
                                    <button
                                        type="button"
                                        class="btn btn-delete nut-xoa"
                                        data-id="<?= $idDangKy ?>"
                                        data-ten="<?= e($dk['ten_su_kien'] ?? '') ?>">
                                        Xóa đăng ký
                                    </button>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <p style="margin-top: 22px;">
        <a href="/Nhom-4-LTW/public/index.php?trang=su-kien"
           style="color: #075b49; font-weight: 700;">
            ← Quay lại danh sách sự kiện
        </a>
    </p>
</main>

<footer class="footer">
    Hệ thống quản lý sự kiện và câu lạc bộ sinh viên
</footer>

<!-- Hộp thoại xác nhận hủy đăng ký -->
<div id="modalXacNhan" class="modal-overlay">
    <div class="modal-box">
        <h3>Xác nhận hủy đăng ký</h3>

        <p id="modalText">
            Bạn có chắc chắn muốn hủy đăng ký sự kiện này không?
        </p>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="btnDong">
                Giữ đăng ký
            </button>

            <button type="button" class="btn btn-cancel" id="btnXacNhanHuy">
                Xác nhận hủy
            </button>
        </div>
    </div>
</div>

<script>
    let dangKyIdCanHuy = null;
    let nutDangHuy = null;

    const modal = document.getElementById('modalXacNhan');
    const modalText = document.getElementById('modalText');
    const btnDong = document.getElementById('btnDong');
    const btnXacNhanHuy = document.getElementById('btnXacNhanHuy');

    function hienThiThongBao(noiDung, loai) {
        const el = document.getElementById('thongBao');
        el.textContent = noiDung;
        el.className = 'message ' + loai;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function moModal(id, tenSuKien, button) {
        dangKyIdCanHuy = id;
        nutDangHuy = button;

        modalText.textContent =
            'Bạn có chắc chắn muốn hủy đăng ký sự kiện "' +
            tenSuKien + '" không?';

        modal.classList.add('show');
    }

    function dongModal() {
        modal.classList.remove('show');
        dangKyIdCanHuy = null;
        nutDangHuy = null;
    }

    btnDong.addEventListener('click', dongModal);

    document.querySelectorAll('.nut-huy').forEach(button => {
        button.addEventListener('click', function () {
            moModal(this.dataset.id, this.dataset.ten, this);
        });
    });

    // Hủy đăng ký
    btnXacNhanHuy.addEventListener('click', async function () {
        if (!dangKyIdCanHuy || !nutDangHuy) {
            return;
        }

        const id = dangKyIdCanHuy;
        const button = nutDangHuy;
        const row = document.getElementById('row-' + id);

        button.disabled = true;
        button.textContent = 'Đang xử lý...';
        btnXacNhanHuy.disabled = true;

        try {
            const body = new URLSearchParams();
            body.append('action', 'huy_dang_ky');
            body.append('dang_ky_id', id);

            const response = await fetch(
                '/Nhom-4-LTW/public/api/dang_ky_tham_gia.php',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body.toString(),
                    credentials: 'same-origin'
                }
            );

            const data = await response.json();

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Không thể hủy đăng ký.');
            }

            hienThiThongBao(
                data.message || 'Hủy đăng ký thành công.',
                'success'
            );

            row.querySelector('.td-trang-thai').innerHTML =
                '<span class="status da-huy">Đã hủy</span>';

            row.querySelector('.td-hanh-dong').innerHTML =
                '<button type="button" class="btn btn-delete nut-xoa" ' +
                'data-id="' + id + '" data-ten="' +
                button.dataset.ten.replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;') +
                '">Xóa đăng ký</button>';

            ganSuKienXoa(row.querySelector('.nut-xoa'));

        } catch (error) {
            hienThiThongBao(
                error.message || 'Không thể kết nối máy chủ.',
                'error'
            );

            button.disabled = false;
            button.textContent = 'Hủy đăng ký';
        } finally {
            dongModal();
            btnXacNhanHuy.disabled = false;
        }
    });

    // Xóa đăng ký đã hủy
    async function xuLyXoa(button) {
        const id = button.dataset.id;
        const tenSuKien = button.dataset.ten;
        const row = document.getElementById('row-' + id);

        if (!confirm(
            'Bạn có chắc chắn muốn xóa đăng ký đã hủy của sự kiện "' +
            tenSuKien + '" không? Thao tác này không thể hoàn tác.'
        )) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Đang xóa...';

        try {
            const body = new URLSearchParams();
            body.append('action', 'xoa_dang_ky');
            body.append('dang_ky_id', id);

            const response = await fetch(
                '/Nhom-4-LTW/public/api/dang_ky_tham_gia.php',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body.toString(),
                    credentials: 'same-origin'
                }
            );

            const data = await response.json();

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Không thể xóa đăng ký.');
            }

            row.remove();

            const tongSo = document.querySelector('.summary-number');
            tongSo.textContent = Math.max(
                0,
                Number(tongSo.textContent) - 1
            );

            hienThiThongBao(data.message || 'Đã xóa đăng ký.', 'success');

            const tbody = document.querySelector('tbody');

            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                window.location.reload();
            }

        } catch (error) {
            hienThiThongBao(
                error.message || 'Không thể kết nối máy chủ.',
                'error'
            );

            button.disabled = false;
            button.textContent = 'Xóa đăng ký';
        }
    }

    function ganSuKienXoa(button) {
        if (!button) {
            return;
        }

        button.addEventListener('click', function () {
            xuLyXoa(this);
        });
    }

    document.querySelectorAll('.nut-xoa').forEach(ganSuKienXoa);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            dongModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            dongModal();
        }
    });
</script>

</body>
</html>
