<?php

use Subscribo\PsrHttpTools\Parsers\ResponseParser;
use Phly\Http\Response;
use Phly\Http\Stream;

class ResponseParserTest extends PHPUnit_Framework_TestCase
{

    public function testJsonParsing()
    {
        $content = '{"a":{"b":"c"}, "d": "e"}';
        $headers = ['content-type' => ' application/json  ;  charset =  UTF-8 '];
        $response = $this->responseFactory($content, $headers);
        $expected = ['a' => ['b' => 'c'], 'd' => 'e'];
        $this->assertSame($expected, ResponseParser::extractDataFromResponse($response));
    }

    public function testFormParsing()
    {
        $content = 'a%5Bb%5D=c&d=e';
        $headers = ['content-type' => 'application/x-www-form-urlencoded;  charset=ISO-8859-1'];
        $response = $this->responseFactory($content, $headers);
        $expected = ['a' => ['b' => 'c'], 'd' => 'e'];
        $parser = new ResponseParser($response);
        $this->assertSame($expected, $parser->extractData());
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
