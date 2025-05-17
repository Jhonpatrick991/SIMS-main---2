<?php
require("../connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentNumber = $_POST["StudentNumber"];
    $name = $_POST["StudentName"];
    $SectionName = $_POST["SectionName"];
    $SubjectCode = $_POST["SubjectCode"];
    $email = $_POST["Email"];

    $sql = "UPDATE students SET StudentName = ?, SectionName = ?, Email = ?, SubjectCode = ? WHERE StudentNumber = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssss", $name, $SectionName, $email, $SubjectCode, $studentNumber);

    if ($stmt->execute()) {
        header("Location: ../Menu/students.php");
    } else {
        echo "Error updating student.";
    }
}
?>
