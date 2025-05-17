<?php
include("../connect.php");
$SectionName = "";

$conn = new mysqli("localhost", "sims", "sims", "sims");

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $SectionName = $_POST['SectionName'];

    do {
        if (empty($SectionName)) {
            $errorMessage = "All fields are required";
            break;
        }
        try {
            $con = "INSERT INTO sections (SectionName)
                VALUES ('$SectionName')";
            $result = $conn->query($con);
        } catch (\Exception $e) {
            $errorMessage = "Invalid Query: " . $conn->error;
            break;
        }

        $successMessage = "Section created successfully";

        header("Location: ../Menu/sections.php");
        exit();
    } while (false);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Section</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container my-5">
            <h2>New Section</h2>

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
                    <label class="col-sm-3 col-form-label">Section Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="SectionName" value="<?php echo $SectionName; ?>">
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
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='../Menu/sections.php';">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>