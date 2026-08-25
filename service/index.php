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


/*
|--------------------------------------------------------------------------
| Get Services
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        ServiceID,
        ServiceName,
        Description,
        Price,
        AvailabilityStatus
    FROM repair_services
    ORDER BY ServiceID DESC
";

$result = $conn->query($sql);

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
        Services | MobiFix
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
            href="../admin/dashboard.php"
            class="back-link"
        >
            ← Back to Dashboard
        </a>



        <!-- Heading -->

        <div class="admin-heading">

            <div>

                <span class="dashboard-label">
                    SERVICE MANAGEMENT
                </span>

                <h1>
                    Repair Services
                </h1>

                <p>
                    Manage the repair services offered by MobiFix.
                </p>

            </div>


            <a
                href="add.php"
                class="auth-button dashboard-button"
            >
                + Add Service
            </a>

        </div>



        <!-- Services -->

        <section class="dashboard-card">

            <div class="dashboard-card-header">

                <div>

                    <h2>
                        All Services
                    </h2>

                    <p>
                        View and manage your available repair services.
                    </p>

                </div>

            </div>


            <?php if ($result && $result->num_rows > 0): ?>

                <div class="repair-table-wrapper">

                    <table class="repair-table">

                        <thead>

                            <tr>

                                <th>
                                    Service
                                </th>

                                <th>
                                    Description
                                </th>

                                <th>
                                    Price
                                </th>

                                <th>
                                    Availability
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php while ($service = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $service["ServiceName"]
                                            );
                                            ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <small>
                                            <?php
                                            echo htmlspecialchars(
                                                $service["Description"]
                                            );
                                            ?>
                                        </small>

                                    </td>


                                    <td>

                                        <strong>
                                            KSh
                                            <?php
                                            echo number_format(
                                                $service["Price"],
                                                2
                                            );
                                            ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <span
                                            class="repair-status
                                            <?php
                                            echo $service["AvailabilityStatus"] === "Available"
                                                ? "status-completed"
                                                : "status-cancelled";
                                            ?>"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $service["AvailabilityStatus"]
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <div class="table-actions">

                                            <a
                                                href="edit.php?id=<?php echo $service["ServiceID"]; ?>"
                                                class="action-link action-edit"
                                            >
                                                Edit
                                            </a>


                                            <a
                                                href="delete.php?id=<?php echo $service["ServiceID"]; ?>"
                                                class="action-link action-delete"
                                                onclick="return confirm('Are you sure you want to delete this service?');"
                                            >
                                                Delete
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>


                <!-- Empty State -->

                <div class="dashboard-empty">

                    <div class="empty-icon">
                        🔧
                    </div>

                    <h3>
                        No services yet
                    </h3>

                    <p>
                        You haven't added any repair services.
                    </p>

                    <a
                        href="add.php"
                        class="auth-button dashboard-button"
                    >
                        Add Your First Service
                    </a>

                </div>


            <?php endif; ?>


        </section>


    </div>

</main>


</body>

</html>