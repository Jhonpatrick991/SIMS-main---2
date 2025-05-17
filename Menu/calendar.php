<?php
require("../connect.php");
if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Teacher Dashboard</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/calendar.css">
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
                    <li class="active"><a href="../Menu/calendar.php"><img src="../logos/calendar.png"> <span>Calendar</span></a></li>
                    <li><a href="../Menu/students.php"><img src="../logos/graduation.png"><span>Students</span></a></li>
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

            <main class="calendar-main">
                <!-- Calendar Container (Left Side) -->
                <div class="calendar-container">
                    <!-- Weekly Calendar -->
                    <div class="weekly-calendar" id="weeklyCalendar"></div>
                    
                    <!-- Monthly Calendar -->
                    <div class="monthly-calendar-main">
                        <div class="monthly-header">
                            <h3 id="currentMonthTitle"></h3>
                            <div class="month-controls">
                                <select id="monthSelect">
                                    <option value="0">January</option>
                                    <option value="1">February</option>
                                    <option value="2">March</option>
                                    <option value="3">April</option>
                                    <option value="4">May</option>
                                    <option value="5">June</option>
                                    <option value="6">July</option>
                                    <option value="7">August</option>
                                    <option value="8">September</option>
                                    <option value="9">October</option>
                                    <option value="10">November</option>
                                    <option value="11">December</option>
                                </select>
                                <select id="yearSelect"></select>
                            </div>
                        </div>
                        <div class="monthly-calendar">
                            <table class="month-table">
                                <thead>
                                    <tr>
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th>Sat</th>
                                        <th>Sun</th>
                                    </tr>
                                </thead>
                                <tbody id="monthBody"></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Calendar Info Section -->
                    <div class="calendar-info-section">
                        <div class="calendar-info-header">
                            Calendar Statistics
                        </div>
                        <div class="calendar-stats">
                            <div class="stat-card">
                                <div class="stat-title">Total Schedules</div>
                                <div class="stat-value" id="totalSchedules">0</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-title">This Month</div>
                                <div class="stat-value" id="monthSchedules">0</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-title">Upcoming</div>
                                <div class="stat-value" id="upcomingSchedules">0</div>
                            </div>
                        </div>
                        <div class="task-breakdown">
                            <h4>Schedule Categories</h4>
                            <div class="task-category">
                                <div class="category-color" style="background-color: #4a5af4;"></div>
                                <div class="category-name">Exams</div>
                                <div class="category-count" id="examsCount">0</div>
                            </div>
                            <div class="task-category">
                                <div class="category-color" style="background-color: #38b2ac;"></div>
                                <div class="category-name">Meetings</div>
                                <div class="category-count" id="meetingsCount">0</div>
                            </div>
                            <div class="task-category">
                                <div class="category-color" style="background-color: #ed8936;"></div>
                                <div class="category-name">Classes</div>
                                <div class="category-count" id="classesCount">0</div>
                            </div>
                            <div class="task-category">
                                <div class="category-color" style="background-color: #9f7aea;"></div>
                                <div class="category-name">Other</div>
                                <div class="category-count" id="otherCount">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Schedule List Container (Right Side) -->
                <div class="schedule-list-container">
                    <div class="schedule-list-title">
                        Your Schedules
                    </div>
                    <div class="schedule-list" id="scheduleList">
                        <!-- Schedule items will be dynamically populated here -->
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Enhanced Event Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Schedule</h2>
            
            <div class="form-group">
                <label for="eventTitle">Title</label>
                <input type="text" id="eventTitle" placeholder="Enter schedule title...">
            </div>
            
            <div class="form-group">
                <label for="eventCategory">Category</label>
                <select id="eventCategory">
                    <option value="exams">Exam</option>
                    <option value="meetings">Meeting</option>
                    <option value="classes">Class</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="eventTime">Time</label>
                <input type="time" id="eventTime">
            </div>
            
            <div class="form-group">
                <label for="eventDescription">Description</label>
                <textarea id="eventDescription" placeholder="Enter description..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button id="deleteEventBtn" class="btn btn-danger">Delete</button>
                <button id="cancelEventBtn" class="btn btn-secondary">Cancel</button>
                <button id="saveEventBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    
    <!-- Date Selector Modal -->
    <div id="dateSelectorModal" class="modal date-selector-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Select Date</h2>
            
            <div class="month-year-selector">
                <select id="modalMonthSelect">
                    <option value="0">January</option>
                    <option value="1">February</option>
                    <option value="2">March</option>
                    <option value="3">April</option>
                    <option value="4">May</option>
                    <option value="5">June</option>
                    <option value="6">July</option>
                    <option value="7">August</option>
                    <option value="8">September</option>
                    <option value="9">October</option>
                    <option value="10">November</option>
                    <option value="11">December</option>
                </select>
                <select id="modalYearSelect"></select>
            </div>
            
            <div class="modal-calendar">
                <table class="month-table">
                    <thead>
                        <tr>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                            <th>Sun</th>
                        </tr>
                    </thead>
                    <tbody id="modalCalendarBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../JS/script.js"></script>
    <script src="../JS/calendar.js"></script>
</body>
</html>
