<?php
require("../connect.php");
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate IDs
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

try {
    $con->begin_transaction();

    switch ($type) {
        case 'students':
            $tableName = 'students';
            $idColumn = 'StudentNumber';
            $redirectUrl = '../Menu/students.php?deleted=multiple';

            // Delete from grades first
            $gradesStmt = $con->prepare("DELETE FROM grades WHERE StudentNumber IN ($idPlaceholders)");
            $types = str_repeat('s', count($ids));
            $gradesStmt->bind_param($types, ...$ids);
            $gradesStmt->execute();
            break;

        case 'grades':
            $tableName = 'grades';
            $idColumn = 'StudentNumber';
            $redirectUrl = '../Menu/grades.php?deleted=multiple';

            // Delete from students first
            $studentsStmt = $con->prepare("DELETE FROM students WHERE StudentNumber IN ($idPlaceholders)");
            $types = str_repeat('s', count($ids));
            $studentsStmt->bind_param($types, ...$ids);
            $studentsStmt->execute();
            break;

        case 'sections':
            $tableName = 'sections';
            $idColumn = 'SectionName';
            $redirectUrl = '../Menu/sections.php?deleted=multiple';

            $studentsStmt = $con->prepare("UPDATE students SET SectionName = NULL WHERE SectionName IN ($idPlaceholders)");
            $types = str_repeat('s', count($ids));
            $studentsStmt->bind_param($types, ...$ids);
            $studentsStmt->execute();
            break;

        case 'subjects':
            $tableName = 'subjects';
            $idColumn = 'SubjectId';
            $redirectUrl = '../Menu/subjects.php?deleted=multiple';
            break;

        default:
            header("Location: ../index.php");
            exit();
    }

    // Now delete from the main table
    $stmt = $con->prepare("DELETE FROM $tableName WHERE $idColumn IN ($idPlaceholders)");
    $types = str_repeat('s', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();

    $con->commit();

    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl");
        exit();
    } else {
        header("Location: $redirectUrl&error=1");
        exit();
    }

} catch (Exception $e) {
    $con->rollback();
    header("Location: $redirectUrl&error=2");
    exit();
}
