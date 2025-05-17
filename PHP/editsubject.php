<?php
require("../connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM subjects WHERE SubjectId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();

    if (!$subject) {
        echo "Subject not found.";
        exit;
    }
} else {
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Subject</title>
    <link rel="stylesheet" href="../CSS/another.css">
</head>
<body>
    <h2>Edit Subject</h2>
    <form action="updatesubject.php" method="POST">
        <input type="hidden" name="SubjectId" value="<?= ($subject['SubjectId']) ?>">
        <input type="hidden" name="oldSubjectCode" value="<?=($subject['SubjectCode']) ?>">
        
        <label>Subject Code:</label>
        <input type="text" name="SubjectCode" value="<?= ($subject['SubjectCode']) ?>" required><br>

        <label>Unit:</label>
        <input type="number" name="Unit" value="<?= ($subject['Unit']) ?>" required><br>

        <label>Subject Name:</label>
        <input type="text" name="SubjectName" value="<?= ($subject['SubjectName']) ?>" required><br>

        <label>Time:</label>
        <input type="text" name="Time" value="<?= ($subject['Time']) ?>" required><br>

        <button type="submit">Update</button>
        <a href="../Menu/subjects.php">Cancel</a>
    </form>
</body>
</html>