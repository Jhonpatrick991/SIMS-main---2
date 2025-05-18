<?php
require("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $SectionName = $_GET['id'];

    // Set SectionName to NULL for students in this section
    $stmtStudents = $con->prepare("UPDATE students SET SectionName = NULL WHERE SectionName = ?");
    $stmtStudents->bind_param("s", $SectionName);
    $stmtStudents->execute();
    $stmtStudents->close();

    // Delete the section
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