<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT s.SubjectId, s.SubjectCode, s.Unit, s.SubjectName, s.Time,
            (SELECT COUNT(*) FROM sections WHERE SubjectCode = s.SubjectCode) AS SectionCount,
            (SELECT COUNT(*) FROM students WHERE SubjectCode = s.SubjectCode) AS StudentCount
        FROM subjects s";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects - Teacher Dashboard</title>
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
                    <li><a href="../Menu/students.php"><img src="../logos/graduation.png"><span>Students</span></a></li>
                    <li><a href="../Menu/sections.php"><img src="../logos/multiple-users-silhouette 1.png"><span>Sections</span></a></li>
                    <li class="active"><a href="../Menu/subjects.php"><img src="../logos/stack 1.png"><span>Subjects</span></a></li>
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
                    <div class="table-header">
                        <button class="add-button" data-modal="subjectModal">
                            <i class="fas fa-plus"></i> New Subject
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
                                <th>Code</th>
                                <th>Unit</th>
                                <th>Subject Name</th>
                                <th>Students Enrolled</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?= ($row['SubjectCode']) ?></td>
                                <td><?= ($row['Unit']) ?></td>
                                <td><?= ($row['SubjectName']) ?></td>
                                <td><?= ($row['StudentCount']) ?></td>
                                <td><?= ($row['Time']) ?></td>
                                <td class="actions-column">
                                    <button class="edit-button" data-modal="editSubjectModal" data-id="<?= $row['SubjectId'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="delete-button" data-modal="deleteSubjectModal" data-id="<?= $row['SubjectId'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7">No Subjects were found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for adding a new subject -->
    <div id="subjectModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Add New Subject</h2>
            
            <form class="modal-form" action="../PHP/api.php?action=create_subject" method="POST">
                <div class="form-group">
                    <label for="SubjectCode">Subject Code</label>
                    <input type="text" id="SubjectCode" name="SubjectCode" required>
                </div>
                
                <div class="form-group">
                    <label for="SubjectName">Subject Name</label>
                    <input type="text" id="SubjectName" name="SubjectName" required>
                </div>
                
                <div class="form-group">
                    <label for="Unit">Unit (1-3)</label>
                    <input type="number" id="Unit" name="Unit" min="1" max="3" required>
                    <small>Must be between 1 and 3</small>
                </div>
                
                <div class="form-group">
                    <label for="Time">Time</label>
                    <input type="text" id="Time" name="Time" placeholder="e.g., Mon 9:00-11:00 AM">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for editing a subject -->
    <div id="editSubjectModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Subject</h2>
            
            <form class="modal-form" action="../PHP/api.php?action=edit_subject" method="POST" data-direct-submit="true">
                <input type="hidden" id="EditSubjectId" name="SubjectId">
                
                <div class="form-group">
                    <label for="EditSubjectCode">Subject Code</label>
                    <input type="text" id="EditSubjectCode" name="SubjectCode" required>
                </div>
                
                <div class="form-group">
                    <label for="EditSubjectName">Subject Name</label>
                    <input type="text" id="EditSubjectName" name="SubjectName" required>
                </div>
                
                <div class="form-group">
                    <label for="EditUnit">Unit (1-3)</label>
                    <input type="number" id="EditUnit" name="Unit" min="1" max="3" required>
                    <small>Must be between 1 and 3</small>
                </div>
                
                <div class="form-group">
                    <label for="EditTime">Time</label>
                    <input type="text" id="EditTime" name="Time" placeholder="e.g., Mon 9:00-11:00 AM">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for deleting a subject -->
    <div id="deleteSubjectModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Are you sure you want to delete this subject?</h3>
            <p>This action cannot be undone. Students enrolled in this subject will be affected.</p>
            
            <div class="btn-container">
                <button class="btn btn-secondary">Cancel</button>
                <button class="btn btn-danger delete-confirm" data-type="subject">Delete</button>
            </div>
        </div>
    </div>

    <!-- Modal for deleting multiple subjects -->
    <div id="deleteMultipleModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Delete <span class="item-count">0</span> subjects?</h3>
            <p>This action cannot be undone. Students enrolled in these subjects will be affected.</p>
            
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
        // Custom handler for subject edit since it needs the subject ID
        document.addEventListener('DOMContentLoaded', function() {
            // Get the hidden SubjectId field from the row data
            const editButtons = document.querySelectorAll('.edit-button[data-modal="editSubjectModal"]');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const subjectId = this.getAttribute('data-id');
                    document.getElementById('EditSubjectId').value = subjectId;
                });
            });
            
            // Same for delete buttons
            const deleteButtons = document.querySelectorAll('.delete-button[data-modal="deleteSubjectModal"]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const subjectId = this.getAttribute('data-id');
                    const confirmBtn = document.querySelector('#deleteSubjectModal .delete-confirm');
                    confirmBtn.setAttribute('data-id', subjectId);
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