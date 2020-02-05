<?php
namespace captchaFactory;

/**
 * Build a captcha PNG file containing a serial of random characters and gives the serial
 * to the $_SESSION['captcha'] variable. The PNG file is located in the path to the captcha
 * provided while instanciating this class.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Samuel Gasc <contact@samuelgasc.fr>
 */
class CaptchaBuilder
{
    protected $tokenGenerator;
    protected $user;
    protected $font;
    protected $captcha;
    protected $pathToCaptcha;
    protected $randomString;

    public function __construct(string $pathToCaptcha)
    {
        $this->tokenGenerator = new TokenGenerator;
        $this->user           = new User;
        $this->pathToCaptcha  = __DIR__.'/'. $pathToCaptcha;

        if ( !is_dir($this->pathToCaptcha) || !is_writable($this->pathToCaptcha))
        {
            throw new \InvalidArgumentException('The path to captcha file must be a valid directory writable by the
                                                 Apache server.');
            
        }
    }

    /**
     * This function will use the given parameter to build a captcha. 
     * The number of characters will change the number of ellipses, and vertical
     * lines (to make the OCR difficult to be done). It will also change the the captcha's width
     * and height as well and the characters' size and the space between them. This in
     * order to have always all of the captcha's items occupying more or less
     * proportionally the captcha's space.
     *
     * In order to function properly, a serial is generated (using the tokenGenerator class),
     * this serial is printed into the captcha PNG file and attributed to the "$_SESSION['captcha']" variable.
     *
     * @param   int   $tokenLength  The number of characters to put in the captcha
     * @return  bool                Whether the captcha has been successfully generated or not
     */
    public function buildCaptcha(int $tokenLength)
    {
        if ($tokenLength < 5 || $tokenLength > 15)
        {
            throw new \InvalidArgumentException('The "$tokenLength" parameter for the "buildCaptcha()" method must be
                                                 a positive integer between 5 and 15');
        }

        /*
         *  This delete the existing captcha file in order not to have an army of useless png files
         *  when creating a new captcha everytime. (Make sure your apache server has the right to access,
         *  write and delete files in the "pathToCaptcha" directory).
         */
        if(null !== $this->user->getAttribute('captchaPngFileName'))
        {
            $captchaFile = $this->pathToCaptcha . '/' . $this->user->getAttribute('captchaPngFileName');

            if (file_exists($captchaFile))
            {
                unlink($captchaFile);
            }

        }

        // Gives a rand name to the captcha PNG file.
        $captchaPngFileName = $this->tokenGenerator->getToken(35, [TokenGenerator::LOWERCAPS,
                                                                   TokenGenerator::UPPERCAPS,
                                                                   TokenGenerator::NUMBERS]);
        
        $this->user->setAttribute('captchaPngFileName', $captchaPngFileName.'.png');


        // Settle the random string to be used in the captcha and keep it in memory using the super global "$_SESSION"
        // (See the "user" class for more info)
        $randStr = $this->tokenGenerator->getToken($tokenLength, [TokenGenerator::LOWERCAPS,
                                                                  TokenGenerator::UPPERCAPS,
                                                                  TokenGenerator::NUMBERS]);

        $this->randomString = $randStr;
        $this->user->setAttribute('captcha', $randStr);

        $ratioWidth      = $tokenLength;
        
        $imgWidth        = $ratioWidth * 105;
        $imgHeight       = $imgWidth / 3;
        
        $xMinRatio       = $imgWidth / $ratioWidth * 0.9;
        $xMaxRatio       = ($imgWidth - ($imgWidth / ($ratioWidth * 2)));

        $yMinRatio       = ($imgHeight / 5);
        $yMaxRatio       = $yMinRatio * 4;

        $img             = imagecreatetruecolor($imgWidth, $imgHeight);

        $backgroundColor = imagecolorallocate($img, random_int(160, 230), random_int(160, 230), random_int(160, 230));
        
        imagefilledrectangle($img, 0, 0, $imgWidth, $imgHeight, $backgroundColor);
        
        /*
         * This loop will put the random string in the captcha using different random parameters
         * The lines and ellipses will be put separately, in others loops.
         */
        for ($i = 0; $i < strlen($this->randomString); $i++)
        { 
            $xPos              = ($imgWidth / (2 * $ratioWidth)) + $i * ($ratioWidth * (rand(97, 103) / $ratioWidth));
            $yPos              = random_int($imgHeight / 3, ($imgHeight / 4) * 3);
            $angle             = random_int(-25, 25);
            $fontChoice        = random_int(0, 1);
            $currentChar       = $this->randomString[$i];
            $pixelOrBlurEffect = random_int(0, 2);
            $pixelEffectLevel  = random_int(1, 2);
            $smoothnessLevel   = random_int(1, 5) * 50;
            $textColor         = imagecolorallocate($img,
                                                    random_int(100, 150),
                                                    random_int(100, 150),
                                                    random_int(100, 150));

            /* 
             * This shoud help a little bit to make the captcha more user friendly for it should extend the difference 
             * between the lowercases and the uppercase (the digits should take the same size than the uppercases but it
             * shouldn't matter for the difference is, normaly significant enough).
             */
            ctype_lower($this->randomString[$i])
                ? $size = random_int($imgWidth / 12, $imgWidth / 11.5)
                : $size = random_int($imgWidth / 10.5, $imgWidth / 10);

            if ($fontChoice === 0)
            {
                $this->setFont('marola.ttf');
            }
            elseif ($fontChoice === 1)
            {
                $this->setFont('whatshappened.ttf');
            }

            // these fonts are used when some characters show a really too unsignificant difference between
            // lowercases, uppercases and the numbers
            if(in_array($currentChar, ['0', 'O', 'I', 'l', 'c', 'C', 's', 'S', 'v', 'V', 'x', 'X', 'z', 'Z']))
            {
                $this->setFont('postnuclear2.ttf');

                /* 
                 * Ok, this is a little bit W.E.T (write everything twice) but, this font is 
                 * really smaller than the other
                 */
                ctype_lower($this->randomString[$i])
                    ? $size = random_int($imgWidth / 10, $imgWidth / 9.5)
                    : $size = random_int($imgWidth / 7.5, $imgWidth / 7);
            }
            elseif (in_array($currentChar, ['q', 'Q', '9', 'j', '1', 'o']))
            {
                $this->setFont('marola.ttf');
            }
            elseif (in_array($currentChar, ['w', 'W']))
            {
                $this->setFont('amadeus/amadeus.ttf');
            }
            elseif (in_array($currentChar, ['y', 'Y', 'L', 'u', 'U']))
            {
                $this->setFont('whatshappened.ttf');
            }

            // Print the random string in the captcha (requires truetype fonts (.ttf) )
            imagettftext($img,
                         $size,
                         $angle,
                         $xPos,
                         $yPos,
                         $textColor,
                         $this->font(),
                         $currentChar);

            if ($pixelOrBlurEffect === 1)
            {
                // Apply a pixel effect filter on the current char
                imagefilter($img, IMG_FILTER_PIXELATE, $pixelEffectLevel, true);
            }
            elseif ($pixelOrBlurEffect === 2) 
            {
                // Apply a blur effect filter on the current char
                imagefilter($img, IMG_FILTER_SMOOTH, $smoothnessLevel);
            }
        }

        /* --- Ellipses --- */
        $ellipseNb = floor($tokenLength * 0.75);

        for ($i = 0; $i < $ellipseNb; $i++) 
        { 
            $ellipseXposIncrem  = ($xMaxRatio - $xMinRatio) / $ellipseNb;
            $ellipseXminPos     = $xMinRatio + $i * $ellipseXposIncrem;
            $ellipseXmaxPos     = $xMinRatio + $ellipseXposIncrem + $i * $ellipseXposIncrem;

            $ellipseXpos   = random_int($ellipseXminPos, $ellipseXmaxPos);
            $ellipseYpos   = random_int($yMinRatio, $yMaxRatio);
            $ellipseWidth  = random_int(80, 240);
            $ellipseHeight = random_int(40, 100);
            $ellipseStart  = random_int(40, 60);
            $ellipseEnd    = random_int(290, 330);
            $ellipseColor  = imagecolorallocate($img,
                                                random_int(140, 190),
                                                random_int(140, 190),
                                                random_int(140, 190));
            
            imagesetthickness($img, random_int(1, 3));
            imagearc($img,
                     $ellipseXpos, 
                     $ellipseYpos, 
                     $ellipseWidth, 
                     $ellipseHeight, 
                     $ellipseStart, 
                     $ellipseEnd, 
                     $ellipseColor);
        }


        /* --- Vertical lines --- */
        $verticalLineNb = floor($tokenLength * 0.6);

        for ($i = 0; $i < $verticalLineNb; $i++) 
        { 
            $verticalLineXposIncrem  = ($xMaxRatio - $xMinRatio) / $verticalLineNb;
            $verticalLineXminPos     = $xMinRatio + $i * $verticalLineXposIncrem;
            $verticalLineXmaxPos     = $xMinRatio + $verticalLineXposIncrem + $i * $verticalLineXposIncrem;

            $verticalLineStartXpos   = random_int($verticalLineXminPos, $verticalLineXmaxPos);
            $verticalLineStartYpos   = random_int($yMinRatio, $yMinRatio * 1.1);
            $verticalLineEndXpos     = random_int($verticalLineXminPos, $verticalLineXmaxPos);
            $verticalLineEndYpos     = random_int($yMaxRatio * 0.9, $yMaxRatio);
            $verticalLineColor       = imagecolorallocate($img,
                                                          random_int(140, 190),
                                                          random_int(140, 190),
                                                          random_int(140, 190));
            
            imagesetthickness($img, random_int(1, 3));
            imageline($img,
                      $verticalLineStartXpos,
                      $verticalLineStartYpos,
                      $verticalLineEndXpos,
                      $verticalLineEndYpos,
                      $verticalLineColor);
        }

        
        /* --- Horizontal lines --- */
        $horizontalLineNb = 3;
                
        for ($i = 0; $i < $horizontalLineNb; $i++) 
        { 
            $horizontalLineYposIncrem  = ($imgHeight - ($yMinRatio * 2)) / $horizontalLineNb;
            $horizontalLineYminPos     = $horizontalLineYposIncrem + $i * $horizontalLineYposIncrem;
            $horizontalLineYmaxPos     = $horizontalLineYposIncrem * 2 + $i * $horizontalLineYposIncrem;

            $horizontalLineStartXpos   = random_int(($xMinRatio * 0.7), $xMinRatio);
            $horizontalLineStartYpos   = random_int($horizontalLineYminPos, $horizontalLineYmaxPos);
            $horizontalLineEndXpos     = random_int($xMaxRatio - $xMinRatio * 0.7, $xMaxRatio);
            $horizontalLineEndYpos     = random_int($horizontalLineYminPos, $horizontalLineYmaxPos);
            $horizontalLineColor       = imagecolorallocate($img,
                                                            random_int(140, 190),
                                                            random_int(140, 190),
                                                            random_int(140, 190));
            
            imagesetthickness($img, random_int(1, 3));
            imageline($img,
                      $horizontalLineStartXpos,
                      $horizontalLineStartYpos,
                      $horizontalLineEndXpos,
                      $horizontalLineEndYpos,
                      $horizontalLineColor);
        }

        $captcha = imagepng($img, $this->pathToCaptcha . $captchaPngFileName . '.png');
        
        return $captcha;
    }

    /* --- GETTERS --- */

    public function font()
    {
        return $this->font;
    }

    public function captcha()
    {
        return $this->captcha;
    }

    /* --- SETTERS --- */
    
    public function setFont($font)
    {
        $font = __DIR__.'/fonts/'.$font;

        if (file_exists($font))
        {
            $this->font = $font;
        }
        else
        {
            throw new \InvalidArgumentException('The font file : "'.$font.'" couldn\'t be found');
        }
    }

    public function setCaptcha($captcha)
    {
        $this->captcha = $captcha;
    }
}