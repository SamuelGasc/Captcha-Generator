<?php
// Basic server side verification (if the user writes the good captcha, then the form is validated)
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['myCaptchaFieldForExample']) && $_POST['myCaptchaFieldForExample'] === $_SESSION['captcha'])
    {
        echo '<h3>Congratulations, this is the right captcha!</h3><a href="index.php">Try again</a>';
    }
    else
    {
        echo '<h3>Wrong captcha.</h3><a href="index.php">Try again</a>';
    }
}
else
{   
    header("Location: index.php");
    exit;
}
