<?php
require("../connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM grades WHERE StudentNumber = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $grade = $result->fetch_assoc();

    if (!$grade) {
        echo "Grade not found.";
        exit;
    }
} else {
    echo "No grade ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Grade</title>
    <link rel="stylesheet" href="../CSS/another.css">
    <style>
        /* You can add styles here or import your CSS */
    </style>
</head>
<body>
    <!-- <h2> <?= ($_GET);?> </h2> -->
    <h2>Edit Grade</h2>
    <form action="updategrade.php" method="POST">
        <input type="hidden" name="GradeID" value="<?= ($grade['StudentNumber']) ?>">

        <label>Student Number:</label>
        <input type="text" name="StudentNumber" value="<?= ($grade['StudentNumber']) ?>" disabled>

        <label>Subject Code:</label>
        <input type="text" name="SubjectCode" value="<?= ($grade['SubjectCode']) ?>" disabled>

        <label>Semester:</label>
        <input type="text" name="Semester" value="<?= ($grade['Semester']) ?>" required>

        <label>Prelim Exam:</label>
        <input type="text" name="Prelim" value="<?= ($grade['Prelim']) ?>"  required>

        <label>Midterm Exam:</label>
        <input type="text" name="Midterm" value="<?= ($grade['Midterm']) ?>"  required>

        <label>Semi Final Exam:</label>
        <input type="text" name="SemiFinal" value="<?= ($grade['SemiFinal']) ?>"  required>

        <label>Final Exam:</label>
        <input type="text" name="Final" value="<?= ($grade['Final']) ?>"  required>

        <button type="submit">Update</button>
        <a href="../Menu/grades.php">Cancel</a>
    </form>
</body>
</html>
