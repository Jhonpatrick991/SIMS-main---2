
<?php
require("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $StudentNumber = $_GET['id'];

    $stmt = $con->prepare("DELETE FROM grades WHERE StudentNumber = ?");
    $stmt->bind_param("s", $StudentNumber);

    if ($stmt->execute()) { 
        header("Location: ../Menu/grades.php?deleted=1");
        exit;
    } else {
        echo "Error deleting grade.";
    }
} else {
    echo "Invalid request.";
}
?>