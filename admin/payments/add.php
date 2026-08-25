<?php

session_start();

require_once "../../config/database.php";

/*
|--------------------------------------------------------------------------
| Protect Admin Page
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["admin_logged_in"])) {

    header("Location: ../login.php");
    exit;
}


$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Get Repairs
|--------------------------------------------------------------------------
|
| We only show existing repairs because every payment
| must belong to a repair.
|
*/

$sql = "
    SELECT
        r.RepairID,
        r.PhoneBrand,
        r.PhoneModel,
        r.EstimatedCost,
        r.Status,

        c.Name AS CustomerName,
        c.Phone AS CustomerPhone

    FROM repairs r

    INNER JOIN customers c
        ON r.CustomerID = c.CustomerID

    ORDER BY r.DateReceived DESC
";

$repairsResult = $conn->query($sql);


/*
|--------------------------------------------------------------------------
| Save Payment
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $repairID = trim($_POST["repair_id"] ?? "");
    $amount = trim($_POST["amount"] ?? "");
    $paymentMethod = trim($_POST["payment_method"] ?? "");
    $paymentStatus = trim($_POST["payment_status"] ?? "");
    $transactionReference = trim(
        $_POST["transaction_reference"] ?? ""
    );


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    $allowedMethods = [
        "Cash",
        "M-Pesa",
        "Card",
        "Bank Transfer"
    ];

    $allowedStatuses = [
        "Pending",
        "Paid",
        "Failed",
        "Refunded"
    ];


    if (!is_numeric($repairID) || $repairID <= 0) {

        $error = "Please select a valid repair.";

    } elseif (!is_numeric($amount) || $amount <= 0) {

        $error = "Please enter a valid payment amount.";

    } elseif (!in_array($paymentMethod, $allowedMethods, true)) {

        $error = "Please select a valid payment method.";

    } elseif (!in_array($paymentStatus, $allowedStatuses, true)) {

        $error = "Please select a valid payment status.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Check Repair Exists
        |--------------------------------------------------------------------------
        */

        $checkStmt = $conn->prepare("
            SELECT RepairID
            FROM repairs
            WHERE RepairID = ?
            LIMIT 1
        ");

        $checkStmt->bind_param(
            "i",
            $repairID
        );

        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows !== 1) {

            $error = "The selected repair does not exist.";

        }

        $checkStmt->close();


        /*
        |--------------------------------------------------------------------------
        | Insert Payment
        |--------------------------------------------------------------------------
        */

        if (empty($error)) {

            $sql = "
                INSERT INTO payments (
                    RepairID,
                    Amount,
                    PaymentMethod,
                    PaymentStatus,
                    TransactionReference
                )
                VALUES (?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($sql);

            $paymentAmount = (float) $amount;

            $stmt->bind_param(
                "idsss",
                $repairID,
                $paymentAmount,
                $paymentMethod,
                $paymentStatus,
                $transactionReference
            );


            if ($stmt->execute()) {

                $success = "Payment recorded successfully.";

                /*
                | Clear form after successful submission
                */

                $_POST = [];

            } else {

                $error = "Unable to record the payment.";

            }

            $stmt->close();
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

    <title>
        Record Payment | MobiFix
    </title>

    <link
        rel="stylesheet"
        href="../../assets/css/style.css"
    >

</head>

<body>


<!-- =========================================================
     ADMIN HEADER
========================================================= -->

<header class="admin-header">

    <div class="admin-nav">

        <a
            href="../dashboard.php"
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
                href="../logout.php"
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
            ← Back to Payments
        </a>



        <!-- Heading -->

        <div class="admin-heading">

            <div>

                <span class="dashboard-label">
                    PAYMENTS
                </span>

                <h1>
                    Record Payment
                </h1>

                <p>
                    Record a payment made towards a customer repair.
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



        <!-- =====================================================
             PAYMENT FORM
        ====================================================== -->

        <section class="admin-card">

            <div class="admin-card-header">

                <div>

                    <h2>
                        Payment Information
                    </h2>

                    <p>
                        Enter the details of the customer's payment.
                    </p>

                </div>

            </div>



            <div class="detail-content">

                <form
                    method="POST"
                    action=""
                >


                    <!-- Repair -->

                    <div class="form-group">

                        <label for="repair_id">
                            Customer Repair
                        </label>

                        <select
                            id="repair_id"
                            name="repair_id"
                            required
                        >

                            <option value="">
                                -- Select Repair --
                            </option>


                            <?php if ($repairsResult && $repairsResult->num_rows > 0): ?>

                                <?php while ($repair = $repairsResult->fetch_assoc()): ?>

                                    <option
                                        value="<?php echo $repair["RepairID"]; ?>"
                                        <?php
                                        echo (
                                            ($_POST["repair_id"] ?? "")
                                            == $repair["RepairID"]
                                        )
                                            ? "selected"
                                            : "";
                                        ?>
                                    >

                                        <?php

                                        echo "#"
                                            . $repair["RepairID"]
                                            . " - "
                                            . $repair["CustomerName"]
                                            . " - "
                                            . $repair["PhoneBrand"]
                                            . " "
                                            . $repair["PhoneModel"];

                                        ?>

                                    </option>

                                <?php endwhile; ?>

                            <?php endif; ?>

                        </select>

                    </div>



                    <!-- Amount -->

                    <div class="form-group">

                        <label for="amount">
                            Payment Amount (KSh)
                        </label>

                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            min="0.01"
                            step="0.01"
                            value="<?php echo htmlspecialchars($_POST["amount"] ?? ""); ?>"
                            placeholder="Enter amount"
                            required
                        >

                    </div>



                    <!-- Payment Method -->

                    <div class="form-group">

                        <label for="payment_method">
                            Payment Method
                        </label>

                        <select
                            id="payment_method"
                            name="payment_method"
                            required
                        >

                            <option value="">
                                -- Select Payment Method --
                            </option>

                            <?php foreach (
                                [
                                    "Cash",
                                    "M-Pesa",
                                    "Card",
                                    "Bank Transfer"
                                ] as $method
                            ): ?>

                                <option
                                    value="<?php echo $method; ?>"
                                    <?php
                                    echo (
                                        ($_POST["payment_method"] ?? "")
                                        === $method
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >

                                    <?php echo $method; ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>



                    <!-- Payment Status -->

                    <div class="form-group">

                        <label for="payment_status">
                            Payment Status
                        </label>

                        <select
                            id="payment_status"
                            name="payment_status"
                            required
                        >

                            <?php foreach (
                                [
                                    "Pending",
                                    "Paid",
                                    "Failed",
                                    "Refunded"
                                ] as $status
                            ): ?>

                                <option
                                    value="<?php echo $status; ?>"
                                    <?php
                                    echo (
                                        ($_POST["payment_status"] ?? "Paid")
                                        === $status
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >

                                    <?php echo $status; ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>



                    <!-- Transaction Reference -->

                    <div class="form-group">

                        <label for="transaction_reference">
                            Transaction Reference
                        </label>

                        <input
                            type="text"
                            id="transaction_reference"
                            name="transaction_reference"
                            value="<?php echo htmlspecialchars($_POST["transaction_reference"] ?? ""); ?>"
                            placeholder="e.g. M-Pesa transaction code"
                            maxlength="100"
                        >

                        <small>
                            Optional. Useful for M-Pesa, bank or card payments.
                        </small>

                    </div>



                    <!-- Buttons -->

                    <div class="form-actions">

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
                            Record Payment
                        </button>

                    </div>


                </form>

            </div>

        </section>


    </div>

</main>


</body>

</html>
