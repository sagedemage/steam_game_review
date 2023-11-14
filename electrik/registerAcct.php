<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	
	</head>

	<body>


	<?php include('navbar.php'); ?>

		<h2>Register Account</h2>

		<form action="/register.php" method="post" accept-charset="utf-8"> <!--route to validateLogin.php for checking-->
			<div class="container">
				<label for="id"><b>Username</b></label>
				<input id="id" type="text" placeholder="Enter Username" name="id" required>

				<label for="email"><b>Email</b></label>
				<input id="email" type="text" placeholder="Enter Email" name="email" required>

				<label for="pwd"><b>Password</b></label>
				<input id="pwd" type="password" placeholder="Enter Password" name="pwd" required>

				<label for="cfrmpwd"><b>Confirm Password</b></label>
				<input id="cfrmpwd" type="password" placeholder="Confirm Password" name="cfrmpwd" required>

				<button type="submit" name="confirm">Submit</button>
			</div>

			<div class="container" style="background-color:#f1f1f1">
				<button type="button" class="cancelbtn" onclick="window.location.href='./index.php'">Cancel</button>



</div>
		</form>



	<?php include('footer.php'); ?>
	</body>
</html>
