<?php
namespace Rafi\Core\Observer\Comment;

use Rafi\Core\Data;

class FeaturesTest extends \PHPUnit_Framework_TestCase {

	protected $features;

	public function setUp()
	{
		$feature_a = new Data\Feature;
		$feature_a->entity = 'comment';
		$feature_a->name = 'Foobar';
		$feature_a->event = 'submit';

		$feature_b = new Data\Feature;
		$feature_b->entity = 'article';
		$feature_b->name = 'Barbaz';
		$feature_b->event = 'delete';

		$this->features = [ $feature_a, $feature_b ];
	}

	public function tearDown()
	{
		unset($this->features);
	}

	public function testCanListFeaturesAndEvents()
	{
		$repo = $this->getMockBuilder('Rafi\Core\Repository\Feature')
			->disableOriginalConstructor()
			->setMethods([ 'find_all' ])
			->getMock();

		$repo->expects($this->once())
			->method('find_all')
			->with(
				$this->equalTo('comment'),
				$this->anything()
			)
			->willReturn($this->features);

		$feature = $this->getMockBuilder('Rafi\Core\Data\Feature')
			->getMock();

		$this->features = new Features($repo, $feature);
		$events = $this->features->get_subscribers();

		$this->assertEquals($events, [
			'submit' => Features::FEATURE_NAMESPACE.'Foobar',
			'delete' => Features::FEATURE_NAMESPACE.'Barbaz'
		]);
	}

}
