<?php
/**
 * Facebook Post Planner
 * http://www.aa-team.com
 * ======================
 *
 * @package			psp_PlannerCron
 * @author			AA-Team
 */

// load wp load script
$absolute_path = __FILE__;
$path_to_file = explode( 'wp-content', $absolute_path );
$path_to_wp = $path_to_file[0];

// Access WordPress
require_once( $path_to_wp . '/wp-load.php' );

// Plugin facebook SDK load
require_once ( 'app.fb-utils.class.php' );

class psp_PlannerCron
{
    // Hold an instance of the class
    private static $instance;
	
	// Hold an utils of the class
    private static $utils;
	
	// Hold an db
    private static $db;
	
	// Hold an fbUtils
    private static $fbUtils;
	private $fb_details = null;
	
	private static $now = null;
	
	public $the_plugin = null;
	
 
    // The singleton method getInstance
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new psp_PlannerCron;
        }
        return self::$instance;
    }
	
	// The constructor, call on class instance
	public function __construct(){
		global $wpdb;
		global $psp;

		$this->the_plugin = $psp;
		
		$this->fb_details = $this->the_plugin->getAllSettings('array', 'facebook_planner');
		
		// store wpdb instance
		self::$db = $wpdb;
		
		// start instance of fb post planner
		self::$fbUtils = psp_fbPlannerUtils::getInstance();

		// tmp array
		$wplannerfb_settings = $this->fb_details;
		
		// create utils
		self::$utils = array(
			'email_prompt'  	=> $wplannerfb_settings['email'],
			'email_subject'  	=> $wplannerfb_settings['email_subject'],
			'email_message'  	=> $wplannerfb_settings['email_message'],
			'email_message_lock'=> 0,
			'cron'				=> array(
				'table'			=> self::$db->prefix . 'psp_post_planner_cron',
				'time_zone'		=> $wplannerfb_settings['timezone'],
				'first_time '	=> time()
			)
		);
		
		// set new timezone
		if(trim(self::$utils['cron']['time_zone']) != ""){
			$this->setTImezone();
		}
		
		// update now
		self::$now = strtotime(date("Y-m-d H:i:s"));
	}

	private function checkPostTo($db_postTo) {
		$post_to = '';
		$db_postTo = unserialize($db_postTo);
		
		$pg = get_option('psp_fb_planner_user_pages');
		if( trim($pg) != '' ){
			$pg = @json_decode($pg);
		}
		
		if( trim($db_postTo['profile']) == 'on' ) {
			$post_to = '<li>Profile</li>';
		}
		
		if( trim($db_postTo['page_group']) != '' ) {
			$page_group = explode('##', $db_postTo['page_group']);
			
			if($page_group[0] == 'page') {
				foreach($pg->pages as $k => $v) {
					if($v->id == $page_group[1]) {
						$post_to_title = $v->name;
					}
				}
			}else if($page_group[0] == 'group') {
				foreach($pg->groups as $k => $v) {
					if($v->id == $page_group[1]) {
						$post_to_title = $v->name;
					}
				}
			}
			
			$post_to .= '<li>' . (ucfirst($page_group[0])).": " . $post_to_title . '</li>';
		}
		
		return '<ul>' . $post_to . '</ul>';
	}
	
	private function publish_to_wall() {
        wp_mail( self::$utils['email_prompt'], self::$utils['email_prompt'], self::$utils['email_message']);
    }
	
	private function setTImezone() {
		date_default_timezone_set(self::$utils['cron']['time_zone']);
	}
	
	private function getAllNewTasks() {
		// CRON: set default status (no tasks running)
		$task_status = false;
		
		// status 1 = completed tasks
		$tasks = self::$db->get_results( "SELECT * FROM " . ( self::$utils['cron']['table'] ) . " where 1=1 and status!='1' and deleted='0'", ARRAY_A );
 
		// exit if no tasks to be run
		if( isset($tasks) && count($tasks) > 0 ) {
			// loop tasks
			foreach ($tasks as $key => $task) {

				//debug:
				//if ( $task['id_post'] != '2830' ) continue 1; 
 
				// run only if task is not running
				if(isset($task['status']) && $task['status'] != '2') {

					// get data from DB and convert to unix time
		            $expiration_date = strtotime($task['run_date']); 
		            if (self::$now >= $expiration_date) {
						// set start date
						self::$db->query("UPDATE " . ( self::$utils['cron']['table'] ) . " set started_at=NOW(), status=2 WHERE id_post=" . $task['id_post']);

						// send post to wall, update DB
						$publishToFBResponse = self::$fbUtils->publishToWall($task['id_post'], $task['post_to'], $task['post_privacy']);
						if(isset($publishToFBResponse) && $publishToFBResponse === true) {
							$updateStatus = ($task['repeat_status'] == 'off' ? 1 : 0);

							// patch/ 2014.02.24
							// self::$db->query("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL ".$task['repeat_interval']." HOUR), status={$updateStatus}, ended_at='".(date('Y-m-d H:i:s', self::$now))."', response='".__('Published with success on Facebook', $this->the_plugin->localizationName)."' WHERE id_post=" . $task['id_post']);
							self::$db->query("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL repeat_interval HOUR), status={$updateStatus}, ended_at='".(date('Y-m-d H:i:s', self::$now))."', response='".__('Published with success on Facebook', $this->the_plugin->localizationName)."' WHERE id_post=" . $task['id_post']);
							//self::$db->query("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL repeat_interval HOUR), status={$updateStatus}, ended_at=DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'), response='".__('Published with success on Facebook', $this->the_plugin->localizationName)."' WHERE id_post=" . $task['id_post']);

							if(isset($task['email_at_post']) && $task['email_at_post'] == 'on') {
								$updatedTask = self::$db->get_row( "SELECT * FROM " . ( self::$utils['cron']['table'] ) . " where id_post=".($task['id_post'])." and deleted='0'", ARRAY_A );
								$postData = self::$fbUtils->getPostByID($task['id_post']);

								self::$utils['email_message'] .= '<br /><br />
									-----------------------------------------------------------------------------------------------------------------------------<br /><br />
									<div style="font-size:18px;"><strong>'.__('Post ID', $this->the_plugin->localizationName).':</strong> '.$task['id_post'].' (<a href="'.$postData['link'].'" target="_blank">'.__('View', $this->the_plugin->localizationName).'</a> | <a href="'.get_edit_post_link($task['id_post']).'" target="_blank">'.__('Edit', $this->the_plugin->localizationName).'</a>)</div><br />
									<span style="text-decoration:underline;">'.__('Published details', $this->the_plugin->localizationName).'</span><br />
									<strong>'.__('Title', $this->the_plugin->localizationName).':</strong> '.$postData['name'].'<br />
									<strong>'.__('Description', $this->the_plugin->localizationName).':</strong> '.$postData['description'].'<br />
									'.(isset($postData['caption']) && trim($postData['caption']) != '' ? '<strong>'.__('Caption', $this->the_plugin->localizationName).':</strong> '.$postData['caption'].'<br />' : '').'
									'.(isset($postData['message']) && trim($postData['message']) != '' ? '<strong>'.__('Message', $this->the_plugin->localizationName).':</strong> '.$postData['message'].'<br />' : '').'
									<strong>'.__('Picture', $this->the_plugin->localizationName).':</strong> '.(isset($postData['picture']) && trim($postData['picture']) != '' && $postData['use_picture'] == 'yes' ? __('YES', $this->the_plugin->localizationName).' (<a href="'.($postData['picture']).'" target="_blank">'.__('view picture', $this->the_plugin->localizationName).'</a>)' : __('NO', $this->the_plugin->localizationName)).'<br />
									<br />
									<span style="text-decoration:underline;">'.__('Publishing settings', $this->the_plugin->localizationName).'</span><br />
									<strong>'.__('Privacy', $this->the_plugin->localizationName).':</strong> '.$task['post_privacy'].'<br />
									<strong>'.__('Published to', $this->the_plugin->localizationName).':</strong> '.(self::checkPostTo($task['post_to'])).'<br />
									<strong>'.__('Started share at', $this->the_plugin->localizationName).':</strong> '.$updatedTask['started_at'].'<br />
									<strong>'.__('Ended share at', $this->the_plugin->localizationName).':</strong> '.$updatedTask['ended_at'].'<br />
								';
								if(isset($task['repeat_interval']) && $task['repeat_interval'] > 0) {
									self::$utils['email_message'] .= '
										<strong>'.__('Next run at', $this->the_plugin->localizationName).':</strong> '.$updatedTask['run_date'].'<br />
										<strong>'.__('Repeat interval', $this->the_plugin->localizationName).':</strong> '.$task['repeat_interval'].' '.__('hour(s)', $this->the_plugin->localizationName).'<br />
									';
								}
								self::$utils['email_message'] .= '
									<strong>'.__('Executed', $this->the_plugin->localizationName).':</strong> '.$updatedTask['attempts'].' '.__('time(s)', $this->the_plugin->localizationName).'<br />
									<strong>'.__('Last response', $this->the_plugin->localizationName).':</strong> '.(isset($updatedTask['response']) && trim($updatedTask['response']) != __('Published with success on Facebook', $this->the_plugin->localizationName) ? '<span style="color: red; font-weight: bold;">'.$updatedTask['response'].'</span>' : $updatedTask['response'])
								;
							}
						}else{
							
							// patch/ 2014.02.24
							// self::$db->query(self::$db->prepare("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL ".$task['repeat_interval']." HOUR), status=3, ended_at='".(date('Y-m-d H:i:s', self::$now))."', response='" . ($publishToFBResponse) . "' WHERE id_post = %d", $task['id_post']));
							self::$db->query(self::$db->prepare("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL repeat_interval HOUR), status=3, ended_at='".(date('Y-m-d H:i:s', self::$now))."', response='" . ($publishToFBResponse) . "' WHERE id_post = %d", $task['id_post']));
							//self::$db->query(self::$db->prepare("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL repeat_interval HOUR), status=3, ended_at=DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'), response='" . ($publishToFBResponse) . "' WHERE id_post = %d", $task['id_post']));
							
							if( isset(self::$utils['email_message_lock']) && self::$utils['email_message_lock'] === 0 ) {
								self::$utils['email_message_lock'] = 1;
								self::$utils['email_message'] = '<h3 style="color:red; font-weight:bold;">'.__('Error on publish !', $this->the_plugin->localizationName).'</h3>';
							}
							
							if(isset($task['email_at_post']) && $task['email_at_post'] == 'on') {
								$updatedTask = self::$db->get_row( "SELECT * FROM " . ( self::$utils['cron']['table'] ) . " where id_post=".($task['id_post'])." and deleted='0'", ARRAY_A );
								$postData = self::$fbUtils->getPostByID($task['id_post']);
								
								self::$utils['email_message'] .= '<br /><br />
									-----------------------------------------------------------------------------------------------------------------------------<br /><br />
									<div style="font-size:18px;"><strong>'.__('Post ID', $this->the_plugin->localizationName).':</strong> '.$task['id_post'].' (<a href="'.$postData['link'].'" target="_blank">'.__('View', $this->the_plugin->localizationName).'</a> | <a href="'.get_edit_post_link($task['id_post']).'" target="_blank">'.__('Edit', $this->the_plugin->localizationName).'</a>)</div><br />
									<span style="text-decoration:underline;">'.__('Published details', $this->the_plugin->localizationName).'</span><br />
									<strong>'.__('Title', $this->the_plugin->localizationName).':</strong> '.$postData['name'].'<br />
									<strong>'.__('Description', $this->the_plugin->localizationName).':</strong> '.$postData['description'].'<br />
									'.(trim($postData['caption']) != '' ? '<strong>'.__('Caption', $this->the_plugin->localizationName).':</strong> '.$postData['caption'].'<br />' : '').'
									'.(trim($postData['message']) != '' ? '<strong>'.__('Message', $this->the_plugin->localizationName).':</strong> '.$postData['message'].'<br />' : '').'
									<strong>'.__('Picture', $this->the_plugin->localizationName).':</strong> '.(isset($postData['picture']) && trim($postData['picture']) != '' && $postData['use_picture'] == 'yes' ? __('YES', $this->the_plugin->localizationName).' (<a href="'.($postData['picture']).'" target="_blank">'.__('view picture', $this->the_plugin->localizationName).'</a>)' : __('NO', $this->the_plugin->localizationName)).'<br />
									<br />
									<span style="text-decoration:underline;">'.__('Publishing settings', $this->the_plugin->localizationName).'</span><br />
									<strong>'.__('Privacy', $this->the_plugin->localizationName).':</strong> '.$task['post_privacy'].'<br />
									<strong>'.__('Published to', $this->the_plugin->localizationName).':</strong> '.(self::checkPostTo($task['post_to'])).'<br />
									<strong>'.__('Started share at', $this->the_plugin->localizationName).':</strong> '.$updatedTask['started_at'].'<br />
									<strong>'.__('Ended share at', $this->the_plugin->localizationName).':</strong> '.$updatedTask['ended_at'].'<br />
								';
								if(isset($task['repeat_interval']) && $task['repeat_interval'] > 0) {
									self::$utils['email_message'] .= '
										<strong>'.__('Next run at', $this->the_plugin->localizationName).':</strong> '.$updatedTask['run_date'].'<br />
										<strong>'.__('Repeat interval', $this->the_plugin->localizationName).':</strong> '.$task['repeat_interval'].' '.__('hour(s)', $this->the_plugin->localizationName).'<br />
									';
								}
								self::$utils['email_message'] .= '
									<strong>'.__('Executed', $this->the_plugin->localizationName).':</strong> '.$updatedTask['attempts'].' '.__('time(s)', $this->the_plugin->localizationName).'<br />
									<strong>'.__('Last response', $this->the_plugin->localizationName).':</strong> '.(trim($updatedTask['response']) != __('Published with success on Facebook', $this->the_plugin->localizationName) ? '<span style="color: red; font-weight: bold;">'.$updatedTask['response'].'</span>' : $updatedTask['response'])
								;
							}
						}
						
						$task_status = true;
					}
				}
			}
			
			if( (isset($task_status) && $task_status === true) && (isset(self::$utils['email_message']) && trim(self::$utils['email_message']) != '') ) {
				add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
				wp_mail( self::$utils['email_prompt'], self::$utils['email_subject'], self::$utils['email_message'] );
			}
		}
		
		return true;
	}
	
	public function wplanner_run_cron() {
		if( $this->getAllNewTasks() ) {
			return '[Facebook - Post Planner]: CRON started';
		}else{
			return '[Facebook - Post Planner]: No tasks to be run';
		}
	}
}
$psp_PlannerCron = psp_PlannerCron::getInstance();

// try to get non running tasks and execute then
echo $psp_PlannerCron->wplanner_run_cron();
