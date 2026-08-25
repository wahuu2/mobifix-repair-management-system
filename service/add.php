<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Protect Admin Page
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: ../admin/login.php");
    exit;
}

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Handle Form Submission
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serviceName = trim($_POST["service_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $availability = trim($_POST["availability"] ?? "Available");


    /*
    |----------------------------------------------------------------------
    | Validation
    |----------------------------------------------------------------------
    */

    if (
        empty($serviceName) ||
        empty($price)
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!is_numeric($price) || $price < 0) {

        $error = "Please enter a valid service price.";

    } elseif (
        !in_array(
            $availability,
            ["Available", "Unavailable"],
            true
        )
    ) {

        $error = "Invalid availability status.";

    } else {

        /*
        |------------------------------------------------------------------
        | Insert Service
        |------------------------------------------------------------------
        */

        $sql = "
            INSERT INTO repair_services
            (
                ServiceName,
                Description,
                Price,
                AvailabilityStatus
            )
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $servicePrice = (float) $price;

        $stmt->bind_param(
            "ssds",
            $serviceName,
            $description,
            $servicePrice,
            $availability
        );

        if ($stmt->execute()) {

            $success = "Repair service added successfully.";

            // Clear form after successful submission
            $serviceName = "";
            $description = "";
            $price = "";
            $availability = "Available";

        } else {

            $error = "Unable to add the repair service.";

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

    <title>
        Add Service | MobiFix
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<!-- =========================================================
     ADMIN HEADER
========================================================= -->

<header class="admin-header">

    <div class="admin-nav">

        <a
            href="../admin/dashboard.php"
            class="auth-logo"
        >
            Mobi<span>Fix</span>
        </a>


        <div class="admin-user">

            <div class="admin-user-info">

                <span class="admin-role">
                    ADMIN
                </span>

                <span>
                    <?php
                    echo htmlspecialchars(
                        $_SESSION["admin_email"]
                    );
                    ?>
                </span>

            </div>


            <a
                href="../admin/logout.php"
                class="admin-logout"
            >
                Logout
            </a>

        </div>

    </div>

</header>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="admin-main">

    <div class="admin-container">


        <!-- Back -->

        <a
            href="index.php"
            class="back-link"
        >
            ← Back to Services
        </a>



        <!-- Heading -->

        <div class="admin-heading">

            <div>

                <span class="dashboard-label">
                    SERVICE MANAGEMENT
                </span>

                <h1>
                    Add Repair Service
                </h1>

                <p>
                    Add a new repair service offered by MobiFix.
                </p>

            </div>

        </div>



        <!-- Alerts -->

        <?php if (!empty($error)): ?>

            <div class="alert alert-error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($success)): ?>

            <div class="alert alert-success">

                <?php
                echo htmlspecialchars($success);
                ?>

            </div>

        <?php endif; ?>



        <!-- Form -->

        <section class="dashboard-card service-form-card">

            <div class="dashboard-card-header">

                <div>

                    <h2>
                        Service Information
                    </h2>

                    <p>
                        Enter the details of the repair service.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action=""
                class="service-form"
            >


                <!-- Service Name -->

                <div class="form-group">

                    <label for="service_name">
                        Service Name
                    </label>

                    <input
                        type="text"
                        id="service_name"
                        name="service_name"
                        placeholder="e.g. Screen Replacement"
                        value="<?php echo htmlspecialchars($serviceName ?? ""); ?>"
                        required
                    >

                </div>



                <!-- Description -->

                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Describe what this service includes..."
                    ><?php
                    echo htmlspecialchars(
                        $description ?? ""
                    );
                    ?></textarea>

                </div>



                <!-- Price -->

                <div class="form-group">

                    <label for="price">
                        Service Price (KSh)
                    </label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        value="<?php echo htmlspecialchars($price ?? ""); ?>"
                        required
                    >

                </div>



                <!-- Availability -->

                <div class="form-group">

                    <label for="availability">
                        Availability
                    </label>

                    <select
                        id="availability"
                        name="availability"
                        required
                    >

                        <option
                            value="Available"
                            <?php
                            echo ($availability ?? "Available") === "Available"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Available
                        </option>

                        <option
                            value="Unavailable"
                            <?php
                            echo ($availability ?? "") === "Unavailable"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Unavailable
                        </option>

                    </select>

                </div>



                <!-- Actions -->

                <div class="service-form-actions">

                    <a
                        href="index.php"
                        class="secondary-button"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="auth-button"
                    >
                        Add Service
                    </button>

                </div>


            </form>

        </section>


    </div>

</main>


</body>

</html>