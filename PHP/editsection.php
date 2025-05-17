<?php
require("../connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM sections WHERE SectionName = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $section = $result->fetch_assoc();

    if (!$section) {
        echo "Section not found.";
        exit;
    }
} else {
    // echo "No grade ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit section</title>
    <link rel="stylesheet" href="../CSS/another.css">
    <style>
        /* You can add styles here or import your CSS */
    </style>
</head>
<body>
    <!-- <h2></h2> Test get or post here print_r($_GET or $_POST) -->
    <h2>Edit section</h2>
    <form action="updatesection.php" method="POST">
        <input type="hidden" name="oldSectionName" value="<?= ($section['SectionName']) ?>">
        <label>Section Name:</label>
        <input type="text" name="sectionName" value="<?= ($section['SectionName']) ?>" required>

        <button type="submit">Update</button>
        <a href="../Menu/sections.php">Cancel</a>
    </form>
</body>
</html>
