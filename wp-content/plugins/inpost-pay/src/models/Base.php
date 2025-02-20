<?php

namespace Ilabs\Inpost_Pay\models;

use Ilabs\Inpost_Pay\Lib\helpers\CacheHelper;
use Ilabs\Inpost_Pay\Logger;

class Base
{
    protected $table = 'izi_cart_session';
    protected $data;
    protected $className;

	protected function baseParams(): array
    {
        return [];
    }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->data->{$name} ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->{$name} = $value;
    }

    public function checkAttribute( $attribute ) {
        $attributes = [
            'id',
            'session_id',
            'cart_id',
            'order_id',
            'redirect_url',
            'binding_data',
            'confirmation_response',
            'basket_cache',
            'basket_cached',
            'coupons',
            'redirected',
            'wc_cart_session',
            'session_expiry',
	        'action',
	        'izi_basket',
        ];
        return in_array($attribute, $attributes);
    }

    public function getById($id): self
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $this->table;
		$data = CacheHelper::getCacheData($table_name.'_'.$id);
		if ($data === false ) {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$table_name} WHERE id = %d",
				$id
			);
			$data = $wpdb->get_results($sql);

			$model = new \stdClass;
			if ($data && isset($data[0])) {
				$model = $data[0];
			}
			return new $this->className($model);
		}


	    if ($data instanceof Base) {
		    return $data;
	    }

	    return new $this->className($data);
    }

    public function getAllByAttributes($attributes): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $sql = "SELECT * FROM {$table_name} WHERE ";
        $where = [];
        foreach ($attributes as $attribute => $value) {
            if( !$this->checkAttribute($attribute) ) continue;
            if (is_numeric($value)) {
                $where[] = $attribute . ' = ' . $wpdb->prepare('%d', $value);
            } else {
                $where[] = $attribute . ' = ' . $wpdb->prepare('%s', $value);
            }
        }
        $sql = $sql .= implode(' AND ', $where);
        $data = $wpdb->get_results($sql);
        $return = [];
        foreach ($data as $model) {
            $return[] =  new $this->className($model);
        }
        return $return;
    }

    public function getAll(): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $sql = "SELECT * FROM {$table_name}";
        $data = $wpdb->get_results($sql);
        $return = [];
        foreach ($data as $model) {
            $return[] =  new $this->className($model);
        }
        return $return;
    }

    public function getByAttributes($attributes): self
    {
        global $wpdb;
        CacheHelper::disableWPCache();
        $table_name = $wpdb->prefix . $this->table;
        $sql = "SELECT * FROM $table_name WHERE ";
        $where = [];
        foreach ($attributes as $attribute => $value) {
            if( !$this->checkAttribute($attribute) ) continue;
            if (is_numeric($value)) {
                $where[] = $attribute . ' = ' . $wpdb->prepare('%d', $value);
            } else {
                $where[] = $attribute . ' = ' . $wpdb->prepare('%s', $value);
            }
        }
        $sql = $sql .= implode(' AND ', $where);
        $data = $wpdb->get_results($sql);
        $model = new \stdClass;
        if ($data && isset($data[0])) {
            $model = $data[0];
        }
        $return = new $this->className($model);
	    $data = CacheHelper::getCacheData($table_name.'_'.$return->id);
		if ($data === false ) {
			return $return;
		}

		if ($data instanceof Base) {
			return $data;
		}

		$data = new $this->className($data);
		return $data;
    }

    public function save($toSave = [])
    {
	    $data = [];
	    foreach (get_object_vars($this->data) as $name => $value) {
		    $data[$name] = $value;
	    }
        if (!isset($this->data->id)) {
            return $this->create($data);
        }
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
		CacheHelper::setCacheData($table_name.'_'.$this->data->id, $this);
        $sql = "UPDATE {$table_name} SET ";
        $set = [];
        foreach ($this->data as $attribute => $value) {
            if (($attribute == 'id' || $value == '') && $value !== 0) {
                continue;
            }
            if( !$this->checkAttribute($attribute) ) continue;
            if($toSave) {
                if( !in_array($attribute, $toSave) ) continue;
            }
            if (is_numeric($value)) {
                $set[] = $attribute . ' = ' . $wpdb->prepare('%d', $value);
            } else {
                $set[] = $attribute . ' = ' . $wpdb->prepare('%s', $value);
            }
        }
        $sql .= implode(', ', $set) . ' WHERE id = ' . $wpdb->prepare('%d', $this->data->id);
//        Logger::debug($sql, true);
        if ($wpdb->query($sql) === false) {
            throw new \Exception($wpdb->last_error);
        }

    }

    public function create($attributes = []): self
    {
        global $wpdb;
	    $table_name = $wpdb->prefix . $this->table;
		$data = array_merge($this->baseParams(), $attributes);
        $wpdb->insert($table_name, $data);
        $data = $this->getById($wpdb->insert_id);
	    CacheHelper::setCacheData($table_name.'_'.$wpdb->insert_id, $data);
		return $data;
    }

    public function execute($sql)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $sql = str_replace('{table_name}', $table_name, $sql);
        $wpdb->query($sql);
    }

    public function getAllBySql($sql, $raw = false)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $sql = str_replace('{table_name}', $table_name, $sql);
        $sql = str_replace('{table_prefix}', $wpdb->prefix, $sql);
        $data = $wpdb->get_results($sql);
        if ($raw) {
            return $data;
        }
        $response = [];
        foreach ($data as $one) {
            $response[] = new $this->className($one);
        }
        return $response;
    }
    public function getBySql($sql)
    {
        $data = $this->getAllBySql($sql, true);
        $model = new \stdClass;
        if ($data && isset($data[0])) {
            $model = $data[0];
        }
        return new $this->className($model);
    }

    public function delete()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table;
        $result = $wpdb->delete($table_name, ['id' => $this->id]);
        Logger::log("DELETING BY ID: {$this->id}, RESULT: " . print_r($result, true));
    }

    public function deleteExpired()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $this->table;
        $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE session_expiry < %d", time() ) ); // @codingStandardsIgnoreLine.
    }

    public function toArray()
    {
        return (array) $this->data;
    }

    public function hasSet($name)
    {
        return isset($this->data->{$name});
    }

    public static function startTransaction()
    {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
    }

    public static function commitTransaction()
    {
        global $wpdb;
        $wpdb->query('COMMIT');
    }

    public static function rollBackTransaction()
    {
        global $wpdb;
        $wpdb->query('ROLLBACK');
    }
}
