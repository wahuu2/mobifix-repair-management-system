<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Protect Admin Page
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: ../admin/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Service ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$serviceID = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Delete Service
|--------------------------------------------------------------------------
*/

$sql = "
    DELETE FROM repair_services
    WHERE ServiceID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $serviceID
);

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Return to Services
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;