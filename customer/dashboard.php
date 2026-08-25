<?php

session_start();

require_once "../config/database.php";

// Make sure customer is logged in

if (!isset($_SESSION["customer_id"])) {

    header("Location: ../login.php");
    exit;
}

$customerID = $_SESSION["customer_id"];
$customerName = $_SESSION["customer_name"];

// Get customer's repairs

$sql = "
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

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $customerID);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Customer Dashboard | MobiFix</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

    <header class="dashboard-header">

        <div class="dashboard-nav">

            <div class="auth-logo">
                Mobi<span>Fix</span>
            </div>

            <div class="dashboard-user">

                <span>
                    <?php echo htmlspecialchars($customerName); ?>
                </span>

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </div>

    </header>


    <main class="dashboard-main">

        <div class="dashboard-container">

            <div class="dashboard-heading">

                <div>

                    <span class="dashboard-label">
                        CUSTOMER DASHBOARD
                    </span>

                    <h1>
                        Welcome,
                        <?php echo htmlspecialchars($customerName); ?>
                    </h1>

                    <p>
                        Manage your phone repairs and track their progress.
                    </p>

                </div>


                <a
                    href="new-repair.php"
                    class="auth-button dashboard-button"
                >
                    + New Repair
                </a>

            </div>


            <section class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2>
                            My Repairs
                        </h2>

                        <p>
                            View your current and previous repair requests.
                        </p>

                    </div>

                </div>


                <?php if ($result->num_rows > 0): ?>

                    <div class="repair-table-wrapper">

                        <table class="repair-table">

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

                                </tr>

                            </thead>

                            <tbody>

                                <?php while ($repair = $result->fetch_assoc()): ?>

                                    <tr>

                                       <td>

    <a 
        href="repair-details.php?id=<?php echo $repair["RepairID"]; ?>" 
        class="repair-view-link"
    >
        View #<?php echo $repair["RepairID"]; ?>
    </a>

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

                                            <small>
                                                <?php
                                                echo htmlspecialchars(
                                                    $repair["IssueDetails"]
                                                );
                                                ?>
                                            </small>

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
                                                $repair["EstimatedCost"],
                                                2
                                            );
                                            ?>

                                        </td>

                                        <td>

                                            <span
                                                class="repair-status status-<?php echo strtolower(str_replace(' ', '-', $repair["Status"])); ?>"
                                            >

                                                <?php
                                                echo htmlspecialchars(
                                                    $repair["Status"]
                                                );
                                                ?>

                                            </span>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="dashboard-empty">

                        <div class="empty-icon">
                            📱
                        </div>

                        <h3>
                            No repair requests yet
                        </h3>

                        <p>
                            You haven't submitted a phone repair request.
                        </p>

                        <a
                            href="new-repair.php"
                            class="auth-button dashboard-button"
                        >
                            Submit Your First Repair
                        </a>

                    </div>

                <?php endif; ?>

            </section>

        </div>

    </main>

</body>

</html>