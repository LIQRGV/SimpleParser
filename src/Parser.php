<?php

namespace LIQRGV\SimpleParser;

define("NUMBER", "number");
define("OPR", "opr");
define("SPACE", "space");

class Parser
{
    /**
     * @param   string  $string
     * @throws  \Exception
     * @return  int
     */
    public function calculateString($string)
    {
    }

    /**
     * @param   int     $a1
     * @param   string  $a2
     * @param   int     $a3
     * @return  int
     */
    private static function operate($a1, $a2, $a3)
    {
    }

    /**
     * @param   string  $string
     * @throws  \Exception
     * @return  array
     */
    public function parseNumOpr($string)
    {
    }

    /**
     * @param   char        $char
     * @throws  \Exception
     * @return  string
     */
    public function checkType($char)
    {
        return (
            in_array($char, ["-", "+", "/", "*"]) ? OPR : (
                 $char === " " ? SPACE : (
                    is_numeric($char) ? NUMBER : (function () {
                        throw new \Exception("Unknown type", 1);
                    })()
                 )
            )
        );
    }
}
