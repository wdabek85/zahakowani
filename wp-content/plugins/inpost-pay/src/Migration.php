<?php

namespace Ilabs\Inpost_Pay;

class Migration
{
    private const version = '1.0.4';
    private array $schemas;

    public function __construct()
    {
        $this->schemas = [
            'cart_session' => "CREATE TABLE {tableName} (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                session_id TEXT,
                confirmation_response TEXT,
                cart_id VARCHAR(255),
                order_id INTEGER,
                redirect_url VARCHAR(255),
                basket_cache TEXT,
                basket_cached TEXT,
                coupons TEXT,
                redirected SMALLINT(1) DEFAULT 0,
                wc_cart_session VARCHAR(255),
                session_expiry BIGINT(20) UNSIGNED DEFAULT 0,
				izi_basket VARCHAR(60) DEFAULT NULL,
				action VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY  (id)
                ) {charset};",
        ];
    }

    public function run(): void
    {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        $error = false;
        if(get_option('izi-db-version') === self::version) {
            return;
        }

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($this->schemas as $schemaName => $schema) {
            $tableName = $wpdb->prefix . 'izi_' . $schemaName;
            $sql = str_replace('{tableName}', $tableName, str_replace('{charset}', $charset, $schema));
            dbDelta($sql);

        }

        update_option('izi-db-version', self::version);

    }
}
