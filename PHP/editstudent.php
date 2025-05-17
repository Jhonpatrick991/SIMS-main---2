<?php
require("../connect.php");

$SectionName = "";       
$SubjectCode = "";

$sectionNames = [];
$sectionResult = $con->query("SELECT SectionName FROM sections");
if ($sectionResult && $sectionResult->num_rows > 0) {
    while ($row = $sectionResult->fetch_assoc()) {
        $sectionNames[] = $row['SectionName'];
    }
}

$subjectCodes = [];
$subjectResult = $con->query("SELECT SubjectCode FROM subjects");
if ($subjectResult && $subjectResult->num_rows > 0) {
    while ($row = $subjectResult->fetch_assoc()) {
        $subjectCodes[] = $row['SubjectCode'];
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM students WHERE StudentNumber = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo "Student not found.";
        exit;
    }
} else {
    echo "No student ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="../CSS/another.css">
</head>
<body>
    <h2>Edit Student</h2>
    <form action="updatestudent.php" method="POST">
        <input type="hidden" name="StudentNumber" value="<?= $student['StudentNumber'] ?>">
        
        <label>Name:</label>
        <input type="text" name="StudentName" value="<?= $student['StudentName'] ?>" required><br>

        <label>Section Name:</label>
        <select name="SectionName" required style="width: 105%; height: 10%;">
            <option value="">Select Section Name</option>
                <?php foreach ($sectionNames as $new): ?>
                <option value="<?= htmlspecialchars($new) ?>" <?= $SectionName == $new ? 'selected' : '' ?>>
                <?= htmlspecialchars($new) ?>
            </option>
                <?php endforeach; ?>
        </select>

        <label>Subject Code:</label>
        <select name="SubjectCode" required style="width: 105%; height: 10%;">
            <option value="">Select Subject Code</option>
                <?php foreach ($subjectCodes as $code): ?>
                <option value="<?= htmlspecialchars($code) ?>" <?= $SubjectCode == $code ? 'selected' : '' ?>>
                <?= htmlspecialchars($code) ?>
            </option>
                <?php endforeach; ?>
        </select>

        <label>Email:</label>
        <input type="email" name="Email" value="<?= $student['Email'] ?>" required><br>

        <button type="submit">Update</button>
        <a href="../Menu/students.php">Cancel</a>
    </form>
</body>
</html>
