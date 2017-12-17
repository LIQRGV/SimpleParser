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
        foreach ($rr = $this->parseNumOpr($string) as $key => $val) {
            $key === 0 and $r = $val;
            if ($this->checkType($val) === OPR) {
                $r = self::operate((int)$r, $val, (int)$rr[$key+1]);
            }
        }
        return $r;
    }

    /**
     * @param   int     $a1
     * @param   string  $a2
     * @param   int     $a3
     * @return  int
     */
    private static function operate($a1, $a2, $a3)
    {
        return ($a2 === '+' ? $a1 + $a3 : ($a2 === '-' ? $a1 - $a3 : ($a2 === '*' ? $a1 * $a3 : ($a2 === '/' ? (int) ($a1 / $a3) : ~0))));
    }

    /**
     * @param   string  $string
     * @throws  \Exception
     * @return  array
     */
    public function parseNumOpr($string)
    {
        if (($this->checkType($curtype = $string[0])) === OPR && $curtype !== "-") {
            throw new \Exception("Syntax error", 1);
        }
        $string = str_replace(" ", "", $string);
        $len    = strlen($string);
        if ($len === 1) {
            return [$string];
        }
        $r = [];
        for ($i=0; $i < $len; $i++) {
            if ($i === 0) {
                $r[$i] = $string[$i];
            } else {
                if ($this->checkType($string[$i]) === OPR and $this->checkType($string[$i-1]) === OPR) {
                    throw new \Exception("Syntax error", 1);
                }
                if (($this->checkType($string[$i]) === NUMBER and $this->checkType($string[$i-1]) === NUMBER) or
                    ($this->checkType($string[$i]) === NUMBER and $string[$i-1] === "-")
                ) {
                    $r[$i-1] .= $string[$i];
                } else {
                    $r[$i]    = $string[$i];
                }
            }
        }
        $rr = [];
        foreach ($r as $val) {
            $rr[] = $val;
        }
        unset($r);
        if ($this->checkType($rr[sizeof($rr) - 1]) === OPR) {
            throw new \Exception("Syntax error", 1);
        }
        return $rr;
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
