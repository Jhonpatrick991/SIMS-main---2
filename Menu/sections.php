<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT s.SectionName, COUNT(st.StudentNumber) AS StudentsEnrolled
        FROM sections s
        LEFT JOIN students st ON s.SectionName = st.SectionName
        GROUP BY s.SectionName";
$result = $con->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sections - Teacher Dashboard</title>
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
                    <li class="active"><a href="../Menu/sections.php"><img src="../logos/multiple-users-silhouette 1.png"><span>Sections</span></a></li>
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
                            <p style="color: green;">Section updated successfully.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-header">
                        <button class="add-button" data-modal="sectionModal">
                            <i class="fas fa-plus"></i> New Section
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
                                <th>Section Name</th>
                                <th>Students Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?= ($row['SectionName']) ?></td>
                                <td><?= ($row['StudentsEnrolled']) ?></td>
                                <td class="actions-column">
                                    <button class="edit-button" data-modal="editSectionModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="delete-button" data-modal="deleteSectionModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No sections were found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for adding a new section -->
    <div id="sectionModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Add New Section</h2>
            
            <form class="modal-form" action="../PHP/api.php?action=create_section" method="POST">
                <div class="form-group">
                    <label for="SectionName">Section Name</label>
                    <input type="text" id="SectionName" name="SectionName" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for editing a section -->
    <div id="editSectionModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Section</h2>
            
            <form class="modal-form" action="../PHP/api.php?action=edit_section" method="POST" data-direct-submit="true">
                <input type="hidden" id="OldSectionName" name="OldSectionName">
                
                <div class="form-group">
                    <label for="EditSectionName">Section Name</label>
                    <input type="text" id="EditSectionName" name="SectionName" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for deleting a section -->
    <div id="deleteSectionModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Are you sure you want to delete this section?</h3>
            <p>This action cannot be undone. Students in this section will be affected.</p>
            
            <div class="btn-container">
                <button class="btn btn-secondary">Cancel</button>
                <button class="btn btn-danger delete-confirm" data-type="section">Delete</button>
            </div>
        </div>
    </div>

    <!-- Modal for deleting multiple sections -->
    <div id="deleteMultipleModal" class="modal delete-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Delete <span class="item-count">0</span> sections?</h3>
            <p>This action cannot be undone. Students in these sections will be affected.</p>
            
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
            // Custom handler for section edit
            const editButtons = document.querySelectorAll('.edit-button[data-modal="editSectionModal"]');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const sectionName = row.querySelector('td:nth-child(2)').textContent.trim();
                    document.getElementById('OldSectionName').value = sectionName;
                    document.getElementById('EditSectionName').value = sectionName;
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