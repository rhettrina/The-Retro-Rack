<?php
session_start();

$message = '';
if (isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']); // Clear the message after using it
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Box icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
  <!-- Custom StyleSheet -->
  <link rel="stylesheet" href="./css/styles.css" />
  <title>Login</title>
  <style>
    .error-message {
      color: red;
      font-size: 0.9em;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <?php if (!empty($message)): ?>
    <div id="popupMessage" class="popup-message">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>
  <div class="top-nav">
    <div class="container d-flex">
      <p>Order Online Or Call Us:(+63) 9073434119</p>
      <ul class="d-flex">
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="admin_dashboard.php" id="adminLink">Admin</a></li>
      </ul>
    </div>
  </div>
  <!-- Login -->
  <div class="container">
    <div class="login-form">
      <form action="login.php" method="post">
        <h1>Login</h1>
        <p>
          Already have an account? Login or
          <a href="signupview.php">Sign Up</a>
        </p>

        <label for="login">Username or Email</label>
        <input type="text" placeholder="Enter Username or Email" name="login" id="login" required />
        <div id="login-error" class="error-message"></div>

        <label for="psw">Password</label>
        <input type="password" placeholder="Enter Password" name="password" id="psw" required />
        <div id="password-error" class="error-message"></div>

        <label>
          <input type="checkbox" checked="checked" name="remember" style="margin-bottom: 15px" />
          Remember me
        </label>

        <p>
          By logging in, you agree to our
          <a href="terms.xml">Terms & Privacy</a>.
        </p>

        <div id="login-failed" class="error-message"></div>

        <div class="buttons">
          <button type="button" class="cancelbtn">Cancel</button>
          <button type="submit" class="signupbtn">Login</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'visitorfooter.php'; ?>

  <!-- Custom Script -->
  <script src="./js/index.js"></script>

  <!-- JavaScript to Handle Error Messages -->
  <script>
    // Function to get query parameters
    function getQueryParams() {
      let params = {};
      window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        params[key] = decodeURIComponent(value);
      });
      return params;
    }

    // Display error messages based on query parameters
    window.onload = function () {
      const params = getQueryParams();

      if (params.error) {
        const error = params.error;

        switch (error) {
          case 'empty_login':
            document.getElementById('login-error').innerText = "Please enter your username or email.";
            break;
          case 'empty_password':
            document.getElementById('password-error').innerText = "Please enter your password.";
            break;
          case 'invalid_credentials':
            document.getElementById('login-failed').innerText = "Invalid username/email or password.";
            break;
          case 'no_account':
            document.getElementById('login-failed').innerText = "No account found with that username or email.";
            break;
          default:
            document.getElementById('login-failed').innerText = "An unknown error occurred.";
        }
      }
    }
  </script>
</body>

</html>