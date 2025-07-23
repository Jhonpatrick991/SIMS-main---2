<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Modified query to include student name for sorting
$sql = "SELECT g.*, s.StudentName, 
    ROUND((IFNULL(g.Prelim,0) + IFNULL(g.Midterm,0) + IFNULL(g.SemiFinal,0) + IFNULL(g.Final,0)) / 4, 2) AS AVG 
    FROM grades g
    LEFT JOIN students s ON g.StudentNumber = s.StudentNumber";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades - Teacher Dashboard</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/table.css">
    <link rel="stylesheet" href="../CSS/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../logos/mainlogo.png" type="image/png">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
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
                    <li><a href="../Menu/students.php"><img src="../logos/graduation.png"><span>Students</span></a></li>
                    <li><a href="../Menu/sections.php"><img src="../logos/multiple-users-silhouette 1.png"><span>Sections</span></a></li>
                    <li><a href="../Menu/subjects.php"><img src="../logos/stack 1.png"><span>Subjects</span></a></li>
                    <li class="active"><a href="../Menu/grades.php"><img src="../logos/exam@2x.png"><span>Grades</span></a></li>
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
                    <div class="filters">
                        <div class="search-container">
                            <label for="search">Search:</label>
                            <input type="text" id="search" class="search-input">
                            <button class="delete-selected btn-danger">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                    
                    <table class="data-table grades-table">
                        <thead>
                            <tr>
                                <th class="checkbox-column">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Student Number</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Subject Code</th>
                                <th>Semester</th>
                                <th>Prelim</th>
                                <th>Midterm</th>
                                <th>Semi Finalss</th>
                                <th>Finals</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result->num_rows > 0):
                                $grades = [];
                                while($row = $result->fetch_assoc()) {
                                    $grades[] = $row;
                                }
                                
                                // Sort grades by student last name
                                usort($grades, function($a, $b) {
                                    $aNameParts = explode(' ', $a['StudentName']);
                                    $bNameParts = explode(' ', $b['StudentName']);
                                    $aLastName = $aNameParts[0] ?? '';
                                    $bLastName = $bNameParts[0] ?? '';
                                    return strcasecmp($aLastName, $bLastName);
                                });
                                
                                foreach($grades as $row):
                                    // Split student name for display
                                    $nameParts = explode(' ', $row['StudentName']);
                                    $lastName = $nameParts[0] ?? '';
                                    $firstName = $nameParts[1] ?? '';
                                    $middleName = isset($nameParts[2]) ? $nameParts[2] : '';
                            ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?= ($row['StudentNumber']) ?></td>
                                <td><?= $lastName ?></td>
                                <td><?= $firstName ?></td>
                                <td><?= ($row['SubjectCode']) ?></td>
                                <td><?= ($row['Semester']) ?></td>
                                <td><?= ($row['Prelim']) ?></td>
                                <td><?= ($row['Midterm']) ?></td>
                                <td><?= ($row['SemiFinal']) ?></td>
                                <td><?= ($row['Final']) ?></td>
                                <td><?= ($row['AVG']) ?></td>
                                <td class="actions-column">
                                    <button class="edit-button" data-modal="editGradeModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="delete-button" data-modal="deleteGradeModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="12">No Grades were found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for editing a grade -->
    <div id="editGradeModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Grade</h2>
            
            <form class="modal-form" action="../PHP/api.php?action=edit_grade" method="POST" data-direct-submit="true">
                <div class="form-group">
                    <label for="EditStudentNumber">Student Number</label>
                    <input type="text" id="EditStudentNumber" name="StudentNumber" readonly>
                </div>
                
                <div class="form-group">
                    <label for="EditSubjectCode">Subject Code</label>
                    <input type="text" id="EditSubjectCode" name="SubjectCode" readonly>
                </div>
                
                <div class="form-group">
                    <label for="EditSemester">Semester</label>
                    <select id="EditSemester" name="Semester">
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="EditPrelim">Prelim Exam</label>
                    <input type="number" id="EditPrelim" name="Prelim" min="0" max="100" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="EditMidterm">Midterm Exam</label>
                    <input type="number" id="EditMidterm" name="Midterm" min="0" max="100" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="EditSemiFinal">Semi Final Exam</label>
                    <input type="number" id="EditSemiFinal" name="SemiFinal" min="0" max="100" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="EditFinal">Final Exam</label>
                    <input type="number" id="EditFinal" name="Final" min="0" max="100" step="0.01">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for deleting a grade -->
    <div id="deleteGradeModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Are you sure you want to delete this grade?</h3>
            <p>This action cannot be undone.</p>
            
            <div class="btn-container">
                <button class="btn btn-secondary">Cancel</button>
                <button class="btn btn-danger delete-confirm" data-type="grade">Delete</button>
            </div>
        </div>
    </div>

    <!-- Modal for deleting multiple grades -->
    <div id="deleteMultipleModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Delete <span class="item-count">0</span> grades?</h3>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Custom handler for grade edit
            const editButtons = document.querySelectorAll('.edit-button[data-modal="editGradeModal"]');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const studentNumber = row.querySelector('td:nth-child(2)').textContent.trim();
                    const subjectCode = row.querySelector('td:nth-child(5)').textContent.trim();
                    const semester = row.querySelector('td:nth-child(6)').textContent.trim();
                    const prelim = row.querySelector('td:nth-child(7)').textContent.trim();
                    const midterm = row.querySelector('td:nth-child(8)').textContent.trim();
                    const semiFinal = row.querySelector('td:nth-child(9)').textContent.trim();
                    const final = row.querySelector('td:nth-child(10)').textContent.trim();
                    
                    document.getElementById('EditStudentNumber').value = studentNumber;
                    document.getElementById('EditSubjectCode').value = subjectCode;
                    
                    // Set the semester dropdown
                    const semesterSelect = document.getElementById('EditSemester');
                    for(let i = 0; i < semesterSelect.options.length; i++) {
                        if(semesterSelect.options[i].value === semester) {
                            semesterSelect.selectedIndex = i;
                            break;
                        }
                    }
                    
                    document.getElementById('EditPrelim').value = prelim;
                    document.getElementById('EditMidterm').value = midterm;
                    document.getElementById('EditSemiFinal').value = semiFinal;
                    document.getElementById('EditFinal').value = final;
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
        });
    </script>
</body>
</html>