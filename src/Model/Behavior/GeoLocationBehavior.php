<?php

/**
 * GeoLocation Behavior
 * 
 */
class GeoLocationBehavior extends ModelBehavior {
	
/**
 * Callback
 *
 * $config for GeoLocationBehavior should be
 * array(
 *	'position' => array('latitude' => 'latitudeName', 'longitude' => 'longitudeName'),
 *  'geom' => true | false,
 *  'distance' => 'distanceName'
 * )
 * 
 * @param Model $model Model the behavior is being attached to.
 * @param array $config Array of configuration information.
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		
		$defaults = array (
			'position' => array('latitude' => 'latitude', 'longitude' => 'longitude'),
			'geom' => false,
			'distance' => 'distance'
		);
		
		$settings = array_merge($defaults, (array) $settings);
		
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $settings;
		}
		
	}
	
/**
 * Cleanup Callback unbinds GeoLocation behavior and deletes setting information.
 *
 * @param Model $model Model being detached.
 * @return void
 */
	public function cleanup(Model $model) {
		$settings = $this->settings[$model->alias];
		unset($this->settings[$model->alias]);
	}
	
/**
 * Unset position fields
 * @param Model $model
 * @param array $results
 * @param boolean $primary
 * @return mixed 
 */
	public function afterFind(Model $model, $results, $primary) {
		//parent::afterFind($model, $results, $primary);
		$settings = $this->settings[$model->alias];
		if( $settings['geom'] ) {
			// unset point field
			
		}
		return $results;
	}
	
/**
 * Convert latitude & longitude into POINT (position)
 * @param Model $model 
 */
	public function beforeSave(Model $model) {
		//parent::beforeSave($model);
		$settings = $this->settings[$model->alias];
		if( $settings['geom'] ) {
			
		}
		return true;
	}
	
/**
 * Find model data adding proximity filtering.
 * 
 * @param Model $model
 * @param array $query 
 */
	public function search(Model $model, $type = 'first', $query = array()) {
		
		$settings = $this->settings[$model->alias];
		
		if( isset($query['center']) ) {
		
			$latitude  = (float) $query['center']['latitude'];
			$longitude = (float) $query['center']['longitude'];
			$radius    = isset($query['center']['radius']) ? (float) $query['center']['radius'] : 1.0;
			
			unset($query['center']);
			
			if( $settings['geom'] ) {
				$defaults = array (
					'fields' => array('*', sprintf("
							SQRT(
								POW( ABS( X(%s.%s) - $latitude ), 2) + POW( ABS( Y(%s.%s) - $longitude ), 2)
							) * 100 AS %s", 
							$model->alias, $settings['position']['latitude'],
							$model->alias, $settings['position']['longitude'],
							$settings['distance']
						)
					),
					'group' => array (
						sprintf("%s.%s HAVING %s <= %f", $model->alias, $model->primaryKey, $settings['distance'], $radius)
					),
					'order' => $settings['distance'] . ' ASC'
				);
			}
			else {
				$defaults = array (
					'fields' => array('*', sprintf("
							SQRT(
								POW( ABS( %s.%s - $latitude ), 2) + POW( ABS( %s.%s - $longitude ), 2)
							) * 100 AS %s", 
							$model->alias, $settings['position']['latitude'],
							$model->alias, $settings['position']['longitude'],
							$settings['distance']
						)
					),
					'group' => array (
						sprintf("%s.%s HAVING %s <= %f", $model->alias, $model->primaryKey, $settings['distance'], $radius)
					),
					'order' => $settings['distance'] . ' ASC'
				);
			}
			
			$criteria = array_merge($defaults, (array) $query);
			
			$results = $model->find($type, $criteria);
			
			return $results;
			
		}
		else {
			return $model->find($type, $query);
		}
		
	}
}