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
 * Arttag Model
 *
 * This class is used to handle the arttags data.
 *
 * @package		68kb
 * @subpackage	Models
 * @Arttag	    Models
 * @author		Fujisan Magazine Service
 * @link		http://68kb.com/user_guide/overview/categories.html
 * @version 	$Id: Arttag_model.php 89 2010-12-27 inadome $
 */
class Arttag_model extends model
{	
	/**
	 * Constructor
	 *
	 * @uses 	get_settings
	 * @return 	void
	 */
	function __construct()
	{
		parent::__construct();
		log_message('debug', 'Arttag Model Initialized');
	}
	
	// ------------------------------------------------------------------------
		
	/**
	* Delete Arttag
	* 
	* @param	int $tag_id The id of the Arttag to delete.
	* @return	true on success.
	*/
	function delete_arttag($tag_id)
	{
		$tag_id=(int)trim($tag_id);
		$this->db->delete('arttags', array('tag_id' => $tag_id)); 
		if ($this->db->affected_rows() > 0) 
		{
			$this->db->cache_delete_all();
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Edit Arttag
 	* 
 	* @param	array $data An array of data.
	* @uses 	format_uri
 	* @return	true on success.
 	*/
	function edit_arttag($tag_id, $data)
	{
		$tag_id = (int)$tag_id;
		
		if (isset($data['tag_uri']) && $data['tag_uri'] != '') 
		{
			$data['tag_uri'] = $this->format_uri($data['tag_uri'], 0, $tag_id);
		}
		else
		{
			$data['tag_uri'] = $this->format_uri($data['tag_name'], 0, $tag_id);
		}
		$this->db->where('tag_id', $tag_id);
		$this->db->update('arttags', $data);
		
		if ($this->db->affected_rows() > 0) 
		{
			$this->db->cache_delete_all();
			return true;
		} 
		else
		{
			log_message('info', 'Could not edit the Arttag id '. $tag_id);
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
 	* Add Arttag
 	* 
 	* @param	array 	$data An array of data.
	* @uses 	format_uri
 	* @return	mixed 	Id on success.
 	*/
	function add_arttag($data)
	{
		if (isset($data['tag_uri']) && $data['tag_uri'] != '') 
		{
			$data['tag_uri'] = $this->format_uri($data['tag_uri']);
		}
		else
		{
			$data['tag_uri'] = $this->format_uri($data['tag_name']);
		}
		$this->db->insert('arttags', $data);
		if ($this->db->affected_rows() > 0) 
		{
			$this->db->cache_delete_all();
			return $this->db->insert_id();
		} 
		else 
		{
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Insert Articles2arttag
	* 
	* Insert the selected arttags
	* into the articles2arttag table.
	*
	* @access	public
	* @param	int - The article id
	* @param	array - The array of arttags.
	* @return 	bool
	*/
	function insert_arttags($id, $arr)
	{
		$this->db->delete('articles2arttag', array('article_id' => $id));
		if (is_array($arr))
		{
			foreach($arr as $arttagObj)
			{
				$data = array('article_id' => $id, 'arttag_id' => $arttagObj);
				$this->db->insert('articles2arttag', $data);
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Check URI
	* 
	* Checks other arttags for the same uri.
	* 
	* @param	string 	$tag_uri The uri name
	* @return	boolean True if checks out ok, false otherwise
	*/
	function check_uri($tag_uri, $tag_id=false)
	{
		if ($tag_id !== false) 
		{
			$tag_id=(int)$tag_id;
			$this->db->select('tag_uri')->from('arttags')->where('tag_uri', $tag_uri)->where('tag_id !=', $tag_id);
		} 
		else 
		{
			$this->db->select('tag_uri')->from('arttags')->where('tag_uri', $tag_uri);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) 
		{
			return false;
		} 
		else 
		{
			return true;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Format URI
	* 
	* Formats a Arttag uri.
	* 
	* @param	string $tag_uri The uri name
	* @uses 	check_uri
	* @uses		remove_accents
	* @uses		seems_utf8
	* @uses		utf8_uri_encode
	* @uses		format_uri
	* @return	string A cleaned uri
	*/
	function format_uri($tag_uri, $i=0, $tag_id=false)
	{
		$tag_uri = strip_tags($tag_uri);
		// Preserve escaped octets.
		$tag_uri = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $tag_uri);
		// Remove percent signs that are not part of an octet.
		$tag_uri = str_replace('%', '', $tag_uri);
		// Restore octets.
		$tag_uri = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $tag_uri);
		
		$tag_uri = remove_accents($tag_uri);
		if (seems_utf8($tag_uri)) 
		{
			if (function_exists('mb_strtolower')) 
			{
				$tag_uri = mb_strtolower($tag_uri, 'UTF-8');
			}
			$tag_uri = utf8_uri_encode($tag_uri, 200);
		}

		$tag_uri = strtolower($tag_uri);
		$tag_uri = preg_replace('/&.+?;/', '', $tag_uri); // kill entities
		$tag_uri = preg_replace('/[^%a-z0-9 _-]/', '', $tag_uri);
		$tag_uri = preg_replace('/\s+/', '-', $tag_uri);
		$tag_uri = preg_replace('|-+|', '-', $tag_uri);
		$tag_uri = trim($tag_uri, '-');
		
		if ($i>0) 
		{
			$tag_uri=$tag_uri."-".$i;
		}
		
		if (!$this->check_uri($tag_uri, $tag_id)) 
		{
			$i++;
			$tag_uri=$this->format_uri($tag_uri, $i);
		}
		return $tag_uri;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get an array of arttags.
	 *
	 * Get an array of arttags and for use
	 * in a select list.
	 *
	 * @access	public
	 * @param	string	the prefix to indent nested cats with.
	 * @param	int	the parent id
	 * @param	bool Inside the admin
	 * @return	array
	 */
	function get_arttags_for_select($prefix='', $parent=0, $article_id='', $admin=FALSE)
	{
		$arr = array();
		$this->db->select('tag_id,tag_uri,tag_name,tag_description')->from('arttags')->orderby('tag_order', 'DESC')->orderby('tag_name', 'asc')->where('tag_parent', $parent); 
		if ($admin==FALSE)
		{
			$this->db->where('tag_display', 'Y');	
		}
		$query = $this->db->get();
		//echo $this->db->last_query();
		foreach ($query->result() as $row)
		{
			$rs['tag_name']=$prefix . $row->tag_name;
			$rs['tag_id']=$row->tag_id;
			$rs['tag_uri']=$row->tag_uri;
			$rs['tag_description']=$row->tag_description;
			$id=$row->tag_id;
			if ($article_id <> '')
			{
				$this->db->from('articles2arttag')->where('article_id', $article_id)->where('arttag_id', $row->tag_id);
				$art2cat = $this->db->get();
				if ($art2cat->num_rows() > 0)
				{
					$rs['selected'] = 'Y';
				}
				else
				{
					$rs['selected'] = 'N';
				}
			}
			else
			{
				$rs['selected'] = 'N';
			}
			array_push($arr, $rs);
			$arr = array_merge($arr, $this->get_arttags_for_select($prefix .'&nbsp;&nbsp;&raquo;&nbsp;', $id, $article_id,$admin));
		}
		return $arr;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttags By Parent.
	 *
	 * Get an array of Arttags that have the
	 * same parent.
	 *
	 * @access	public
	 * @param	int	the parent id
	 * @return	array
	 */
	function get_arttags_by_parent($parent)
	{
		$arr = array();
		$this->db->from('arttags')->orderby('tag_order', 'DESC')->orderby('tag_name', 'asc')->where('tag_parent', $parent)->where('tag_display', 'Y');
		$query = $this->db->get();
		return $query;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttag By URI.
	 *
	 * Get a single Arttag from its tag_uri
	 *
	 * @access	public
	 * @param	string	the unique uri
	 * @return	array
	 */
	function get_tag_by_uri($uri)
	{
		$this->db->from('arttags')->where('tag_uri', $uri)->where('tag_display', 'Y');
		$query = $this->db->get();
		$data = $query->row();
		$query->free_result();
		return  $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttag By ID.
	 *
	 * Get a single Arttag from its id
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	array
	 */
	function get_tag_by_id($id)
	{
		$id=(int)$id;
		$this->db->from('arttags')->where('tag_id', $id);
		$query = $this->db->get();
		$data = $query->row();
		$query->free_result();
		return  $data;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttag Name By ID.
	 *
	 * Get a single Arttag Name
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	string
	 */
	function get_tag_name_by_id($id)
	{
		$this->db->select('tag_name')->from('arttags')->where('tag_id', $id);
		$query = $this->db->get();
		$data = $query->row();
		$query->free_result();
		return  $data->tag_name;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttag By Article.
	 *
	 * Get a list of Arttags an article is associated with.
	 *
	 * @access	public
	 * @param	int	the unique id
	 * @return	array
	 */
	function get_arttags_by_article($id)
	{
		$this->db->select('*');
		$this->db->from('articles2arttag');
		$this->db->join('arttags', 'articles2arttag.arttag_id = arttags.tag_id', 'left');
		$this->db->where('article_id', $id);
		$this->db->where('tag_display', 'Y');
		$query = $this->db->get();
		return $query;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttag Tree.
	 *
	 * Get a recursive list of Arttags.
	 *
	 * @access	public
	 * @param	string Orderby
	 * @param	string Order ASC or DESC
	 * @param	int	The parent to start at
	 * @return	array
	 */
	function get_tree($orderby='tag_name', $order='ASC', $parent=0)
	{
		$cat = array();
		$this->db->from('arttags')->orderby('tag_order', 'DESC')->orderby($orderby, $order)->where('tag_parent', $parent)->where('tag_display', 'Y');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$rs['tag_id']=$row->tag_id;
			$rs['tag_name']=$row->tag_name;
			$rs['tag_parent']=$row->tag_parent;
			$rs['tag_url']=$row->tag_uri;
			$rs['tag_total'] = $this->get_arttag_count($row->tag_id);
			$rs['tag_link'] = site_url("arttag/".$row->tag_uri."/");
			$cat[]=$rs;
		}
		return $cat;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Get Arttag Tree.
	 *
	 * Get a recursive list of Arttags.
	 *
	 * @access	public
	 * @param	string Orderby
	 * @param	string Order ASC or DESC
	 * @param	int	The parent to start at
	 * @return	array
	 */
	function get_arttag_count($cat=0)
	{
		$this->db->from('articles');
		$this->db->join('articles2arttag', 'articles.article_id = articles2arttag.article_id', 'left');
		$this->db->where('arttag_id', $cat);
		$this->db->where('article_display', 'Y');
		return $this->db->count_all_results();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Get Sub Arttags
	* 
	* @param	int		parent id
	* @return	mixed
	*/
	function get_sub_arttags($parent)
	{
		$parent = (int)$parent;
		$this->db->select('tag_id,tag_uri,tag_name,tag_parent')->from('arttags')->where('tag_parent', $parent)->where('tag_display !=', 'N')->order_by('tag_order DESC, tag_name ASC');
		$query = $this->db->get();
		if ($query->num_rows() > 0) 
		{
			$cat = $query->result_array();
			$query->free_result();
			return $cat;
		} 
		else 
		{
			return false;
		}
	}
}
	
/* End of file arttag_model.php */
/* Location: ./upload/includes/application/models/arttag_model.php */ 
