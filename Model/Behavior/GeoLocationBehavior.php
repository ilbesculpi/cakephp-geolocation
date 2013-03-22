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
 *	'fields' => array('latitude' => 'latitudeName', 'longitude' => 'longitudeName'),
 *	'tableFields' => array('position', 'positionField')
 * )
 * 
 * @param Model $model Model the behavior is being attached to.
 * @param array $config Array of configuration information.
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array (
				'fields' => array('latitude' => 'latitude', 'longitude' => 'longitude'),
				'tableFields' => array('position' => 'position')
			);
		}
		
		$this->settings[$Model->alias] = array_merge(
			$this->settings[$Model->alias], (array) $settings
		);
		
		$settings = $this->settings[$Model->alias];
		// attach virtualFields to the Model
		$Model->virtualFields[$settings['fields']['latitude']] = sprintf('X(%s.%s)', $Model->alias, $settings['tableFields']['position']);
		$Model->virtualFields[$settings['fields']['longitude']] = sprintf('Y(%s.%s)', $Model->alias, $settings['tableFields']['position']);
	}
	
/**
 * Cleanup Callback unbinds GeoLocation behavior and deletes setting information.
 *
 * @param Model $model Model being detached.
 * @return void
 */
	public function cleanup(Model $model) {
		$settings = $this->settings[$model->alias];
		// dettach virtualFields from Model
		unset($Model->virtualFields[$settings['fields']['latitude']]);
		unset($Model->virtualFields[$settings['fields']['longitude']]);
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
		foreach($results as $k => $result) {
			if( isset($results[$k][$model->alias]) )
				unset($results[$k][$model->alias][$settings['tableFields']['position']]);
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
		if( isset($model->data[$model->alias][$settings['fields']['latitude']]) &&
				isset($model->data[$model->alias][$settings['fields']['longitude']]) ) {
			$model->data[$model->alias][$settings['tableFields']['position']] = DboSource::expression(
				sprintf('GeomFromText("POINT(%f %f)")', 
						$model->data[$model->alias][$settings['fields']['latitude']],
						$model->data[$model->alias][$settings['fields']['longitude']]
				)
			);
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
			
			$defaults = array (
				'fields' => array('*', sprintf("
						SQRT(
							POW( ABS( X(%s.%s) - $latitude ), 2) + POW( ABS( Y(%s.%s) - $longitude ), 2)
						) * 100 AS distance", 
						$model->alias, $settings['tableFields']['position'],
						$model->alias, $settings['tableFields']['position']
					)
				),
				'group' => array (
					sprintf("%s.id HAVING distance <= $radius", $model->alias)
				),
				'order' => 'distance ASC'
			);
			
			$criteria = array_merge($defaults, (array) $query);
			
			$results = $model->find($type, $criteria);
			
			return $results;
			
		}
		else {
			return $model->find($type, $query);
		}
		
	}
}