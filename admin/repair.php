<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Protect Admin Page
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["admin_logged_in"])) {

    header("Location: login.php");
    exit;
}


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

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Update Repair
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $estimatedCost = trim($_POST["estimated_cost"]);
    $status = trim($_POST["status"]);
    $technicianNotes = trim($_POST["technician_notes"]);

    $allowedStatuses = [
        "Pending",
        "Diagnosing",
        "In Progress",
        "Ready for Collection",
        "Completed",
        "Cancelled"
    ];

    if (!in_array($status, $allowedStatuses, true)) {

        $error = "Invalid repair status.";

    } elseif (!is_numeric($estimatedCost) || $estimatedCost < 0) {

        $error = "Please enter a valid estimated cost.";

    } else {

        /*
         * If repair is completed, record completion date.
         * Otherwise keep DateCompleted empty.
         */

        if ($status === "Completed") {

            $sql = "
                UPDATE repairs
                SET
                    EstimatedCost = ?,
                    TechnicianNotes = ?,
                    Status = ?,
                    DateCompleted = COALESCE(
                        DateCompleted,
                        NOW()
                    )
                WHERE RepairID = ?
            ";

        } else {

            $sql = "
                UPDATE repairs
                SET
                    EstimatedCost = ?,
                    TechnicianNotes = ?,
                    Status = ?,
                    DateCompleted = NULL
                WHERE RepairID = ?
            ";
        }

        $stmt = $conn->prepare($sql);

        $cost = (float) $estimatedCost;

        $stmt->bind_param(
            "dssi",
            $cost,
            $technicianNotes,
            $status,
            $repairID
        );

        if ($stmt->execute()) {

            $success = "Repair updated successfully.";

        } else {

            $error = "Unable to update the repair.";

        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Get Repair Details
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
        r.TechnicianNotes,
        r.Status,
        r.DateCompleted,

        c.CustomerID,
        c.Name AS CustomerName,
        c.Email AS CustomerEmail,
        c.Phone AS CustomerPhone,
        c.Address AS CustomerAddress

    FROM repairs r

    INNER JOIN customers c
        ON r.CustomerID = c.CustomerID

    WHERE r.RepairID = ?

    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $repairID);

$stmt->execute();

$result = $stmt->get_result();

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
        Manage Repair #<?php echo $repair["RepairID"]; ?> | MobiFix
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
            href="dashboard.php"
            class="back-link"
        >
            ← Back to Dashboard
        </a>



        <!-- Heading -->

        <div class="admin-heading repair-management-heading">

            <div>

                <span class="dashboard-label">
                    REPAIR #<?php echo $repair["RepairID"]; ?>
                </span>

                <h1>
                    Manage Repair
                </h1>

                <p>
                    Review the customer's repair request and update
                    its progress.
                </p>

            </div>

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



        <div class="repair-management-grid">


            <!-- =========================
                 LEFT COLUMN
            ========================= -->

            <div>


                <!-- Customer -->

                <section class="admin-card detail-card">

                    <div class="detail-card-header">

                        <h2>
                            Customer Information
                        </h2>

                    </div>


                    <div class="detail-content">

                        <div class="detail-item">

                            <span>
                                Full Name
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $repair["CustomerName"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Email
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $repair["CustomerEmail"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Phone
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $repair["CustomerPhone"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="detail-item">

                            <span>
                                Address
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $repair["CustomerAddress"]
                                );
                                ?>
                            </strong>

                        </div>

                    </div>

                </section>



                <!-- Device -->

                <section class="admin-card detail-card">

                    <div class="detail-card-header">

                        <h2>
                            Device Information
                        </h2>

                    </div>


                    <div class="detail-content">

                        <div class="device-title">

                            <div class="device-icon">
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


                        <div class="issue-box">

                            <span>
                                Reported Problem
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


                        <div class="detail-item">

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

            </div>



            <!-- =========================
                 RIGHT COLUMN
            ========================= -->

            <div>


                <section class="admin-card detail-card">

                    <div class="detail-card-header">

                        <h2>
                            Repair Management
                        </h2>

                        <p>
                            Update the repair progress below.
                        </p>

                    </div>


                    <div class="detail-content">

                        <form
                            method="POST"
                            action=""
                        >


                            <!-- Status -->

                            <div class="form-group">

                                <label for="status">
                                    Repair Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    required
                                >

                                    <?php

                                    $statuses = [
                                        "Pending",
                                        "Diagnosing",
                                        "In Progress",
                                        "Ready for Collection",
                                        "Completed",
                                        "Cancelled"
                                    ];

                                    foreach ($statuses as $status):

                                    ?>

                                        <option
                                            value="<?php echo $status; ?>"
                                            <?php
                                            echo $repair["Status"] === $status
                                                ? "selected"
                                                : "";
                                            ?>
                                        >

                                            <?php echo $status; ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>



                            <!-- Cost -->

                            <div class="form-group">

                                <label for="estimated_cost">
                                    Estimated Repair Cost (KSh)
                                </label>

                                <input
                                    type="number"
                                    id="estimated_cost"
                                    name="estimated_cost"
                                    min="0"
                                    step="0.01"
                                    value="<?php echo htmlspecialchars($repair["EstimatedCost"]); ?>"
                                    placeholder="0.00"
                                    required
                                >

                            </div>



                            <!-- Notes -->

                            <div class="form-group">

                                <label for="technician_notes">
                                    Technician Notes
                                </label>

                                <textarea
                                    id="technician_notes"
                                    name="technician_notes"
                                    rows="7"
                                    placeholder="Add diagnosis, repair details or technician notes..."
                                ><?php
                                echo htmlspecialchars(
                                    $repair["TechnicianNotes"] ?? ""
                                );
                                ?></textarea>

                            </div>



                            <?php if (!empty($repair["DateCompleted"])): ?>

                                <div class="completed-notice">

                                    <strong>
                                        ✓ Repair Completed
                                    </strong>

                                    <span>

                                        <?php

                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $repair["DateCompleted"]
                                            )
                                        );

                                        ?>

                                    </span>

                                </div>

                            <?php endif; ?>



                            <button
                                type="submit"
                                class="auth-button"
                            >
                                Save Repair Changes
                            </button>


                        </form>

                    </div>

                </section>

            </div>


        </div>

    </div>

</main>


</body>

</html>