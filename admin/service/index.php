<?php

require_once "../config/database.php";
require_once "../includes/header.php";
require_once "../includes/navbar.php";

$sql = "SELECT * FROM repair_services ORDER BY ServiceID DESC";

$result = $conn->query($sql);

?>

<main>

    <!-- Page Hero -->

    <section class="services-hero">

        <div class="container">

            <span class="hero-badge">
                MOBIFIX SERVICES
            </span>

            <h1>
                Professional Phone
                <span>Repair Services</span>
            </h1>

            <p>
                From cracked screens to charging problems, our repair
                services are designed to get your device back in shape.
            </p>

        </div>

    </section>


    <!-- Services -->

    <section class="services-page">

        <div class="container">

            <div class="section-heading">

                <span>WHAT WE REPAIR</span>

                <h2>
                    Choose a Repair Service
                </h2>

                <p>
                    Select the service you need and submit a repair
                    request through your customer account.
                </p>

            </div>


            <div class="services-grid">

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($service = $result->fetch_assoc()): ?>

                        <article class="service-card professional-service">

                            <div class="service-card-top">

                                <div class="service-icon">

    <?php
        $serviceName = strtolower($service['ServiceName']);

        if (strpos($serviceName, 'screen') !== false) {
            echo '📱';
        } elseif (strpos($serviceName, 'battery') !== false) {
            echo '🔋';
        } elseif (strpos($serviceName, 'charging') !== false) {
            echo '🔌';
        } elseif (strpos($serviceName, 'software') !== false) {
            echo '💻';
        } elseif (strpos($serviceName, 'camera') !== false) {
            echo '📷';
        } elseif (
            strpos($serviceName, 'speaker') !== false ||
            strpos($serviceName, 'microphone') !== false
        ) {
            echo '🔊';
        } else {
            echo '🛠️';
        }
    ?>

</div>

                                <span class="service-status">
                                    Available
                                </span>

                            </div>


                            <h3>
                                <?php echo htmlspecialchars($service['ServiceName']); ?>
                            </h3>


                            <p>
                                <?php echo htmlspecialchars($service['Description']); ?>
                            </p>


                            <div class="service-card-bottom">

                                <div>

                                    <span class="price-label">
                                        Starting from
                                    </span>

                                    <strong>
                                        KSh <?php echo number_format($service['Price'], 2); ?>
                                    </strong>

                                </div>


                                <a
                                    href="../register.php"
                                    class="service-button"
                                >
                                    Book Repair
                                </a>

                            </div>

                        </article>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="empty-state">

                        <h3>
                            No services available
                        </h3>

                        <p>
                            Our repair services are currently being updated.
                            Please check again later.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- CTA -->

    <section class="services-cta">

        <div class="container">

            <div class="cta-content">

                <span>
                    NEED HELP?
                </span>

                <h2>
                    Not sure what is wrong with your phone?
                </h2>

                <p>
                    Submit a repair request and our technician can
                    diagnose the problem for you.
                </p>

                <a
                    href="../register.php"
                    class="primary-button"
                >
                    Request a Diagnosis
                </a>

            </div>

        </div>

    </section>

</main>


<?php require_once "../includes/footer.php"; ?>