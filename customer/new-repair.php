<?php

session_start();

require_once "../config/database.php";

// Make sure customer is logged in

if (!isset($_SESSION["customer_id"])) {

    header("Location: ../login.php");
    exit;
}

$customerID = $_SESSION["customer_id"];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phoneBrand = trim($_POST["phone_brand"]);
    $phoneModel = trim($_POST["phone_model"]);
    $issueDetails = trim($_POST["issue_details"]);

    if (
        empty($phoneBrand) ||
        empty($phoneModel) ||
        empty($issueDetails)
    ) {

        $error = "Please fill in all fields.";

    } else {

        $sql = "
            INSERT INTO repairs
            (
                CustomerID,
                PhoneBrand,
                PhoneModel,
                IssueDetails
            )
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "isss",
            $customerID,
            $phoneBrand,
            $phoneModel,
            $issueDetails
        );

        if ($stmt->execute()) {

            $repairID = $stmt->insert_id;

            header(
                "Location: dashboard.php?repair=success"
            );

            exit;

        } else {

            $error =
                "Unable to submit your repair request. Please try again.";
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

    <title>New Repair | MobiFix</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

    <header class="dashboard-header">

        <div class="dashboard-nav">

            <a
                href="dashboard.php"
                class="auth-logo"
            >
                Mobi<span>Fix</span>
            </a>

            <div class="dashboard-user">

                <span>
                    <?php
                    echo htmlspecialchars(
                        $_SESSION["customer_name"]
                    );
                    ?>
                </span>

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </div>

    </header>


    <main class="repair-form-main">

        <div class="repair-form-container">

            <div class="repair-form-header">

                <a
                    href="dashboard.php"
                    class="back-link"
                >
                    ← Back to Dashboard
                </a>

                <span class="dashboard-label">
                    MOBIFIX REPAIR REQUEST
                </span>

                <h1>
                    Submit a Repair Request
                </h1>

                <p>
                    Tell us about your phone and the problem you're
                    experiencing. Our technician will diagnose it
                    and update your repair status.
                </p>

            </div>


            <div class="repair-form-card">

                <?php if (!empty($error)): ?>

                    <div class="alert alert-error">

                        <?php echo htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <form method="POST" action="">

                    <div class="form-row">

                        <div class="form-group">

                            <label for="phone_brand">
                                Phone Brand
                            </label>

                            <select
                                id="phone_brand"
                                name="phone_brand"
                                required
                            >

                                <option value="">
                                    Select phone brand
                                </option>

                                <option value="Apple">
                                    Apple
                                </option>

                                <option value="Samsung">
                                    Samsung
                                </option>

                                <option value="Tecno">
                                    Tecno
                                </option>

                                <option value="Infinix">
                                    Infinix
                                </option>

                                <option value="Xiaomi">
                                    Xiaomi
                                </option>

                                <option value="Oppo">
                                    Oppo
                                </option>

                                <option value="Huawei">
                                    Huawei
                                </option>

                                <option value="Nokia">
                                    Nokia
                                </option>

                                <option value="Google">
                                    Google
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="phone_model">
                                Phone Model
                            </label>

                            <input
                                type="text"
                                id="phone_model"
                                name="phone_model"
                                placeholder="e.g. iPhone 13"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="issue_details">
                            Describe the Problem
                        </label>

                        <textarea
                            id="issue_details"
                            name="issue_details"
                            rows="7"
                            placeholder="Describe what is wrong with your phone..."
                            required
                        ></textarea>

                    </div>


                    <div class="repair-info">

                        <div class="repair-info-icon">
                            ℹ
                        </div>

                        <div>

                            <strong>
                                What happens next?
                            </strong>

                            <p>
                                Our technician will review your request,
                                diagnose the device and update the repair
                                status and estimated cost.
                            </p>

                        </div>

                    </div>


                    <div class="repair-form-actions">

                        <a
                            href="dashboard.php"
                            class="secondary-button"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="auth-button"
                        >
                            Submit Repair Request
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

</body>

</html>