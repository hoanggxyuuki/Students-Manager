<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT MaSV, HoTen, CONVERT(varchar, NgaySinh, 23) as NgaySinh, DiaChi, MaKhoa, MaNganh, MaKhoaHoc
            FROM SinhVien
            WHERE MaSV = ?";
    $params = array($id);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false || sqlsrv_has_rows($stmt) == 0) {
        header('Location: index.php');
        exit();
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $name = $row['HoTen'];
    $dob = $row['NgaySinh'];
    $address = $row['DiaChi'];
    $faculty = $row['MaKhoa'];
    $major = $row['MaNganh'];
    $course = $row['MaKhoaHoc'];
} else {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $faculty = $_POST['faculty'];
    $major = $_POST['major'];
    $course = $_POST['course'];

    $sql = "UPDATE SinhVien
            SET HoTen = ?, NgaySinh = ?, DiaChi = ?, MaKhoa = ?, MaNganh = ?, MaKhoaHoc = ?
            WHERE MaSV = ?";
    $params = array($name, $dob, $address, $faculty, $major, $course, $id);

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
    <title>Update Student</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Update Student</h1>
        <form method="POST" action="update_student.php?id=<?php echo $id; ?>">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $name; ?>" required>

            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?php echo $dob; ?>" required>

            <label>Address</label>
            <input type="text" name="address" value="<?php echo $address; ?>" required>

            <label>Faculty</label>
            <select name="faculty" required>
                <?php
                while ($row = sqlsrv_fetch_array($result_faculty, SQLSRV_FETCH_ASSOC)) {
                    $selected = ($row['MaKhoa'] == $faculty) ? 'selected' : '';
                    echo "<option value='{$row['MaKhoa']}' $selected>{$row['TenKhoa']}</option>";
                }
                ?>
            </select>

            <label>Major</label>
            <select name="major" required>
                <?php
                while ($row = sqlsrv_fetch_array($result_major, SQLSRV_FETCH_ASSOC)) {
                    $selected = ($row['MaNganh'] == $major) ? 'selected' : '';
                    echo "<option value='{$row['MaNganh']}' $selected>{$row['TenNganh']}</option>";
                }
                ?>
            </select>

            <label>Course</label>
            <select name="course" required>
                <?php
                while ($row = sqlsrv_fetch_array($result_course, SQLSRV_FETCH_ASSOC)) {
                    $selected = ($row['MaKhoaHoc'] == $course) ? 'selected' : '';
                    echo "<option value='{$row['MaKhoaHoc']}' $selected>{$row['TenKhoaHoc']}</option>";
                }
                ?>
            </select>

            <button type="submit" class="button">Update Student</button>
        </form>
        <a href="index.php" class="button">Back to Home</a>
    </div>
</body>
</html>
