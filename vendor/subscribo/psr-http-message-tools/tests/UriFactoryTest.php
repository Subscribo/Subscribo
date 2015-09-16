<?php

use Subscribo\PsrHttpMessageTools\Factories\UriFactory;
use Zend\Diactoros\Uri;


class UriFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testMakeDefault()
    {
        $uri = UriFactory::make('http://www.example.com');
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $uri);
        $this->assertEmpty($uri->getQuery());
    }

    public function testAddQueryParameters()
    {
        $uri = UriFactory::make('https://www.example.com/some/dir?first=one&second=two&third%5Ba%5D=x&third[b]=y&third%5Bd%5D=z&fourth[h][i]=four');
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $uri);
        $this->assertSame('first=one&second=two&third%5Ba%5D=x&third%5Bb%5D=y&third%5Bd%5D=z&fourth%5Bh%5D%5Bi%5D=four', $uri->getQuery());
        $queryParameters = ['first' => 'different', 'third' => ['c' => 'x', 'b' => 'm', 'd' => null,  'e' => ''], 'fourth' => ['h' => ['j' => 4]]];
        $newUri = UriFactory::addQueryParameters($uri, $queryParameters);
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $uri);
        $expectedQuery = 'first=different&second=two&third%5Ba%5D=x&third%5Bb%5D=m&third%5Bc%5D=x&third%5Be%5D=&fourth%5Bh%5D%5Bi%5D=four&fourth%5Bh%5D%5Bj%5D=4';
        $this->assertSame($expectedQuery, $newUri->getQuery());
    }

    public function testMakeWithQueryParametersAdded()
    {
        $queryParameters = ['first' => 'different', 'third' => ['c' => 'x', 'b' => 'm', 'd' => null,  'e' => ''], 'fourth' => ['h' => ['j' => 4]]];
        $uri = UriFactory::make('https://www.example.com/some/dir?first=one&second=two&third%5Ba%5D=x&third[b]=y&third%5Bd%5D=z&fourth[h][i]=four', $queryParameters);
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $uri);
        $expectedQuery = 'first=different&second=two&third%5Ba%5D=x&third%5Bb%5D=m&third%5Bc%5D=x&third%5Be%5D=&fourth%5Bh%5D%5Bi%5D=four&fourth%5Bh%5D%5Bj%5D=4';
        $this->assertSame($expectedQuery, $uri->getQuery());
    }

    public function testChangeQueryParameter()
    {
        $uri = new Uri('http://www.example.com?first[a][b][c]=one&second=two');
        $queryParameters = ['first' => 'different'];
        $newUri = UriFactory::addQueryParameters($uri, $queryParameters);
        $expected = 'first=different&second=two';
        $this->assertSame($expected, $newUri->getQuery());
    }
}
