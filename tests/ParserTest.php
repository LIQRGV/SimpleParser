<?php
namespace LIQRGV\SimpleParser;

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

    public function testParseNumOprWithTimesOprAtFirstInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("*1");
    }

    public function testParseNumOprWithDivOprAtFirstInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("/1");
    }

    public function testParseNumOprWith2OprAtFirstInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("++1");
    }

    public function testParseNumOprWith2OprWithTimesInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("1+*1");
    }

    public function testParseNumOprWith2OprWithDivInvalid(){
        $this->expectException(\Exception::class);
        $this->parser->parseNumOpr("1+/1");
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

    public function testCalculateStringNotSingleDigit() {
        $this->assertEquals($this->parser->calculateString("1+11"), 12);
        $this->assertEquals($this->parser->calculateString("-1+11"), 10);
        $this->assertEquals($this->parser->calculateString("-11*2"), -22);
        $this->assertEquals($this->parser->calculateString("11/2"), 5);
    }

    public function testCalculateStringWithArithmOrder() {
        $this->assertEquals($this->parser->calculateString("12/2+1"), 7);
        $this->assertEquals($this->parser->calculateString("-1+11*2"), 21);
        $this->assertEquals($this->parser->calculateString("-11*2-1"), -23);
        $this->assertEquals($this->parser->calculateString("11/2*4"), 22);
    }
}

