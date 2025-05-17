<?php
require("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $SubjectId = $_GET['id'];

    // Get the SubjectCode for this SubjectId
    $stmt = $con->prepare("SELECT SubjectCode FROM subjects WHERE SubjectId = ?");
    $stmt->bind_param("i", $SubjectId);
    $stmt->execute();
    $stmt->bind_result($SubjectCode);
    $stmt->fetch();
    $stmt->close();

    if ($SubjectCode) {
        // Set SubjectCode to NULL for students linked to this subject
        $stmt1 = $con->prepare("UPDATE students SET SubjectCode = NULL WHERE SubjectCode = ?");
        $stmt1->bind_param("s", $SubjectCode);
        $stmt1->execute();
        $stmt1->close();

        // Delete the subject
        $stmt2 = $con->prepare("DELETE FROM subjects WHERE SubjectId = ?");
        $stmt2->bind_param("i", $SubjectId);
        if ($stmt2->execute()) {
            $stmt2->close();
            header("Location: ../Menu/subjects.php?deleted=1");
            exit;
        } else {
            echo "Error deleting subject: " . $stmt2->error;
        }
    } else {
        echo "Subject not found.";
    }
} else {
    echo "Invalid request.";
}
?>