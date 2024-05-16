<?php
include 'config.php';

$searchNameOrID = isset($_GET['search_name_or_id']) ? $_GET['search_name_or_id'] : '';
$searchFaculty = isset($_GET['search_faculty']) ? $_GET['search_faculty'] : '';
$searchMajor = isset($_GET['search_major']) ? $_GET['search_major'] : '';
$searchCourse = isset($_GET['search_course']) ? $_GET['search_course'] : '';
$advancedSearch = isset($_GET['advanced_search']) ? $_GET['advanced_search'] : false;

$whereConditions = [];
$params = [];

if (!empty($searchNameOrID)) {
    $whereConditions[] = "(SV.MaSV LIKE ? OR SV.HoTen LIKE ?)";
    $params[] = '%' . $searchNameOrID . '%';
    $params[] = '%' . $searchNameOrID . '%';
}

if ($advancedSearch) {
    if (!empty($searchFaculty)) {
        $whereConditions[] = "K.MaKhoa = ?";
        $params[] = $searchFaculty;
    }

    if (!empty($searchMajor)) {
        $whereConditions[] = "NG.MaNganh = ?";
        $params[] = $searchMajor;
    }

    if (!empty($searchCourse)) {
        $whereConditions[] = "KH.MaKhoaHoc = ?";
        $params[] = $searchCourse;
    }
}

$whereSql = '';
if (count($whereConditions) > 0) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereConditions);
}

$sql = "SELECT SV.MaSV, SV.HoTen, CONVERT(varchar, SV.NgaySinh, 23) as NgaySinh, SV.DiaChi, K.TenKhoa, NG.TenNganh, KH.TenKhoaHoc
        FROM SinhVien SV
        JOIN Khoa K ON SV.MaKhoa = K.MaKhoa
        JOIN Nganh NG ON SV.MaNganh = NG.MaNganh
        JOIN KhoaHoc KH ON SV.MaKhoaHoc = KH.MaKhoaHoc
        $whereSql";

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql_faculty = "SELECT MaKhoa, TenKhoa FROM Khoa";
$result_faculty = sqlsrv_query($conn, $sql_faculty);

$sql_major = "SELECT MaNganh, TenNganh FROM Nganh";
$result_major = sqlsrv_query($conn, $sql_major);

$sql_course = "SELECT MaKhoaHoc, TenKhoaHoc FROM KhoaHoc";
$result_course = sqlsrv_query($conn, $sql_course);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lí sinh viên</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .advanced-search { display: <?php echo $advancedSearch ? 'block' : 'none'; ?>; }
        .toggle-button { cursor: pointer; width: 30px; height: 24px;  color: #10B981; }
        .toggle-button svg { fill: currentColor; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    
    <div class="container mx-auto p-8 bg-white rounded-lg shadow-lg mt-10">
    
        <h1 class="text-4xl font-bold mb-6 text-center text-green-700">Quản lý Sinh Viên</h1>

        <form method="GET" action="index.php" class="flex items-center justify-between mb-6">
            <div class="flex flex-col w-2/3">
                <label for="search_name_or_id" class="text-sm font-semibold mb-2 text-gray-600">Tên hoặc Mã Sinh Viên</label>
                <input type="text" id="search_name_or_id" name="search_name_or_id" value="<?php echo $searchNameOrID; ?>" class="rounded-md border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-500 mb-2">
            </div>
            <input type="hidden" name="advanced_search" value="<?php echo $advancedSearch ? '1' : '0'; ?>">
            <button type="submit" class="ml-4 bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow-lg">Tìm Kiếm</button>
            <span class="toggle-button" onclick="toggleAdvancedSearch()">
                <svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"></path></svg>
            </span>
            <a href="add_student.php" class="ml-2 bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded-md shadow-lg">Thêm Sinh Viên Mới</a>
        </form>

        <form method="GET" action="index.php" class="advanced-search p-6 bg-gray-50 rounded-lg shadow-inner mb-6">
            <input type="hidden" name="search_name_or_id" value="<?php echo $searchNameOrID; ?>">
            <input type="hidden" name="advanced_search" value="1">
            <div class="flex flex-wrap gap-6 mb-4">
                <div class="flex flex-col w-1/3">
                    <label for="search_faculty" class="text-sm font-semibold mb-2 text-gray-600">Khoa</label>
                    <select id="search_faculty" name="search_faculty" class="rounded-md border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tất Cả</option>
                        <?php
                        while ($row = sqlsrv_fetch_array($result_faculty, SQLSRV_FETCH_ASSOC)) {
                            $selected = ($row['MaKhoa'] == $searchFaculty) ? 'selected' : '';
                            echo "<option value='{$row['MaKhoa']}' $selected>{$row['TenKhoa']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex flex-col w-1/3">
                    <label for="search_major" class="text-sm font-semibold mb-2 text-gray-600">Ngành</label>
                    <select id="search_major" name="search_major" class="rounded-md border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tất Cả</option>
                        <?php
                        while ($row = sqlsrv_fetch_array($result_major, SQLSRV_FETCH_ASSOC)) {
                            $selected = ($row['MaNganh'] == $searchMajor) ? 'selected' : '';
                            echo "<option value='{$row['MaNganh']}' $selected>{$row['TenNganh']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex flex-col w-1/3">
                    <label for="search_course" class="text-sm font-semibold mb-2 text-gray-600">Khóa</label>
                    <select id="search_course" name="search_course" class="rounded-md border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Tất Cả</option>
                        <?php
                        while ($row = sqlsrv_fetch_array($result_course, SQLSRV_FETCH_ASSOC)) {
                            $selected = ($row['MaKhoaHoc'] == $searchCourse) ? 'selected' : '';
                            echo "<option value='{$row['MaKhoaHoc']}' $selected>{$row['TenKhoaHoc']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow-lg">Tìm Kiếm Nâng Cao</button>
        </form>

        <table class="w-full table-auto border-collapse bg-white shadow-lg rounded-lg">
            <thead>
                <tr class="bg-green-700 text-white">
                    <th class="px-4 py-2 border">Mã SV</th>
                    <th class="px-4 py-2 border">Họ Tên</th>
                    <th class="px-4 py-2 border">Ngày Sinh</th>
                    <th class="px-4 py-2 border">Địa Chỉ</th>
                    <th class="px-4 py-2 border">Khoa</th>
                    <th class="px-4 py-2 border">Ngành</th>
                    <th class="px-4 py-2 border">Khóa</th>
                    <th class="px-4 py-2 border">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr class='even:bg-gray-100 odd:bg-white'>
                            <td class='border px-4 py-2'>{$row['MaSV']}</td>
                            <td class='border px-4 py-2'>{$row['HoTen']}</td>
                            <td class='border px-4 py-2'>{$row['NgaySinh']}</td>
                            <td class='border px-4 py-2'>{$row['DiaChi']}</td>
                            <td class='border px-4 py-2'>{$row['TenKhoa']}</td>
                            <td class='border px-4 py-2'>{$row['TenNganh']}</td>
                            <td class='border px-4 py-2'>{$row['TenKhoaHoc']}</td>
                            <td class='border px-4 py-2'>
                                <a href='update_student.php?id={$row['MaSV']}' class='text-green-600 hover:text-green-800'>Chỉnh Sửa</a>
                                <a href='delete_student.php?id={$row['MaSV']}' class='ml-2 text-red-600 hover:text-red-800' onclick='return confirm(\"Bạn có chắc chắn không?\")'>Xóa</a>
                            </td>
                          </tr>";
                }
                if (sqlsrv_num_rows($stmt) === false || sqlsrv_num_rows($stmt) === 0) {
                    echo "<tr><td colspan='8' class='border px-4 py-2 text-center'>Không tìm thấy sinh viên</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function toggleAdvancedSearch() {
            var form = document.querySelector('.advanced-search');
            var hiddenInput = document.querySelector('input[name="advanced_search"]');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            hiddenInput.value = form.style.display === 'block' ? '1' : '0';
        }
    </script>
</body>
</html>