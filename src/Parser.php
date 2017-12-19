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
        $pos = $pos2 = $cost = $rr = 0;
        $raw = $this->parseNumOpr($string);
        $rn  = [];
        while (in_array("*", $raw) || in_array("/", $raw)) {
            foreach ($raw as $key => $val) {
                if ($val === "*" or $val === "/") {
                    $raw[$key-1] = self::operate((int) $raw[$key-1], $val, (int) $raw[$key+1]);
                    unset($raw[$key], $raw[$key+1]);
                    $rtmp = [];
                    foreach ($raw as $subval) {
                        $rtmp[] = $subval;
                    }
                    $raw = $rtmp;
                    break;
                }
            }
        }
        while (in_array("+", $raw) || in_array("-", $raw)) {
            foreach ($raw as $key => $val) {
                if ($val === "+" or $val === "-") {
                    $raw[$key-1] = self::operate((int) $raw[$key-1], $val, (int) $raw[$key+1]);
                    unset($raw[$key], $raw[$key+1]);
                    $rtmp = [];
                    foreach ($raw as $subval) {
                        $rtmp[] = $subval;
                    }
                    $raw = $rtmp;
                    break;
                }
            }
        }
        return $raw[0];
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

        $string = str_replace(" ", "", $string);

        if (($this->checkType($string[0]) === OPR and ($string[0] !== "-" and $string[0] !== "+"))) {
            throw new \Exception("Syntax error", 1);
        }

        if (($len = strlen($string)) === 1) {
            if ($this->checkType($string) === OPR) {
                throw new \Exception("Syntax error", 1);
            }
            return [$string];
        }

        $r   = [];
        $cost = $pos = 0;

        for ($i=0; $i < $len; $i++) {
            if ($i === 0) {
                $r[$pos] = $string[$i];
                if ($this->checkType($string[$i]) === OPR) {
                    $cost = 2;
                }
            } else {
                if (isset($string[$i]) and $this->checkType($string[$i-1]) === OPR and $this->checkType($string[$i-1]) === OPR and $this->checkType($string[$i]) === OPR) {
                    throw new \Exception("Syntax error", 1);
                } elseif ($this->checkType($string[$i]) === NUMBER) {
                    if (1 === $cost and $this->checkType($r[$pos]) === OPR and isset($r[$pos-1]) and $this->checkType($r[$pos-1]) === OPR) {
                        if ($r[$pos] === "+" or $r[$pos] === "-") {
                            $r[$pos] .= $string[$i];
                            $cost = 0;
                        } else {
                            throw new \Exception("Syntax error", 1);
                        }
                    } elseif (2 === $cost) {
                        $r[$pos] .= $string[$i];
                    } elseif ($this->checkType($r[$pos]) === NUMBER) {
                        $r[$pos] .= $string[$i];
                    } else {
                        $r[++$pos] = $string[$i];
                    }
                } else {
                    if ($this->checkType($string[$i]) === OPR) {
                        if (2 === $cost and $i === 1) {
                            throw new \Exception("Syntax error", 1);
                        }
                        $r[++$pos] = $string[$i];
                        if (isset($string[$i+1]) and $this->checkType($string[$i]) === OPR) {
                            $cost = 1;
                        }
                    }
                }
            }
        }

        if ($this->checkType(end($r)) === OPR) {
            throw new \Exception("Syntax error", 1);
        }

        return $r;
    }

    /**
     * @param   char        $char
     * @throws  \Exception
     * @return  string
     */
    public function checkType($char)
    {
        return (in_array($char, ["+", "-", "/", "*"]) ? OPR : (is_numeric($char) ? NUMBER : ($char === " " ? SPACE : (function() use ($char) { throw new \Exception("Unknown type", 1); })())));
    }
}
