
<?php
// Dữ liệu đã được Controller lấy và truyền sang giao diện.

// Hàm an toàn khi hiển thị dữ liệu
function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Định dạng ngày giờ
function dinhDangNgayGio($value)
{
    if (empty($value)) {
        return 'Chưa cập nhật';
    }

    $timestamp = strtotime($value);

    return $timestamp !== false
        ? date('d/m/Y H:i', $timestamp)
        : 'Chưa cập nhật';
}

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

        /* Tương thích với những CSS đang sử dụng biến cũ */
        --primary: var(--green);
        --primary-dark: var(--green-dark);
        --background: var(--cream);
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

    /* ==================== THANH MENU ==================== */

    .navbar {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--white);
        border-bottom: 1px solid var(--border);
    }

    .nav-inner {
        width: 100%;
        min-height: 60px;
        padding: 10px 3%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
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

    .brand-icon {
        font-size: 17px;
    }

    .nav-links {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 24px;
        flex-wrap: wrap;
    }

    .nav-links a {
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        transition: color .2s;
    }

    .nav-links a:hover,
    .nav-links a.active {
        color: var(--green);
    }

    /* ==================== NỘI DUNG TRANG ==================== */

    .container {
        width: min(1200px, calc(100% - 40px));
        margin: 28px auto;
    }

    .page-heading {
        margin-bottom: 20px;
    }

    .breadcrumb {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 10px;
    }

    .breadcrumb a {
        color: var(--green);
    }

    h1 {
        margin: 0 0 8px;
        font-size: clamp(24px, 4vw, 30px);
        line-height: 1.3;
        color: var(--green-dark);
    }

    .subtitle {
        color: var(--muted);
        margin: 0;
    }

    /* Thẻ tổng số lượt đăng ký */

    .summary {
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 12px rgba(30, 55, 40, .04);
    }

    .summary-icon {
        width: 46px;
        height: 46px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--green-light);
        font-size: 23px;
        flex-shrink: 0;
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

    /* ==================== DANH SÁCH ĐĂNG KÝ ==================== */

    .table-card {
        background: transparent;
        border: 0;
        border-radius: 10px;
        overflow: visible;
        box-shadow: none;
    }

    .table-heading {
        padding: 0 0 14px;
        border: 0;
    }

    .table-heading h2 {
        font-size: 18px;
        margin: 0 0 4px;
        color: var(--green-dark);
    }

    .table-heading p {
        color: var(--muted);
        font-size: 13px;
        margin: 0;
    }

    /* Ô tìm kiếm */

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
        color: var(--text);
        font-size: 14px;
        outline-color: var(--green);
    }

    /* Hai tab trạng thái */

    .registration-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 14px;
        border-bottom: 1px solid var(--border);
    }

    .tab-btn {
        min-width: 150px;
        padding: 11px 20px;
        border: 0;
        border-radius: 7px 7px 0 0;
        background: transparent;
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s, color .2s;
    }

    .tab-btn:hover {
        background: var(--green-light);
        color: var(--green);
    }

    .tab-btn.active {
        background: var(--green);
        color: var(--white);
    }

    /* Bảng dữ liệu */

    .table-wrap {
        width: 100%;
        overflow-x: auto;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(30, 55, 40, .04);
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
        text-transform: none;
        letter-spacing: 0;
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
        color: var(--text);
    }

    .club-name {
        min-width: 150px;
    }

    .muted {
        color: var(--muted);
    }

    /* ==================== NHÃN TRẠNG THÁI ==================== */

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

    /* ==================== CÁC NÚT ==================== */

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
        transition: background .2s, transform .2s;
        white-space: nowrap;
    }

    .btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .btn-primary {
        padding: 11px 18px;
        color: var(--white);
        background: var(--green);
        font-size: 13px;
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--green-dark);
    }

    .btn-cancel {
        color: #fff;
        background: var(--red);
    }

    .btn-cancel:hover:not(:disabled) {
        background: #b92e2e;
        transform: translateY(-1px);
    }

    .btn-delete {
        color: #fff;
        background: #64748b;
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

    /* ==================== TRẠNG THÁI TRỐNG ==================== */

    .empty {
        text-align: center;
        padding: 50px 20px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
    }

    .empty-icon {
        font-size: 46px;
        margin-bottom: 12px;
    }

    .empty h3 {
        margin: 0 0 8px;
        font-size: 20px;
        color: var(--green-dark);
    }

    .empty p {
        margin: 0 0 22px;
        color: var(--muted);
    }

    /* ==================== THÔNG BÁO ==================== */

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

    /* ==================== HỘP THOẠI XÁC NHẬN ==================== */

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
        font-size: 19px;
        color: var(--green-dark);
    }

    .modal-box p {
        color: var(--muted);
        font-size: 14px;
        margin-bottom: 22px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* ==================== CHÂN TRANG ==================== */

    .footer {
        text-align: center;
        color: var(--muted);
        font-size: 12px;
        padding: 25px 16px;
    }

    /* ==================== ĐIỆN THOẠI ==================== */

    @media (max-width: 650px) {
        .nav-inner {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
            padding: 14px 16px;
        }

        .nav-links {
            justify-content: flex-start;
            gap: 14px;
        }

        .container {
            width: calc(100% - 24px);
            margin-top: 22px;
        }

        .summary {
            padding: 14px;
        }

        .registration-tabs {
            width: 100%;
        }

        .tab-btn {
            flex: 1;
            min-width: 0;
            padding: 10px 8px;
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
        <a class="brand" href="/Nhom-4-LTW/public/index.php">
            <span class="brand-icon">▣</span>
            Sự kiện sinh viên
        </a>

        <nav class="nav-links">
            <a href="/Nhom-4-LTW/public/index.php">
                Trang chủ
            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=su-kien">
                Sự kiện
            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=cau-lac-bo">
                Câu lạc bộ
            </a>

            <a href="/Nhom-4-LTW/public/index.php?trang=dang-ky-cua-toi"
               class="active">
                Đăng ký của tôi
            </a>
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
            value="<?= e($tuKhoa ?? '') ?>"
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

                <a class="btn btn-primary" href="../index.php?trang=su-kien">
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
                        $trangThai = $dk['trang_thai_dang_ky'];

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
                        ?>

                        <tr id="row-<?= (int) $dk['dang_ky_id'] ?>">

                            <td class="event-name">
                                <?= e($dk['ten_su_kien']) ?>
                            </td>

                            <td class="club-name">
                                <?= e($dk['ten_cau_lac_bo']) ?>
                            </td>

                            <td><?= e($dk['dia_diem']) ?></td>

                            <td>
                                <?= e(dinhDangNgayGio($dk['thoi_gian_bat_dau'])) ?>
                            </td>

                            <td>
                                <?= e(dinhDangNgayGio($dk['thoi_gian_dang_ky'])) ?>
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
                                        data-id="<?= (int) $dk['dang_ky_id'] ?>"
                                        data-ten="<?= e($dk['ten_su_kien']) ?>">
                                        Hủy đăng ký
                                    </button>

                                
<?php elseif ($trangThai === 'da_huy'): ?>

    <button
        type="button"
        class="btn btn-delete nut-xoa"
        data-id="<?= (int) $dk['dang_ky_id'] ?>"
        data-ten="<?= e($dk['ten_su_kien']) ?>">
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
        <a href="../index.php?trang=su-kien" style="color: #2878d0; font-weight: 700;">
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

    // Hiển thị thông báo
    function hienThiThongBao(noiDung, loai) {
        const el = document.getElementById('thongBao');

        el.textContent = noiDung;
        el.className = 'message ' + loai;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Mở hộp thoại xác nhận
    function moModal(id, tenSuKien, button) {
        dangKyIdCanHuy = id;
        nutDangHuy = button;

        modalText.textContent =
            'Bạn có chắc chắn muốn hủy đăng ký sự kiện "' +
            tenSuKien + '" không?';

        modal.classList.add('show');
    }

    // Đóng hộp thoại
    function dongModal() {
        modal.classList.remove('show');
        dangKyIdCanHuy = null;
        nutDangHuy = null;
    }

    btnDong.addEventListener('click', dongModal);

    // Gắn sự kiện cho các nút hủy
    document.querySelectorAll('.nut-huy').forEach(button => {
        button.addEventListener('click', function () {
            moModal(
                this.dataset.id,
                this.dataset.ten,
                this
            );
        });
    });

    // Xác nhận hủy đăng ký
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

            const response = await fetch('/Nhom-4-LTW/public/api/dang_ky_tham_gia.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: body.toString(),
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (!response.ok || data.status !== 'success') {
                throw new Error(
                    data.message || 'Không thể hủy đăng ký.'
                );
            }

            hienThiThongBao(
                data.message || 'Hủy đăng ký thành công.',
                'success'
            );

            // Cập nhật trạng thái ngay trên giao diện
            row.querySelector('.td-trang-thai').innerHTML =
                '<span class="status da-huy">Đã hủy</span>';

            row.querySelector('.td-hanh-dong').innerHTML =
                '<span class="muted">—</span>';

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
    document.querySelectorAll('.nut-xoa').forEach(button => {
        button.addEventListener('click', async function () {
            const id = this.dataset.id;
            const tenSuKien = this.dataset.ten;
            const row = document.getElementById('row-' + id);
            const buttonXoa = this;

            if (!confirm(
                'Bạn có chắc chắn muốn xóa đăng ký đã hủy của sự kiện "' +
                tenSuKien + '" không? Thao tác này không thể hoàn tác.'
            )) {
                return;
            }

            buttonXoa.disabled = true;
            buttonXoa.textContent = 'Đang xóa...';

            try {
                const body = new URLSearchParams();
                body.append('action', 'xoa_dang_ky');
                body.append('dang_ky_id', id);

                const response = await fetch(
                    '/Nhom-4-LTW/public/api/dang_ky_tham_gia.php',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type':
                                'application/x-www-form-urlencoded'
                        },
                        body: body.toString(),
                        credentials: 'same-origin'
                    }
                );

                const data = await response.json();

                if (!response.ok || data.status !== 'success') {
                    throw new Error(
                        data.message || 'Không thể xóa đăng ký.'
                    );
                }

                row.remove();

                const tongSo = document.querySelector('.summary-number');
                const soHienTai = Number(tongSo.textContent);

                tongSo.textContent = Math.max(0, soHienTai - 1);

                hienThiThongBao(
                    data.message || 'Đã xóa đăng ký.',
                    'success'
                );

                // Nếu không còn hàng nào, tải lại để hiện giao diện trống.
                const tbody = document.querySelector('tbody');

                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                    window.location.reload();
                }

            } catch (error) {
                hienThiThongBao(
                    error.message || 'Không thể kết nối máy chủ.',
                    'error'
                );

                buttonXoa.disabled = false;
                buttonXoa.textContent = 'Xóa đăng ký';
            }
        });
    });

    // Đóng hộp thoại khi bấm ra ngoài
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            dongModal();
        }
    });

    // Đóng hộp thoại bằng phím Escape
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            dongModal();
        }
    });
</script>

</body>
</html>
