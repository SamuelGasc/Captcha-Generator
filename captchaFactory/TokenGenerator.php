<?php
namespace captchaFactory;

/**
 * Generate a serial of random characters
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Samuel Gasc <contact@samuelgasc.fr>
 */
class TokenGenerator
{
    const LOWERCAPS = 0;
    const UPPERCAPS = 1;
    const NUMBERS   = 2;
    const SYMBOLS   = 3;

    /**
     * The bases' models, contains all the bases
     *
     * @var array
     */
    protected $validBases = [];

    /**
     * The selected bases to be used when generating the token
     *
     * @var array
     */
    protected $bases = [];

    public function __construct()
    {
        $this->validBases = [self::LOWERCAPS => 'abcdefghijklmnopqrstuvwxyz',
                             self::UPPERCAPS => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                             self::NUMBERS   => '0123456789',
                             self::SYMBOLS   => '&#-_+%.^'];
    }

    /**
     * Will use the $requiredBases given to select which base(s) should be mixed in order
     * to generate the token. And use the $tokenLengh given to calculate the length of the
     * token to be generated.
     *
     * @param  int     $tokenLength
     * @param  array   $requiredBases
     * @return string  The generated token
     */
    public function getToken(int $tokenLength, array $requiredBases)
    {
        $requiredBasesMatchesValidBases = [];
            
        // will register an error (false) in "$requiredBasesMatchesValidBases" if one the value 
        // in "$requiredBases" is not matching any key "validBases[]".
        foreach ($requiredBases as $requiredBase)
        {
            array_key_exists($requiredBase, $this->validBases) 
                ? $requiredBasesMatchesValidBases[] = true
                : $requiredBasesMatchesValidBases[] = false;
        }

        // Verify that "$requiredBases" contains only values refering to "$validBases" values.
        if (!in_array(false, $requiredBasesMatchesValidBases))
        {
            $baseContent = [];

            foreach ($this->validBases as $key => $value)
            {
                if (in_array($key, $requiredBases))
                {
                    $baseContent[] = $value;
                }
            }

            $this->bases = $baseContent;
        }
        else
        {
            throw new \InvalidArgumentException('"$requiredBases" parameter must contains only
                                                 int numbers refering to a base\'s constant');
        }

        if (!empty($this->bases))
        {
            $basesNB = count($this->bases);

            if ($tokenLength >= $basesNB)
            {
                $oneOfEach = $tokenLength / $basesNB; // Will select the number
                $oneOfEach = floor($oneOfEach);
            }
            else
            {
                $oneOfEach = 1;
            }

            $psswd = '';

            foreach ($this->bases as $base)
            {
                $charsNB = strlen($base);

                for ($i = 0; $i <= $oneOfEach; $i++)
                { 
                    $psswd .= $base[random_int(0, ($charsNB - 1))];
                }
            }

            $a = strlen($psswd);

            if ($a > $tokenLength)
            {
                return substr(str_shuffle($psswd), 0, $tokenLength);
            }
            elseif (($tokenLength - $a) > 0)
            {
                $remaind = $this->bases[random_int(0, ($basesNB - 1))];
                $charsNB = strlen($remaind);

                for ($i = 0; $i < $a; $i++)
                {
                    $psswd .= $remaind[random_int(0, ($charsNB - 1))];
                }
                
                return str_shuffle($psswd);
            }

            return str_shuffle($psswd);
        }
        else
        {
            throw new \RuntimeException('The token couldn\'t be generated');
        }
    }
}