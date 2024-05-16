<?php
$serverName = "DESKTOP-PLDJTOG\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "btl",
    "Uid" => "",
    "PWD" => "",
    "TrustServerCertificate" => true,
    "Encrypt" => false
);

// Tạo kết nối
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Xử lý yêu cầu tìm kiếm
$searchName = isset($_GET['name']) ? $_GET['name'] : '';
$queryResults = [];

if ($searchName !== '') {
    $sql = "SELECT * FROM Students WHERE name LIKE ?";
    $params = array("%$searchName%");
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $queryResults[] = $row;
    }
}

// Đóng kết nối
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            transition: 0.3s;
            border-radius: 5px;
            padding: 20px;
        }
        .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
        }
        .result-table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Search Student</h2>
            <form action="" method="GET" class="form-inline">
                <div class="form-group mb-2">
                    <label for="name" class="sr-only">Student Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter student name" value="<?php echo htmlspecialchars($searchName); ?>">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Search</button>
            </form>
            <?php if (count($queryResults) > 0): ?>
                <table class="table table-striped result-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Major</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queryResults as $student): ?>
                            <tr>
                                <td><?php echo $student['ID']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td><?php echo $student['age']; ?></td>
                                <td><?php echo $student['major']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No student found with that name.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
