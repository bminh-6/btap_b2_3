
<?php
$students = [
    ['name' => 'An',   'score' => 8.0],
    ['name' => 'Binh', 'score' => 6.5],
    ['name' => 'Chi',  'score' => 4.5]
];

foreach ($students as $student) {
    echo 'Tên: ' . $student['name'];
    echo ' - Điểm: ' . $student['score'];
    echo '<br>';
}
?>
