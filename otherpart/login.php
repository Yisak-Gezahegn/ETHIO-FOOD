<?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
    <div id="successToast" class="success-toast">
        <div class="toast-content">
            <center><i class="fas fa-check-circle"></i>
            <span>Account created successfully! Please log in.</span></center>
        </div>
    </div>
    
    <script>
        // 1. Hide the message after 4 seconds
        setTimeout(function() {
            const toast = document.getElementById('successToast');
            if(toast) toast.style.display = 'none';
        }, 4000);

        // 2. STRESS TEST FIX: Remove the 'signup=success' from the URL bar
        // This stops the message from appearing again if they refresh!
        if (window.history.replaceState) {
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: url}, '', url);
        }
    </script>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ETHIO FOOD</title>
    <link rel="stylesheet" href="./styling/loginstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <a href="../index.php" class="logo">
                &#127839 ETHIO FOOD
            </a>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="./howwork.php">How It Works</a></li>
                    <li><a href="./aboutus.php">About Us</a></li>
                    <li><a href="./faq.php">FAQ</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="login.php" class="btn">Login</a>
            </div>
        </div>
    </header>

    <section class="auth-section">
        <div class="auth-container">
            <div class="auth-welcome">
                <h1>Welcome to ETHIO FOOD</h1>
                <p>Order delicious Ethiopian food from the best restaurants.</p>
                <ul class="auth-features">
                    <li>Fast delivery in 30-40 minutes</li>
                    <li>Secure payment options</li>
                </ul>
            </div>
            
            <div class="auth-forms">
                <div class="form-container">
                    <div class="form-toggle">
                        <button class="toggle-btn active" data-form="login">Login</button>
                        <button class="toggle-btn" data-form="signup">Sign Up</button>
                    </div>
                    
                    <form class="auth-form active" id="login-form" action="../include/auth_handler.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" name="login_btn" class="btn">Login</button>
                        <center><h2>or</h2></center>
                        <button type="button" id="openForgot" class="btn">FORGET PASSWORD</button>
                    </form>
                    
                    <form class="auth-form" id="signup-form" action="../include/auth_handler.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-input" placeholder="Enter your full name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-input" placeholder="Enter your phone" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input" placeholder="Create a password" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Select Role</label>
                            <select name="user_role" class="form-input" required>
                                <option value="customer">Customer</option>
                                <option value="owner">Restaurant Owner</option>
                                <option value="developer">Admin</option>
                            </select>
                        </div>
                           <div class="form-group">
    <label class="form-label">Location</label>
    <input type="text" name="location" class="form-input" placeholder="e.g. Addis Ababa, Bole" required>
</div>
                        
                        <button type="submit" name="signup_btn" class="btn">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <div id="forgotModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 10000; justify-content: center; align-items: center; font-family: sans-serif;">
    
    <div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 400px; position: relative; box-shadow: 0px 10px 30px rgba(0,0,0,0.3); text-align: center;">
        
        <span class="close-modal" style="position: absolute; right: 20px; top: 10px; font-size: 28px; cursor: pointer; color: #888;">&times;</span>
        
        <h3 style="color: #333; margin-bottom: 10px;">Reset Password</h3>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Verify your Email and Phone to get your code.</p>
        
        <form id="forgotForm">
            <div style="margin-bottom: 15px; text-align: left;">
                <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #444;">Email Address</label>
                <input type="email" id="reset_email" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;" required>
            </div>
            
            <div style="margin-bottom: 20px; text-align: left;">
                <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #444;">Phone Number</label>
                <input type="tel" id="reset_phone" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;" required>
            </div>
            
            <button type="submit" class="btn" style="width: 100%; padding: 12px; cursor: pointer;">Verify Identity</button>
        </form>

        <div id="codeResult" style="display:none; margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
            <p style="color: #28a745; font-weight: bold; margin-bottom: 10px;">Identity Verified!</p>
            <div style="display: flex; gap: 5px;">
                <input type="text" id="generatedCode" readonly style="flex: 1; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; text-align: center; font-family: monospace; font-weight: bold; background: white;">
                <button type="button" onclick="copyCode()" style="background: #007bff; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
</div>
    <script>
        // --- 5. Toggle Logic for Login and Sign Up ---
const toggleBtns = document.querySelectorAll('.toggle-btn');
const loginForm = document.getElementById('login-form');
const signupForm = document.getElementById('signup-form');

toggleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove 'active' class from both buttons and forms
        toggleBtns.forEach(b => b.classList.remove('active'));
        loginForm.classList.remove('active');
        signupForm.classList.remove('active');

        // Add 'active' class to the clicked button and targeted form
        btn.classList.add('active');
        if (btn.dataset.form === 'login') {
            loginForm.classList.add('active');
        } else {
            signupForm.classList.add('active');
        }
    });
});
    const modal = document.getElementById('forgotModal');
    const forgotForm = document.getElementById('forgotForm');

    // 1. Open the Modal
    document.getElementById('openForgot').onclick = () => {
        modal.style.setProperty('display', 'flex', 'important');
    };

    // 2. Close the Modal
    document.querySelector('.close-modal').onclick = () => {
        modal.style.display = 'none';
        document.getElementById('codeResult').style.display = 'none'; // Reset result box
        forgotForm.style.display = 'block'; // Show form again
    };

    // 3. Handle the Data Fetching
    forgotForm.onsubmit = function(e) {
    e.preventDefault(); 

    const email = document.getElementById('reset_email').value;
    const phone = document.getElementById('reset_phone').value;

    console.log("Sending request for:", email, phone);

    fetch('../include/auth_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`
    })
    .then(response => response.text()) // Get raw text first to avoid the JSON crash
    .then(text => {
        try {
            const data = JSON.parse(text);
            if(data.success) {
                document.getElementById('forgotForm').style.display = 'none'; 
                document.getElementById('codeResult').style.display = 'block';
                document.getElementById('generatedCode').value = data.code;
            } else {
                alert("Details not found in the ETHIO FOOD database.");
            }
        } catch(err) {
            // This catches the <br /> error and shows you the REAL PHP error
            console.error("Server sent back invalid data:", text);
            alert("PHP Error! Check console to see the real error message.");
        }
    });
};

    // 4. Copy Function
    function copyCode() {
        const codeInput = document.getElementById('generatedCode');
        codeInput.select();
        document.execCommand('copy');
        alert("Password copied to clipboard!");
    }

    // Close on outside click
    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = 'none';
    };
</script>
</body>
</html>