<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Protect Customer Page
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["customer_id"])) {

    header("Location: ../login.php");
    exit;
}

$customerID = $_SESSION["customer_id"];
$customerName = $_SESSION["customer_name"];


/*
|--------------------------------------------------------------------------
| Get Repair ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: dashboard.php");
    exit;
}

$repairID = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Get Repair Details
|--------------------------------------------------------------------------
|
| IMPORTANT:
| CustomerID is included in the WHERE clause so a customer
| cannot access another customer's repair by changing the URL.
|
*/

$sql = "
    SELECT

        r.RepairID,
        r.PhoneBrand,
        r.PhoneModel,
        r.IssueDetails,
        r.DateReceived,
        r.EstimatedCost,
        r.TechnicianNotes,
        r.Status,
        r.DateCompleted,

        c.Name AS CustomerName,
        c.Email AS CustomerEmail,
        c.Phone AS CustomerPhone

    FROM repairs r

    INNER JOIN customers c
        ON r.CustomerID = c.CustomerID

    WHERE
        r.RepairID = ?
        AND r.CustomerID = ?

    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $repairID,
    $customerID
);

$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Repair Not Found
|--------------------------------------------------------------------------
*/

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: dashboard.php");
    exit;
}

$repair = $result->fetch_assoc();

$stmt->close();

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
        Repair #<?php echo $repair["RepairID"]; ?> | MobiFix
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<!-- =========================================================
     CUSTOMER HEADER
========================================================= -->

<header class="dashboard-header">

    <div class="dashboard-nav">


        <!-- Logo -->

        <a
            href="dashboard.php"
            class="auth-logo"
        >
            Mobi<span>Fix</span>
        </a>


        <!-- Customer -->

        <div class="dashboard-user">

            <span>
                <?php
                echo htmlspecialchars($customerName);
                ?>
            </span>

            <a href="logout.php">
                Logout
            </a>

        </div>


    </div>

</header>



<!-- =========================================================
     MAIN
========================================================= -->

<main class="dashboard-main">

    <div class="dashboard-container">

        <!-- =====================================================
             PAGE HEADING
        ====================================================== -->

        <div class="dashboard-heading">

            <div>

                <span class="dashboard-label">
                    REPAIR #<?php echo $repair["RepairID"]; ?>
                </span>

                <h1>
                    Repair Details
                </h1>

                <p>
                    Track the progress and details of your phone repair.
                </p>

            </div>

        </div>



        <!-- =====================================================
             STATUS BANNER
        ====================================================== -->

        <section class="repair-status-banner">

            <div>

                <span>
                    CURRENT STATUS
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $repair["Status"]
                    );
                    ?>
                </strong>

            </div>


            <div class="repair-status-large">

                <?php
                echo htmlspecialchars(
                    $repair["Status"]
                );
                ?>

            </div>

        </section>



        <!-- =====================================================
             REPAIR INFORMATION GRID
        ====================================================== -->

        <div class="repair-details-grid">


            <!-- =================================================
                 DEVICE INFORMATION
            ================================================== -->

            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2>
                            Device Information
                        </h2>

                        <p>
                            Details about the phone submitted for repair.
                        </p>

                    </div>

                </div>


                <div class="repair-detail-content">


                    <div class="repair-device">

                        <div class="repair-device-icon">
                            📱
                        </div>

                        <div>

                            <span>
                                Device
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $repair["PhoneBrand"]
                                    . " "
                                    . $repair["PhoneModel"]
                                );

                                ?>

                            </strong>

                        </div>

                    </div>



                    <div class="repair-detail-item">

                        <span>
                            Problem Reported
                        </span>

                        <p>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $repair["IssueDetails"]
                                )
                            );

                            ?>

                        </p>

                    </div>



                    <div class="repair-detail-item">

                        <span>
                            Date Received
                        </span>

                        <strong>

                            <?php

                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $repair["DateReceived"]
                                )
                            );

                            ?>

                        </strong>

                    </div>


                </div>

            </section>



            <!-- =================================================
                 COST & COMPLETION
            ================================================== -->

            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2>
                            Repair Summary
                        </h2>

                        <p>
                            Current repair cost and completion information.
                        </p>

                    </div>

                </div>


                <div class="repair-detail-content">


                    <div class="repair-summary-price">

                        <span>
                            Estimated Cost
                        </span>

                        <strong>

                            KSh
                            <?php

                            echo number_format(
                                $repair["EstimatedCost"],
                                2
                            );

                            ?>

                        </strong>

                    </div>



                    <div class="repair-detail-item">

                        <span>
                            Current Status
                        </span>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $repair["Status"]
                            );

                            ?>

                        </strong>

                    </div>



                    <?php if (!empty($repair["DateCompleted"])): ?>

                        <div class="repair-detail-item">

                            <span>
                                Date Completed
                            </span>

                            <strong>

                                <?php

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $repair["DateCompleted"]
                                    )
                                );

                                ?>

                            </strong>

                        </div>

                    <?php endif; ?>


                </div>

            </section>


        </div>



        <!-- =====================================================
             TECHNICIAN NOTES
        ====================================================== -->

        <section class="dashboard-card repair-notes-card">

            <div class="dashboard-card-header">

                <div>

                    <h2>
                        Technician Notes
                    </h2>

                    <p>
                        Updates provided by the MobiFix technician.
                    </p>

                </div>

            </div>


            <div class="technician-notes">

                <?php if (!empty($repair["TechnicianNotes"])): ?>

                    <p>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $repair["TechnicianNotes"]
                            )
                        );

                        ?>

                    </p>

                <?php else: ?>

                    <div class="notes-empty">

                        <span>
                            ⓘ
                        </span>

                        <p>
                            No technician notes have been added yet.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>



        <!-- =====================================================
             REPAIR PROGRESS
        ====================================================== -->

        <section class="dashboard-card repair-progress-card">

            <div class="dashboard-card-header">

                <div>

                    <h2>
                        Repair Progress
                    </h2>

                    <p>
                        Follow the progress of your repair.
                    </p>

                </div>

            </div>


            <?php

            $progressSteps = [
                "Pending",
                "Diagnosing",
                "In Progress",
                "Ready for Collection",
                "Completed"
            ];

            $currentStatus = $repair["Status"];

            $currentIndex = array_search(
                $currentStatus,
                $progressSteps
            );

            ?>


            <div class="repair-progress">


                <?php foreach ($progressSteps as $index => $step): ?>

                    <?php

                    $isCompleted = false;
                    $isCurrent = false;

                    if ($currentIndex !== false) {

                        if ($index < $currentIndex) {
                            $isCompleted = true;
                        }

                        if ($index === $currentIndex) {
                            $isCurrent = true;
                        }

                    }

                    ?>


                    <div
                        class="
                            progress-step
                            <?php
                            echo $isCompleted
                                ? "completed"
                                : "";
                            ?>
                            <?php
                            echo $isCurrent
                                ? "current"
                                : "";
                            ?>
                        "
                    >


                        <div class="progress-dot">

                            <?php if ($isCompleted): ?>

                                ✓

                            <?php elseif ($isCurrent): ?>

                                ●

                            <?php else: ?>

                                <?php echo $index + 1; ?>

                            <?php endif; ?>

                        </div>


                        <div class="progress-info">

                            <strong>
                                <?php echo $step; ?>
                            </strong>

                            <?php if ($isCurrent): ?>

                                <span>
                                    Current stage
                                </span>

                            <?php elseif ($isCompleted): ?>

                                <span>
                                    Completed
                                </span>

                            <?php endif; ?>

                        </div>


                    </div>


                <?php endforeach; ?>


            </div>

        </section>



        <!-- =====================================================
             CANCELLED NOTICE
        ====================================================== -->

        <?php if ($repair["Status"] === "Cancelled"): ?>

            <section class="repair-cancelled">

                <strong>
                    Repair Cancelled
                </strong>

                <p>
                    This repair request has been cancelled.
                    Please contact MobiFix if you need more information.
                </p>

            </section>

        <?php endif; ?>



        <!-- =====================================================
             BACK BUTTON
        ====================================================== -->

        <div class="repair-details-actions">

            <a
                href="dashboard.php"
                class="auth-button dashboard-button"
            >
                ← Back to My Repairs
            </a>

        </div>


    </div>

</main>


</body>

</html>