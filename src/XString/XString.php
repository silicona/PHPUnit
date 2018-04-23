<?php

    namespace App\XString;
    //namespace App\clases;

    class XString {

        protected $str;

        /**
         * Construct
         *
         * @param string  $str          Str var.
         * @param string  $encoding    Encoding.
         * @param boolean $forceEncode Force encoding.
         *
         * @return void
        */
        public function __construct($str, $encoding = 'UTF-8', $forceEncode = true)
        {
            if (mb_detect_encoding($str) != 'UTF-8' && $forceEncode) {
                $str = mb_convert_encoding($str, 'UTF-8');
            }
            $this->str = $str;
        }

        /**
         * Return true, if the string starts with $prefix
         *
         * @param string $prefix Prefix string.
         *
         * @return boolean
        */
        public function startWith($prefix)
        {
            return mb_substr($this->str, 0, mb_strlen($prefix)) === $prefix;
        }

        /**
         * Return true, if the string ends with $suffix
         *
         * @param string $suffix Suffix string.
         *
         * @return boolean
        */
        public function endWith($suffix)
        {
            return mb_substr($this->str, mb_strlen($this->str) - mb_strlen($suffix), mb_strlen($suffix)) == $suffix;
        }
    }

?>