<?php
/** 
* 
* @package Bruninoit - HashTag 
* @copyright (c) 2014 brunino
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2 
* 
*/ 
namespace bruninoit\hashtag\event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */	
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\user */
	protected $user;
	protected $root_path;
	
	protected $phpEx;
	
/** 
 	* Constructor 
 	* 
 	* @param \phpbb\config\config   		$config             	 Config object 
 	* @param \phpbb\db\driver\driver_interface      $db        	 	 DB object 
 	* @param \phpbb\template\template    		$template  	 	 Template object 
 	* @param \phpbb\auth\auth      			$auth           	 Auth object 
 	* @param \phpbb\use		     		$user           	 User object 
 	* @param	                		$root_path          	 Root Path object 
 	* @param                  	     		$phpEx          	 phpEx object 
 	* @return \staffit\toptentopics\event\listener 
 	* @access public 
 	*/ 
public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\auth\auth $auth, \phpbb\user $user, $root_path, $phpEx) 
{
   $this->config = $config;
   $this->db = $db;
   $this->template = $template; 
   $this->auth = $auth;
   $this->user = $user;
   $this->root_path = $root_path;
   $this->phpEx   = $phpEx ;
}
/** 
 	* Assign functions defined in this class to event listeners in the core 
 	* 
 	* @return array 
 	* @static 
 	* @access public 
 	*/ 
static public function getSubscribedEvents()	
{
return array(			
'core.user_setup'						=> 'setup',
'core.viewtopic_modify_post_row' => 'viewtopic_add'
);	
}

public function setup($event)	{	
//language start
$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'bruninoit/hashtag',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
}

public function viewtopic_add($event)	
{
//languages
//$lingua=$this->user->lang['LANGUAGE'];
//define("LANGUAGE", "$l_topic_list");
//end languages


//$array_topic_data=$event['post_row'];
$rowmessage=$event['post_row'];
$message=$rowmessage['MESSAGE'];
$post_id=$rowmessage['POST_ID'];

$message=str_replace("&#","[&&]",$message);
$message=str_replace("#\"","[&$]",$message);
$message=str_replace("#","[ht]",$message);
//hastag
preg_match_all("(\[ht\](.*?) )", $message, $matches);
for($n=0;$n<count($matches[1]);$n++)
{
$ht_testo=$matches[1][$n];
$message=str_replace("[ht]$ht_testo","<a href=\"{$this->root_path}search.{$this->phpEx}?keywords=%23$ht_testo&sc=1&sf=%23$ht_testo&sr=posts&sk=t&sd=d&st=0&ch=300&t=0&submit=Cerca\">#$ht_testo</a>",$message);
}
$message=str_replace("[ht]","#",$message);
$message=str_replace("[&&]","&#",$message);
$message=str_replace("[&$]","#\"",$message);
$rowmessage['MESSAGE']=$message;
$event['post_row'] = $rowmessage;
}
}
