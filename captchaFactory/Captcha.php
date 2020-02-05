<?php
namespace captchaFactory;

/**
 * Call the captcha builder
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Samuel Gasc <contact@samuelgasc.fr>
 */
class Captcha
{
    protected $captchaSerialNb;
    protected $captchaBuilder;
    protected $pathToCaptcha;

    public function __construct(string $pathToCaptcha)
    {
        if (empty($pathToCaptcha))
        {
            throw new \InvalidArgumentException("The path to captcha must not be empty");
        }

        $this->pathToCaptcha = $pathToCaptcha;
    }

    public function getCaptcha(int $captchaLength)
    {
        $this->CaptchaBuilder = new CaptchaBuilder($this->pathToCaptcha);
        
        return $this->CaptchaBuilder->buildCaptcha($captchaLength);
    }
}
