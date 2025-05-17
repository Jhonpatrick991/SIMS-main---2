<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if IDs are provided
if (!isset($_POST['ids']) || !is_array($_POST['ids']) || count($_POST['ids']) == 0) {
    header("Location: ../Menu/students.php");
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$ids = $_POST['ids'];
$idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
$redirectUrl = '';
$idColumn = '';
$tableName = '';

// Set table and ID column based on type
switch($type) {
    case 'students':
        $tableName = 'students';
        $idColumn = 'StudentNumber';
        $redirectUrl = '../Menu/students.php?deleted=multiple';
        break;
    case 'sections':
        $tableName = 'sections';
        $idColumn = 'SectionName';
        $redirectUrl = '../Menu/sections.php?deleted=multiple';
        break;
    case 'subjects':
        $tableName = 'subjects';
        $idColumn = 'SubjectId';
        $redirectUrl = '../Menu/subjects.php?deleted=multiple';
        break;
    case 'grades':
        $tableName = 'grades';
        $idColumn = 'StudentNumber';
        $redirectUrl = '../Menu/grades.php?deleted=multiple';
        break;
    default:
        header("Location: ../index.php");
        exit();
}

try {
    // Prepare the SQL statement
    $stmt = $con->prepare("DELETE FROM $tableName WHERE $idColumn IN ($idPlaceholders)");
    
    // Bind parameters
    $types = str_repeat('s', count($ids)); // Assuming all IDs are strings
    $stmt->bind_param($types, ...$ids);
    
    // Execute the statement
    $stmt->execute();
    
    // Check for success
    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl");
        exit();
    } else {
        header("Location: $redirectUrl&error=1");
        exit();
    }
} catch (Exception $e) {
    header("Location: $redirectUrl&error=2");
    exit();
}
