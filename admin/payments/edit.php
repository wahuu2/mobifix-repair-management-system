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


/*
|--------------------------------------------------------------------------
| Get Payment ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: index.php");
    exit;
}

$paymentID = (int) $_GET["id"];

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Get Payment Details
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        p.PaymentID,
        p.RepairID,
        p.Amount,
        p.PaymentMethod,
        p.PaymentStatus,
        p.TransactionReference,
        p.PaymentDate,

        c.Name AS CustomerName,
        c.Phone AS CustomerPhone,

        r.PhoneBrand,
        r.PhoneModel,
        r.EstimatedCost,
        r.Status AS RepairStatus

    FROM payments p

    INNER JOIN repairs r
        ON p.RepairID = r.RepairID

    INNER JOIN customers c
        ON r.CustomerID = c.CustomerID

    WHERE p.PaymentID = ?

    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $paymentID
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php");
    exit;
}


$payment = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Update Payment
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $amount = trim($_POST["amount"] ?? "");
    $paymentMethod = trim($_POST["payment_method"] ?? "");
    $paymentStatus = trim($_POST["payment_status"] ?? "");
    $transactionReference = trim(
        $_POST["transaction_reference"] ?? ""
    );


    /*
    |--------------------------------------------------------------------------
    | Allowed Values
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


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (!is_numeric($amount) || $amount <= 0) {

        $error = "Please enter a valid payment amount.";

    } elseif (!in_array($paymentMethod, $allowedMethods, true)) {

        $error = "Please select a valid payment method.";

    } elseif (!in_array($paymentStatus, $allowedStatuses, true)) {

        $error = "Please select a valid payment status.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Update Payment
        |--------------------------------------------------------------------------
        */

        $sql = "
            UPDATE payments

            SET
                Amount = ?,
                PaymentMethod = ?,
                PaymentStatus = ?,
                TransactionReference = ?

            WHERE PaymentID = ?
        ";

        $stmt = $conn->prepare($sql);

        $paymentAmount = (float) $amount;

        $stmt->bind_param(
            "dsssi",
            $paymentAmount,
            $paymentMethod,
            $paymentStatus,
            $transactionReference,
            $paymentID
        );


        if ($stmt->execute()) {

            $success = "Payment updated successfully.";


            /*
            |--------------------------------------------------------------------------
            | Update Displayed Payment
            |--------------------------------------------------------------------------
            */

            $payment["Amount"] = $paymentAmount;
            $payment["PaymentMethod"] = $paymentMethod;
            $payment["PaymentStatus"] = $paymentStatus;
            $payment["TransactionReference"] = $transactionReference;

        } else {

            $error = "Unable to update the payment.";

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
        Edit Payment #<?php echo $payment["PaymentID"]; ?> | MobiFix
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
                    PAYMENT #<?php echo $payment["PaymentID"]; ?>
                </span>

                <h1>
                    Edit Payment
                </h1>

                <p>
                    Update the payment information for this repair.
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
             PAYMENT INFORMATION
        ====================================================== -->

        <section class="admin-card">

            <div class="admin-card-header">

                <div>

                    <h2>
                        Payment Information
                    </h2>

                    <p>
                        Review the repair and update its payment details.
                    </p>

                </div>

            </div>



            <div class="detail-content">


                <!-- =================================================
                     REPAIR INFORMATION
                ================================================== -->

                <div class="payment-repair-info">

                    <div class="detail-item">

                        <span>
                            Repair
                        </span>

                        <strong>

                            #<?php
                            echo $payment["RepairID"];
                            ?>

                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>
                            Customer
                        </span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $payment["CustomerName"]
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
                                $payment["PhoneBrand"]
                                . " "
                                . $payment["PhoneModel"]
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="detail-item">

                        <span>
                            Payment Date
                        </span>

                        <strong>

                            <?php

                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $payment["PaymentDate"]
                                )
                            );

                            ?>

                        </strong>

                    </div>

                </div>



                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    method="POST"
                    action=""
                >


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
                            value="<?php
                            echo htmlspecialchars(
                                $payment["Amount"]
                            );
                            ?>"
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
                                    echo $payment["PaymentMethod"] === $method
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
                                    echo $payment["PaymentStatus"] === $status
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
                            maxlength="100"
                            value="<?php
                            echo htmlspecialchars(
                                $payment["TransactionReference"] ?? ""
                            );
                            ?>"
                            placeholder="e.g. M-Pesa transaction code"
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
                            Save Payment Changes
                        </button>

                    </div>


                </form>

            </div>

        </section>


    </div>

</main>


</body>

</html>
