<?php

use Subscribo\PsrHttpMessageTools\Factories\RequestFactory;

class RequestFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testMakeDefault()
    {
        $request = RequestFactory::make('http://www.example.com');
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $request->getBody()->rewind();
        $this->assertEmpty($request->getBody()->getContents(), 'Request does not have empty body content.');
        $this->assertSame('GET', $request->getMethod(), 'Request does not have expected method');
    }

    public function testMakeEmptyBody()
    {
        $request = RequestFactory::make('http://www.example.com', '');
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $request->getBody()->rewind();
        $this->assertEmpty($request->getBody()->getContents(), 'Request does not have empty body content.');
        $this->assertSame('application/x-www-form-urlencoded; charset=UTF-8', $request->getHeaderLine('Content-Type'), 'Request does not have expected value of Content-Type header.');
        $this->assertSame('POST', $request->getMethod(), 'Request does not have expected method');
    }

    public function testMakeForm()
    {
        $data = ['a' => ['b' => 'some@email'], 'd' => 'Some text'];
        $request = RequestFactory::make('http://www.example.com', $data);
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $request->getBody()->rewind();
        $this->assertSame('a%5Bb%5D=some%40email&d=Some+text', $request->getBody()->getContents(), 'Request does not have expected body content.');
        $this->assertSame('application/x-www-form-urlencoded; charset=UTF-8', $request->getHeaderLine('Content-Type'), 'Request does not have expected value of Content-Type header.');
        $this->assertSame('POST', $request->getMethod(), 'Request does not have expected method');
    }

    public function testContentTypeInHeader()
    {
        $data = ['a' => ['b' => 'c']];
        $uri = 'https://user@www.example.com/some/path?key=value#bla';
        $headers = ['content-type' => ' application/json ; charset =  ISO-8859-1'];
        $request = RequestFactory::make($uri, $data, ['other' => 5], $headers);
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $this->assertSame('POST', $request->getMethod(), 'Request does not have expected method');
        $this->assertSame('application/json; charset=ISO-8859-1',  $request->getHeaderLine('Content-Type'), 'Request does not have expected value of Content-Type header.');
        $this->assertSame('key=value&other=5', $request->getUri()->getQuery(), 'Request does not have expected URI Query');
        $request->getBody()->rewind();
        $this->assertJson($request->getBody()->getContents(), 'Request body content is not valid json.');
        $request->getBody()->rewind();
        $this->assertSame('{"a":{"b":"c"}}', $request->getBody()->getContents(), 'Request does not have expected body content.');
        $request->getBody()->rewind();
        $this->assertJsonStringEqualsJsonString('{"a":{"b":"c"}}', $request->getBody()->getContents(), 'Request does not have expected body content.');
    }

    public function testContentTypeInParameters()
    {
        $data = ['a' => ['b' => 'c']];
        $uri = 'https://user@www.example.com/some/path?key=value#bla';
        $headers = ['content-type' => ' text/html ; charset =  ISO-8859-1'];
        $request = RequestFactory::make($uri, $data, ['other' => 5], $headers, null, RequestFactory::CONTENT_TYPE_JSON);
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $this->assertSame('POST', $request->getMethod(), 'Request does not have expected method');
        $this->assertSame('application/json; charset=UTF-8',  $request->getHeaderLine('Content-Type'), 'Request does not have expected value of Content-Type header.');
        $this->assertSame('key=value&other=5', $request->getUri()->getQuery(), 'Request does not have expected URI Query');
        $request->getBody()->rewind();
        $this->assertJson($request->getBody()->getContents(), 'Request body content is not valid json.');
        $request->getBody()->rewind();
        $this->assertSame('{"a":{"b":"c"}}', $request->getBody()->getContents(), 'Request does not have expected body content.');
        $request->getBody()->rewind();
        $this->assertJsonStringEqualsJsonString('{"a":{"b":"c"}}', $request->getBody()->getContents(), 'Request does not have expected body content.');
    }

    public function testMakeParameters()
    {
        $data = ['a' => ['b' => 'c']];
        $uri = 'https://user@www.example.com/some/path?key=value#bla';
        $headers = ['content-type' => ' text/html ; charset =  ISO-8859-1'];
        $request = RequestFactory::make($uri, $data, ['other' => 5], $headers, 'PATCH', RequestFactory::CONTENT_TYPE_JSON, 'ISO-8859-2');
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $this->assertSame('PATCH', $request->getMethod(), 'Request does not have expected method');
        $this->assertSame('application/json; charset=ISO-8859-2',  $request->getHeaderLine('Content-Type'), 'Request does not have expected value of Content-Type header.');
        $this->assertSame('key=value&other=5', $request->getUri()->getQuery(), 'Request does not have expected URI Query');
        $request->getBody()->rewind();
        $this->assertJson($request->getBody()->getContents(), 'Request body content is not valid json.');
        $request->getBody()->rewind();
        $this->assertSame('{"a":{"b":"c"}}', $request->getBody()->getContents(), 'Request does not have expected body content.');
        $request->getBody()->rewind();
        $this->assertJsonStringEqualsJsonString('{"a":{"b":"c"}}', $request->getBody()->getContents(), 'Request does not have expected body content.');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidData()
    {
        $request = RequestFactory::make('http://www.example.com', false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFormat()
    {
        $data = ['a' => ['b' => 'c']];
        $uri = 'https://user@www.example.com/some/path?key=value#bla';
        $headers = ['content-type' => ' text/html ; charset =  ISO-8859-1'];
        $request = RequestFactory::make($uri, $data, ['other' => 5], $headers, 'PATCH');
    }
}
