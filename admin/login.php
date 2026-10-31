<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('uploads/WhatsApp Image 2025-02-10 at 15.46.24_fe750651.jpg') no-repeat center center/cover;
            position: relative;
        }

        .header {
            position: absolute;
            top: 50px;
            left: 50px;
        }

        .header img {
            height: 70px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: 10px;
           box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
            text-align: center;
            width: 350px;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container input {
            width: 93%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background: #243043;
            color: #fff;
            border: 1ps solid black;
            border-radius: 5px;
            cursor: pointer;
            color:white;
            
        }

        .forgot-password {
            margin-top: 10px;
        }

        .forgot-password a {
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="/"><img src="/assets/corporate/img/Hangar-2-4-White-Final-1.png" alt="Logo"></a>
    </div>

    <div class="form-container">
        <h2>Admin Login</h2>
        <form id="loginForm" action="admin-login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>">
            
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="password" name="password" id="password" placeholder="Password" required>

            <button type="submit">Login</button>

            <div class="forgot-password">
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </form>

        <div id="messageBox" style="color:red; font-size:15px;"></div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
    $("#loginForm").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        var email = $("#email").val();
        var password = $("#password").val();

        $.ajax({
            url: "admin-login.php",
            type: "POST",
            data: { email: email, password: password },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $("#messageBox").css("color", "green").text(response.message);
                    setTimeout(() => {
                        window.location.href = "index.php"; // Redirect on success
                    }, 1000);
                } else {
                    $("#messageBox").css("color", "red").text(response.message);
                }
            },
            error: function () {
                $("#messageBox").css("color", "red").text("An error occurred. Please try again.");
            }
        });
    });
});

    </script>
</body>
</html>
