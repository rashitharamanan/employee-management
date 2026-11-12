/*
|--------------------------------------------------------------------------
| API Configuration
|--------------------------------------------------------------------------
*/

const API_BASE_URL = "http://localhost/employee-qr-system/backend/public";

/*
|--------------------------------------------------------------------------
| DOM Elements
|--------------------------------------------------------------------------
*/

const loginForm = document.getElementById("loginForm");
const usernameInput = document.getElementById("username") || document.getElementById("email");
const passwordInput = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");
const loginButton = document.getElementById("loginButton");
const loginButtonText = document.getElementById("loginButtonText");
const loginError = document.getElementById("loginError");

/*
|--------------------------------------------------------------------------
| Show / Hide Password
|--------------------------------------------------------------------------
*/

if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
        const isPassword = passwordInput.type === "password";
        if (isPassword) {
            passwordInput.type = "text";
            togglePassword.textContent = "🙈";
            togglePassword.setAttribute("aria-label", "Hide password");
        } else {
            passwordInput.type = "password";
            togglePassword.textContent = "👁";
            togglePassword.setAttribute("aria-label", "Show password");
        }
    });
}

/*
|--------------------------------------------------------------------------
| Show / Hide Error
|--------------------------------------------------------------------------
*/

function showLoginError(message) {
    if (!loginError) return;
    loginError.textContent = message;
    loginError.style.display = "block";
}

function hideLoginError() {
    if (!loginError) return;
    loginError.textContent = "";
    loginError.style.display = "none";
}

/*
|--------------------------------------------------------------------------
| Loading State
|--------------------------------------------------------------------------
*/

function setLoading(loading) {
    if (!loginButton) return;
    if (loading) {
        loginButton.disabled = true;
        loginButton.classList.add("loading");
        if (loginButtonText) {
            loginButtonText.textContent = "Signing in...";
        }
    } else {
        loginButton.disabled = false;
        loginButton.classList.remove("loading");
        if (loginButtonText) {
            loginButtonText.textContent = "Login";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Login Form Submission
|--------------------------------------------------------------------------
*/

if (loginForm) {
    loginForm.addEventListener("submit", async function (event) {
        event.preventDefault();
        hideLoginError();

        const username = usernameInput ? usernameInput.value.trim() : "";
        const password = passwordInput ? passwordInput.value : "";

        if (!username) {
            showLoginError("Please enter your username or email.");
            if (usernameInput) usernameInput.focus();
            return;
        }

        if (!password) {
            showLoginError("Please enter your password.");
            if (passwordInput) passwordInput.focus();
            return;
        }

        setLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/api/login`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                credentials: "include",
                body: JSON.stringify({
                    email: username,
                    username: username,
                    identifier: username,
                    password: password
                })
            });

            let result;
            try {
                result = await response.json();
            } catch (parseErr) {
                console.error("JSON parse error:", parseErr);
                throw new Error("Invalid response from server.");
            }

            console.log("Login response:", result);

            if (!response.ok || !result.success) {
                showLoginError(result.message || "Invalid email/username or password.");
                setLoading(false);
                return;
            }

            const token = result.data?.token;
            if (!token) {
                showLoginError("Login succeeded, but authentication token was not received.");
                setLoading(false);
                return;
            }

            // Save tokens for all pages
            localStorage.setItem("auth_token", token);
            localStorage.setItem("token", token);

            if (result.data?.user) {
                localStorage.setItem("auth_user", JSON.stringify(result.data.user));
                const uName = result.data.user.full_name || result.data.user.name || result.data.user.username || "User";
                localStorage.setItem("username", uName);
            }

            const user = result.data?.user || {};
            const role = String(user.role || user.role_name || user.roleName || "")
                .trim()
                .toUpperCase();

            console.log("Logged-in user:", user, "Role:", role);

            // Redirect based on role
            if (role === "HR" || role.includes("HUMAN RESOURCE") || role.includes("HR MANAGER")) {
                window.location.href = "hr-dashboard.php";
            } else if (role === "MANAGER" || role.includes("TEAM MANAGER") || Number(user.role_id) === 3) {
                window.location.href = "manager/manager-dashboard.php";
            } else if (role === "ADMIN" || role === "SUPER_ADMIN" || !role) {
                window.location.href = "dashboard.php";
            } else if (role === "EMPLOYEE" || role === "STAFF") {
                window.location.href = "employee-dashboard.php";
            } else {
                window.location.href = "dashboard.php";
            }

        } catch (error) {
            console.error("Login error:", error);
            showLoginError(error.message || "Unable to connect to the server. Please check Apache.");
            setLoading(false);
        }
    });
}