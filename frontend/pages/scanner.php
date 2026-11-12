<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Employee QR System - QR Scanner</title>

    <link
        rel="stylesheet"
        href="../assets/css/app-theme.css?v=1"
    >

    <link
        rel="stylesheet"
        href="../assets/css/sidebar.css?v=<?= time() ?>"
    >

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css?v=10"
    >

    <link
        rel="stylesheet"
        href="../assets/css/scanner.css"
    >

    <link rel="stylesheet" href="../assets/css/topbar.css?v=1">

</head>

<body>

<div class="app">

    <?php include '../includes/sidebar.php'; ?>


    <!-- ================= MAIN ================= -->

    <main class="main-content">


        <?php include '../includes/topbar.php'; ?>


        <!-- ================= PAGE CONTENT ================= -->

        <section class="scanner-page">


            <div class="scanner-page-header">

               
                <p>
                    Scan an employee QR code to view their details
                </p>

            </div>


            <div class="scanner-layout">


                <!-- ================= SCANNER CARD ================= -->

                <div class="scanner-card">

                    <div class="scanner-card-header">

                        <div class="scanner-card-icon">
                            📷
                        </div>

                        <div>
                            <h2>
                                Scan Employee QR
                            </h2>

                            <p class="card-subtitle">
                                Use your camera to scan an employee QR code
                            </p>
                        </div>

                    </div>


                    <!-- IMPORTANT
                         Keep ID = reader
                         because scanner.js uses it
                    -->

                    <div class="reader-container">

                        <div id="reader"></div>

                    </div>


                    <button
                        type="button"
                        id="startCamera"
                        class="camera-button"
                    >
                        Start Camera
                    </button>


                    <div class="upload-section">

                        <div class="divider">
                            <span>OR</span>
                        </div>


                        <label
                            for="qrImageInput"
                            class="upload-button"
                        >
                            Upload QR Image
                        </label>


                        <input
                            type="file"
                            id="qrImageInput"
                            accept="image/*"
                            hidden
                        >


                        <p class="upload-hint">
                            Upload a QR image from your device
                        </p>

                    </div>


                   

                    <div
                        id="scanError"
                        class="error"
                    ></div>

                </div>


                <!-- ================= EMPLOYEE RESULT ================= -->

                <div
                    id="employeeDetails"
                    class="employee-details"
                >

                    <div class="result-header">

                        <div class="result-icon">
                            👤
                        </div>

                        <div>

                            <h2>
                                Employee Details
                            </h2>

                            <p>
                                Scanned employee information
                            </p>

                        </div>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Employee Code
                        </span>

                        <span
                            class="value"
                            id="employeeCode"
                        >
                            -
                        </span>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Full Name
                        </span>

                        <span
                            class="value"
                            id="employeeName"
                        >
                            -
                        </span>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Email
                        </span>

                        <span
                            class="value"
                            id="employeeEmail"
                        >
                            -
                        </span>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Phone
                        </span>

                        <span
                            class="value"
                            id="employeePhone"
                        >
                            -
                        </span>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Department
                        </span>

                        <span
                            class="value"
                            id="employeeDepartment"
                        >
                            -
                        </span>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Designation
                        </span>

                        <span
                            class="value"
                            id="employeeDesignation"
                        >
                            -
                        </span>

                    </div>


                    <div class="detail">

                        <span class="label">
                            Address
                        </span>

                        <span
                            class="value"
                            id="employeeAddress"
                        >
                            -
                        </span>

                    </div>


                    <button
                        type="button"
                        id="scanAgain"
                        class="scan-again"
                    >
                        Scan Another Employee
                    </button>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- QR SCANNER LIBRARY -->
<script
    src="https://unpkg.com/html5-qrcode"
    type="text/javascript"
></script>

<!-- QR IMAGE DECODER -->
<script
    src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"
    type="text/javascript"
></script>

<!-- THEME JS -->
<script src="../assets/js/theme.js?v=1"></script>

<!-- YOUR SCANNER JAVASCRIPT -->
<script
    src="../assets/js/scanner.js"
></script>

</body>
</html>