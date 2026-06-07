<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/db.php');
include('includes/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_token'])) {
    $id_token = $_POST['id_token'];
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : 'index.php';

    // Verify token with Google's API
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($id_token);
    
    // Fetch token details
    $response = @file_get_contents($url);
    if ($response === false) {
        // cURL fallback if file_get_contents is disabled on url wrappers in php.ini
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    if ($response) {
        $data = json_decode($response, true);
        
        // Ensure email and audience are present, and audience matches our Google Client ID
        if (isset($data['email']) && isset($data['aud']) && $data['aud'] === GOOGLE_CLIENT_ID) {
            $email = mysqli_real_escape_string($conn, $data['email']);
            $name = mysqli_real_escape_string($conn, $data['name']);
            
            // Check if user exists in database
            $query = "SELECT * FROM users WHERE email='$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                // User exists, log them in
                $row = mysqli_fetch_assoc($result);
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                $_SESSION['user_role'] = $row['role'];
                $_SESSION['user_id'] = $row['user_id'];
                
                // Prefill details for passenger forms
                $_SESSION['customer_name'] = $row['name'];
                $_SESSION['customer_phone'] = $row['phone'];
                
                session_write_close();
                header("Location: " . $redirect_url);
                exit();
            } else {
                // User does not exist, register them
                $random_pass = bin2hex(random_bytes(16));
                $hashed_password = password_hash($random_pass, PASSWORD_DEFAULT);
                $phone = ''; // Google OAuth does not return mobile numbers
                
                $insert_query = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$hashed_password')";
                
                if (mysqli_query($conn, $insert_query)) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['user_id'] = mysqli_insert_id($conn);
                    $_SESSION['user_role'] = 'user';
                    
                    // Prefill details for passenger forms
                    $_SESSION['customer_name'] = $name;
                    $_SESSION['customer_phone'] = '';
                    
                    session_write_close();
                    header("Location: " . $redirect_url);
                    exit();
                } else {
                    $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
                    header("Location: " . $redirect_url . $separator . "error=register_failed");
                    exit();
                }
            }
        }
    }
    
    // Redirect back on validation failure
    $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
    header("Location: " . $redirect_url . $separator . "error=google_auth_failed");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
