<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Protect Admin Dashboard
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["admin_logged_in"])) {

    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

// Total repairs

$totalQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM repairs
");

$totalRepairs = $totalQuery->fetch_assoc()["total"];


// Pending repairs

$pendingQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM repairs
    WHERE Status = 'Pending'
");

$pendingRepairs = $pendingQuery->fetch_assoc()["total"];


// In progress repairs

$progressQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM repairs
    WHERE Status IN ('Diagnosing', 'In Progress')
");

$progressRepairs = $progressQuery->fetch_assoc()["total"];


// Completed repairs

$completedQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM repairs
    WHERE Status = 'Completed'
");

$completedRepairs = $completedQuery->fetch_assoc()["total"];


/*
|--------------------------------------------------------------------------
| Recent Repairs
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        r.RepairID,
        r.PhoneBrand,
        r.PhoneModel,
        r.IssueDetails,
        r.DateReceived,
        r.EstimatedCost,
        r.Status,

        c.CustomerID,
        c.Name AS CustomerName,
        c.Email AS CustomerEmail,
        c.Phone AS CustomerPhone

    FROM repairs r

    INNER JOIN customers c
        ON r.CustomerID = c.CustomerID

    ORDER BY r.DateReceived DESC

    LIMIT 10
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

    <title>Admin Dashboard | MobiFix</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>


<!-- =========================
     ADMIN HEADER
========================= -->

<header class="admin-header">

    <div class="admin-nav">

        <a
            href="dashboard.php"
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
                href="logout.php"
                class="admin-logout"
            >
                Logout
            </a>

        </div>

    </div>

</header>



<!-- =========================
     DASHBOARD
========================= -->

<main class="admin-main">

    <div class="admin-container">


        <!-- Page Heading -->

        <div class="admin-heading">

            <div>

                <span class="dashboard-label">
                    ADMINISTRATION
                </span>

                <h1>
                    Repair Dashboard
                </h1>

                <p>
                    Monitor and manage all MobiFix repair requests.
                </p>

            </div>

        </div>



        <!-- =========================
             STATISTICS
        ========================= -->

        <section class="admin-stats">


            <div class="stat-card">

                <div class="stat-icon stat-blue">
                    📱
                </div>

                <div>

                    <span>
                        Total Repairs
                    </span>

                    <strong>
                        <?php echo $totalRepairs; ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon stat-yellow">
                    ⏳
                </div>

                <div>

                    <span>
                        Pending
                    </span>

                    <strong>
                        <?php echo $pendingRepairs; ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon stat-purple">
                    🔧
                </div>

                <div>

                    <span>
                        In Progress
                    </span>

                    <strong>
                        <?php echo $progressRepairs; ?>
                    </strong>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon stat-green">
                    ✓
                </div>

                <div>

                    <span>
                        Completed
                    </span>

                    <strong>
                        <?php echo $completedRepairs; ?>
                    </strong>

                </div>

            </div>


        </section>

<!-- =========================
     ADMIN MANAGEMENT
========================= -->

<section class="admin-management">

    <div class="admin-management-card">

        <div class="management-icon">
            🔧
        </div>

        <div class="management-content">

            <span class="dashboard-label">
                SERVICES
            </span>

            <h2>
                Repair Services
            </h2>

            <p>
                Add, edit, remove and manage the repair services
                offered by MobiFix.
            </p>

        </div>

        <div class="management-action">

            <a
                href="../service/index.php"
                class="manage-button"
            >
                Manage Services →
            </a>

        </div>

    </div>

</section>

        <!-- =========================
             REPAIR REQUESTS
        ========================= -->

        <section class="admin-card">


            <div class="admin-card-header">

                <div>

                    <h2>
                        Recent Repair Requests
                    </h2>

                    <p>
                        Latest repair requests submitted by customers.
                    </p>

                </div>

            </div>



            <?php if ($result->num_rows > 0): ?>


                <div class="admin-table-wrapper">

                    <table class="admin-table">


                        <thead>

                            <tr>

                                <th>
                                    Repair
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Device
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Cost
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>



                        <tbody>


                            <?php while ($repair = $result->fetch_assoc()): ?>


                                <tr>


                                    <!-- Repair -->

                                    <td>

                                        <strong>
                                            #<?php
                                            echo $repair["RepairID"];
                                            ?>
                                        </strong>

                                    </td>



                                    <!-- Customer -->

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $repair["CustomerName"]
                                            );
                                            ?>
                                        </strong>

                                        <small>
                                            <?php
                                            echo htmlspecialchars(
                                                $repair["CustomerPhone"]
                                            );
                                            ?>
                                        </small>

                                    </td>



                                    <!-- Device -->

                                    <td>

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $repair["PhoneBrand"]
                                                . " "
                                                . $repair["PhoneModel"]
                                            );
                                            ?>

                                        </strong>

                                        <small>

                                            <?php
                                            echo htmlspecialchars(
                                                $repair["IssueDetails"]
                                            );
                                            ?>

                                        </small>

                                    </td>



                                    <!-- Date -->

                                    <td>

                                        <?php

                                        echo date(
                                            "d M Y",
                                            strtotime(
                                                $repair["DateReceived"]
                                            )
                                        );

                                        ?>

                                    </td>



                                    <!-- Cost -->

                                    <td>

                                        KSh
                                        <?php

                                        echo number_format(
                                            $repair["EstimatedCost"],
                                            2
                                        );

                                        ?>

                                    </td>



                                    <!-- Status -->

                                    <td>

                                        <span
                                            class="repair-status status-<?php

                                            echo strtolower(
                                                str_replace(
                                                    " ",
                                                    "-",
                                                    $repair["Status"]
                                                )
                                            );

                                            ?>"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $repair["Status"]
                                            );

                                            ?>

                                        </span>

                                    </td>



                                    <!-- Action -->

                                    <td>

                                        <a
                                            href="repair.php?id=<?php echo $repair["RepairID"]; ?>"
                                            class="manage-button"
                                        >
                                            Manage
                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        </tbody>


                    </table>

                </div>


            <?php else: ?>


                <div class="admin-empty">

                    <div>
                        📱
                    </div>

                    <h3>
                        No repair requests
                    </h3>

                    <p>
                        Customer repair requests will appear here.
                    </p>

                </div>


            <?php endif; ?>


        </section>


    </div>

</main>


</body>

</html>