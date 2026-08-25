<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Customers
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        c.CustomerID,
        c.Name,
        c.Email,
        c.Phone,
        c.Address,
        COUNT(r.RepairID) AS RepairCount

    FROM customers c

    LEFT JOIN repairs r
        ON c.CustomerID = r.CustomerID

    GROUP BY
        c.CustomerID,
        c.Name,
        c.Email,
        c.Phone,
        c.Address

    ORDER BY c.CustomerID DESC
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

    <title>Customers | MobiFix Admin</title>

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


        <!-- Heading -->

        <div class="admin-heading">

            <div>

                <span class="dashboard-label">
                    CUSTOMER MANAGEMENT
                </span>

                <h1>
                    Customers
                </h1>

                <p>
                    View registered customers and their repair activity.
                </p>

            </div>

        </div>


        <!-- Customers Card -->

        <section class="admin-card customers-card">

            <div class="customers-card-header">

                <div>

                    <h2>
                        All Customers
                    </h2>

                    <p>
                        Registered MobiFix customers
                    </p>

                </div>

                <div class="customer-count">

                    <?php echo $result->num_rows; ?>

                    <span>
                        Customers
                    </span>

                </div>

            </div>


            <?php if ($result->num_rows > 0): ?>

                <div class="table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Contact
                                </th>

                                <th>
                                    Address
                                </th>

                                <th>
                                    Repairs
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php while ($customer = $result->fetch_assoc()): ?>

                                <tr>

                                    <!-- Customer -->

                                    <td>

                                        <div class="customer-table-info">

                                            <div class="customer-avatar">

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

                                                <strong>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $customer["Name"]
                                                    );

                                                    ?>

                                                </strong>

                                                <span>

                                                    ID #<?php
                                                    echo $customer["CustomerID"];
                                                    ?>

                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- Contact -->

                                    <td>

                                        <div class="contact-info">

                                            <span>

                                                <?php

                                                echo htmlspecialchars(
                                                    $customer["Email"]
                                                );

                                                ?>

                                            </span>

                                            <span>

                                                <?php

                                                echo htmlspecialchars(
                                                    $customer["Phone"]
                                                );

                                                ?>

                                            </span>

                                        </div>

                                    </td>


                                    <!-- Address -->

                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $customer["Address"]
                                        );

                                        ?>

                                    </td>


                                    <!-- Repairs -->

                                    <td>

                                        <span class="repair-count-badge">

                                            <?php

                                            echo $customer["RepairCount"];

                                            ?>

                                            <?php

                                            echo $customer["RepairCount"] == 1
                                                ? " Repair"
                                                : " Repairs";

                                            ?>

                                        </span>

                                    </td>


                                    <!-- Action -->

                                    <td>

                                        <a
                                            href="customer.php?id=<?php echo $customer["CustomerID"]; ?>"
                                            class="table-action"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        👥
                    </div>

                    <h3>
                        No customers yet
                    </h3>

                    <p>
                        Registered customers will appear here.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>


</body>

</html>