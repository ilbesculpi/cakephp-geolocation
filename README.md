# CakePHP GeoLocation Tools
CakePHP 2.x tools for searching and geotagging data.

## Installation
1. Create your table with <b>latitude</b> and <b>longitude</b> fields.

2. Place GeoLocationBehavior into <i>app/Model/Behavior</i>

3. Add the Behavior to your Model:
```php
class Item extends AppModel {
	public $actsAs = array('GeoLocation');
}
```

## Usage
Perform search on your Controller:
```php
public function search() {
	$results = $this->Item->search('all', array (
		'center' => array (
			'latitude'  => 40.708,
			'longitude' => -74.002,
			'radius'    => 5.0	// Kmts
		)
	));
	
	pr( $results );
}
```
