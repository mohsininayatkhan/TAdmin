<?php

class Reports extends \Forge\Controller {
	
	protected static $module = 'reports';
	
	public static function index() {
		return Blade::make(self::$module.'._menu');
	}
	
	// Admin Reports
	public static function outbound_report() {
		return Blade::make(self::$module.'.outbound_report');
	}
	
	public static function trunk_group_reports() {
		return Blade::make(self::$module.'.trunk_group_reports');
	}
	
	// Call Reports
	public static function call_reports() {
		return Blade::make(self::$module.'.call_reports');
	}
	
	public static function queue_call_reports() {
		return Blade::make(self::$module.'.queue_call_reports');
	}
	
	public static function quick_search() {
		return Blade::make(self::$module.'.quick_search');
	}
	
	// Queue Reports
	public static function real_time_queue_monitoring() {
		return Blade::make(self::$module.'.real_time_queue_monitoring');
	}
	
	public static function queue_service_levels() {
		return Blade::make(self::$module.'.queue_service_levels');
	}
	
	public static function answered_calls() {
		return Blade::make(self::$module.'.answered_calls');
	}
	
	public static function agent_statistics() {
		return Blade::make(self::$module.'.agent_statistics');
	}
	
	public static function agent_log_statistics() {
		return Blade::make(self::$module.'.agent_log_statistics');
	}
	
	public static function no_answer_by_agents() {
		return Blade::make(self::$module.'.no_answer_by_agents');
	}
	
	public static function wallboard() {
		return Blade::make(self::$module.'.wallboard');
	}
	
	public static function live_report() {
		return Blade::make(self::$module.'.dashboard');
	}
}