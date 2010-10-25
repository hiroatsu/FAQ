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
        if ( ! $CI->db->table_exists('articles2fujisan'))
        {
        $CI->dbforge->add_field("article_id int(20) default NULL");
        $CI->dbforge->add_field("site_id int(20) default NULL");
        $CI->dbforge->add_key('article_id', TRUE);
        if($CI->dbforge->create_table('articles2fujisan'))
        {
        //      return 'articles2fujisan table installed...<br />';
        }
        }
        if ( ! $CI->db->table_exists('categories2fujisan'))
        {
                $CI->dbforge->add_field("category_id int(20) default NULL");
        $CI->dbforge->add_field("site_id int(20) default NULL");
        $CI->dbforge->add_key('category_id', TRUE);
        if($CI->dbforge->create_table('categories2fujisan'))
        {
                return 'categories2fujisan table installed...<br />';
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
	//$CI->dbforge->drop_table('articles2fujisan');
       //$CI->dbforge->drop_table('categories2fujisan');
//	return 'test table dropped';
}
