
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Employee QR System - Login</title>

   <link
    rel="stylesheet"
    href="../assets/css/login.css?v=2"
>
</head>

<body>

<div class="login-page">

    <div class="login-card">

        <!-- QR LOGO -->

        <div class="brand-icon">
            QR
        </div>


        <!-- LOGIN -->

        <h1 class="login-title">
            Login
        </h1>


        <!-- LOGIN FORM -->

        <form
            id="loginForm"
            autocomplete="off"
        >

            <!-- USERNAME / EMAIL -->

            <div class="form-group">

                <label for="username">
                    Username or Email
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username or email"
                    autocomplete="username"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="password-wrapper">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        id="togglePassword"
                        class="toggle-password"
                        aria-label="Show password"
                    >
                        👁
                    </button>

                </div>

            </div>


            <!-- ERROR -->

            <div
                id="loginError"
                class="login-error"
            ></div>


            <!-- LOGIN BUTTON -->

            <button
                type="submit"
                id="loginButton"
                class="login-button"
            >

                <span id="loginButtonText">
                    Login
                </span>

                <span
                    id="loginLoader"
                    class="loader"
                ></span>

            </button>

        </form>

    </div>

</div>


<script
    src="../assets/js/login.js?v=<?= time() ?>"
></script>

</body>

</html>