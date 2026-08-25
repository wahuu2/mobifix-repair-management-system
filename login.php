<?php

session_start();

require_once "config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        // Find customer by email

        $sql = "
            SELECT
                CustomerID,
                Name,
                Email,
                Address,
                Phone,
                Password
            FROM customers
            WHERE Email = ?
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $customer = $result->fetch_assoc();

            // Verify password

            if (password_verify($password, $customer["Password"])) {

                // Store customer information in session

                $_SESSION["customer_id"] = $customer["CustomerID"];
                $_SESSION["customer_name"] = $customer["Name"];
                $_SESSION["customer_email"] = $customer["Email"];

                // Prevent session fixation

                session_regenerate_id(true);

                // Redirect to customer dashboard

                header("Location: customer/dashboard.php");
                exit;

            } else {

                $error = "Incorrect email or password.";
            }

        } else {

            $error = "Incorrect email or password.";
        }

        $stmt->close();
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

    <title>Login | MobiFix</title>

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
                            Welcome Back
                        </h2>

                        <p>
                            Login to manage your phone repairs.
                        </p>

                    </div>


                    <?php if (!empty($error)): ?>

                        <div class="alert alert-error">

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">

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

                            <label for="password">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="auth-button"
                        >
                            Login
                        </button>

                    </form>


                    <p class="auth-footer-text">

                        Don't have an account?

                        <a href="register.php">
                            Create an account
                        </a>

                    </p>

                </div>

            </div>

        </section>

    </main>

</body>

</html>