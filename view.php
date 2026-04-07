<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Access</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }

        input[type="password"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #ff3c00;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #e03500;
        }

        .error-message {
            color: #ff3c00;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Enter Password</h2>
    <input type="password" id="password" placeholder="Enter Password" required>
    <br>
    <button onclick="checkPassword()">Submit</button>
    <p id="error-message" class="error-message"></p>

    <script>
        function checkPassword() {
            const correctPassword = "Rishit@5105"; // Set your hardcoded password here
            const userPassword = document.getElementById("password").value;

            if (userPassword === correctPassword) {
                // Redirect to another page
                window.location.href = "^~personal@dwk$~^.php"; // Replace with your target page
            } else {
                // Show error message
                document.getElementById("error-message").innerText = "Incorrect password. Please try again.";
            }
        }
    </script>
</body>
</html>
