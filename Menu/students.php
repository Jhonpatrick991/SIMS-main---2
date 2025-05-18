<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM students";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Teacher Dashboard</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/table.css">
    <link rel="stylesheet" href="../CSS/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../logos/mainlogo.png" type="image/png">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <div class="logo-circle">
                    <img src="../logos/mainlogo.png" alt="Logo">
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php"><img src="../logos/table.png"> <span>Dashboard</span></a></li>
                    <li><a href="../Menu/calendar.php"><img src="../logos/calendar.png"> <span>Calendar</span></a></li>
                    <li class="active"><a href="../Menu/students.php"><img src="../logos/graduation.png"><span>Students</span></a></li>
                    <li><a href="../Menu/sections.php"><img src="../logos/multiple-users-silhouette 1.png"><span>Sections</span></a></li>
                    <li><a href="../Menu/subjects.php"><img src="../logos/stack 1.png"><span>Subjects</span></a></li>
                    <li><a href="../Menu/grades.php"><img src="../logos/exam@2x.png"><span>Grades</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <header>
                <div class="clock" id="liveClock"></div>
                <div class="user-info">
                    <span>Admin</span>
                    <div class="avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="dropdownContent blue">
                        <div class="dropdown_Avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h1>Admin</h1> 
                        <p>Joined on 2000 B.C.</p>
                        <button onclick="window.location.href='../logout.php'">Logout</button>
                    </div>
                </div>
            </header>

            <main class="table-main">
                <div class="table-container">
                    <?php if (isset($_GET['updated'])): ?>
                        <div class="success-message">
                            <p style="color: green;">Student updated successfully.</p>
                        </div>
                    <?php endif; ?>

                    <div class="table-header">
                        <button class="add-button" data-modal="studentModal">
                            <i class="fas fa-plus"></i> New Student
                        </button>
                        <button class="delete-selected btn-danger">
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                        <div class="search-container">
                            <label for="search">Search:</label>
                            <input type="text" id="search" class="search-input">
                        </div>
                    </div>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="checkbox-column">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Student Number</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Suffix</th>
                                <th>Section</th>
                                <th>Subject Code</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result->num_rows > 0):
                                $students = [];
                                while($row = $result->fetch_assoc()) {
                                    $students[] = $row;
                                }
                                
                                // Sort students by last name
                                usort($students, function($a, $b) {
                                    $aLastName = explode(' ', $a['StudentName'])[0]; // Temporary sorting until DB structure is updated
                                    $bLastName = explode(' ', $b['StudentName'])[0];
                                    return strcasecmp($aLastName, $bLastName);
                                });
                                
                                foreach($students as $row):
                                    // Temporary name splitting until DB structure is updated
                                    $nameParts = explode(' ', $row['StudentName']);
                                    $lastName = $nameParts[0] ?? '';
                                    $firstName = $nameParts[1] ?? '';
                                    $middleName = isset($nameParts[2]) ? $nameParts[2] : '';
                                    $suffix = isset($nameParts[3]) ? $nameParts[3] : '';
                            ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?= ($row['StudentNumber']) ?></td>
                                <td><?= $lastName ?></td>
                                <td><?= $firstName ?></td>
                                <td><?= $middleName ?></td>
                                <td><?= $suffix ?></td>
                                <td><?= ($row['SectionName']) ?></td>
                                <td><?= ($row['SubjectCode']) ?></td>
                                <td><?= ($row['Email']) ?></td>
                                <td class="actions-column">
                                    <button class="edit-button" data-modal="editStudentModal" 
                                            data-section="<?= htmlspecialchars($row['SectionName'], ENT_QUOTES) ?>" 
                                            data-subject="<?= htmlspecialchars($row['SubjectCode'], ENT_QUOTES) ?>"
                                            data-email="<?= htmlspecialchars($row['Email'], ENT_QUOTES) ?>"
                                            data-suffix="<?= htmlspecialchars($suffix, ENT_QUOTES) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="delete-button" data-modal="deleteStudentModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="9">No students were found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for adding a new student -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Add New Student</h2>
            //
            <form class="modal-form" action="../PHP/api.php?action=create_student" method="POST">
                <div class="form-group">
                    <label for="StudentNumber">Student Number (7 digits)</label>
                    <input type="text" id="StudentNumber" name="StudentNumber" pattern="\d{7}" maxlength="7" required>
                    <small>Must be exactly 7 digits</small>
                </div>
                
                <div class="form-group">
                    <label for="LastName">Last Name</label>
                    <input type="text" id="LastName" name="LastName" required>
                </div>
                
                <div class="form-group">
                    <label for="FirstName">First Name</label>
                    <input type="text" id="FirstName" name="FirstName" required>
                </div>
                
                <div class="form-group">
                    <label for="MiddleName">Middle Name</label>
                    <input type="text" id="MiddleName" name="MiddleName">
                </div>
                
                <div class="form-group">
                    <label for="Suffix">Suffix</label>
                    <input type="text" id="Suffix" name="Suffix" placeholder="Jr., Sr., III, etc.">
                </div>
                
                <div class="form-group">
                    <label for="SectionName">Section</label>
                    <select id="SectionName" name="SectionName" required>
                        <option value="">Select a Section</option>
                        <?php
                        $sectionsQuery = "SELECT SectionName FROM sections";
                        $sectionsResult = $con->query($sectionsQuery);
                        while($sectionRow = $sectionsResult->fetch_assoc()) {
                            echo "<option value='" . $sectionRow['SectionName'] . "'>" . $sectionRow['SectionName'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="SubjectCode">Subject Code</label>
                    <select id="SubjectCode" name="SubjectCode" required>
                        <option value="">Select a Subject</option>
                        <?php
                        $subjectsQuery = "SELECT SubjectCode FROM subjects";
                        $subjectsResult = $con->query($subjectsQuery);
                        while($subjectRow = $subjectsResult->fetch_assoc()) {
                            echo "<option value='" . $subjectRow['SubjectCode'] . "'>" . $subjectRow['SubjectCode'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="Email">Email</label>
                    <input type="email" id="Email" name="Email" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for editing a student -->
    <div id="editStudentModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Student</h2>
            
            <form class="modal-form" action="../PHP/api.php?action=edit_student" method="POST" data-direct-submit="true">
                <input type="hidden" id="OldStudentNumber" name="OldStudentNumber">
                
                <div class="form-group">
                    <label for="EditStudentNumber">Student Number (7 digits)</label>
                    <input type="text" id="EditStudentNumber" name="StudentNumber" pattern="\d{7}" maxlength="7" required>
                    <small>Must be exactly 7 digits</small>
                </div>
                
                <div class="form-group">
                    <label for="EditLastName">Last Name</label>
                    <input type="text" id="EditLastName" name="LastName" required>
                </div>
                
                <div class="form-group">
                    <label for="EditFirstName">First Name</label>
                    <input type="text" id="EditFirstName" name="FirstName" required>
                </div>
                
                <div class="form-group">
                    <label for="EditMiddleName">Middle Name</label>
                    <input type="text" id="EditMiddleName" name="MiddleName" required>
                </div>
                
                <div class="form-group">
                    <label for="EditSuffix">Suffix</label>
                    <input type="text" id="EditSuffix" name="Suffix" placeholder="Jr., Sr., III, etc.">
                </div>
                
                <div class="form-group">
                    <label for="EditSectionName">Section</label>
                    <select id="EditSectionName" name="SectionName" required>
                        <option value="">Select a Section</option>
                        <?php
                        $sectionsQuery = "SELECT SectionName FROM sections";
                        $sectionsResult = $con->query($sectionsQuery);
                        while($sectionRow = $sectionsResult->fetch_assoc()) {
                            echo "<option value='" . $sectionRow['SectionName'] . "'>" . $sectionRow['SectionName'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="EditSubjectCode">Subject Code</label>
                    <select id="EditSubjectCode" name="SubjectCode" required>
                        <option value="">Select a Subject</option>
                        <?php
                        $subjectsQuery = "SELECT SubjectCode FROM subjects";
                        $subjectsResult = $con->query($subjectsQuery);
                        while($subjectRow = $subjectsResult->fetch_assoc()) {
                            echo "<option value='" . $subjectRow['SubjectCode'] . "'>" . $subjectRow['SubjectCode'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="EditEmail">Email</label>
                    <input type="email" id="EditEmail" name="Email" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for deleting a student -->
    <div id="deleteStudentModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Are you sure you want to delete this student?</h3>
            <p>This action cannot be undone.</p>
            
            <div class="btn-container">
                <button class="btn btn-secondary">Cancel</button>
                <button class="btn btn-danger delete-confirm" data-type="student">Delete</button>
            </div>
        </div>
    </div>

    <!-- Modal for deleting multiple students -->
    <div id="deleteMultipleModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Delete <span class="item-count">0</span> students?</h3>
            <p>This action cannot be undone.</p>
            
            <div class="btn-container">
                <button class="btn btn-secondary">Cancel</button>
                <button class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>

    <!-- Toast notification container -->
    <div id="toast" class="toast"></div>

    <script src="../JS/script.js"></script>
    <script src="../JS/table.js"></script>
    <script src="../JS/modal.js"></script>
    
    <script>
        // Custom handler for student edit
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-button[data-modal="editStudentModal"]');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const studentNumber = row.querySelector('td:nth-child(2)').textContent.trim();
                    const lastName = row.querySelector('td:nth-child(3)').textContent.trim();
                    const firstName = row.querySelector('td:nth-child(4)').textContent.trim();
                    const middleName = row.querySelector('td:nth-child(5)').textContent.trim();
                    const suffix = row.querySelector('td:nth-child(6)').textContent.trim();
                    const sectionName = row.querySelector('td:nth-child(7)').textContent.trim();
                    const subjectCode = row.querySelector('td:nth-child(8)').textContent.trim();
                    const email = row.querySelector('td:nth-child(9)').textContent.trim();
                    
                    // Set values in the edit form
                    document.getElementById('OldStudentNumber').value = studentNumber;
                    document.getElementById('EditStudentNumber').value = studentNumber;
                    document.getElementById('EditLastName').value = lastName;
                    document.getElementById('EditFirstName').value = firstName;
                    document.getElementById('EditMiddleName').value = middleName;
                    document.getElementById('EditSuffix').value = suffix;
                    
                    // Set dropdown values
                    const sectionDropdown = document.getElementById('EditSectionName');
                    for (let i = 0; i < sectionDropdown.options.length; i++) {
                        if (sectionDropdown.options[i].value === sectionName) {
                            sectionDropdown.selectedIndex = i;
                            break;
                        }
                    }
                    
                    const subjectDropdown = document.getElementById('EditSubjectCode');
                    for (let i = 0; i < subjectDropdown.options.length; i++) {
                        if (subjectDropdown.options[i].value === subjectCode) {
                            subjectDropdown.selectedIndex = i;
                            break;
                        }
                    }
                    
                    document.getElementById('EditEmail').value = email;
                });
            });
            
            // Fix for cancel buttons in delete modals
            const deleteModals = document.querySelectorAll('.delete-modal');
            deleteModals.forEach(modal => {
                const cancelBtn = modal.querySelector('.btn-secondary');
                cancelBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            });
            
            // Add direct form submit handler for student edit form
            const editStudentForm = document.querySelector('#editStudentModal form');
            editStudentForm.addEventListener('submit', function(e) {
                // Let the form submit naturally, avoiding the modal.js handler
                // The form will submit directly to the API endpoint
                return true;
            });
        });
    </script>
</body>
</html>


