<?php

/* ------------------ Example of captcha's installation'  ------------------ */

// Calls the autoloader
require __DIR__.'/../captchaFactory/SplClassLoader.php';

// Register the captcha generator's directory
$loader = new SplClassLoader('captchaFactory', __DIR__.'/../');
$loader->register();


// Necessary in order to instantiate the captcha object
use \captchaFactory\Captcha;

$pathToCaptcha = '../captchaFile/';



/* ------------------ Example of captcha's use  ------------------ */

try
{
	// Instantiates the captcha object (will do all the captcha's creation job)
	$captchaBuilder = new Captcha($pathToCaptcha);

	// creates the captcha
	if (true === $captchaBuilder->getCaptcha(8))
	{
		$captchaFileName = $_SESSION['captchaPngFileName'];

		?>

		<!-- EXAMPLE OF CAPTCHA USE -->
		<!DOCTYPE html>
		<html>
		<head>
			<title>Test captcha</title>
		</head>
		<body>
			<?php
				/* !!!!! It goes obvious that this is a HUGE security leak to display the following to the user  !!!!! */
				/* !!!!!          DO NOT FORGET TO REMOVE IT FROM YOUR CODE WHEN STEPING TO PRODUCTION           !!!!! */

				print_r('<pre>');
				var_dump($_SESSION['captcha']);
				print_r('</pre>');

				/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
			?>
			<img src="<?= $pathToCaptcha.$captchaFileName; ?>" width="250" height="auto" />
			<hr />
			<form method="POST" action="captchaVerification.php">
				<p>
					<input type="text" name="myCaptchaFieldForExample" placeholder="Write the captcha's value here." /><br/>
					<input type="submit">
				</p>
			</form>
		</body>
		</html>

	<?php
	}

}
catch (Exception $e)
{
	echo "<h3>" . $e->getMessage() . "</h3>";
}