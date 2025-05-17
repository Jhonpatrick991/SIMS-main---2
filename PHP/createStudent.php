<?php
include("../connect.php");
$StudentNumber = "";
$Name = "";
$SectionName = "";       
$Email = "";
$SubjectCode = "";

$conn = new mysqli("localhost", "sims", "sims", "sims");

$errorMessage = "";
$successMessage = "";

$subjectCodes = [];
$sectionNames = [];
$sectionResult = $conn->query("SELECT SectionName FROM sections");
if ($sectionResult && $sectionResult->num_rows > 0) {
    while ($row = $sectionResult->fetch_assoc()) {
        $sectionNames[] = $row['SectionName'];
    }
}
$subjectResult = $conn->query("SELECT SubjectCode FROM subjects");
if ($subjectResult && $subjectResult->num_rows > 0) {
    while ($row = $subjectResult->fetch_assoc()) {
        $subjectCodes[] = $row['SubjectCode'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $StudentNumber = $_POST['StudentNumber'];
    $Name = $_POST['Name'];
    $SectionName = $_POST['SectionName'];
    $Email = $_POST['Email'];
    $SubjectCode = $_POST['SubjectCode'];

    do {
        if (empty($StudentNumber) || empty($Name) || empty($SectionName) || empty($Email) || empty($SubjectCode)) {
            $errorMessage = "All fields are required";
            break;
        }
          try {
                $con = "INSERT INTO students (StudentNumber, StudentName, SectionName, Email, SubjectCode)
                    VALUES ('$StudentNumber', '$Name', '$SectionName', '$Email', '$SubjectCode')"; 
                $con ="INSERT INTO grades (StudentNumber, SubjectCode)
                    VALUES ('$StudentNumber', '$SubjectCode')";
                $result = $conn->query($con);
            } catch (\Exception $e) {
                $errorMessage = "Invalid Query: " . $conn->error;
                break;
            };

        $successMessage = "Student created successfully";
        
        header("Location: ../Menu/students.php");
        exit();
    } while (false);

}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <!-- bootstrap link for js and css -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container my-5">
            <h2>New Student</h2>

            <?php
            if (!empty($errorMessage)) {
                echo "
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <strong>$errorMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>


            <form method="POST">
                <!-- For each input -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Student Number</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="StudentNumber" value="<?php echo $StudentNumber; ?>">
                    </div>
                </div>    
                <!-- End of an input -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="Name" value="<?php echo $Name; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Section Name</label>
                    <div class="col-sm-6">
                        <select class="form-control" name="SectionName" required>
                            <option value="">Select Section Name</option>
                                <?php foreach ($sectionNames as $new): ?>
                                <option value="<?= htmlspecialchars($new) ?>" <?= $SectionName == $new ? 'selected' : '' ?>>
                                <?= htmlspecialchars($new) ?>
                            </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Subject Code</label>
                    <div class="col-sm-6">
                        <select class="form-control" name="SubjectCode" required>
                            <option value="">Select Subject Code</option>
                                <?php foreach ($subjectCodes as $code): ?>
                                <option value="<?= htmlspecialchars($code) ?>" <?= $SubjectCode == $code ? 'selected' : '' ?>>
                                <?= htmlspecialchars($code) ?>
                            </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="Email" value="<?php echo $Email; ?>">
                    </div>
                </div>

                <?php
                if (!empty($successMessage)) {
                    echo "
                    <div class='row mb-3'>
                        <div class='offset-sm-3 col-sm-6'>
                            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <strong>$successMessage</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        </div>
                    </div>";
                }
                ?>

                <!-- For the Submit and Cancel form -->
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                    <div class="col-sm-3 d-grid">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='students.php';">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>