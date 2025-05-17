<?php
require("../connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $SubjectId = $_POST["SubjectId"];
    $oldSubjectCode = $_POST["oldSubjectCode"]; // hidden input in your form
    $newSubjectCode = $_POST["SubjectCode"];
    $unit = $_POST["Unit"];
    $subjectName = $_POST["SubjectName"];
    $time = $_POST["Time"];

    // Update the subject in the subjects table
    $sql = "UPDATE subjects SET SubjectCode = ?, Unit = ?, SubjectName = ?, Time = ? WHERE SubjectId = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sissi", $newSubjectCode, $unit, $subjectName, $time, $SubjectId);
    $stmt->execute();
    $stmt->close();

    // Update SubjectCode for all students linked to this subject
    $sql2 = "UPDATE students SET SubjectCode = ? WHERE SubjectCode = ?";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("ss", $newSubjectCode, $oldSubjectCode);
    $stmt2->execute();
    $stmt2->close();

    $sql1 = "UPDATE grades SET SubjectCode = ? WHERE SubjectCode = ?";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("ss", $newSubjectCode, $oldSubjectCode);
    $stmt1->execute();
    $stmt1->close();

    header("Location: ../Menu/subjects.php?");
    exit;
}
?>