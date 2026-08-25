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


/*
|--------------------------------------------------------------------------
| Get Payments For This Repair
|--------------------------------------------------------------------------
*/

$paymentSql = "
    SELECT

        PaymentID,
        Amount,
        PaymentMethod,
        PaymentStatus,
        TransactionReference,
        PaymentDate

    FROM payments

    WHERE RepairID = ?

    ORDER BY PaymentDate DESC
";

$paymentStmt = $conn->prepare($paymentSql);

$paymentStmt->bind_param(
    "i",
    $repairID
);

$paymentStmt->execute();

$paymentResult = $paymentStmt->get_result();


/*
|--------------------------------------------------------------------------
| Calculate Total Paid
|--------------------------------------------------------------------------
*/

$totalPaid = 0;

$payments = [];

while ($payment = $paymentResult->fetch_assoc()) {

    $payments[] = $payment;

    if ($payment["PaymentStatus"] === "Paid") {

        $totalPaid += (float) $payment["Amount"];
    }
}

$paymentStmt->close();


/*
|--------------------------------------------------------------------------
| Calculate Outstanding Balance
|--------------------------------------------------------------------------
*/

$estimatedCost = (float) $repair["EstimatedCost"];

$outstandingBalance = max(
    0,
    $estimatedCost - $totalPaid
);

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

                echo htmlspecialchars(
                    $customerName
                );

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
             BACK LINK
        ====================================================== -->

        <a
            href="dashboard.php"
            class="back-link"
        >
            ← Back to Dashboard
        </a>



        <!-- =====================================================
             PAGE HEADING
        ====================================================== -->

        <div class="dashboard-heading">

            <div>

                <span class="dashboard-label">

                    REPAIR #<?php
                    echo $repair["RepairID"];
                    ?>

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


                    <!-- Device -->

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



                    <!-- Problem -->

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



                    <!-- Date Received -->

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
                 REPAIR SUMMARY
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


                    <!-- Estimated Cost -->

                    <div class="repair-summary-price">

                        <span>
                            Estimated Cost
                        </span>

                        <strong>

                            KSh
                            <?php

                            echo number_format(
                                $estimatedCost,
                                2
                            );

                            ?>

                        </strong>

                    </div>



                    <!-- Status -->

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



                    <!-- Total Paid -->

                    <div class="repair-detail-item">

                        <span>
                            Total Paid
                        </span>

                        <strong>

                            KSh
                            <?php

                            echo number_format(
                                $totalPaid,
                                2
                            );

                            ?>

                        </strong>

                    </div>



                    <!-- Outstanding -->

                    <div class="repair-detail-item">

                        <span>
                            Outstanding Balance
                        </span>

                        <strong>

                            KSh
                            <?php

                            echo number_format(
                                $outstandingBalance,
                                2
                            );

                            ?>

                        </strong>

                    </div>



                    <!-- Date Completed -->

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
             PAYMENT INFORMATION
        ====================================================== -->

        <section class="dashboard-card repair-payments-card">

            <div class="dashboard-card-header">

                <div>

                    <h2>
                        Payment Information
                    </h2>

                    <p>
                        View payments recorded for this repair.
                    </p>

                </div>

            </div>


            <?php if (count($payments) > 0): ?>


                <div class="payment-summary">


                    <!-- Payment Records -->

                    <?php foreach ($payments as $payment): ?>


                        <div class="payment-item">


                            <div class="payment-item-main">

                                <div class="payment-icon">
                                    💳
                                </div>


                                <div>

                                    <strong>

                                        KSh
                                        <?php

                                        echo number_format(
                                            $payment["Amount"],
                                            2
                                        );

                                        ?>

                                    </strong>

                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $payment["PaymentMethod"]
                                        );

                                        ?>

                                    </span>

                                </div>

                            </div>



                            <div class="payment-item-details">


                                <span
                                    class="payment-status payment-status-<?php

                                    echo strtolower(
                                        $payment["PaymentStatus"]
                                    );

                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $payment["PaymentStatus"]
                                    );

                                    ?>

                                </span>



                                <span>

                                    <?php

                                    echo date(
                                        "d M Y, h:i A",
                                        strtotime(
                                            $payment["PaymentDate"]
                                        )
                                    );

                                    ?>

                                </span>


                                <?php if (!empty($payment["TransactionReference"])): ?>

                                    <small>

                                        Ref:
                                        <?php

                                        echo htmlspecialchars(
                                            $payment["TransactionReference"]
                                        );

                                        ?>

                                    </small>

                                <?php endif; ?>


                            </div>


                        </div>


                    <?php endforeach; ?>



                    <!-- Payment Total -->

                    <div class="payment-total">

                        <span>
                            Total Paid
                        </span>

                        <strong>

                            KSh
                            <?php

                            echo number_format(
                                $totalPaid,
                                2
                            );

                            ?>

                        </strong>

                    </div>



                    <!-- Balance -->

                    <div class="payment-total payment-balance">

                        <span>
                            Outstanding Balance
                        </span>

                        <strong>

                            KSh
                            <?php

                            echo number_format(
                                $outstandingBalance,
                                2
                            );

                            ?>

                        </strong>

                    </div>


                </div>


            <?php else: ?>


                <div class="notes-empty">

                    <span>
                        💳
                    </span>

                    <p>
                        No payments have been recorded for this repair yet.
                    </p>

                </div>


            <?php endif; ?>


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