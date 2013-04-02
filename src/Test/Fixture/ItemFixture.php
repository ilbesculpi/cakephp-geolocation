<?php

/**
 * ItemFixture
 * 
 * provides location data with latitude, longitude 
 */
class ItemFixture extends CakeTestFixture {
	
	public $useDbConfig = 'test';
	
	public $fields = array (
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'title' => array('type' => 'string', 'length' => 255, 'null' => false),
		'description' => 'text',
		'latitude' => 'float',
		'longitude' => 'float',
		'created' => 'datetime',
		'updated' => 'datetime'
	);
	
	public $records = array (
		array('id' => 1, 'title' => 'My first Item', 'description' => '"My first Item" description.', 'latitude' => 10.200, 'longitude' => -73.002),
		array('id' => 2, 'title' => 'My second Item', 'description' => '"My second Item" description.', 'latitude' => 10.200, 'longitude' => -73.002),
		array('id' => 3, 'title' => 'My third Item', 'description' => '"My third Item" description.', 'latitude' => 10.200, 'longitude' => -73.002),
		array('id' => 4, 'title' => 'Just another Item', 'description' => '"Just another Item" description.', 'latitude' => 10.200, 'longitude' => -73.002),
		array('id' => 5, 'title' => 'Yet another Item', 'description' => '"Yet another Item" description.', 'latitude' => 10.200, 'longitude' => -73.002)
	);
	
}