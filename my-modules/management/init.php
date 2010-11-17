<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * test Init file
 *
 * This is an example file used to add a "test" table, alter it, and finally
 * delete it. Please note the use of the $CI =& get_instance(); which is 
 * used to access the CodeIgniter object.
 *
 */

// ------------------------------------------------------------------------

/**
 * Install or alter any database fields. This is an example and you can 
 * see the install function just adds a test table.
 */
function install()
{
        $CI =& get_instance();
	    $CI->load->dbforge();
        if ( ! $CI->db->table_exists('articles2site'))
        {
        $CI->dbforge->add_field("article_id int(20) NOT NULL");
        $CI->dbforge->add_field("site_id int(20) default NULL");
        $CI->dbforge->add_key('article_id', TRUE);
        if($CI->dbforge->create_table('articles2site'))
        {
        //      return 'articles2site table installed...<br />';
        }
        }
        if ( ! $CI->db->table_exists('categories2site'))
        {
                $CI->dbforge->add_field("category_id int(20) default NULL");
        $CI->dbforge->add_field("site_id int(20) default NULL");
        $CI->dbforge->add_key('category_id', TRUE);
        if($CI->dbforge->create_table('categories2site'))
        {
        //        return 'categories2site table installed...<br />';
        }
        }	
        if ( ! $CI->db->table_exists('siteinfo'))
        {
        $CI->dbforge->add_field("site_id int(20) NOT NULL");
        $CI->dbforge->add_field("shortname varchar(10) default NULL");
        $CI->dbforge->add_field("name varchar(50) default NULL");
        $CI->dbforge->add_field("url varchar(50) default NULL");
        $CI->dbforge->add_field("template varchar(50) default NULL");
        $CI->dbforge->add_field("title varchar(50) default NULL");
        $CI->dbforge->add_field("keywords varchar(255) default NULL");
        $CI->dbforge->add_field("description varchar(255) default NULL");
        $CI->dbforge->add_key('site_id', TRUE);
        if($CI->dbforge->create_table('siteinfo'))
        {
		$data = array('site_id' => 0,'shortname' => "ALL",'template' => 'default');
		$CI->db->insert('siteinfo', $data);
		$data = array('site_id' => 1,'shortname' => "PC",'template' => 'pc','title' => 'PC Site');
		$CI->db->insert('siteinfo', $data);
		$data = array('site_id' => 2,'shortname' => "Mobile",'template' => 'mobile','title' => 'Mobile Site');
		$CI->db->insert('siteinfo', $data);
        //return 'siteinfo table installed...<br />';
        }
        }	

        if ( ! $CI->db->table_exists('clickinfo'))
        {
		$fields = array(
			'id'=> array('type' => 'INT','constraint'=>20,'unsigned'=> TRUE,'auto_increment'=> TRUE)
		);	
        $CI->dbforge->add_field($fields);
		$CI->dbforge->add_field("article_id int(20) NOT NULL");
		$CI->dbforge->add_field("referrer varchar(255) default NULL");
		$CI->dbforge->add_field("visit_datetime datetime default NULL");
		$CI->dbforge->add_key('id', TRUE);
        if($CI->dbforge->create_table('clickinfo'))
        {
        //      return 'articles2site table installed...<br />';
        }
        }
        if ( ! $CI->db->table_exists('rating_log'))
        {
		$fields = array(
			'id'=> array('type' => 'INT','constraint'=>20,'unsigned'=> TRUE,'auto_increment'=> TRUE)
		);	
        $CI->dbforge->add_field($fields);
		$CI->dbforge->add_field("article_id int(20) NOT NULL");
		$CI->dbforge->add_field("article_rating int(11) NOT NULL default '0'");
		$CI->dbforge->add_field("datetime datetime default NULL");
		$CI->dbforge->add_key('id', TRUE);
        if($CI->dbforge->create_table('rating_log'))
        {
        //      return 'articles2site table installed...<br />';
        }
        }
        if ( ! $CI->db->table_exists('articles_log'))
        {
		$fields = array(
			'id'=> array('type' => 'INT','constraint'=>20,'unsigned'=> TRUE,'auto_increment'=> TRUE)
		);	
        $CI->dbforge->add_field($fields);
		$CI->dbforge->add_field("article_id int(20) NOT NULL");
	    $CI->dbforge->add_field("article_uri varchar(55) default '0'");
    	$CI->dbforge->add_field("article_title varchar(255) default ''");
    	$CI->dbforge->add_field("article_keywords varchar(255) default ''");
    	$CI->dbforge->add_field("article_description text NULL");
    	$CI->dbforge->add_field("article_short_desc text NULL");
		$CI->dbforge->add_field("modified_datetime datetime default NULL");
		$CI->dbforge->add_field("modified_user int(11) NOT NULL default '0'");
		$CI->dbforge->add_key('id', TRUE);
        if($CI->dbforge->create_table('articles_log'))
        {
        //      return 'articles2site table installed...<br />';
        }
        }

        if ( ! $CI->db->table_exists('arttags'))
        {
		$fields = array(
				'tag_id' => array('type' => 'INT','constraint' => 11,'unsigned' => TRUE,'auto_increment' => TRUE),
		);
		$CI->dbforge->add_field($fields);
		$CI->dbforge->add_field("tag_parent int(11) NOT NULL default '0'");
		$CI->dbforge->add_field("tag_uri varchar(55) NOT NULL default '0'");
		$CI->dbforge->add_field("tag_name varchar(255) NOT NULL default ''");
		$CI->dbforge->add_field("tag_description text NOT NULL");
		$CI->dbforge->add_field("tag_display char(1) NOT NULL DEFAULT 'N'");
		$CI->dbforge->add_field("tag_order int(11) NOT NULL default '0'");
		$CI->dbforge->add_key('tag_id', TRUE);
		$CI->dbforge->add_key('tag_uri', TRUE);
		$CI->dbforge->add_key('tag_name');
		$CI->dbforge->add_key('tag_parent');
		$CI->dbforge->add_key('tag_order');
        	if($CI->dbforge->create_table('arttags'))
        	{
        	//      return 'articles2site table installed...<br />';
        	}
        }

        if ( ! $CI->db->table_exists('articles2arttag'))
        {
		$CI->dbforge->add_field("article_id int(20) default NULL");
		$CI->dbforge->add_field("arttag_id int(20) default NULL");

		$CI->dbforge->add_key('article_id', TRUE);
		$CI->dbforge->add_key('arttag_id', TRUE);
		if($CI->dbforge->create_table('articles2arttag'))
		{
		//	return 'article2cat table installed...<br />';
		}
        }


}

// ------------------------------------------------------------------------

/**
 * Upgrade is ran to make any adjustments to any tables.
 */
function upgrade()
{
}

// ------------------------------------------------------------------------

/**
 * Uninstall is used for removing any changes your module makes to the db.
 */
function uninstall()
{
	//$CI =& get_instance();
	//$CI->load->dbforge();
	//$CI->dbforge->drop_table('articles2site');
       //$CI->dbforge->drop_table('categories2site');
//	return 'test table dropped';
}
