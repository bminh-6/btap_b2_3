
<?php
// 1. Tạo mảng gồm 3 sinh viên
$students = [
    [
        'name' => 'An',
        'midterm' => 8,
        'final' => 9
    ],
    [
        'name' => 'Binh',
        'midterm' => 6,
        'final' => 7
    ],
    [
        'name' => 'Chi',
        'midterm' => 4,
        'final' => 3
    ]
];

// 2. Hàm tính điểm trung bình
function calculateAverage(float $midterm, float $final): float {
    return ($midterm + $final) / 2;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điểm sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        table {
            border-collapse: collapse;
            width: 700px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #a16330;
            color: white;
        }
    </style>
</head>
<body>

<h2>BẢNG ĐIỂM 3 SINH VIÊN</h2>

<table>
    <tr>
        <th>STT</th>
        <th>Họ tên</th>
        <th>Điểm giữa kỳ</th>
        <th>Điểm cuối kỳ</th>
        <th>Điểm trung bình</th>
        <th>Kết quả</th>
    </tr>

    <?php foreach ($students as $index => $student): ?>
        <?php
            // Tính điểm trung bình
            $average = calculateAverage(
                $student['midterm'],
                $student['final']
            );

            // 4. Xét kết quả
            $result = $average >= 5 ? "Đạt" : "Chưa đạt";
        ?>

        <tr>
            <td><?= $index + 1 ?></td>

            <td>
                <?= htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?>
            </td>

            <td><?= $student['midterm'] ?></td>
            <td><?= $student['final'] ?></td>
            <td><?= number_format($average, 2) ?></td>
            <td><?= $result ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
