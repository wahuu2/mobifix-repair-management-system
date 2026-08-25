<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Customer ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: customers.php");
    exit;
}

$customerID = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Get Customer
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        CustomerID,
        Name,
        Email,
        Phone,
        Address
    FROM customers
    WHERE CustomerID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $customerID);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: customers.php");
    exit;
}

$customer = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Get Customer Repairs
|--------------------------------------------------------------------------
*/

$repairSql = "
    SELECT
        RepairID,
        PhoneBrand,
        PhoneModel,
        IssueDetails,
        DateReceived,
        EstimatedCost,
        Status,
        DateCompleted
    FROM repairs
    WHERE CustomerID = ?
    ORDER BY DateReceived DESC
";

$repairStmt = $conn->prepare($repairSql);

$repairStmt->bind_param("i", $customerID);

$repairStmt->execute();

$repairs = $repairStmt->get_result();


/*
|--------------------------------------------------------------------------
| Repair Statistics
|--------------------------------------------------------------------------
*/

$totalRepairs = $repairs->num_rows;

$completedRepairs = 0;
$activeRepairs = 0;

$repairRows = [];

while ($repair = $repairs->fetch_assoc()) {

    $repairRows[] = $repair;

    if ($repair["Status"] === "Completed") {
        $completedRepairs++;
    }

    if (
        $repair["Status"] !== "Completed" &&
        $repair["Status"] !== "Cancelled"
    ) {
        $activeRepairs++;
    }
}

$repairStmt->close();

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
        <?php echo htmlspecialchars($customer["Name"]); ?>
        | MobiFix Admin
    </title>

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
     MAIN
========================= -->

<main class="admin-main">

    <div class="admin-container">


        <!-- Back -->

        <a
            href="customers.php"
            class="back-link"
        >
            ← Back to Customers
        </a>


        <!-- =========================
             CUSTOMER PROFILE
        ========================= -->

        <section class="customer-profile-card">

            <div class="customer-profile-main">


                <div class="large-customer-avatar">

                    <?php

                    echo strtoupper(
                        substr(
                            $customer["Name"],
                            0,
                            1
                        )
                    );

                    ?>

                </div>


                <div>

                    <span class="dashboard-label">
                        CUSTOMER #<?php echo $customer["CustomerID"]; ?>
                    </span>

                    <h1>

                        <?php

                        echo htmlspecialchars(
                            $customer["Name"]
                        );

                        ?>

                    </h1>

                    <p>
                        MobiFix Customer
                    </p>

                </div>

            </div>


            <div class="customer-contact-grid">

                <div>

                    <span>
                        Email
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $customer["Email"]
                        );

                        ?>

                    </strong>

                </div>


                <div>

                    <span>
                        Phone
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $customer["Phone"]
                        );

                        ?>

                    </strong>

                </div>


                <div>

                    <span>
                        Address
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $customer["Address"]
                        );

                        ?>

                    </strong>

                </div>

            </div>

        </section>


        <!-- =========================
             STATISTICS
        ========================= -->

        <div class="customer-stat-grid">


            <div class="customer-stat-card">

                <span>
                    Total Repairs
                </span>

                <strong>
                    <?php echo $totalRepairs; ?>
                </strong>

            </div>


            <div class="customer-stat-card">

                <span>
                    Active Repairs
                </span>

                <strong>
                    <?php echo $activeRepairs; ?>
                </strong>

            </div>


            <div class="customer-stat-card">

                <span>
                    Completed
                </span>

                <strong>
                    <?php echo $completedRepairs; ?>
                </strong>

            </div>


        </div>


        <!-- =========================
             REPAIR HISTORY
        ========================= -->

        <section class="admin-card customer-history-card">

            <div class="customers-card-header">

                <div>

                    <h2>
                        Repair History
                    </h2>

                    <p>
                        All repairs submitted by this customer
                    </p>

                </div>

            </div>


            <?php if ($totalRepairs > 0): ?>

                <div class="table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Repair
                                </th>

                                <th>
                                    Device
                                </th>

                                <th>
                                    Date Received
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

                            <?php foreach ($repairRows as $repair): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            #<?php
                                            echo $repair["RepairID"];
                                            ?>
                                        </strong>

                                    </td>


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

                                    </td>


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


                                    <td>

                                        KSh
                                        <?php

                                        echo number_format(
                                            (float) $repair["EstimatedCost"],
                                            2
                                        );

                                        ?>

                                    </td>


                                    <td>

                                        <span
                                            class="status-badge status-<?php echo strtolower(str_replace(" ", "-", $repair["Status"])); ?>"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $repair["Status"]
                                            );

                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <a
                                            href="repair.php?id=<?php echo $repair["RepairID"]; ?>"
                                            class="table-action"
                                        >
                                            Manage
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        📱
                    </div>

                    <h3>
                        No Repairs Yet
                    </h3>

                    <p>
                        This customer has not submitted any repair requests.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>


</body>

</html>