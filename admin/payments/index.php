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
| Get Payments
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
        r.Status AS RepairStatus

    FROM payments p

    INNER JOIN repairs r
        ON p.RepairID = r.RepairID

    INNER JOIN customers c
        ON r.CustomerID = c.CustomerID

    ORDER BY p.PaymentDate DESC
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

    <title>Payments | MobiFix</title>

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
            href="../dashboard.php"
            class="back-link"
        >
            ← Back to Dashboard
        </a>



        <!-- Heading -->

        <div class="admin-heading">

            <div>

                <span class="dashboard-label">
                    PAYMENTS
                </span>

                <h1>
                    Payment Management
                </h1>

                <p>
                    View and manage payments made for customer repairs.
                </p>

            </div>


            <a
                href="add.php"
                class="auth-button dashboard-button"
            >
                + Record Payment
            </a>

        </div>



        <!-- =====================================================
             PAYMENTS TABLE
        ====================================================== -->

        <section class="admin-card">

            <div class="admin-card-header">

                <div>

                    <h2>
                        All Payments
                    </h2>

                    <p>
                        Payment records associated with MobiFix repairs.
                    </p>

                </div>

            </div>



            <?php if ($result && $result->num_rows > 0): ?>

                <div class="admin-table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Payment
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Repair
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Method
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php while ($payment = $result->fetch_assoc()): ?>

                                <tr>


                                    <!-- Payment -->

                                    <td>

                                        <strong>
                                            #<?php
                                            echo $payment["PaymentID"];
                                            ?>
                                        </strong>

                                        <?php if (!empty($payment["TransactionReference"])): ?>

                                            <small>
                                                <?php
                                                echo htmlspecialchars(
                                                    $payment["TransactionReference"]
                                                );
                                                ?>
                                            </small>

                                        <?php endif; ?>

                                    </td>



                                    <!-- Customer -->

                                    <td>

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $payment["CustomerName"]
                                            );
                                            ?>

                                        </strong>

                                        <small>

                                            <?php
                                            echo htmlspecialchars(
                                                $payment["CustomerPhone"]
                                            );
                                            ?>

                                        </small>

                                    </td>



                                    <!-- Repair -->

                                    <td>

                                        <strong>

                                            #<?php
                                            echo $payment["RepairID"];
                                            ?>

                                        </strong>

                                        <small>

                                            <?php

                                            echo htmlspecialchars(
                                                $payment["PhoneBrand"]
                                                . " "
                                                . $payment["PhoneModel"]
                                            );

                                            ?>

                                        </small>

                                    </td>



                                    <!-- Amount -->

                                    <td>

                                        <strong>

                                            KSh
                                            <?php

                                            echo number_format(
                                                $payment["Amount"],
                                                2
                                            );

                                            ?>

                                        </strong>

                                    </td>



                                    <!-- Method -->

                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $payment["PaymentMethod"]
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
                                                    $payment["PaymentStatus"]
                                                )
                                            );

                                            ?>"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $payment["PaymentStatus"]
                                            );

                                            ?>

                                        </span>

                                    </td>



                                    <!-- Date -->

                                    <td>

                                        <?php

                                        echo date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $payment["PaymentDate"]
                                            )
                                        );

                                        ?>

                                    </td>
                                    <td>

    <a
        href="edit.php?id=<?php echo $payment["PaymentID"]; ?>"
        class="manage-button"
    >
        Edit
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
                        💳
                    </div>

                    <h3>
                        No payments recorded
                    </h3>

                    <p>
                        Payments recorded for customer repairs
                        will appear here.
                    </p>

                    <a
                        href="add.php"
                        class="auth-button dashboard-button"
                    >
                        Record First Payment
                    </a>

                </div>


            <?php endif; ?>


        </section>


    </div>

</main>


</body>

</html>