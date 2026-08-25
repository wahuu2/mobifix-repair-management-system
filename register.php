<?php

require_once "config/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if (
        empty($name) ||
        empty($email) ||
        empty($phone) ||
        empty($address) ||
        empty($password) ||
        empty($confirmPassword)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    } elseif ($password !== $confirmPassword) {

        $error = "Passwords do not match.";

    } else {

        // Check whether email already exists

        $checkSql = "SELECT CustomerID FROM customers WHERE Email = ?";

        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $error = "An account with this email already exists.";

        } else {

            // Securely hash password

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert customer

            $sql = "
                INSERT INTO customers
                (Name, Email, Address, Phone, Password)
                VALUES (?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $address,
                $phone,
                $hashedPassword
            );

            if ($stmt->execute()) {

                $success =
                    "Account created successfully. You can now log in.";

            } else {

                $error =
                    "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $checkStmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Account | MobiFix</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

    <main>

        <section class="auth-section">

            <div class="auth-container">

                <div class="auth-card">

                    <div class="auth-card-header">

                        <div class="auth-logo">
                            Mobi<span>Fix</span>
                        </div>

                        <h2>
                            Create Your Account
                        </h2>

                        <p>
                            Register to book and track your phone repairs.
                        </p>

                    </div>


                    <?php if (!empty($error)): ?>

                        <div class="alert alert-error">

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <?php if (!empty($success)): ?>

                        <div class="alert alert-success">

                            <?php echo htmlspecialchars($success); ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">

                        <div class="form-group">

                            <label for="name">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                placeholder="Enter your full name"
                                value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="email">
                                Email Address
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="you@example.com"
                                value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="phone">
                                Phone Number
                            </label>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                placeholder="+254 700 000 000"
                                value="<?php echo htmlspecialchars($_POST["phone"] ?? ""); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="address">
                                Address
                            </label>

                            <input
                                type="text"
                                id="address"
                                name="address"
                                placeholder="Enter your address"
                                value="<?php echo htmlspecialchars($_POST["address"] ?? ""); ?>"
                                required
                            >

                        </div>


                        <div class="form-row">

                            <div class="form-group">

                                <label for="password">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Minimum 6 characters"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="confirm_password">
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    placeholder="Repeat password"
                                    required
                                >

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="auth-button"
                        >
                            Create Account
                        </button>

                    </form>


                    <p class="auth-footer-text">

                        Already have an account?

                        <a href="login.php">
                            Login
                        </a>

                    </p>

                </div>

            </div>

        </section>

    </main>

</body>

</html>