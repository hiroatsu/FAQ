<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 68KB
 *
 * An open source knowledge base script
 *
 * @package		68kb
 * @author		68kb Dev Team
 * @copyright	Copyright (c) 2009, 68 Designs, LLC
 * @license		http://68kb.com/user_guide/license.html
 * @link		http://68kb.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Admin Categories Controller
 *
 * Handles the categories pages
 *
 * @package		68kb
 * @subpackage	Admin_Controllers
 * @category	Admin_Controllers
 * @author		68kb Dev Team
 * @link		http://68kb.com/user_guide/overview/categories.html
 * @version 	$Id: categories.php 134 2009-12-02 01:29:40Z suzkaw68 $
 */
class Arttags extends Controller
{
	/**
	* Constructor
	*
	* Requires needed models and helpers.
	* 
	* @access	public
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->model('init_model');
		$this->load->model('arttag_model');
		$this->load->helper('cookie');
		$this->load->library('auth');
		$this->auth->restrict();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Index Controller
	*
	* Redirects to this->grid
	*
	* @access	public
	*/
	function index()
	{
		$data='';
		redirect('admin/arttags/grid/');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Revert
	*
	* Show a message and redirect the user
	* 
	* @access	public
	* @param	string -- The location to goto
	* @return	array
	*/
	function revert($goto)
	{
		$data['goto'] = $goto;
		$data['nav'] = 'Arttags';
		$this->init_model->display_template('content', $data, 'admin');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Grid
	*
	* Show a table of Arttags
	*
	* @access	public
	* @return	array
	*/
	function grid()
	{
		$data['nav'] = 'Arttags';
		if ( ! $this->auth->check_level(3))
		{
			$data['not_allowed'] = TRUE;
			$this->init_model->display_template('content', $data, 'admin'); 
			return FALSE;
		}
		$data['options'] = $this->arttag_model->get_arttags_for_select('',0,'',TRUE);
		$this->init_model->display_template('arttags/grid', $data, 'admin'); 
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Edit arttag
	* 
	* @access	public
	*/
	function edit()
	{
		$this->load->library('form_validation');
		$data['nav'] = 'Arttags';
		if ( ! $this->auth->check_level(3))
		{
			$data['not_allowed'] = TRUE;
			$this->init_model->display_template('content', $data, 'admin'); 
			return FALSE;
		}
		$this->load->helper('form');
		$id = (int) $this->uri->segment(4, 0);
		$data['art'] = $this->arttag_model->get_tag_by_id($id);
		$data['options'] = $this->arttag_model->get_arttags_for_select('',0,'',TRUE);
		$data['action'] = site_url('admin/arttags/edit/'.$id);
		
		$this->form_validation->set_rules('tag_name', 'lang:kb_title', 'required');
		$this->core_events->trigger('arttags/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->init_model->display_template('arttags/form', $data, 'admin');
		}
		else
		{
			//success
			$id = $this->input->post('tag_id', TRUE);
			$parent = $this->input->post('tag_parent', TRUE);
			if ($parent=='') 
			{
				$parent=0;
			}
			$tag_uri = $this->input->post('tag_uri', TRUE);
			$data = array(
				'tag_uri' => $tag_uri, 
				'tag_name' => $this->input->post('tag_name', TRUE),
				'tag_description' => $this->input->post('tag_description', TRUE),
				'tag_parent' => $parent,
				'tag_display' => $this->input->post('tag_display', TRUE),
				'tag_order' => $this->input->post('tag_order', TRUE)
			);
			$var = $this->arttag_model->edit_arttag($id, $data);
			$this->core_events->trigger('arttag/edit',$id);
			$this->revert('admin/arttags/');
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Add arttag
	* 
	* @access	public
	*/
	function add()
	{
		$this->load->library('form_validation');
		$data['nav'] = 'Arttags';
		if ( ! $this->auth->check_level(3))
		{
			$data['not_allowed'] = TRUE;
			$this->init_model->display_template('content', $data, 'admin'); 
			return FALSE;
		}
		$this->load->helper('form');
		$id = (int) $this->uri->segment(4, 0);
		$data['options'] = $this->arttag_model->get_arttags_for_select('',0,'',TRUE);
		$data['action'] = site_url('admin/arttags/add/');
		
		$this->form_validation->set_rules('tag_name', 'lang:kb_title', 'required');
		$this->form_validation->set_rules('tag_uri', 'lang:kb_uri', 'alpha_dash');
		$this->form_validation->set_rules('tag_description', 'lang:kb_description', '');
		$this->form_validation->set_rules('tag_parent', 'lang:kb_parent_cat', 'numeric');
		$this->form_validation->set_rules('tag_order', 'lang:kb_weight', 'numeric');
		$this->core_events->trigger('arttags/validation');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->init_model->display_template('arttags/form', $data, 'admin');
		}
		else
		{
			//success
			$parent = $this->input->post('tag_parent', TRUE);
			if ($parent=='') 
			{
				$parent=0;
			}
			$tag_uri = $this->input->post('tag_uri', TRUE);
			$data = array(
				'tag_uri' => $tag_uri, 
				'tag_name' => $this->input->post('tag_name', TRUE),
				'tag_description' => $this->input->post('tag_description', TRUE),
				'tag_parent' => $parent,
				'tag_display' => $this->input->post('tag_display', TRUE),
				'tag_order' => $this->input->post('tag_order', TRUE)
			);
			$var = $this->arttag_model->add_arttag($data);
			$this->core_events->trigger('arttag/edit',$var);
			$this->revert('admin/arttags/');
		}
		
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Duplicate Article
	* 
	* @access	public
	*/
	function duplicate()
	{
		$data['nav'] = 'Arttags';
		if ( ! $this->auth->check_level(3))
		{
			$data['not_allowed'] = TRUE;
			$this->init_model->display_template('content', $data, 'admin'); 
			return FALSE;
		}
		$this->load->helper('form');
		$id = (int) $this->uri->segment(4, 0);
		$data['art'] = $this->arttag_model->get_tag_by_id($id);
		$data['options'] = $this->arttag_model->get_arttags_for_select('',0);
		$data['action'] = 'add';
		$this->init_model->display_template('arttags/form', $data, 'admin');
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Delete arttag
	* 
	* @access	public
	*/
	function delete()
	{
		$data['nav'] = 'Arttags';
		if ( ! $this->auth->check_level(3))
		{
			$data['not_allowed'] = TRUE;
			$this->init_model->display_template('content', $data, 'admin'); 
			return FALSE;
		}
		$id = (int) $this->uri->segment(4, 0);
		$this->db->delete('arttags', array('tag_id' => $id));
		$this->core_events->trigger('arttag/delete', $id);
		$this->revert('admin/arttags/');
	}
	
}

/* End of file arttags.php */
/* Location: ./upload/includes/application/controllers/admin/arttags.php */ 