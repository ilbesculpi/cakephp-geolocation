<?php

/**
 * PointFixture
 * 
 * provides location data GEOPOINT
 */
class PointFixture extends CakeTestFixture {
	
	public $useDbConfig = 'test';
	
	public function init() {
		
		$this->fields = array (
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'title' => array('type' => 'string', 'length' => 255, 'null' => false),
			'description' => 'text',
			'x' => 'float',
			'y' => 'float',
			'created' => 'datetime',
			'updated' => 'datetime'
		);
		
		$this->records = array (
			array('id' => 1, 'title' => 'My first Point', 'description' => '"My first Point" description.', 'x' => 10.200, 'y' => -73.002),
			array('id' => 2, 'title' => 'My second Point', 'description' => '"My second Point" description.', 'x' => 10.200, 'y' => -73.002),
			array('id' => 3, 'title' => 'My third Point', 'description' => '"My third Point" description.', 'x' => 10.200, 'y' => -73.002),
			array('id' => 4, 'title' => 'Just another Point', 'description' => '"Just another Point" description.', 'x' => 10.200, 'y' => -73.002),
			array('id' => 5, 'title' => 'Yet another Point', 'description' => '"Yet another Point" description.', 'x' => 10.200, 'y' => -73.002)
		);
		
		parent::init();
	}
	
}