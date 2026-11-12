/*
|--------------------------------------------------------------------------
| Employee QR Scanner
|--------------------------------------------------------------------------
| Frontend only
| Backend/API is NOT changed
|--------------------------------------------------------------------------
*/

const API_BASE_URL =
    "http://localhost/employee-qr-system/backend/public";


/*
|--------------------------------------------------------------------------
| GLOBAL VARIABLES
|--------------------------------------------------------------------------
*/

let scanner = null;
let scannerRunning = false;
let scanInProgress = false;


/*
|--------------------------------------------------------------------------
| DOM ELEMENTS
|--------------------------------------------------------------------------
*/

const readerElement =
    document.getElementById("reader");

const startCameraButton =
    document.getElementById("startCamera");

const qrImageInput =
    document.getElementById("qrImageInput");

const employeeDetails =
    document.getElementById("employeeDetails");

const scanAgainButton =
    document.getElementById("scanAgain");

const errorElement =
    document.getElementById("scanError");


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

function setStatus(message) {

    console.log(
        "Scanner:",
        message
    );

}


/*
|--------------------------------------------------------------------------
| SHOW ERROR
|--------------------------------------------------------------------------
*/

function showError(message) {

    console.error(
        "Scanner Error:",
        message
    );

    if (errorElement) {

        errorElement.textContent =
            message;

        errorElement.style.display =
            "block";
    }

}


/*
|--------------------------------------------------------------------------
| CLEAR ERROR
|--------------------------------------------------------------------------
*/

function clearError() {

    if (errorElement) {

        errorElement.textContent =
            "";

        errorElement.style.display =
            "none";
    }

}


/*
|--------------------------------------------------------------------------
| CHECK CAMERA SUPPORT
|--------------------------------------------------------------------------
*/

function checkCameraSupport() {

    if (
        !navigator.mediaDevices ||
        !navigator.mediaDevices.getUserMedia
    ) {

        showError(
            "Camera access is not supported by this browser."
        );

        return false;
    }

    return true;
}


/*
|--------------------------------------------------------------------------
| START CAMERA
|--------------------------------------------------------------------------
*/

async function startScanner() {

    if (!readerElement) {

        showError(
            "Scanner area was not found."
        );

        return;
    }


    if (
        typeof Html5Qrcode ===
        "undefined"
    ) {

        showError(
            "QR scanner library is not loaded. Please refresh the page."
        );

        return;
    }


    if (scannerRunning) {

        return;
    }


    if (!checkCameraSupport()) {

        return;
    }


    try {

        clearError();


        /*
        |--------------------------------------------------------------------------
        | HTTPS / SECURE CONTEXT CHECK
        |--------------------------------------------------------------------------
        */

        const isLocalhost =
            location.hostname === "localhost" ||
            location.hostname === "127.0.0.1";

        const isSecure =
            window.isSecureContext ||
            isLocalhost;


        if (!isSecure) {

            showError(
                "Camera requires HTTPS on mobile. Open this website using HTTPS, or use localhost on the computer."
            );

            console.error(
                "Camera blocked because this page is not a secure context:",
                location.href
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | REQUEST CAMERA PERMISSION
        |--------------------------------------------------------------------------
        |
        | This explicitly asks the browser for camera permission.
        | It also helps mobile browsers expose the available cameras.
        |
        */

        setStatus(
            "Requesting camera permission..."
        );


        let permissionStream = null;


        try {

            permissionStream =
                await navigator.mediaDevices.getUserMedia(
                    {
                        video: {
                            facingMode: {
                                ideal: "environment"
                            }
                        },
                        audio: false
                    }
                );


            console.log(
                "Camera permission granted."
            );


        } catch (permissionError) {

            console.error(
                "Camera permission error:",
                permissionError
            );


            let permissionMessage =
                "Unable to access the camera.";


            if (
                permissionError.name ===
                "NotAllowedError"
            ) {

                permissionMessage =
                    "Camera permission was denied. Please allow camera access in your browser settings and try again.";


            } else if (
                permissionError.name ===
                "NotFoundError"
            ) {

                permissionMessage =
                    "No camera was found on this device.";


            } else if (
                permissionError.name ===
                "NotReadableError"
            ) {

                permissionMessage =
                    "The camera is already being used by another application.";


            } else if (
                permissionError.name ===
                "SecurityError"
            ) {

                permissionMessage =
                    "Camera access is blocked because this page is not using a secure connection.";


            } else if (
                permissionError.message
            ) {

                permissionMessage =
                    "Camera error: " +
                    permissionError.message;
            }


            showError(
                permissionMessage
            );

            return;

        } finally {

            /*
            |--------------------------------------------------------------------------
            | Stop temporary permission stream
            |--------------------------------------------------------------------------
            */

            if (permissionStream) {

                permissionStream
                    .getTracks()
                    .forEach(
                        track => track.stop()
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | REMOVE OLD SCANNER
        |--------------------------------------------------------------------------
        */

        if (scanner) {

            try {

                if (scannerRunning) {

                    await scanner.stop();
                }

            } catch (stopError) {

                console.warn(
                    "Previous scanner stop warning:",
                    stopError
                );
            }


            try {

                if (scanner.clear) {

                    await scanner.clear();
                }

            } catch (clearError) {

                console.warn(
                    "Previous scanner clear warning:",
                    clearError
                );
            }


            scanner = null;
            scannerRunning = false;
        }


        readerElement.innerHTML =
            "";


        /*
        |--------------------------------------------------------------------------
        | CREATE HTML5 QR SCANNER
        |--------------------------------------------------------------------------
        */

        scanner =
            new Html5Qrcode(
                "reader"
            );


        /*
        |--------------------------------------------------------------------------
        | SCANNER CONFIGURATION
        |--------------------------------------------------------------------------
        */

        const config = {

            fps: 10,

            qrbox: function (
               viewfinderWidth,
                viewfinderHeight
            ) {

                const minEdge =
                    Math.min(
                        viewfinderWidth,
                        viewfinderHeight
                    );


                const size =
                    Math.floor(
                        minEdge * 0.68
                    );


                const finalSize =
                    Math.max(
                        180,
                        Math.min(
                            size,
                            280
                        )
                    );


                return {

                    width:
                        finalSize,

                    height:
                        finalSize
                };
            },


            aspectRatio: 1.0,

            disableFlip: false,


            formatsToSupport: [

                Html5QrcodeSupportedFormats.QR_CODE

            ]

        };


        /*
        |--------------------------------------------------------------------------
        | GET AVAILABLE CAMERAS
        |--------------------------------------------------------------------------
        */

        setStatus(
            "Finding camera..."
        );


        const cameras =
            await Html5Qrcode.getCameras();


        if (
            !cameras ||
            cameras.length === 0
        ) {

            throw new Error(
                "No camera was found on this device."
            );
        }


        console.log(
            "Available cameras:",
            cameras
        );


        /*
        |--------------------------------------------------------------------------
        | SELECT REAR CAMERA
        |--------------------------------------------------------------------------
        */

        let selectedCamera =
            cameras.find(
                camera =>
                    /back|rear|environment/i.test(
                        camera.label || ""
                    )
            );


        /*
        |--------------------------------------------------------------------------
        | IF REAR CAMERA NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!selectedCamera) {

            selectedCamera =
                cameras[
                    cameras.length - 1
                ];
        }


        console.log(
            "Selected camera:",
            selectedCamera
        );


        /*
        |--------------------------------------------------------------------------
        | START HTML5 QR SCANNER
        |--------------------------------------------------------------------------
        */

        setStatus(
            "Starting camera..."
        );


        await scanner.start(

            selectedCamera.id,

            config,

            onScanSuccess,

            onScanFailure

        );


        /*
        |--------------------------------------------------------------------------
        | CAMERA SUCCESS
        |--------------------------------------------------------------------------
        */

        scannerRunning =
            true;

        scanInProgress =
            false;


        setStatus(
            "Camera started successfully."
        );


        /*
        |--------------------------------------------------------------------------
        | HIDE START CAMERA BUTTON
        |--------------------------------------------------------------------------
        */

        if (startCameraButton) {

            startCameraButton.style.display =
                "none";
        }


        console.log(
            "Camera started successfully."
        );


    } catch (error) {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | SHOW THE REAL CAMERA ERROR
        |--------------------------------------------------------------------------
        */

        console.error(
            "CAMERA START ERROR:",
            error
        );


        console.error(
            "Error name:",
            error?.name
        );


        console.error(
            "Error message:",
            error?.message
        );


        scannerRunning =
            false;


        let message =
            "Unable to start camera.";


        if (
            error?.name ===
            "NotAllowedError"
        ) {

            message =
                "Camera permission was denied. Please allow camera access for this website.";


        } else if (
            error?.name ===
            "NotFoundError"
        ) {

            message =
                "No camera was found on this device.";


        } else if (
            error?.name ===
            "NotReadableError"
        ) {

            message =
                "Camera is already being used by another application. Close other camera apps and try again.";


        } else if (
            error?.name ===
            "OverconstrainedError"
        ) {

            message =
                "The selected camera is not available. Please try again.";


        } else if (
            error?.name ===
            "SecurityError"
        ) {

            message =
                "Camera access is blocked because this page is not using a secure connection.";


        } else if (
            error?.message
        ) {

            message =
                "Camera error: " +
                error.message;
        }


        showError(
            message
        );


        if (startCameraButton) {

            startCameraButton.style.display =
                "block";

            startCameraButton.disabled =
                false;

            startCameraButton.textContent =
                "Start Camera";
        }

    }

}


/*
|--------------------------------------------------------------------------
| CAMERA QR SUCCESS
|--------------------------------------------------------------------------
*/

function onScanSuccess(
    decodedText
) {

    if (scanInProgress) {

        return;
    }


    scanInProgress =
        true;


    console.log(
        "QR DETECTED:",
        decodedText
    );


    clearError();


    /*
    |--------------------------------------------------------------------------
    | EXTRACT EMPLOYEE CODE
    |--------------------------------------------------------------------------
    */

    const employeeCode =
        extractEmployeeCode(
            decodedText.trim()
        );


    if (!employeeCode) {

        scanInProgress =
            false;

        showError(
            "Employee code was not found in the QR code."
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | STOP CAMERA
    |--------------------------------------------------------------------------
    */

    stopScanner()
        .then(
            function () {

                getEmployeeDetails(
                    employeeCode
                );

            }
        );

}


/*
|--------------------------------------------------------------------------
| CAMERA SCAN FAILURE
|--------------------------------------------------------------------------
*/

function onScanFailure(
    errorMessage
) {

    /*
    |--------------------------------------------------------------------------
    | DO NOTHING
    |--------------------------------------------------------------------------
    |
    | This function is called continuously while
    | the camera searches for a QR code.
    |
    */

}


/*
|--------------------------------------------------------------------------
| EXTRACT EMPLOYEE CODE
|--------------------------------------------------------------------------
*/

function extractEmployeeCode(
    value
) {

    if (!value) {

        return "";
    }


    /*
    |--------------------------------------------------------------------------
    | NORMAL QR
    |--------------------------------------------------------------------------
    |
    | Example:
    | EMP001
    |
    */

    if (
        !/^https?:\/\//i.test(
            value
        )
    ) {

        return value;
    }


    /*
    |--------------------------------------------------------------------------
    | QR CONTAINS URL
    |--------------------------------------------------------------------------
    */

    try {

        const url =
            new URL(value);


        /*
        |--------------------------------------------------------------------------
        | SEARCH PARAMETERS
        |--------------------------------------------------------------------------
        */

        const code =
            url.searchParams.get(
                "employee_code"
            ) ||

            url.searchParams.get(
                "employeeCode"
            ) ||

            url.searchParams.get(
                "code"
            );


        if (code) {

            return code;
        }


        /*
        |--------------------------------------------------------------------------
        | LAST URL PART
        |--------------------------------------------------------------------------
        */

        const parts =
            url.pathname
                .split("/")
                .filter(
                    part =>
                        part.length > 0
                );


        if (
            parts.length > 0
        ) {

            return decodeURIComponent(
                parts[
                    parts.length - 1
                ]
            );
        }


    } catch (error) {

        console.warn(
            "QR URL parsing failed:",
            error
        );
    }


    return value;
}


/*
|--------------------------------------------------------------------------
| STOP CAMERA
|--------------------------------------------------------------------------
*/

async function stopScanner() {

    if (!scanner) {

        scannerRunning =
            false;

        return;
    }


    try {

        if (scannerRunning) {

            await scanner.stop();
        }

    } catch (error) {

        console.warn(
            "Scanner stop warning:",
            error
        );
    }


    scannerRunning =
        false;


    try {

        if (scanner.clear) {

            await scanner.clear();
        }

    } catch (error) {

        console.warn(
            "Scanner clear warning:",
            error
        );
    }


    scanner =
        null;


    if (readerElement) {

        readerElement.innerHTML =
            "";
    }

}


/*
|--------------------------------------------------------------------------
| GET EMPLOYEE DETAILS
|--------------------------------------------------------------------------
*/

async function getEmployeeDetails(
    employeeCode
) {

    if (!employeeCode) {

        scanInProgress =
            false;

        showError(
            "Employee code was not found in the QR code."
        );

        return;
    }


    console.log(
        "Employee Code:",
        employeeCode
    );


    clearError();


    setStatus(
        "Getting employee details..."
    );


    try {

        /*
        |--------------------------------------------------------------------------
        | EXISTING BACKEND API
        |--------------------------------------------------------------------------
        */

        const response =
            await fetch(

                `${API_BASE_URL}/api/qr/${encodeURIComponent(employeeCode)}`

            );


        console.log(
            "API Status:",
            response.status
        );


        if (!response.ok) {

            throw new Error(
                `Employee not found. Server returned ${response.status}.`
            );
        }


        const result =
            await response.json();


        console.log(
            "API Response:",
            result
        );


        if (
            !result.success
        ) {

            throw new Error(
                result.message ||
                "Employee not found."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GET EMPLOYEE OBJECT
        |--------------------------------------------------------------------------
        */

        let employee =
            null;


        if (
            result.data &&
            result.data.employee
        ) {

            employee =
                result.data.employee;


        } else if (
            result.employee
        ) {

            employee =
                result.employee;


        } else if (
            result.data
        ) {

            employee =
                result.data;
        }


        if (!employee) {

            throw new Error(
                "Employee information was not returned by the server."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DISPLAY EMPLOYEE
        |--------------------------------------------------------------------------
        */

        displayEmployee(
            employee
        );


    } catch (error) {

        console.error(
            "Employee API error:",
            error
        );


        scanInProgress =
            false;


        showError(
            error.message ||
            "Unable to retrieve employee details."
        );

    }

}


/*
|--------------------------------------------------------------------------
| DISPLAY EMPLOYEE
|--------------------------------------------------------------------------
*/

function displayEmployee(
    employee
) {

    const employeeCodeElement =
        document.getElementById(
            "employeeCode"
        );


    const employeeNameElement =
        document.getElementById(
            "employeeName"
        );


    const employeeEmailElement =
        document.getElementById(
            "employeeEmail"
        );


    const employeePhoneElement =
        document.getElementById(
            "employeePhone"
        );


    const employeeDepartmentElement =
        document.getElementById(
            "employeeDepartment"
        );


    const employeeDesignationElement =
        document.getElementById(
            "employeeDesignation"
        );


    const employeeAddressElement =
        document.getElementById(
            "employeeAddress"
        );


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE CODE
    |--------------------------------------------------------------------------
    */

    if (
        employeeCodeElement
    ) {

        employeeCodeElement.textContent =

            employee.employee_code ||

            employee.employeeCode ||

            employee.code ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | NAME
    |--------------------------------------------------------------------------
    */

    if (
        employeeNameElement
    ) {

        employeeNameElement.textContent =

            employee.full_name ||

            employee.fullName ||

            employee.name ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | EMAIL
    |--------------------------------------------------------------------------
    */

    if (
        employeeEmailElement
    ) {

        employeeEmailElement.textContent =

            employee.email ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | PHONE
    |--------------------------------------------------------------------------
    */

    if (
        employeePhoneElement
    ) {

        employeePhoneElement.textContent =

            employee.phone ||

            employee.mobile ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | DEPARTMENT
    |--------------------------------------------------------------------------
    */

    if (
        employeeDepartmentElement
    ) {

        employeeDepartmentElement.textContent =

            employee.department ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | DESIGNATION
    |--------------------------------------------------------------------------
    */

    if (
        employeeDesignationElement
    ) {

        employeeDesignationElement.textContent =

            employee.designation ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | ADDRESS
    |--------------------------------------------------------------------------
    */

    if (
        employeeAddressElement
    ) {

        employeeAddressElement.textContent =

            employee.address ||

            "-";
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW EMPLOYEE CARD
    |--------------------------------------------------------------------------
    */

    if (
        employeeDetails
    ) {

        employeeDetails.style.display =
            "block";
    }


    if (
        scanAgainButton
    ) {

        scanAgainButton.style.display =
            "block";
    }


    setStatus(
        "Employee found."
    );

}


/*
|--------------------------------------------------------------------------
| RESET SCANNER UI
|--------------------------------------------------------------------------
*/

function resetScannerUI() {

    clearError();


    scanInProgress =
        false;


    if (
        employeeDetails
    ) {

        employeeDetails.style.display =
            "none";
    }


    if (
        scanAgainButton
    ) {

        scanAgainButton.style.display =
            "none";
    }

}


/*
|--------------------------------------------------------------------------
| START CAMERA BUTTON
|--------------------------------------------------------------------------
*/

if (
    startCameraButton
) {

    startCameraButton.addEventListener(

        "click",

        async function () {

            resetScannerUI();


            startCameraButton.disabled =
                true;


            startCameraButton.textContent =
                "Starting Camera...";


            await startScanner();


            if (
                !scannerRunning
            ) {

                startCameraButton.disabled =
                    false;

                startCameraButton.textContent =
                    "Start Camera";
            }

        }

    );

}


/*
|--------------------------------------------------------------------------
| SCAN AGAIN BUTTON
|--------------------------------------------------------------------------
*/

if (
    scanAgainButton
) {

    scanAgainButton.addEventListener(

        "click",

        async function () {

            await stopScanner();


            resetScannerUI();


            if (
                startCameraButton
            ) {

                startCameraButton.style.display =
                    "block";

                startCameraButton.disabled =
                    false;

                startCameraButton.textContent =
                    "Start Camera";
            }

        }

    );

}


/*
|--------------------------------------------------------------------------
| UPLOAD QR IMAGE
|--------------------------------------------------------------------------
*/

if (
    qrImageInput
) {

    qrImageInput.addEventListener(

        "change",

        function () {

            const file =
                this.files &&
                this.files[0];


            if (!file) {

                return;
            }


            clearError();


            /*
            |--------------------------------------------------------------------------
            | CHECK FILE TYPE
            |--------------------------------------------------------------------------
            */

            if (
                !file.type.startsWith(
                    "image/"
                )
            ) {

                showError(
                    "Please select a valid QR image."
                );


                this.value =
                    "";


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | STOP CAMERA
            |--------------------------------------------------------------------------
            */

            stopScanner()
                .then(

                    function () {

                        readUploadedQr(
                            file
                        );

                    }

                );


            /*
            |--------------------------------------------------------------------------
            | RESET INPUT
            |--------------------------------------------------------------------------
            */

            this.value =
                "";

        }

    );

}


/*
|--------------------------------------------------------------------------
| READ UPLOADED QR
|--------------------------------------------------------------------------
*/

function readUploadedQr(
    file
) {

    clearError();


    setStatus(
        "Reading QR image..."
    );


    scanInProgress =
        true;


    /*
    |--------------------------------------------------------------------------
    | CHECK jsQR
    |--------------------------------------------------------------------------
    */

    if (
        typeof jsQR ===
        "undefined"
    ) {

        scanInProgress =
            false;


        showError(
            "QR image decoder is not loaded. Please refresh the page."
        );


        return;
    }


    /*
    |--------------------------------------------------------------------------
    | FILE READER
    |--------------------------------------------------------------------------
    */

    const fileReader =
        new FileReader();


    fileReader.onload =
        function (event) {

            const image =
                new Image();


            image.onload =
                function () {

                    decodeUploadedImage(
                        image
                    );

                };


            image.onerror =
                function () {

                    scanInProgress =
                        false;


                    showError(
                        "The selected image could not be opened."
                    );

                };


            image.src =
                event.target.result;

        };


    fileReader.onerror =
        function () {

            scanInProgress =
                false;


            showError(
                "Unable to read the selected image."
            );

        };


    fileReader.readAsDataURL(
        file
    );

}


/*
|--------------------------------------------------------------------------
| DECODE UPLOADED IMAGE
|--------------------------------------------------------------------------
*/

function decodeUploadedImage(
    image
) {

    /*
    |--------------------------------------------------------------------------
    | ORIGINAL IMAGE
    |--------------------------------------------------------------------------
    */

    const result =
        decodeImageCanvas(
            image,
            1
        );


    if (result) {

        qrDecoded(
            result
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | LARGE IMAGE
    |--------------------------------------------------------------------------
    */

    const largeResult =
        decodeImageCanvas(
            image,
            1.5
        );


    if (largeResult) {

        qrDecoded(
            largeResult
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | SMALL IMAGE
    |--------------------------------------------------------------------------
    */

    const smallResult =
        decodeImageCanvas(
            image,
            0.75
        );


    if (smallResult) {

        qrDecoded(
            smallResult
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CENTER CROP
    |--------------------------------------------------------------------------
    */

    const cropResult =
        decodeCenterCrop(
            image
        );


    if (cropResult) {

        qrDecoded(
            cropResult
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | QR NOT FOUND
    |--------------------------------------------------------------------------
    */

    scanInProgress =
        false;


    showError(
        "QR code could not be detected. Please upload a clear QR image."
    );

}


/*
|--------------------------------------------------------------------------
| DECODE IMAGE USING CANVAS
|--------------------------------------------------------------------------
*/

function decodeImageCanvas(
    image,
    scale
) {

    let width =
        Math.floor(
            image.naturalWidth *
            scale
        );


    let height =
        Math.floor(
            image.naturalHeight *
            scale
        );


    if (
        width <= 0 ||
        height <= 0
    ) {

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | MAXIMUM CANVAS SIZE
    |--------------------------------------------------------------------------
    */

    const maxSize =
        2500;


    if (
        width > maxSize ||
        height > maxSize
    ) {

        const ratio =
            Math.min(
                maxSize / width,
                maxSize / height
            );


        width =
            Math.floor(
                width * ratio
            );


        height =
            Math.floor(
                height * ratio
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE CANVAS
    |--------------------------------------------------------------------------
    */

    const canvas =
        document.createElement(
            "canvas"
        );


    canvas.width =
        width;


    canvas.height =
        height;


    const context =
        canvas.getContext(
            "2d",
            {
                willReadFrequently:
                    true
            }
        );


    if (!context) {

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | WHITE BACKGROUND
    |--------------------------------------------------------------------------
    */

    context.fillStyle =
        "#ffffff";


    context.fillRect(
        0,
        0,
        width,
        height
    );


    /*
    |--------------------------------------------------------------------------
    | DRAW IMAGE
    |--------------------------------------------------------------------------
    */

    context.drawImage(
        image,
        0,
        0,
        width,
        height
    );


    /*
    |--------------------------------------------------------------------------
    | GET PIXELS
    |--------------------------------------------------------------------------
    */

    const imageData =
        context.getImageData(
            0,
            0,
            width,
            height
        );


    /*
    |--------------------------------------------------------------------------
    | DECODE QR
    |--------------------------------------------------------------------------
    */

    const qr =
        jsQR(

            imageData.data,

            imageData.width,

            imageData.height,

            {
                inversionAttempts:
                    "attemptBoth"
            }

        );


    if (qr) {

        return qr.data;
    }


    return null;

}


/*
|--------------------------------------------------------------------------
| CENTER CROP DECODER
|--------------------------------------------------------------------------
*/

function decodeCenterCrop(
    image
) {

    const width =
        image.naturalWidth;


    const height =
        image.naturalHeight;


    if (
        width <= 0 ||
        height <= 0
    ) {

        return null;
    }


    const size =
        Math.min(
            width,
            height
        );


    const canvas =
        document.createElement(
            "canvas"
        );


    canvas.width =
        size;


    canvas.height =
        size;


    const context =
        canvas.getContext(
            "2d",
            {
                willReadFrequently:
                    true
            }
        );


    if (!context) {

        return null;
    }


    const sourceX =
        (
            width -
            size
        ) / 2;


    const sourceY =
        (
            height -
            size
        ) / 2;


    context.drawImage(

        image,

        sourceX,
        sourceY,

        size,
        size,

        0,
        0,

        size,
        size

    );


    const imageData =
        context.getImageData(
            0,
            0,
            size,
            size
        );


    const qr =
        jsQR(

            imageData.data,

            imageData.width,

            imageData.height,

            {
                inversionAttempts:
                    "attemptBoth"
            }

        );


    if (qr) {

        return qr.data;
    }


    return null;

}


/*
|--------------------------------------------------------------------------
| QR DECODED FROM IMAGE
|--------------------------------------------------------------------------
*/

function qrDecoded(
    decodedText
) {

    console.log(
        "UPLOADED QR DETECTED:",
        decodedText
    );


    const employeeCode =
        extractEmployeeCode(
            decodedText.trim()
        );


    if (!employeeCode) {

        scanInProgress =
            false;


        showError(
            "Employee code was not found in the QR image."
        );


        return;
    }


    clearError();


    getEmployeeDetails(
        employeeCode
    );

}


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

document.addEventListener(

    "DOMContentLoaded",

    function () {

        console.log(
            "Employee QR Scanner loaded successfully."
        );


        /*
        |--------------------------------------------------------------------------
        | HIDE RESULT CARD INITIALLY
        |--------------------------------------------------------------------------
        */

        if (
            employeeDetails
        ) {

            employeeDetails.style.display =
                "none";
        }


        if (
            scanAgainButton
        ) {

            scanAgainButton.style.display =
                "none";
        }


        clearError();

    }

);


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

const logoutButton =
    document.getElementById("logoutButton");

if (logoutButton) {

    logoutButton.addEventListener(
        "click",
        async () => {

            if (
                !confirm(
                    "Are you sure you want to logout?"
                )
            ) {

                return;

            }


            try {

                await fetch(
                    `${API_BASE_URL}/api/logout`,
                    {
                        method: "POST",
                        credentials: "include"
                    }
                );

            }

            catch (error) {

                console.error(
                    error
                );

            }


            sessionStorage.clear();
            localStorage.clear();

            window.location.href =
                "login.php";

        }
    );

}


/*
|--------------------------------------------------------------------------
| MOBILE MENU
|--------------------------------------------------------------------------
*/

const mobileMenuButton =
    document.getElementById("mobileMenuButton");

const sidebarElement =
    document.querySelector(".sidebar");

if (
    mobileMenuButton &&
    sidebarElement
) {

    mobileMenuButton.addEventListener(
        "click",
        () => {

            sidebarElement.classList.toggle(
                "mobile-open"
            );

        }
    );

}