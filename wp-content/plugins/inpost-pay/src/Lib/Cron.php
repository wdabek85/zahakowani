<?php

namespace Ilabs\Inpost_Pay\Lib;

use Ilabs\Inpost_Pay\models\Base;

class Cron
{
    const JOB_NAME = 'inpost_pay_cron';

    public function __construct()
    {

    }

    public function attachHook()
    {
        add_action(self::JOB_NAME, [$this, 'run']);
    }

    public function run()
    {
        $model = new Base(new \stdClass());
        $model->deleteExpired();
    }

    public function schedule()
    {
        if ( ! wp_next_scheduled( self::JOB_NAME ) ) {
            wp_schedule_event( time(), 'hourly', self::JOB_NAME );
        }
    }

    public function deactivate()
    {
        $timestamp = wp_next_scheduled( self::JOB_NAME );
        wp_unschedule_event( $timestamp, self::JOB_NAME );
    }
}