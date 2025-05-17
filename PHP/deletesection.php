<?php
require("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $SectionName = $_GET['id'];

    $stmt = $con->prepare("DELETE FROM sections WHERE SectionName = ?");
    $stmt->bind_param("s", $SectionName);

    if ($stmt->execute()) {
        header("Location: ../Menu/sections.php?deleted=1");
        exit;
    } else {
        echo "Error deleting section." . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>