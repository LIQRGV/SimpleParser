<?php

namespace LIQRGV\SimpleParser;

define("NUMBER", "number");
define("OPR", "opr");
define("SPACE", "space");

class Parser
{
    function calculateString($string) {
        $stack = $this->parseNumOpr($string);
        $initValue = array_shift($stack);
        $chunkedArray = array_chunk($stack, 2);
        $reducer = function($carrier, $currentVal) {
            switch($currentVal[0]) {
            case "+":
                return $carrier + $currentVal[1];
            case "-":
                return $carrier - $currentVal[1];
            case "*":
                return $carrier * $currentVal[1];
            case "/":
                return intdiv($carrier, $currentVal[1]);
            }
        };
        return array_reduce($chunkedArray, $reducer, $initValue);
    }

    function parseNumOpr($string) {
        $normalizedBucket = array();
        $typeTracker = new \SplFixedArray(2); // we want so save last 2, anyway

        $length = strlen($string);

        for($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            $type = $this->checkType($char);

            if($type == SPACE) {
                continue;
            } else {
                list($normalizedBucket, $typeTracker) = $this->addToBucket($normalizedBucket, $typeTracker, $char, $type);
            }
        }

        {
            $typeTrackerLength = count($typeTracker);
            if($typeTracker[$typeTrackerLength - 1] == OPR) {
                throw new \Exception("invalid input"); // OPR at last is illegal
            }
        }

        return $normalizedBucket;
    }

    function checkType($char) {
        if($this->isSpace($char)) {
            return SPACE;
        } else if($this->isOpr($char)) {
            return OPR;
        } else if($this->isNumber($char)) {
            return NUMBER;
        } else {
            throw new \Exception("invalid input");
        }
    }

    private function isSpace($char) {
        return $char == " ";
    }

    private function isOpr($char) {
        $oprCollection = array("*", "/", "+", "-");
        return in_array($char, $oprCollection);
    }

    private function isNumber($char) {
        $numCollection = array("0","1","2","3","4","5","6","7","8","9");
        return in_array($char, $numCollection);
    }

    private function addToBucket($bucket, $typeTracker, $char, $typeToAdd) {
        $bucketSize = count($bucket);
        $typeArray = $typeTracker->toArray();

        if(!is_null($typeArray[1])) {
            if($typeArray[1] != $typeToAdd) {
                if($typeToAdd == NUMBER) {
                    if(array(OPR, OPR) == $typeArray) {
                        $lastOpr = array_pop($bucket);
                        if($lastOpr == "-") {
                            $char *= -1;
                        }
                    }
                }
                $typeArray[0] = $typeArray[1];
                $typeArray[1] = $typeToAdd;
                $bucket[] = $char;
            } else {
                if($typeToAdd == NUMBER) {
                    $bucket[$bucketSize - 1] *= 10;
                    if($bucket[$bucketSize - 1] > 0) {
                        $bucket[$bucketSize - 1] += $char;
                    } else {
                        $bucket[$bucketSize - 1] -= $char;
                    }
                } else {
                    if($typeArray[0] == OPR) {
                        throw new \Exception("invalid input"); // OPR OPR OPR is illegal
                    } else if($char == "/" || $char == "*") {
                        throw new \Exception("invalid input"); // OPR (*||/) is illegal
                    } else {
                        $typeArray[0] = $typeArray[1];
                        $typeArray[1] = $typeToAdd;
                        $bucket[] = $char;
                    }
                }
            }
        } else if(!is_null($typeArray[0])) {
            if($typeArray[0] != $typeToAdd) {
                if($typeArray[0] == OPR) {
                    $firstOpr = array_shift($bucket);
                    if($firstOpr == "-") {
                        $bucket[0] = $char * -1;
                    } else {
                        $bucket[0] = $char;
                    }
                    $typeArray[0] = $typeToAdd;
                } else {
                    $typeArray[1] = $typeToAdd;
                    $bucket[] = $char;
                }
            } else {
                if($typeToAdd == NUMBER) {
                    $bucket[$bucketSize - 1] *= 10;
                    if($bucket[$bucketSize - 1] > 0) {
                        $bucket[$bucketSize - 1] += $char;
                    } else {
                        $bucket[$bucketSize - 1] -= $char;
                    }
                } else {
                    if($typeArray[0] == OPR) {
                        throw new \Exception("invalid input"); // OPR OPR at first is illegal
                    } else {
                        $typeArray[1] = $typeToAdd;
                        $bucket[] = $char;
                    }
                }
            }
        } else {
            if($char == "/" || $char == "*") {
                throw new \Exception("invalid input");   // * or / at first is illegal
            } else {
                $typeArray[0] = $typeToAdd;
                $bucket[0] = $char;
            }
        }

        return array($bucket, \SplFixedArray::fromArray($typeArray));
    }

}
