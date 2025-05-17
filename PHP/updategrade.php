<?php
require("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['StudentNumber'])) {
    $studentNumber = $_POST['StudentNumber'];
    $subjectCode = $_POST['SubjectCode'];
    $semester = $_POST['Semester'];
    $prelim = $_POST['Prelim'];
    $midterm = $_POST['Midterm'];
    $semiFinal = $_POST['SemiFinal'];
    $final = $_POST['Final'];

    // Update grade info (not StudentNumber)
    $stmt = $con->prepare("UPDATE grades SET SubjectCode=?, Semester=?, Prelim=?, Midterm=?, SemiFinal=?, Final=? WHERE StudentNumber=?");
    $stmt->bind_param("ssiiiis", $subjectCode, $semester, $prelim, $midterm, $semiFinal, $final, $studentNumber);


    if ($stmt->execute()) {
        header("Location: ../Menu/grades.php?updated=1");
        exit;
    } else {
        echo "Error updating grade: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>