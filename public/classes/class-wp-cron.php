<?php

/**
 * WP Cron class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_WP_Cron {
	
	const FiveSeconds = 5;
	const OneWeek     = 604800;
	const OneMonth    = 2635200;

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'cron_schedules', array( $this, 'ctlggi_wp_cron_add_new_intervals') ); // wp use: cron_schedules
		add_action( 'wp', array( $this, 'ctlggi_wp_cron_add_events' ) );

	}

	/**
	 * Register new schedules.
	 *
	 * @since 1.0.0
	 * @param array $schedules
	 * @return array
	 */
	public function ctlggi_wp_cron_add_new_intervals($schedules) 
	{
		// The default intervals provided by WordPress are: hourly, twicedaily, daily
		
		// add weekly interval
		$schedules['weekly'] = array(
			'interval' => self::OneWeek,
			'display'  => esc_html__('Once Weekly', 'cataloggi')
		);
		/*
	    // add monthly interval
		$schedules['monthly'] = array(
			'interval' => self::OneMonth,
			'display'  => esc_html__('Once a month', 'cataloggi')
		);
		*/
	
		return $schedules;
	}
	
	/**
	 * Add events.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_wp_cron_add_events() {
		//$this->ctlggi_daily_event();
		$this->ctlggi_weekly_event();
		//$this->ctlggi_monthly_event();
	}
	
	/**
	 * Schedule event Once a Day.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function ctlggi_daily_event() {
		if ( ! wp_next_scheduled( 'ctlggi_wp_cron_daily_event' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'daily', 'ctlggi_wp_cron_daily_event' );
		}
	}
	
	/**
	 * Schedule event Once a Week.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function ctlggi_weekly_event() {
		if ( ! wp_next_scheduled( 'ctlggi_wp_cron_weekly_event' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'ctlggi_wp_cron_weekly_event' );
		}
	}

	/**
	 * Schedule event Once a Month.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function ctlggi_monthly_event() {
		if ( ! wp_next_scheduled( 'ctlggi_wp_cron_monthly_event' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'monthly', 'ctlggi_wp_cron_monthly_event' );
		}
	}
	
	
}

$ctlggi_wp_cron = new CTLGGI_WP_Cron;

?>