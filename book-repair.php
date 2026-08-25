<?php

session_start();

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| Customer Authentication
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}


$customerID = $_SESSION["customer_id"];

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Submit Repair Request
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phoneBrand = trim($_POST["phone_brand"] ?? "");
    $phoneModel = trim($_POST["phone_model"] ?? "");
    $issueDetails = trim($_POST["issue_details"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($phoneBrand) ||
        empty($phoneModel) ||
        empty($issueDetails)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Insert Repair
        |--------------------------------------------------------------------------
        */

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

            $success =
                "Your repair request has been submitted successfully.";

            // Clear form values
            $phoneBrand = "";
            $phoneModel = "";
            $issueDetails = "";

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

    <title>
        Book a Repair | MobiFix
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<main class="repair-booking-page">

    <div class="repair-booking-container">


        <!-- =========================
             HEADER
        ========================= -->

        <div class="repair-booking-header">

            <a
                href="dashboard.php"
                class="back-link"
            >
                ← Back to Dashboard
            </a>


            <span class="dashboard-label">
                MOBIFIX REPAIR SERVICE
            </span>


            <h1>
                Book a Phone Repair
            </h1>


            <p>
                Tell us what's wrong with your phone and
                our technicians will take it from there.
            </p>

        </div>


        <!-- =========================
             FORM CARD
        ========================= -->

        <section class="repair-booking-card">


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


            <form
                method="POST"
                action=""
            >


                <!-- Phone Brand -->

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

                        <option
                            value="Apple"
                            <?php
                            echo ($phoneBrand ?? "") === "Apple"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Apple
                        </option>

                        <option
                            value="Samsung"
                            <?php
                            echo ($phoneBrand ?? "") === "Samsung"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Samsung
                        </option>

                        <option
                            value="Tecno"
                            <?php
                            echo ($phoneBrand ?? "") === "Tecno"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Tecno
                        </option>

                        <option
                            value="Infinix"
                            <?php
                            echo ($phoneBrand ?? "") === "Infinix"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Infinix
                        </option>

                        <option
                            value="Oppo"
                            <?php
                            echo ($phoneBrand ?? "") === "Oppo"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Oppo
                        </option>

                        <option
                            value="Xiaomi"
                            <?php
                            echo ($phoneBrand ?? "") === "Xiaomi"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Xiaomi
                        </option>

                        <option
                            value="Huawei"
                            <?php
                            echo ($phoneBrand ?? "") === "Huawei"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Huawei
                        </option>

                        <option
                            value="Nokia"
                            <?php
                            echo ($phoneBrand ?? "") === "Nokia"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Nokia
                        </option>

                        <option
                            value="Other"
                            <?php
                            echo ($phoneBrand ?? "") === "Other"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Other
                        </option>

                    </select>

                </div>


                <!-- Phone Model -->

                <div class="form-group">

                    <label for="phone_model">
                        Phone Model
                    </label>

                    <input
                        type="text"
                        id="phone_model"
                        name="phone_model"
                        placeholder="e.g. iPhone 13 Pro"
                        value="<?php
                        echo htmlspecialchars(
                            $phoneModel ?? ""
                        );
                        ?>"
                        required
                    >

                </div>


                <!-- Issue -->

                <div class="form-group">

                    <label for="issue_details">
                        Describe the Problem
                    </label>

                    <textarea
                        id="issue_details"
                        name="issue_details"
                        rows="6"
                        placeholder="Describe what is wrong with your phone..."
                        required
                    ><?php
                    echo htmlspecialchars(
                        $issueDetails ?? ""
                    );
                    ?></textarea>

                </div>


                <!-- Information -->

                <div class="repair-info-box">

                    <strong>
                        What happens next?
                    </strong>

                    <p>
                        Your request will initially be marked
                        <strong>Pending</strong>. Our technician
                        will diagnose the device and update the
                        repair status as work progresses.
                    </p>

                </div>


                <!-- Submit -->

                <button
                    type="submit"
                    class="auth-button"
                >
                    Submit Repair Request
                </button>


            </form>

        </section>

    </div>

</main>


</body>

</html>