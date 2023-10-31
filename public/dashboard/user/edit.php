<?php
require_once("../../header/header.php");
require_once("../../functions.php");

// Check if the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['loggedin'])) {
    header('Location:' . BASE_URL . "/login/index.php");
    exit();
}

$id = $_GET["id"];

// Main code
try {
    $data = readDatabaseFile(DB_FILE_PATH);
    $item = findDataById($data, $id);

    if (!$item) {
        header("Location:" . BASE_URL . "/dashboard/index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["update"])) {
        $username   = validatedInput($_POST['username']);
        $email      = validatedInput($_POST['email']);
        $role       = validatedInput($_POST['role']);
        $password   = validatedInput($_POST['password']);

        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("All fields are required.");
        }

        validateUsername($username);
        validateEmail($email);
        validatePassword($password);

        // Update the data
        foreach ($data as $key => &$item) {
            if ($item["id"] == $id) {
                $item = [
                    'id'        => $id,
                    'username'  => strtolower($username),
                    'email'     => $email,
                    'role'      => $role,
                    'password'  => password_hash($password, PASSWORD_DEFAULT),
                ];
                break;
            }
        }

        writeDatabaseFile(DB_FILE_PATH, $data);
        $successMessage = 'Update Successfully.';
        header("location:" . BASE_URL . "/dashboard/index.php?success=" . urlencode($successMessage));
        exit();
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}

?>

<!-- Section: Design Block -->
<section class="text-center">
    <div class="container">

        <!-- Background image -->

        <div class="card mx-4 mx-md-5 shadow-5-strong">
            <div class="card-body py-5 px-md-5">

                <div class="row d-flex justify-content-center">
                    <div class="col-lg-6">
                        <h2 class="fw-bold mb-5">Edit User</h2>
                        <form
                            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>/?id=<?php echo $id; ?>"
                            method="POST">
                            <?php if (isset($errorMessage)) :  ?>
                            <div class="alert alert-danger">
                                <?php echo $errorMessage; ?>
                            </div>
                            <?php endif; ?>
                            <div class="form-outline mb-4">
                                <input type="text" name="username" id="userName" class="form-control"
                                    value="<?php echo $item['username']; ?>" />
                                <label class="form-label" for="userName">Your Userame</label>
                            </div>
                            <!-- Email input -->
                            <div class="form-outline mb-4">
                                <input type="email" name="email" id="email3c" class="form-control"
                                    value="<?php echo $item['email']; ?>" />
                                <label class="form-label" for="email3c">Your Email</label>
                            </div>

                            <!-- Role input -->
                            <select class="browser-default form-select mb-4" name="role">
                                <option value="admin" <?php echo ($item['role'] == 'admin') ? "selected" : ""  ?>>Admin
                                </option>
                                <option value="manager" <?php echo ($item['role'] == 'manager') ? "selected" : "" ?>>Manager
                                </option>
                                <option value="user" <?php echo ($item['role'] == 'user') ? "selected" : "" ?>>User
                                </option>
                            </select>

                            <!-- Password input -->
                            <div class="form-outline mb-4">
                                <input type="password" name="password" id="form3Example4cd" class="form-control" />
                                <label class="form-label" for="form3Example4cd">Enter your password</label>
                            </div>


                            <!-- Submit button -->
                            <button type="submit" name="update" class="btn btn-primary btn-block mb-4">
                                Update
                            </button>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section: Design Block -->

<?php require_once("../../header/footer.php"); ?>