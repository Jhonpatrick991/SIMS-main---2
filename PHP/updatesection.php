<?php
require("../connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldSectionName = $_POST["oldSectionName"]; // hidden input in your form
    $newSectionName = $_POST["sectionName"];

    // Update section name in sections table
    $sql = "UPDATE sections SET SectionName = ? WHERE SectionName = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $newSectionName, $oldSectionName);
    $stmt->execute();
    $stmt->close();

    // Update section name for all students enrolled in this section
    $sql2 = "UPDATE students SET SectionName = ? WHERE SectionName = ?";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("ss", $newSectionName, $oldSectionName);
    $stmt2->execute();
    $stmt2->close();

    header("Location: ../Menu/sections.php");
    exit;
}
?>