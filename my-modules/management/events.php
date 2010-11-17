<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * test Events File
 *
 * The class name must be named "yourmodule_events" where your module is the name
 * of the module. For this module it is named "developer". 
 */
class management_events
{
	/**
	* Class constructor
	*
	* The constructor takes the $core_events as the param.
	* Inside this you will register your events to interact
	* with the core system. 
	*/
	function __construct(&$core_events)
	{
	 // for add site_info on article page, category page
     $core_events->register('articles/form/description', $this, 'show_last_modified_user_and_date_with_link');
     $core_events->register('articles/form/description', $this, 'show_article_siteinfo');
     $core_events->register('category/form', $this, 'show_last_modified_user');
     $core_events->register('category/form', $this, 'show_category_siteinfo');
     $core_events->register('category/add', $this, 'add_category_siteinfo');
     $core_events->register('category/edit', $this, 'add_category_siteinfo');
	 $core_events->register('categories/delete', $this, 'delete_categories2site_by_id');
     $core_events->register('show_siteinfo_on_categories', $this, 'show_siteinfo_on_categories');
     $core_events->register('articles/add', $this, 'add_article_siteinfo');
     $core_events->register('articles/edit', $this, 'add_article_siteinfo');
     $core_events->register('articles/delete', $this, 'delete_articles2site_by_id');
     $core_events->register('modulecategories/grid', $this, 'show_siteinfo_on_categories');
     $core_events->register('show_site_id_selection', $this, 'show_site_id_selection');
     $core_events->register('show_siteinfo_on_articles', $this, 'show_siteinfo_on_articles');
     $core_events->register('th_site_info_label', $this, 'th_site_info_label');
     $core_events->register('create_article_log', $this, 'check_article_and_insert_article_log');
     $core_events->register('show_last_modified_user_and_date', $this, 'show_last_modified_user_and_date');
     $core_events->register('management_active', $this, 'management_active');

	}
	
	function management_active()
	{
		return TRUE;
	}
	// ------------------------------------------------------------------------
	//For Atricle
	function th_site_info_label()
	{
		echo "<th>PC or MOBILE</th>";
	}

	function show_last_modified_user_and_date_with_link($article_id)
	{
		if($this->show_last_modified_user_and_date($article_id) == TRUE){
			echo '<a href="'.site_url('admin/modules/show/management/'.$article_id).'">----Go to Article Log----</a>';
		}
	}

	function show_last_modified_user_and_date($article_id)
	{
		$CI =& get_instance();
		if(strcmp($article_id,"") == 0){
				return FALSE;	
		}else{
			$modified_user_and_date=$this->get_article_modified_date_and_user_on_articles_log($article_id);	
			if($modified_user_and_date !== NULL){
				echo $modified_user_and_date;
				return TRUE;	
			}else{
				echo $this->get_article_modified_date_and_author_on_articles($article_id);	
				return FALSE;	
			}
		}
		return FALSE;	
	}

	function get_article_modified_date_and_author_on_articles($article_id){
		$CI =& get_instance();
		$CI->db->select('article_modified,article_author');
		$CI->db->from('articles');
		$CI->db->where('article_id',(int)$article_id);
        $query = $CI->db->get();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
			$modified_date = ' Last Modified time: '.date($CI->config->item('article_date_format'), $row->article_modified);
			$CI->load->model('users_model');
			$user = $CI->users_model->get_user_by_id($row->article_author);
			$artcile_author = ' Article author: '.$user->username;
			}
			return $modified_date.$artcile_author;
		}
		return NULL;
	}

	function get_article_modified_date_and_user_on_articles_log($article_id){
		$CI =& get_instance();
		$CI->db->select('modified_datetime,modified_user');
		$CI->db->from('articles_log');
		$CI->db->where('article_id',(int)$article_id);
		$CI->db->orderby('modified_datetime', 'DESC');
		$CI->db->limit(1, 0);
		
        $query = $CI->db->get();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
			$modified_date = ' Last Modified Time: '.$row->modified_datetime;
			$CI->load->model('users_model');
			$user = $CI->users_model->get_user_by_id($row->modified_user);
			$modified_user = ' Last Modified User: '.$user->username;
			}
			return $modified_date.$modified_user;
		}else{
			return NULL;
		}
		return NULL;
	}

	
	function check_article_and_insert_article_log($edit_data){
		$CI =& get_instance();
        $id = (int) $CI->uri->segment(4, 0);
		$CI->db->from('articles');
		$CI->db->where('article_id',$id);
        $query = $CI->db->get();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$change = FALSE;
				if(strcmp($row->article_uri,$edit_data['article_uri']) !== 0){
					$edit_data['article_uri'] =$row->article_uri;
					$change = TRUE;
				}else{
					unset($edit_data['article_url']);
				}
				if(strcmp($row->article_title,$edit_data['article_title']) !== 0){
					$edit_data['article_title'] =$row->article_title;
					$change = TRUE;
				}else{
					unset($edit_data['article_title']);
				}
				if(strcmp($row->article_keywords,$edit_data['article_keywords']) !== 0){
					$edit_data['article_keywords'] =$row->article_keywords;
					$change = TRUE;
				}else{
					unset($edit_data['article_keywords']);
				}
				if(strcmp($row->article_description,$edit_data['article_description']) !== 0){
					$edit_data['article_description'] =$row->article_description;
					$change = TRUE;
				}else{
					unset($edit_data['article_description']);
				}
				if(strcmp($row->article_short_desc,$edit_data['article_short_desc']) !== 0){
					$edit_data['article_short_desc'] =$row->article_short_desc;
					$change = TRUE;
				}else{
					unset($edit_data['article_short_desc']);
				}
				unset($edit_data['article_display']);
				unset($edit_data['article_order']);
				$edit_data['article_id'] = $id;
				$edit_data['modified_user'] = $CI->session->userdata('userid');
				$edit_data['modified_datetime'] = date('Y-m-d H:i:s',time());
				if($change == TRUE){
					$this->insert_article_log($edit_data);					
				}
			}
		}
	} 

	function insert_article_log($edit_data){
		$CI =& get_instance();
        $CI->db->insert('articles_log', $edit_data);
        if ($CI->db->affected_rows() > 0){
        	$CI->db->cache_delete_all();
        }
	}

	function show_article_siteinfo()
	{
        $CI =& get_instance();
        $id = (int) $CI->uri->segment(4, 0);
		$articles2site = $this->get_articles2site_by_id($id);
        echo '<p class="row2">';
        echo '<label for="article_display">Site Info:</label>';
        echo '<select tabindex="6" name="site_id" id="site_id" >';
		if ($articles2site !== NULL){
			foreach ($articles2site->result() as $row){
           		if((int)$row->site_id == 1){
			    	$site_infos=$this->get_all_ids_and_shortnames_on_siteinfo();
		    	    if ($site_infos !== NULL){
		        		foreach ($site_infos->result() as $site_info){
        	   				if( (int)$site_info->site_id == 0){
          					}
        	   				if( (int)$site_info->site_id == 1){
		               		echo '<option value="'.$site_info->site_id.'" selected>'.$site_info->shortname.'</option>';
							}
							if((int)$site_info->site_id !== 0 && (int)$site_info->site_id !== 1 ){
		               		echo '<option value="'.$site_info->site_id.'" >'.$site_info->shortname.'</option>';
							}
        				}
					}else{
               		echo '<option value="1" selected>PC</option>';
               		echo '<option value="2">Mobile</option>';
					}
          		}
          		if( (int)$row->site_id == 2){
			    echo $row->site_id;
			    	$site_infos=$this->get_all_ids_and_shortnames_on_siteinfo();
		    	    if ($site_infos !== NULL){
		        		foreach ($site_infos->result() as $site_info){
        	   				if( (int)$site_info->site_id == 0){
          					}
        	   				if( (int)$site_info->site_id == 2){
		               		echo '<option value="'.$site_info->site_id.'" selected>'.$site_info->shortname.'</option>';
							}
							if((int)$site_info->site_id !== 0 && (int)$site_info->site_id !== 2 ){
		               		echo '<option value="'.$site_info->site_id.'" >'.$site_info->shortname.'</option>';
							}
        				}
					}else{
               		echo '<option value="1">PC</option>';
               		echo '<option value="2" selected>Mobile</option>';
					}
          		}
          		if( (int) $row->site_id !== 2 && (int)$row->site_id !== 1 ){
          		echo $row->site_id;
			    echo '<option value="2" >Mobile</option>';
          		echo '<option value="1" >PC</option>';
         		}
        	}
        }else{
          echo '<option value="1" >PC</option>';
          echo '<option value="2" >Mobile</option>';
        }
        echo  '</select>';
        echo '</p>';
	}

	function show_siteinfo_on_articles($article_id)
	{
        $CI =& get_instance();
        $id = (int) $article_id;
		$articles2site = $this->get_articles2site_by_id($id);
		echo "<td>";
        if ($articles2site !== NULL){
        	foreach ($articles2site->result() as $row){
				if( $row->site_id == null || ($row->site_id !== null && (int)$row->site_id == 0)){
					echo "N/A";
				}
		    	$shortname = $this->get_shortname_on_siteinfo_by_id($row->site_id);
		    	if($shortname !== NULL){
					echo $shortname;
				}else{
	               	echo "N/A";
				}
        	}
        }else{
               	echo "N/A";
        }
		echo "</td>";
	}

	function add_article_siteinfo($id){
        $CI =& get_instance();
		$data = array(
			'article_id' => (int) $id, 
			'site_id' => (int) $CI->input->post('site_id', TRUE)
		);
		$this -> insert_or_update_articles2site($data);
	}

	function delete_articles2site_by_id($id){
        $CI =& get_instance();
        $id = (int) $id; 
		$articles2site = $this->get_articles2site_by_id($id);
        if ($articles2site !== NULL){
			$CI->db->delete('articles2site', array('article_id' => $id)); 
			if ($CI->db->affected_rows() > 0) 
			{
				$CI->db->cache_delete_all();
			} 
        }
	}

	function insert_or_update_articles2site($data)
	{
        $CI =& get_instance();
        $id = (int) $data['article_id']; 
        $articles2site = $this->get_articles2site_by_id($id);
		if ($articles2site !== NULL){
        	$CI->db->where('article_id', $id);
        	$CI->db->update('articles2site', $data);
        	if ($CI->db->affected_rows() > 0){
        	$CI->db->cache_delete_all();
        	}
        }else{
        $CI->db->insert('articles2site', $data);
        	if ($CI->db->affected_rows() > 0){
        	$CI->db->cache_delete_all();
        	}
	    }
	}

	//For Category
	function show_siteinfo_on_categories($category_id)
	{
        $CI =& get_instance();
        $id = (int) $category_id;
		$categories2site = $this->get_categories2site_by_id($id);
		echo "<td>";
        if ($categories2site !== NULL){
        	foreach ($categories2site->result() as $row){
				if( $row->site_id == null || ($row->site_id !== null && (int)$row->site_id == 0)){
					echo "N/A";
				}
		    	$shortname=$this->get_shortname_on_siteinfo_by_id($row->site_id);
				if ($shortname !== null){
					echo $shortname;
				}
        	}
        }else{
               	echo "N/A";
        }
		echo "</td>";
	}

    function show_category_siteinfo()
    {
        $CI =& get_instance();
        $id = (int) $CI->uri->segment(4, 0);
        $categories2site = $this->get_categories2site_by_id($id);
		echo '<p class="row2">';
        echo '<label for="category_display">PC or Mobile:</label>';
        echo '<select tabindex="6" name="site_id" id="site_id" >';
		if ($categories2site !== NULL){
			foreach ($categories2site->result() as $row){
		    	if((int)$row->site_id == 1){
					$site_infos=$this->get_all_ids_and_shortnames_on_siteinfo();
           			if ($site_infos !== null){
		        		foreach ($site_infos->result() as $site_info){
        	   				if( (int)$site_info->site_id == 0){
          					}
        	   				if( (int)$site_info->site_id == 1){
		               		echo '<option value="'.$site_info->site_id.'" selected>'.$site_info->shortname.'</option>';
							}
							if((int)$site_info->site_id !== 0 && (int)$site_info->site_id !== 1 ){
		               		echo '<option value="'.$site_info->site_id.'" >'.$site_info->shortname.'</option>';
							}
        				}
					}else{
               		echo '<option value="1" selected>PC</option>';
               		echo '<option value="2">Mobile</option>';
					}
          		}
          		if( (int)$row->site_id == 2){
		    	    $site_infos=$this->get_all_ids_and_shortnames_on_siteinfo();
           			if ($site_infos !== null){
		        		foreach ($site_infos->result() as $site_info){
        	   				if( (int)$site_info->site_id == 0){
          					}
        	   				if( (int)$site_info->site_id == 2){
		               		echo '<option value="'.$site_info->site_id.'" selected>'.$site_info->shortname.'</option>';
							}
							if((int)$site_info->site_id !== 0 && (int)$site_info->site_id !== 2 ){
		               		echo '<option value="'.$site_info->site_id.'" >'.$site_info->shortname.'</option>';
							}
        				}
					}else{
               		echo '<option value="1">PC</option>';
               		echo '<option value="2" selected>Mobile</option>';
					}
          		}
          		if( (int) $row->site_id !== 2 && (int)$row->site_id !== 1 ){
          		echo '<option value="2" >Mobile</option>';
          		echo '<option value="1" >PC</option>';
         		}
        	}
        }else{
          echo '<option value="1" >PC</option>';
          echo '<option value="2" >Mobile</option>';
        }
        echo  '</select>';
        echo '</p>';
	} 

	function add_category_siteinfo($id){
        $CI =& get_instance(); 
		$data = array(
			'site_id' => (int) $CI->input->post('site_id', TRUE),
			'category_id' => (int) $id
		);
        $this -> insert_or_update_categories2site($data);
	}

	function delete_categories2site_by_id($id){
        $CI =& get_instance(); 
        $id = (int) $id; 
        $CI->db->from('categories2site')->where('category_id', $id);
        $query = $CI->db->get();
        if ($query->num_rows() > 0){
			$CI->db->delete('categories2site', array('category_id' => $id)); 
			if ($CI->db->affected_rows() > 0) 
			{
				$CI->db->cache_delete_all();
			} 
        } 
	}

	function insert_or_update_categories2site($data)
	{
        $CI =& get_instance();
        $id = (int) $data['category_id'];
        $CI->db->from('categories2site')->where('category_id', $id);
        $query = $CI->db->get();
        if ($query->num_rows() > 0){
        	$CI->db->where('category_id', $id);
        	$CI->db->update('categories2site', $data);
        	if ($CI->db->affected_rows() > 0){
        	$CI->db->cache_delete_all();
        	}
        }else{
			$CI->db->insert('categories2site', $data);
        	if ($CI->db->affected_rows() > 0){
        	$CI->db->cache_delete_all();
        	}
	 }
	}

    function show_site_id_selection(){
        $selectionarray = array();
		$CI =& get_instance();
		$CI->load->helper('form');
		$CI->db->select('site_id,shortname');
		$query = $CI->db->get('siteinfo');
		if ($query->num_rows >0){
			foreach($query->result() as $row){
				$selectionarray[$row->site_id] = $row->shortname;
			}
			
		}
		echo '<td>';
		echo form_dropdown('a_site_id',$selectionarray,'0');			
		echo '</td>';
	}

    function get_all_ids_and_shortnames_on_siteinfo(){
		$CI =& get_instance();
		$CI->db->select('site_id,shortname');
		$siteinfo = $CI->db->get('siteinfo');
		if ($siteinfo->num_rows >0){
			return $$siteinfo;		
		}else{
			return NULL;
		}
	}

    function get_shortname_on_siteinfo_by_id($site_id){
		$CI =& get_instance();
		$CI->db->select('shortname');
		$CI->db->from('siteinfo');
		$CI->db->where('site_id',(int)$site_id);
		$siteinfo = $CI->db->get();
		if ($siteinfo->num_rows >0){
			foreach($siteinfo->result() as $row){
				return $row->shortname;		
			}
		}else{
			return NULL;
		}
	}

    function get_categories2site_by_id($category_id){
		$CI =& get_instance();
        $id = (int) $category_id; 
        $CI->db->from('categories2site')->where('category_id', $id);
		$categories2site = $CI->db->get();
		if ($categories2site->num_rows >0){
			return $categories2site;		
		}else{
			return NULL;
		}
	}

    function get_articles2site_by_id($article_id){
		$CI =& get_instance();
        $id = (int) $article_id;
		$CI->db->from('articles2site')->where('article_id', $id);
        $articles2site = $CI->db->get();
 		if ($articles2site->num_rows >0){
			return $articles2site;		
		}else{
			return NULL;
		}
	}

}

/* End of file events.php */
/* Location: ./upload/my-modules/management/events.php */ 
