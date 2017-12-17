<?php
namespace LIQRGV\SimpleParser;

require __DIR__ . "/../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use LIQRGV\SimpleParser\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->parser = new Parser;
    }

    public function testCheckTypeForOpr(){
        $this->assertEquals($this->parser->checkType('-'), "opr");
        $this->assertEquals($this->parser->checkType('+'), "opr");
        $this->assertEquals($this->parser->checkType('/'), "opr");
        $this->assertEquals($this->parser->checkType('*'), "opr");
    }

    public function testCheckTypeForSpace(){
        $this->assertEquals($this->parser->checkType(' '), "space");
    }

    public function testCheckTypeForNum(){
        $this->assertEquals($this->parser->checkType('1'), "number");
        $this->assertEquals($this->parser->checkType('2'), "number");
        $this->assertEquals($this->parser->checkType('3'), "number");
        $this->assertEquals($this->parser->checkType('4'), "number");
        $this->assertEquals($this->parser->checkType('5'), "number");
        $this->assertEquals($this->parser->checkType('6'), "number");
        $this->assertEquals($this->parser->checkType('7'), "number");
        $this->assertEquals($this->parser->checkType('8'), "number");
        $this->assertEquals($this->parser->checkType('9'), "number");
        $this->assertEquals($this->parser->checkType('0'), "number");
    }

    public function testCheckTypeInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->checkType("a");
    }

    public function testParseNumOprWithForbidenOprAtFirstInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("*1");
        $this->parser->parseNumOpr("/1");
    }

    public function testParseNumOprWith2OprAtFirstInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("++1");
    }

    public function testParseNumOprWith3OprInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("1+++1");
    }

    public function testParseNumOprWithOprOnLastInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("1+1+");
    }

    public function testParseNumOprWithSingleDigitNumber(){
        $expectedResult = array("1");
        $result = $this->parser->parseNumOpr("1");

        $this->assertEquals($result, $expectedResult);
    }

    public function testParseNumOprWithNonSingleDigitNumber(){
        $expectedResult = array("11");
        $result = $this->parser->parseNumOpr("11");

        $this->assertEquals($result, $expectedResult);
    }

    public function testParseNumOprWithSpaceNumber(){
        $expectedResult = array("11");
        $result = $this->parser->parseNumOpr("1   1");

        $this->assertEquals($result, $expectedResult);
    }

    public function testParseNumOprWithCombinedNumberOpr(){
        $expectedResult = array("11", "+", "1");
        $result = $this->parser->parseNumOpr("11+1");

        $this->assertEquals($result, $expectedResult);
    }

    public function testCalculateString() {
        $this->assertEquals($this->parser->calculateString("1+1"), 2);
        $this->assertEquals($this->parser->calculateString("-1+1"), 0);
        $this->assertEquals($this->parser->calculateString("-1*2"), -2);
        $this->assertEquals($this->parser->calculateString("1/2"), 0);
    }
}
