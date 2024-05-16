<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $faculty = $_POST['faculty'];
    $major = $_POST['major'];
    $course = $_POST['course'];

    $sql = "INSERT INTO SinhVien (HoTen, NgaySinh, DiaChi, MaKhoa, MaNganh, MaKhoaHoc)
            VALUES (?, ?, ?, ?, ?, ?)";
    $params = array($name, $dob, $address, $faculty, $major, $course);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error: ";
        print_r(sqlsrv_errors());
    }
}

$sql_faculty = "SELECT MaKhoa, TenKhoa FROM Khoa";
$result_faculty = sqlsrv_query($conn, $sql_faculty);

$sql_major = "SELECT MaNganh, TenNganh FROM Nganh";
$result_major = sqlsrv_query($conn, $sql_major);

$sql_course = "SELECT MaKhoaHoc, TenKhoaHoc FROM KhoaHoc";
$result_course = sqlsrv_query($conn, $sql_course);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Student</h1>
        <form method="POST" action="add_student.php">
            <label>Name</label>
            <input type="text" name="name" required>

            <label>Date of Birth</label>
            <input type="date" name="dob" required>

            <label>Address</label>
            <input type="text" name="address" required>

            <label>Faculty</label>
            <select name="faculty" required>
                <?php
                while ($row = sqlsrv_fetch_array($result_faculty, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['MaKhoa']}'>{$row['TenKhoa']}</option>";
                }
                ?>
            </select>

            <label>Major</label>
            <select name="major" required>
                <?php
                while ($row = sqlsrv_fetch_array($result_major, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['MaNganh']}'>{$row['TenNganh']}</option>";
                }
                ?>
            </select>

            <label>Course</label>
            <select name="course" required>
                <?php
                while ($row = sqlsrv_fetch_array($result_course, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['MaKhoaHoc']}'>{$row['TenKhoaHoc']}</option>";
                }
                ?>
            </select>

            <button type="submit" class="button">Add Student</button>
        </form>
        <a href="index.php" class="button">Back to Home</a>
    </div>
</body>
</html>
