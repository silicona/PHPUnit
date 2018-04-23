<?php


namespace App\MasTest;
//namespace Src;

/**
* Example class with private constructor.
*
*/
//class ClassPrivateConstruct
class Constructor {
    /**
     * Unique instance.
     *
     * @var \stdClass number of pings
     */
    private static $instance = null;

    /**
     * Variable One.
     *
     * @var string
     */
    protected $varOne = null;

    /**
     * Variable Two.
     *
     * @var string
     */
    protected $varTwo = null;

    /**
     * Private constructor.
     *
     * @param string $varOne Variable One.
     * @param string $varTwo Variable Two.
     *
     *
     * @return void
    */
    private function __construct($varOne, $varTwo)
    {
        $this->varOne = $varOne;
        $this->varTwo = $varTwo;
    }

    /**
     * Public creation of instance.
     *
     * @param string $varOne Variable One.
     * @param string $varTwo Variable Two.
    *
     * @return ClassPrivateConstruct
    */
    public static function create($varOne, $varTwo)
    {
        if (!self::$instance) {
            //self::$instance = new ClassPrivateConstruct($varOne, $varTwo);
            self::$instance = new Constructor($varOne, $varTwo);
        }

        return self::$instance;
    }
}

?>