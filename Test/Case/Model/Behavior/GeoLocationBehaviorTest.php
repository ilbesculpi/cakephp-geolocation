<?php

/**
 * GeoLocationBehaviorTest file
 * 
 * Test the GeoLocation Behavior.
 * 
 */

App::uses('GeoLocationBehavior', 'Model/Behavior');

/**
 * Item Model
 * 
 * @package App.Test.Case.Model.Behavior
 */
class Item extends CakeTestModel {
	
/**
 * Model name
 * 
 * @var string
 */
	public $name = 'Item';
	
/**
 * Behaviors to load with this model
 * 
 * @var array $actsAs
 */
	public $actsAs = array('GeoLocation');
	
}

/**
 * Point Model
 * 
 * @package App.Test.Cake.Model.Behavior
 */
class Point extends CakeTestModel {
	
/**
 * Model name
 * 
 * @var string
 */
	public $name = 'Point';
	
/**
 * Behaviors to load with this model
 * 
 * @var array $actsAs
 */
	public $actsAs = array('GeoLocation' => array (
		'position' => array (
			'latitude' => 'x',
			'longitude' => 'y'
		),
		'distance' => 'proximity'
	));
	
}


class GeoLocationBehaviorTest extends CakeTestCase {
	
/**
 * Fixtures associated with this test case
 * 
 * @var array
 */
	public $fixtures = array('app.item', 'app.point');
	
/**
 * Holds the instance of the Item Model
 * 
 * @var mixed $Item
 */
	public $Item = null;
	
/**
 * Holds the instance of the Point Model
 * 
 * @var mixed $Point
 */
	public $Point = null;
	
/**
 * Method executed before each test
 * 
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Item = ClassRegistry::init('Item');
		$this->Item->Behaviors->load('GeoLocation');
		$this->Point = ClassRegistry::init('Point');
		$this->Point->Behaviors->load('GeoLocation');
	}
	
/**
 * Method executed after each test
 * 
 * @return void
 */
	public function tearDown() {
		unset($this->Item, $this->Point);
		ClassRegistry::flush();
		parent::tearDown();
	}
	
/**
 * Test search capabilities for Items. 
 * 
 * @return void
 */
	public function testItemSearch() {
		$results = $this->Item->search('all', array(
			'center' => array (
				'latitude' => 10.232,
				'longitude' => -73.000,
				'radius' => 5.0
			)
		));
		
		$this->assertTrue(!empty($results));
		
		foreach($results as $result) {
			$this->assertTrue(isset($result[0]['distance']));
			$this->assertTrue($result[0]['distance'] <= 5.0);
		}
	}
	
/**
 * Test search capabilities for Points.
 * 
 * @return void
 */
	public function testPointSearch() {
		
		$results = $this->Point->search('all', array (
			'center' => array (
				'latitude' => 10.232,
				'longitude' => -73.000,
				'radius' => 5.0
			),
			'order' => 'Point.x ASC'
		));
		
		$this->assertTrue(!empty($results));
		
		foreach($results as $result) {
			$this->assertTrue(isset($result[0]['proximity']));
			$this->assertTrue($result[0]['proximity'] <= 5.0);
		}
		
	}
	
}