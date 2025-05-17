<?php
require('connect.php');
if (!isset($_SESSION['loggedin']) && $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$studentCount = $con->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$sectionCount = $con->query("SELECT COUNT(*) FROM sections")->fetch_row()[0];
$subjectCount = $con->query("SELECT COUNT(*) FROM subjects")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="logos/mainlogo.png" type="image/png">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <div class="logo-circle">
                    <img src="logos/mainlogo.png" alt="Logo">
                </div>
                
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="index.php"><img src="logos/table.png"> <span>Dashboard</span></a></li>
                    <li><a href="Menu/calendar.php"><img src="logos/calendar.png"> <span>Calendar</span></a></li>
                    <li><a href="Menu/students.php"><img src="logos/graduation.png"><span>Students</span></a></li>
                    <li><a href="Menu/sections.php"><img src="logos/multiple-users-silhouette 1.png"><span>Sections</span></a></li>
                    <li><a href="Menu/subjects.php"><img src="logos/stack 1.png"><span>Subjects</span></a></li>
                    <li><a href="Menu/grades.php"><img src="logos/exam@2x.png"><span>Grades</span></a></li>
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
                        <button onclick="window.location.href='logout.php'">Logout</button>
                    </div>
                </div>
            </header>

            <main>
                <div class="stats-row">
                    <div class="stat-card red">
                        <div class="stat-value"><?= $studentCount ?></div>
                        <div class="stat-title">Total Students</div>
                        <div class="more-info">
                            <span>More info</span>
                            <i class="fas fa-circle-info"></i>
                        </div>
                    </div>

                    <div class="stat-card purple">
                        <div class="stat-value"><?= $sectionCount ?></div>
                        <div class="stat-title">Registered Sections</div>
                        <div class="more-info">
                            <span>More info</span>
                            <i class="fas fa-circle-info"></i>
                        </div>
                    </div>

                    <div class="stat-card gold">
                        <div class="stat-value"><?= $subjectCount ?></div>
                        <div class="stat-title">Ongoing Subjects</div>
                        <div class="more-info">
                            <span>More info</span>
                            <i class="fas fa-circle-info"></i>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Summary Section -->
                <div class="dashboard-summary">
                    <div class="summary-panel">
                        <h2>System Overview</h2>
                        <div class="summary-content">
                            <div class="summary-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="item-details">
                                    <h3>Student Management</h3>
                                    <p>Add, edit, and manage student records including contact information and course enrollment.</p>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-users"></i>
                                <div class="item-details">
                                    <h3>Section Organization</h3>
                                    <p>Group students into sections for better organization and class management.</p>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-book"></i>
                                <div class="item-details">
                                    <h3>Subject Tracking</h3>
                                    <p>Manage course details including subject codes, descriptions, and credit units.</p>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-chart-line"></i>
                                <div class="item-details">
                                    <h3>Grade Recording</h3>
                                    <p>Record and calculate student grades across multiple assessment periods.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="upcoming-events">
                        <h2>Upcoming Events & Schedules</h2>
                        <div class="events-list" id="dashboardEvents">
                            <div class="no-events">
                                <i class="fas fa-calendar-plus"></i>
                                <p>No upcoming events found.</p>
                                <a href="Menu/calendar.php" class="add-event-btn">Add Event</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toast notification container -->
                <div id="toast" class="toast"></div>

                <!-- <div class="class-cards">
                    <div class="class-card">
                        <div class="class-header blue">
                            <div class="class-code">WS101</div>
                            <div class="class-dept">CSA</div>
                        </div>
                        <div class="class-schedule">
                            <div>Monday - 7:30am to 10:30am</div>
                            <div>Saturday - 7:30am to 10:30am</div>
                        </div>
                    </div>

                    <div class="class-card">
                        <div class="class-header purple">
                            <div class="class-code">WS101</div>
                            <div class="class-dept">CSA</div>
                        </div>
                        <div class="class-schedule">
                            <div>Monday - 7:30am to 10:30am</div>
                            <div>Saturday - 7:30am to 10:30am</div>
                        </div>
                    </div>

                    <div class="class-card">
                        <div class="class-header green">
                            <div class="class-code">WS101</div>
                            <div class="class-dept">CSA</div>
                        </div>
                        <div class="class-schedule">
                            <div>Monday - 7:30am to 10:30am</div>
                            <div>Saturday - 7:30am to 10:30am</div>
                        </div>
                    </div>

                    <div class="class-card">
                        <div class="class-header red">
                            <div class="class-code">WS101</div>
                            <div class="class-dept">CSA</div>
                        </div>
                        <div class="class-schedule">
                            <div>Monday - 7:30am to 10:30am</div>
                        </div>
                    </div>

                    <div class="class-card">
                        <div class="class-header teal">
                            <div class="class-code">WS101</div>
                            <div class="class-dept">CSA</div>
                        </div>
                        <div class="class-schedule">
                            <div>Monday - 7:30am to 10:30am</div>
                        </div>
                    </div>

                    <div class="class-card">
                        <div class="class-header gold">
                            <div class="class-code">WS101</div>
                            <div class="class-dept">CSA</div>
                        </div>
                        <div class="class-schedule">
                            <div>Monday - 7:30am to 10:30am</div>
                        </div>
                    </div>
                </div> -->
            </main>
        </div>
    </div>

    <script src="JS/script.js"></script>
</body>
</html>