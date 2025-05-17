<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

// Function to send JSON response
function sendResponse($success, $message, $data = [], $redirect = null) {
    // Check if it's an AJAX request
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    } else if ($redirect && $success) {
        // For non-AJAX requests, redirect with success message
        header("Location: $redirect");
        exit();
    } else if ($redirect && !$success) {
        // For non-AJAX requests, redirect with error message
        header("Location: $redirect");
        exit();
    } else {
        // For non-AJAX requests without redirect
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    }
    exit();
}

// Get the action from the URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle create student
if ($action === 'create_student') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $studentNumber = $_POST['StudentNumber'] ?? '';
        $lastName = $_POST['LastName'] ?? '';
        $firstName = $_POST['FirstName'] ?? '';
        $middleName = $_POST['MiddleName'] ?? '';
        $suffix = $_POST['Suffix'] ?? '';
        $sectionName = $_POST['SectionName'] ?? '';
        $email = $_POST['Email'] ?? '';
        $subjectCode = $_POST['SubjectCode'] ?? '';

        if (empty($studentNumber) || empty($lastName) || empty($firstName) || empty($sectionName) || empty($email) || empty($subjectCode)) {
            sendResponse(false, 'Student number, name fields, section, email and subject code are required');
        }

        // Combine name parts into a full name (temporary solution until DB structure is updated)
        $fullName = $lastName . ' ' . $firstName;
        if (!empty($middleName)) {
            $fullName .= ' ' . $middleName;
        }
        if (!empty($suffix)) {
            $fullName .= ' ' . $suffix;
        }

        try {
            // Insert into students table
            $stmt = $con->prepare("INSERT INTO students (StudentNumber, StudentName, SectionName, Email, SubjectCode) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $studentNumber, $fullName, $sectionName, $email, $subjectCode);
            $stmt->execute();
            
            // Insert into grades table
            $stmt = $con->prepare("INSERT INTO grades (StudentNumber, SubjectCode) VALUES (?, ?)");
            $stmt->bind_param("ss", $studentNumber, $subjectCode);
            $stmt->execute();
            
            sendResponse(true, 'Student created successfully');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage());
        }
    }
}

// Handle edit student
else if ($action === 'edit_student') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $oldStudentNumber = $_POST['OldStudentNumber'] ?? '';
        $studentNumber = $_POST['StudentNumber'] ?? '';
        $lastName = $_POST['LastName'] ?? '';
        $firstName = $_POST['FirstName'] ?? '';
        $middleName = $_POST['MiddleName'] ?? '';
        $suffix = $_POST['Suffix'] ?? '';
        $sectionName = $_POST['SectionName'] ?? '';
        $email = $_POST['Email'] ?? '';
        $subjectCode = $_POST['SubjectCode'] ?? '';

        if (empty($studentNumber) || empty($lastName) || empty($firstName) || empty($middleName) || empty($sectionName) || empty($email) || empty($subjectCode)) {
            sendResponse(false, 'All fields except Suffix are required', [], '../Menu/students.php?updated=0');
        }

        // Validate student number is 7 digits
        if (!preg_match('/^\d{7}$/', $studentNumber)) {
            sendResponse(false, 'Student number must be exactly 7 digits', [], '../Menu/students.php?updated=0');
        }

        // Combine name parts into a full name (temporary solution until DB structure is updated)
        $fullName = $lastName . ' ' . $firstName;
        if (!empty($middleName)) {
            $fullName .= ' ' . $middleName;
        }
        if (!empty($suffix)) {
            $fullName .= ' ' . $suffix;
        }

        try {
            $stmt = $con->prepare("UPDATE students SET StudentNumber = ?, StudentName = ?, SectionName = ?, Email = ?, SubjectCode = ? WHERE StudentNumber = ?");
            $stmt->bind_param("ssssss", $studentNumber, $fullName, $sectionName, $email, $subjectCode, $oldStudentNumber);
            $stmt->execute();
            
            // Update grade entry
            if ($oldStudentNumber != $studentNumber) {
                $stmt = $con->prepare("UPDATE grades SET StudentNumber = ? WHERE StudentNumber = ?");
                $stmt->bind_param("ss", $studentNumber, $oldStudentNumber);
                $stmt->execute();
            }
            
            // Update subject code if it changed
            $stmt = $con->prepare("UPDATE grades SET SubjectCode = ? WHERE StudentNumber = ?");
            $stmt->bind_param("ss", $subjectCode, $studentNumber);
            $stmt->execute();
            
            sendResponse(true, 'Student updated successfully', [], '../Menu/students.php?updated=1');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage(), [], '../Menu/students.php?updated=0');
        }
    }
}

// Handle create section
else if ($action === 'create_section') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sectionName = $_POST['SectionName'] ?? '';

        if (empty($sectionName)) {
            sendResponse(false, 'Section name is required');
        }

        try {
            $stmt = $con->prepare("INSERT INTO sections (SectionName) VALUES (?)");
            $stmt->bind_param("s", $sectionName);
            $stmt->execute();
            
            sendResponse(true, 'Section created successfully');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage());
        }
    }
}

// Handle edit section
else if ($action === 'edit_section') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $oldSectionName = $_POST['OldSectionName'] ?? '';
        $newSectionName = $_POST['SectionName'] ?? '';

        if (empty($oldSectionName) || empty($newSectionName)) {
            sendResponse(false, 'Section name is required', [], '../Menu/sections.php?updated=0');
        }

        try {
            // Update the section name
            $stmt = $con->prepare("UPDATE sections SET SectionName = ? WHERE SectionName = ?");
            $stmt->bind_param("ss", $newSectionName, $oldSectionName);
            $stmt->execute();
            
            // Update all references in students table
            $stmt = $con->prepare("UPDATE students SET SectionName = ? WHERE SectionName = ?");
            $stmt->bind_param("ss", $newSectionName, $oldSectionName);
            $stmt->execute();
            
            sendResponse(true, 'Section updated successfully', [], '../Menu/sections.php?updated=1');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage(), [], '../Menu/sections.php?updated=0');
        }
    }
}

// Handle create subject
else if ($action === 'create_subject') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $subjectCode = $_POST['SubjectCode'] ?? '';
        $subjectName = $_POST['SubjectName'] ?? '';
        $unit = $_POST['Unit'] ?? '';
        $time = $_POST['Time'] ?? '';

        if (empty($subjectCode) || empty($subjectName) || empty($unit)) {
            sendResponse(false, 'Subject code, name and unit are required');
        }

        try {
            $stmt = $con->prepare("INSERT INTO subjects (SubjectCode, SubjectName, Unit, Time) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $subjectCode, $subjectName, $unit, $time);
            $stmt->execute();
            
            sendResponse(true, 'Subject created successfully');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage());
        }
    }
}

// Handle edit subject
else if ($action === 'edit_subject') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $subjectId = $_POST['SubjectId'] ?? '';
        $subjectCode = $_POST['SubjectCode'] ?? '';
        $subjectName = $_POST['SubjectName'] ?? '';
        $unit = $_POST['Unit'] ?? '';
        $time = $_POST['Time'] ?? '';

        if (empty($subjectId) || empty($subjectCode) || empty($subjectName) || empty($unit)) {
            sendResponse(false, 'Subject ID, code, name and unit are required', [], '../Menu/subjects.php?updated=0');
        }

        try {
            // Get the old subject code
            $stmt = $con->prepare("SELECT SubjectCode FROM subjects WHERE SubjectId = ?");
            $stmt->bind_param("i", $subjectId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $oldSubjectCode = $row['SubjectCode'];
            
            // Update the subject
            $stmt = $con->prepare("UPDATE subjects SET SubjectCode = ?, SubjectName = ?, Unit = ?, Time = ? WHERE SubjectId = ?");
            $stmt->bind_param("ssisi", $subjectCode, $subjectName, $unit, $time, $subjectId);
            $stmt->execute();
            
            // If subject code changed, update references in students table
            if ($oldSubjectCode !== $subjectCode) {
                $stmt = $con->prepare("UPDATE students SET SubjectCode = ? WHERE SubjectCode = ?");
                $stmt->bind_param("ss", $subjectCode, $oldSubjectCode);
                $stmt->execute();
                
                // Update references in grades table
                $stmt = $con->prepare("UPDATE grades SET SubjectCode = ? WHERE SubjectCode = ?");
                $stmt->bind_param("ss", $subjectCode, $oldSubjectCode);
                $stmt->execute();
            }
            
            sendResponse(true, 'Subject updated successfully', [], '../Menu/subjects.php?updated=1');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage(), [], '../Menu/subjects.php?updated=0');
        }
    }
}

// Handle edit grade
else if ($action === 'edit_grade') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $studentNumber = $_POST['StudentNumber'] ?? '';
        $subjectCode = $_POST['SubjectCode'] ?? '';
        $semester = $_POST['Semester'] ?? '';
        $prelim = $_POST['Prelim'] ?? '';
        $midterm = $_POST['Midterm'] ?? '';
        $semiFinal = $_POST['SemiFinal'] ?? '';
        $final = $_POST['Final'] ?? '';

        if (empty($studentNumber) || empty($subjectCode)) {
            sendResponse(false, 'Student number and subject code are required', [], '../Menu/grades.php?updated=0');
        }

        try {
            $stmt = $con->prepare("UPDATE grades SET Semester = ?, Prelim = ?, Midterm = ?, SemiFinal = ?, Final = ? WHERE StudentNumber = ? AND SubjectCode = ?");
            $stmt->bind_param("sddddss", $semester, $prelim, $midterm, $semiFinal, $final, $studentNumber, $subjectCode);
            $stmt->execute();
            
            sendResponse(true, 'Grade updated successfully', [], '../Menu/grades.php?updated=1');
        } catch (Exception $e) {
            sendResponse(false, 'Error: ' . $e->getMessage(), [], '../Menu/grades.php?updated=0');
        }
    }
}

// If no valid action is found
sendResponse(false, 'Invalid action');
?> 