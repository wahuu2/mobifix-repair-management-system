<?php

require_once "../config/database.php";

?>

<?php require_once "../includes/header.php"; ?>

<?php require_once "../includes/navbar.php"; ?>


<main>
<br>

    <!-- =========================
         SERVICES
    ========================= -->

    <section class="all-services-section">

        <div class="container">


            <div class="section-heading">

                <span>
                    OUR SERVICES
                </span><br>

                <h2>
                    Professional Repair Services
                </h2>

                <p>
                    We provide reliable solutions for common smartphone hardware and software problems.<br>Choose from our available phone repair services.
                </p>

            </div>



            <div class="services-grid">


                <?php

                /*
                |----------------------------------------------------------
                | Get Services
                |----------------------------------------------------------
                |
                | Services are loaded directly from the database.
                | Therefore, when an admin adds a new service from:
                |
                | admin/service/
                |
                | it automatically appears on this page.
                |
                */

                $sql = "
                    SELECT
                        ServiceID,
                        ServiceName,
                        Description,
                        Price,
                        AvailabilityStatus

                    FROM repair_services

                    ORDER BY ServiceID DESC
                ";

                $result = $conn->query($sql);

                ?>


                <?php if ($result && $result->num_rows > 0): ?>


                    <?php while ($service = $result->fetch_assoc()): ?>


                        <?php

                        /*
                        |--------------------------------------------------
                        | Service Icons
                        |--------------------------------------------------
                        */

                        $serviceName = strtolower(
                            $service["ServiceName"]
                        );


                        $icon = "🔧";


                        if (
                            strpos($serviceName, "screen") !== false ||
                            strpos($serviceName, "display") !== false
                        ) {

                            $icon = "📱";

                        } elseif (
                            strpos($serviceName, "battery") !== false
                        ) {

                            $icon = "🔋";

                        } elseif (
                            strpos($serviceName, "charging") !== false ||
                            strpos($serviceName, "charger") !== false ||
                            strpos($serviceName, "port") !== false
                        ) {

                            $icon = "⚡";

                        } elseif (
                            strpos($serviceName, "software") !== false ||
                            strpos($serviceName, "system") !== false
                        ) {

                            $icon = "💻";

                        } elseif (
                            strpos($serviceName, "camera") !== false
                        ) {

                            $icon = "📷";

                        } elseif (
                            strpos($serviceName, "speaker") !== false ||
                            strpos($serviceName, "audio") !== false
                        ) {

                            $icon = "🔊";

                        } elseif (
                            strpos($serviceName, "microphone") !== false ||
                            strpos($serviceName, "mic") !== false
                        ) {

                            $icon = "🎙️";

                        } elseif (
                            strpos($serviceName, "water") !== false ||
                            strpos($serviceName, "liquid") !== false
                        ) {

                            $icon = "💧";

                        } elseif (
                            strpos($serviceName, "network") !== false ||
                            strpos($serviceName, "signal") !== false
                        ) {

                            $icon = "📶";

                        } elseif (
                            strpos($serviceName, "unlock") !== false
                        ) {

                            $icon = "🔓";

                        } elseif (
                            strpos($serviceName, "diagnos") !== false
                        ) {

                            $icon = "🔍";

                        }

                        ?>


                        <!-- =========================
                             SERVICE CARD
                        ========================= -->

                        <div class="service-card">


                            <div class="service-icon">

                                <?php echo $icon; ?>

                            </div>


                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $service["ServiceName"]
                                );

                                ?>

                            </h3>


                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $service["Description"]
                                );

                                ?>

                            </p>


                            <div class="service-price">

                                <span>
                                    Starting from<br>
                                </span>

                                <strong>

                                    KSh
                                    <?php

                                    echo number_format(
                                        $service["Price"],
                                        2
                                    );

                                    ?>

                                </strong>

                            </div>


                            <?php if (
                                $service["AvailabilityStatus"]
                                === "Available"
                            ): ?>


                                <span class="service-available">

                                    Available

                                </span>


                            <?php else: ?>


                                <span class="service-unavailable">

                                    Currently Unavailable

                                </span>


                            <?php endif; ?>


                        </div>


                    <?php endwhile; ?>


                <?php else: ?>


                    <!-- =========================
                         EMPTY STATE
                    ========================= -->

                    <div class="services-empty">

                        <div class="empty-icon">
                            🔧
                        </div>

                        <h3>
                            No services available
                        </h3>

                        <p>
                            Our repair services will appear here
                            once they are added by the administrator.
                        </p>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </section>



    <!-- =========================
         CTA
    ========================= -->

    <section class="cta-section">

        <div class="container cta-content">

            <h2>
                Need Your Phone Repaired?
            </h2>

            <p>
                Create your MobiFix account and submit
                your repair request today.
            </p>

            <a
                href="../register.php"
                class="primary-button"
            >
                Request a Repair
            </a>

        </div>

    </section>


</main>


<?php require_once "../includes/footer.php"; ?>