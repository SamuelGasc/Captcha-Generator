<?php
namespace captchaFactory;

session_start();
/**
 * Handles the session generated for the user accessing the server.
 *
 * @author Victor Thuillier <http://www.victorthuillier.com>
 */
class User
{
    public function getAttribute($attr)
    {
        return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
    }

    public function setAttribute($attr, $value)
    {
        $_SESSION[$attr] = $value;
    }

    public function getFlash()
    {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    public function hasFlash()
    {
        return isset($_SESSION['flash']);
    }

    public function setFlash($value)
    {
        $_SESSION['flash'] = $value;
    }

    public function logOut()
    {
        session_unset();
        session_destroy();
        session_start();
    }
}