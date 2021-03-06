<?php

namespace Betalabs\Engine\Tests\Requests\Methods;

use Betalabs\Engine\Requests\EndpointResolver;
use Betalabs\Engine\Requests\Methods\Delete;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Betalabs\Engine\Requests\Header;
use Betalabs\Engine\Tests\TestCase;
use GuzzleHttp\Client;

class DeleteTest extends TestCase
{

    public function testPostMethod()
    {

        $client = $this->mockClient();

        $header = \Mockery::mock(Header::class);
        $header->shouldReceive('headers')
            ->andReturn([
                'header-key' => 'header-value'
            ]);

        $endpoint = \Mockery::mock(EndpointResolver::class);
        $endpoint->shouldReceive('endpoint')
            ->andReturn('http://test.local/');

        $put = new Delete($client, $header, $endpoint);

        $this->assertEquals(
            (object)[
                'data' => (object)[
                    'one' => 'field1',
                    'two' => 'field2',
                    'three' => 'field3'
                ]
            ],
            $put->send('path/to/api', [
                'parameter1' => 'fieldOne',
                'parameter2' => 'fieldTwo'
            ])
        );

    }

    protected function mockClient()
    {
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')
            ->once()
            ->andReturn(json_encode([
                'data' => [
                    'one' => 'field1',
                    'two' => 'field2',
                    'three' => 'field3'
                ]
            ]));

        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('delete')
            ->once()
            ->with('http://test.local/api/path/to/api', [
                'json' => ['parameter1' => 'fieldOne', 'parameter2' => 'fieldTwo'],
                'headers' => ['header-key' => 'header-value']
            ])
            ->andReturn($response);

        return $client;
    }

}