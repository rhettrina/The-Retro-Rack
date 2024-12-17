<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
  <!-- Glide js -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/css/glide.core.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/css/glide.theme.css">
  <!-- Custom StyleSheet -->
  <link rel="stylesheet" href="./css/styles.css" />
  <link rel="stylesheet" href="./css/contact.css">

  <title>E-commerce Website</title>
</head>

<body>

  <?php include 'visitorheader.php'; ?>


  <div class="bgc">
    <div class="container9">
      <h1 style="font-size: 50px;">Get in Touch with Us!</h1>
      <div class="contact-info">
        <!-- Contact Form -->
        <form action="contact_process.php" method="post">
          <h2>Write to Us</h2>
          <input type="text" name="fullname" placeholder="Full Name" required>
          <input type="email" name="email" placeholder="Email" required>
          <input type="text" name="issue_type" placeholder="Issue Type" required>
          <label>Are you an existing customer? *</label>
          <div class="customer-check-wrap">
            <label><input type="radio" name="customercheck" value="Yes" required> Yes</label>
            <label><input type="radio" name="customercheck" value="No" required> No</label>
          </div>
          <textarea name="msg" cols="30" rows="10" placeholder="Your Message Here..." required></textarea>
          <button type="submit">Send</button>
        </form>
        <!-- Developer Details -->
        <div class="details">
          <h3>Developer's Info</h3>
          <p>Support available from 6am-5pm, Mon-Fri</p>
          <div class="expert-wrapper">
            <!-- Developer 1 -->
            <div class="expert">
              <img src="./images/developer1.jpg" alt="Louis Ricafrente" />
              <p>Louis Ricafrente</p>
            </div>
            <!-- Developer 2 -->
            <div class="expert">
              <img src="./images/developer2.jpg" alt="Trisha Panganiban" />
              <p>Trisha Panganiban</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'visitorfooter.php'; ?>
</body>

</html>