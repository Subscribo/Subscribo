<?php

use Subscribo\PsrHttpMessageTools\Parsers\ResponseParser;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ResponseParserTest extends PHPUnit_Framework_TestCase
{

    public function testJsonParsing()
    {
        $content = '{"a":{"b":"c"}, "d": "e"}';
        $headers = ['content-type' => ' application/json  ;  charset =  UTF-8 '];
        $response = $this->responseFactory($content, $headers);
        $expected = ['a' => ['b' => 'c'], 'd' => 'e'];
        $this->assertSame($expected, ResponseParser::extractDataFromResponse($response));
        $parser = new ResponseParser($response);
        $this->assertSame($expected, $parser->extractData());
    }


    public function testFormParsing()
    {
        $content = 'a%5Bb%5D=c&d=e';
        $headers = ['content-type' => 'application/x-www-form-urlencoded;  charset=ISO-8859-1'];
        $response = $this->responseFactory($content, $headers);
        $expected = ['a[b]' => 'c', 'd' => 'e'];
        $expectedTransformed = ['a' => ['b' => 'c'], 'd' => 'e'];
        $parser = new ResponseParser($response);
        $this->assertSame($expected, $parser->extractData());
        $this->assertSame($expected, ResponseParser::extractDataFromResponse($response));
        $transformed = $parser::transformBracketedArrayIntoMultidimensional($parser->extractData());
        $this->assertSame($expectedTransformed, $transformed);
    }


    public function testParseContentTypeApplicationXWwwFormUrlencoded()
    {
        $data = 'a%5Bb%5D=c&d=e&NAME.WITH.DOTS=content+with+spaces&no_content&empty_content=&NAME+WITH+SPACES=content.with.dots&repeated=one&repeated=two&repeated=three&repeated=four&repeated=five&multi[a][b][c]=some&multi[a][x][y]=thing&&multi[a][x][z]=else';
        $expected = [
            'a[b]' => 'c',
            'd' => 'e',
            'NAME.WITH.DOTS' => 'content with spaces',
            'no_content' => null,
            'empty_content' => '',
            'NAME WITH SPACES' => 'content.with.dots',
            'repeated' => ['one', 'two', 'three', 'four', 'five'],
            'multi[a][b][c]' => 'some',
            'multi[a][x][y]' => 'thing',
            'multi[a][x][z]' => 'else'
        ];
        $this->assertSame($expected, ResponseParser::parseContentTypeApplicationXWwwFormUrlencoded($data));
    }

    public function testTransformBracketedArrayIntoMultidimensional()
    {
        $this->assertSame([], ResponseParser::transformBracketedArrayIntoMultidimensional([]));
        $source = [
            'simple' => 'value',
            'list[]' => ['one', 'two', 'three'],
            'multi[a][b][c][d]' => 'something',
            'multi[a][b][c][e]' => 'else',
            'multi[x][y]' => 'or',
            'multi[x][z]' => 'different',
        ];
        $expected = [
            'simple' => 'value',
            'list' => ['one', 'two', 'three'],
            'multi' => [
                    'a' => [
                        'b' => [
                            'c' => [
                                'd' => 'something',
                                'e' => 'else',
                            ],
                        ],
                    ],
                    'x' => [
                        'y' => 'or',
                        'z' => 'different',
                    ],
                ],
            ];
        $this->assertSame($expected, ResponseParser::transformBracketedArrayIntoMultidimensional($source));
        $data = 'a%5Bb%5D=c&d=e&NAME.WITH.DOTS=content+with+spaces&no_content&empty_content=&NAME+WITH+SPACES=content.with.dots&repeated=one&repeated=two&repeated=three&repeated=four&repeated=five&multi[a][b][c]=some&multi[a][x][y]=thing&&multi[a][x][z]=else';
        $expected2 =  [
            'a' => ['b' => 'c'],
            'd' => 'e',
            'NAME.WITH.DOTS' => 'content with spaces',
            'no_content' => null,
            'empty_content' => '',
            'NAME WITH SPACES' => 'content.with.dots',
            'repeated' => ['one', 'two', 'three', 'four', 'five'],
            'multi' => [
                    'a' => [
                        'b' => [
                            'c' => 'some',
                        ],
                        'x' => [
                            'y' => 'thing',
                            'z' => 'else'
                        ],
                    ],
                ],
            ];
        $this->assertSame($expected2, ResponseParser::transformBracketedArrayIntoMultidimensional(
            ResponseParser::parseContentTypeApplicationXWwwFormUrlencoded($data)
        ));
    }


    public function testEmptyStringParsing()
    {
        $content = '';
        $headers = [];
        $response = $this->responseFactory($content, $headers);
        $expected = [];
        $this->assertSame($expected, ResponseParser::extractDataFromResponse($response));
        $parser = new ResponseParser($response);
        $this->assertSame($expected, $parser->extractData());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnsupportedFormat()
    {
        $content = '<html></html>';
        $headers = ['content-type' => 'text/html;  charset=ISO-8859-1'];
        $response = $this->responseFactory($content, $headers);
        ResponseParser::extractDataFromResponse($response);
    }

    /**
     * @param string $content
     * @param array $headers
     * @param int $statusCode
     * @return Response
     */
    protected function responseFactory($content, array $headers = [], $statusCode = 200)
    {
        $stream = new Stream('php://memory', 'r+');
        $stream->write($content);
        $response = new Response($stream, $statusCode, $headers);
        return $response;
    }
}
