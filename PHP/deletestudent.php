
<?php
require("../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $StudentNumber = $_GET['id'];

    // First, delete from grades
    $stmt1 = $con->prepare("DELETE FROM grades WHERE StudentNumber = ?");
    $stmt1->bind_param("s", $StudentNumber);
    $stmt1->execute();
    $stmt1->close();

    // Then, delete from students
    $stmt2 = $con->prepare("DELETE FROM students WHERE StudentNumber = ?");
    $stmt2->bind_param("s", $StudentNumber);

    if ($stmt2->execute()) {
        $stmt2->close();
        header("Location: ../Menu/students.php?deleted=1");
        exit;
    } else {
        echo "Error deleting student: " . $stmt2->error;
    }
} else {
    echo "Invalid request.";
}
?>