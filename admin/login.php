<?php

session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        /*
         * Temporary admin credentials.
         *
         * We will move these into an admins table
         * when we build the final admin database structure.
         */

        $adminEmail = "admin@mobifix.com";
        $adminPassword = "Admin@123";

        if (
            $email === $adminEmail &&
            $password === $adminPassword
        ) {

            session_regenerate_id(true);

            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_email"] = $adminEmail;

            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Incorrect admin email or password.";
        }
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

    <title>Admin Login | MobiFix</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
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

                        <span class="dashboard-label">
                            ADMIN PORTAL
                        </span>

                        <h2>
                            Admin Login
                        </h2>

                        <p>
                            Sign in to manage customer repairs.
                        </p>

                    </div>


                    <?php if (!empty($error)): ?>

                        <div class="alert alert-error">

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">

                        <div class="form-group">

                            <label for="email">
                                Admin Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="admin@mobifix.com"
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
                                placeholder="Enter admin password"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="auth-button"
                        >
                            Login to Admin Portal
                        </button>

                    </form>


                    <p class="auth-footer-text">

                        <a href="../index.php">
                            ← Back to MobiFix
                        </a>

                    </p>

                </div>

            </div>

        </section>

    </main>

</body>

</html>