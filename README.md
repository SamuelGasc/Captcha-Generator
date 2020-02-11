# Captcha-Generator
A plugin which will generate captchas ready to be used for your website's forms

This captcha generator is licensed under the MIT license. Copyright 2019 Samuel Gasc
(see http://www.opensource.org/licenses/mit-license.html ).

1. Installation

    You just need to copy this captcha plugin into your "vendor" directory.

    In order to store the generated captcha PNG files, You can decide to use the provided 
    "captchaFile" directory or to use your own. But whatever directory you choose to use, make sure 
    that it is writable by PHP or else the captcha will simply not be generated.
    A good practice is to give the ownership of the captcha storage directory to your Apache server
    and to give write, read and access rights to it while keeping the other visitors rights
    to "access" (chmod 755).



2. Utilisation

    In order to use this captcha, you must require the loader ("SplClassLoader") provided by the
    Symfony project (MIT license).
    A copy of this file is located in the "captchaFactory" directory so you can just call it with
    the php "require" function. You, of course, don't have to do so if your application is already
    using this autoloader.

    In both situation what is important is that you register the "captchaFactory" directory with the
    loader so that the classes that the captcha needs to use can be instanciated.

    You will then have to instantiate the "Captcha" class using its namespace
    ("use \captchaFactory\Captcha;").
    While instanciating the "Captcha" class you must give it the path to captcha's files (as a 
    a parameter of the constructor). This path must be relative for the PHP const "__DIR__" will be
    automatically added after, during the captcha's creation. Don't open the path with a slash.
    (example of valid path: '../captchaFile/').

    You now only have to call the "getCaptcha" method in the "Captcha" object with a positive 
    integer (between 5 and 15) as a parameter. It will generate a captcha containing random 
    characters (as much as the number you provided in the "getCaptcha" parameter).



3. How does it works

    As explained above, the captcha generator print a serial of characters into a PNG file. 
    Now it's important to notice that it also gives the same exact serial to the superglobal 
    "$_SESSION['captcha']" and gives the PNG file's name to the superglobal 
    "$_SESSION['captchaPngFileName']".

    A good way to use this captcha is to create an html tag "img" and give the 
    "$_SESSION['captchaPngFileName']" to the "src" attribute. Then to create a simple text
    form field (ex:'name="myCaptchaField"') which will contains the captcha validation written
    by the user.
    
    This way, you just have to do a basic server side comparison in order to validate your form.
    
    (ex: "if ($_POST['myCaptchaField'] === $_SESSION['captcha'])" ).

    Note: Since the superglobal "$_SESSION['captcha']" is overwritten every time the "getCaptcha"
          method is called, if the form is sending datas to the same page (action="#") then the 
          "$_SESSION['captcha']" will be overwritten again and the value given by the user
          ($_POST['myCaptchaField']) won't match the new "$_SESSION['captcha']".
          Knowing this, it is way easier to send data to another script i order to do the
          server side validation.

    
An example of captcha's use is available in the "test" directory. 

Enjoy, and may the force be with you !
