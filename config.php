<?php
$serverName = "DESKTOP-PLDJTOG\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "btl",
    "Uid" => "",
    "PWD" => "",
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8",
    "Encrypt" => false
);

$conn = sqlsrv_connect($serverName, $connectionOptions);


if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
