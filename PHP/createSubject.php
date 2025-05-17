<?php
include("../connect.php");
$SubjectCode = "";
$Unit = "";
$SubjectName = "";
$TotalSections = "";
$StudentsEnrolled = "";
$Time = "";

$conn = new mysqli("localhost", "sims", "sims", "sims");

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $SubjectCode = $_POST['SubjectCode'];
    $Unit = $_POST['Unit'];
    $SubjectName = $_POST['SubjectName'];
    $Time = $_POST['Time'];

    do {
        if (
            empty($SubjectCode) || empty($Unit) || empty($SubjectName) || empty($Time)
        ) {
            $errorMessage = "All fields are required";
            break;
        }
        try {
            $con = "INSERT INTO subjects (SubjectCode, Unit, SubjectName, TotalSections, StudentsEnrolled, Time)
                VALUES ('$SubjectCode', '$Unit', '$SubjectName', '$TotalSections', '$StudentsEnrolled', '$Time')";
            $result = $conn->query($con);
        } catch (\Exception $e) {
            $errorMessage = "Invalid Query: " . $conn->error;
            break;
        }

        $successMessage = "Subject created successfully";

        header("Location: ../Menu/subjects.php");
        exit();
    } while (false);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Subject</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container my-5">
            <h2>New Subject</h2>

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
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Code</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="SubjectCode" value="<?php echo $SubjectCode; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Unit</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" name="Unit" value="<?php echo $Unit; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Subject Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="SubjectName" value="<?php echo $SubjectName; ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Time</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="Time" value="<?php echo $Time; ?>">
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

                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                    <div class="col-sm-3 d-grid">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='../Menu/subjects.php';">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>