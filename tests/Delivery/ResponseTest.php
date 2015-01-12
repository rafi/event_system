<?php
namespace Rafi\Delivery;

class ResponseTest extends \PHPUnit_Framework_Testcase {

	public function testCanOutputBody()
	{
		$body = 'foo';
		$response = new Response([ 'body' => $body ]);

		$this->assertEquals($body, (string) $response);
		$this->assertEquals($body, $response->body());
		$this->assertEquals(strlen($body), $response->content_length());
	}

}
