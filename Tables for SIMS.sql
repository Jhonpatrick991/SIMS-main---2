CREATE TABLE IF NOT EXISTS Students (
    StudentNumber VARCHAR(20) PRIMARY KEY,
    StudentName VARCHAR(100) NOT NULL,
    SectionName VARCHAR(100),
    SubjectCode VARCHAR(20),
    Email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Subjects (
    SubjectId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    SubjectCode VARCHAR(20) UNIQUE,
    Unit INT NOT NULL,
    SubjectName VARCHAR(255) NOT NULL,
    TotalSections INT NOT NULL DEFAULT 0,
    StudentsEnrolled INT DEFAULT 0,
    Time VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS Sections (
    SectionId INT PRIMARY KEY AUTO_INCREMENT,
    SectionName VARCHAR(100) UNIQUE NOT NULL,
    StudentsEnrolled INT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS Grades (
    StudentNumber VARCHAR(20) NOT NULL,
    SubjectCode VARCHAR(20) NOT NULL,
    Semester VARCHAR(10) NOT NULL,
    Prelim DECIMAL(5,2) DEFAULT NULL,
    Midterm DECIMAL(5,2) DEFAULT NULL,
    SemiFinal DECIMAL(5,2) DEFAULT NULL,
    Final DECIMAL(5,2) DEFAULT NULL
);




    -- PRIMARY KEY (StudentNumber, SubjectCode),
    -- FOREIGN KEY (StudentNumber) REFERENCES Students(StudentNumber),
    -- FOREIGN KEY (SubjectCode) REFERENCES Subjects(SubjectCode)


-- CREATE TABLE IF NOT EXISTS StudentSubjects (
--     StudentNumber VARCHAR(20),
--     SubjectCode VARCHAR(20),
--     PRIMARY KEY (StudentNumber, SubjectCode),
--     FOREIGN KEY (StudentNumber) REFERENCES Students(StudentNumber),
--     FOREIGN KEY (SubjectCode) REFERENCES Subjects(SubjectCode)
-- );
