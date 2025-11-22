<?php
	session_start();

	// Get errors and old values from session
	$errors = $_SESSION['errors'] ?? array();
	$values = $_SESSION['old_values'] ?? array(
			'mem_id' => '',
			'password' => ''
	);

	// Clear session data after retrieving it
	unset($_SESSION['errors'], $_SESSION['old_values']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!--
    Name: Justin Edwards
    ID: 2106166
  -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/page.css">
  <link rel="stylesheet" href="styles/login_page.css">
  <!-- Remove duplicate Poppins font import since it's in page.css -->
  <script src="https://kit.fontawesome.com/1cd4fd6359.js" crossorigin="anonymous"></script>

  <title>Login Page</title>
</head>
<body>
  <div class="container">
    <div class="content">
      <div class="title">
       <h3>LOGIN</h3>
      </div>
      <form action="loginval.php" method="post" enctype="multipart/form-data">
        <div class="user-details">
          <div class="user-input">
            <label for="">Member ID: </label>
            <?php $cls = isset($errors['mem_id']) ? 'input-error' : (($values['mem_id'] !== '') ? 'input-success' : ''); ?>
            <input class="<?php echo $cls; ?>" type="text" name="mem_id" placeholder="Enter your ID Number" value="<?php echo htmlspecialchars($values['mem_id'], ENT_QUOTES); ?>">
            <?php if (isset($errors['mem_id'])): ?>
              <div class="error-text"><?php echo htmlspecialchars($errors['mem_id']); ?></div>
            <?php endif; ?>
          </div>
          <div class="user-input">
            <label for="">Password: </label>
            <?php $cls = isset($errors['password']) ? 'input-error' : (($values['password'] !== '') ? 'input-success' : ''); ?>
            <input class="<?php echo $cls; ?>" type="password" name="password" placeholder="••••••••" value="<?php echo htmlspecialchars($values['password'], ENT_QUOTES); ?>">
            <?php if (isset($errors['password'])): ?>
              <div class="error-text"><?php echo htmlspecialchars($errors['password']); ?></div>
            <?php endif; ?>
          </div>
          <button type="submit" class="loginbtn" name="submitbtn">Login</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>