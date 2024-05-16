<?php
include 'config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM SinhVien WHERE MaSV = ?";
    $params = array($id);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error: ";
        print_r(sqlsrv_errors());
    }
} else {
    echo "Error: Missing or Invalid ID";
}
?>