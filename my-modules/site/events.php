<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * test Events File
 *
 * The class name must be named "yourmodule_events" where your module is the name
 * of the module. For this module it is named "developer". 
 */
class site_events
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
     $core_events->register('viewtop', $this, 'viewtop');
     $core_events->register('viewarticle', $this, 'viewarticle');
     $core_events->register('viewcategory', $this, 'viewcategory');
     $core_events->register('viewallarticles', $this, 'viewallarticles');
	}

    function viewtop()
    {
	    $CI =& get_instance();
		$user_agent = $CI->input->user_agent();
		$is_shiftjis = $this->isShiftjis($user_agent);

		$data['parents'] = $this->get_categories_by_parent_with_useragent(0);
		$data['cat_tree'] = $CI->category_model->get_cats_for_select();		
		$data['pop'] = $CI->article_model->get_most_popular(10);
		$data['latest'] = $CI->article_model->get_latest(10);
		$data['title'] = $CI->init_model->get_setting('site_name');
		$this -> display_template_with_useragent('home',$data,$user_agent,$is_shiftjis,'front');
		return "done";
    }


    function viewallarticles(){
	    $CI =& get_instance();
		$user_agent = $CI->input->user_agent();
		$is_shiftjis = $this->isShiftjis($user_agent);
		
		$data['parents'] = $this->get_categories_by_parent_with_useragent(0);
		foreach($data['parents']->result() as $row)
		{
			$data['articles'][$row->cat_id] =$CI->article_model->get_articles_by_catid($row->cat_id);
		}
		$data['title'] = $CI->init_model->get_setting('site_name');
		$this->display_template_with_useragent('all',$data,$user_agent,$is_shiftjis,'front');
		return "done";
	}
    function viewarticle($uri='')
    {
	    $CI =& get_instance();
		$user_agent = $CI->input->user_agent();
		$is_shiftjis = $this->isShiftjis($user_agent);

		$data['title'] = $CI->init_model->get_setting('site_name');
		if($uri<>'' && $uri<>'index') 
		{
			$uri = $CI->input->xss_clean($uri);
			$article = $CI->article_model->get_article_by_uri($uri);
			if($article)
			{
				$data['article'] = $article;
				$CI->article_model->add_hit($data['article']->article_id);
				
				//format description
				$data['article']->article_description = $CI->article_model->glossary($data['article']->article_description);
				
				// call hooks
				$arr = array('article_id' => $data['article']->article_id, 'article_title' => $data['article']->article_title);
				if($CI->core_events->trigger('article/title', $arr) != '')
				{
					$data['article']->article_description = $CI->core_events->trigger('article/title', $arr);
				}
				$arr = array('article_id' => $data['article']->article_id, 'article_description' => $data['article']->article_description);
				if($CI->core_events->trigger('article/description', $arr) != '')
				{
					$data['article']->article_description = $CI->core_events->trigger('article/description', $arr);
				}
				
				$data['article_cats'] = $CI->category_model->get_cats_by_article($data['article']->article_id);
				$data['attach'] = $CI->article_model->get_attachments($data['article']->article_id);
				$data['author'] = $CI->users_model->get_user_by_id($data['article']->article_author);
				
				$data['title'] = $data['article']->article_title. ' | '. $CI->init_model->get_setting('site_name');
				$data['meta_keywords'] = $data['article']->article_keywords;
				$data['meta_description'] = $data['article']->article_short_desc;
				$data['comments'] = $CI->comments_model->get_article_comments($data['article']->article_id);
				$data['comments_total'] = $CI->comments_model->get_article_comments_count($data['article']->article_id);
				
				$data['comment_author'] = get_cookie('kb_author', TRUE);
				$data['comment_author_email'] = get_cookie('kb_email', TRUE);
				
				$data['comment_template'] = $CI->init_model->load_body('comments', 'front', $data);
			}
			else
			{
				$data = '';
			}
		}
		else
		{
			$data='';
		}
		$this -> display_template_with_useragent('article',$data,$user_agent,$is_shiftjis,'front');
		return "done";
    }

    function viewcategory($uri='')
    {
	    $CI =& get_instance();
		$user_agent = $CI->input->user_agent();
		$is_shiftjis = $this->isShiftjis($user_agent);

		if($uri<>'' && $uri<>'index') 
		{
			//echo $uri;
			$uri = $CI->input->xss_clean($uri);
			//$data['cat']=$CI->category_model->get_cat_by_uri($uri);
			$data['cat']=$this->get_cat_by_uri_with_useragent($uri,$user_agent);
			if($data['cat'])
			{
				$id = $data['cat']->cat_id;
				$data['title'] = $data['cat']->cat_name. ' | '. $CI->init_model->get_setting('site_name');
				$data['parents'] = $this->get_categories_by_parent_with_useragent($id);
				//pagination
				$CI->load->library('pagination');

				//$config['total_rows'] = $CI->article_model->get_articles_by_catid($id, 0, 0, TRUE);
				$config['total_rows'] = $this->get_articles_by_catid_with_useragent($id, 0, 0, TRUE,$user_agent);
				$config['per_page'] = $CI->init_model->get_setting('max_search');

				$config['uri_segment'] = '3';
				$config['base_url'] = site_url("category/". $uri);

				$CI->pagination->initialize($config); 
				$data["pagination"] = $CI->pagination->create_links();
				
				$data['articles'] = $CI->article_model->get_articles_by_catid($id, $config['per_page'], $CI->uri->segment(3), FALSE);
			}
			else
			{
				//陦ｨ遉ｺ縺吶ｋ繧ｫ繝・ざ繝ｪ繝ｼ縺後↑縺九▲縺溘ｉ蜈ｨ縺ｦ縺ｮ繧ｫ繝・ざ繝ｪ繝ｼ繧定｡ｨ遉ｺ縺吶ｋ繝壹・繧ｸ縺ｸ縺・▽繧九・
				redirect('all');
			}
		}
		else 
		{
			$data['title'] = $CI->init_model->get_setting('site_name');
			$data['parents'] = $CI->category_model->get_categories_by_parent(0);
		}
		$this -> display_template_with_useragent('category',$data,$user_agent,$is_shiftjis,'front');
		return "done";
	}

	function display_template_with_useragent($template,$data,$user_agent,$is_shiftjis, $dir='front')
	{
        $CI =& get_instance();

		$data['settings']=$CI->init_model->settings;
		
		// check directory
		if ($dir=='admin')
		{
			define('IN_ADMIN', TRUE);
		}
		else
		{
			$dir='front';
			// are we caching?
			if ($CI->init_model->get_setting('cache_time') > 0)
			{
				$CI->output->cache($CI->init_model->get_setting('cache_time'));
			}
		}
		
		// meta content
		if ( ! isset($data['title']))
		{
			$data['title'] = $CI->init_model->get_setting('site_name');
		}
		if ( ! isset($data['meta_keywords']))
		{
			$data['meta_keywords'] = $CI->init_model->get_setting('site_keywords');
		}
		if ( ! isset($data['meta_description']))
		{
			$data['meta_description'] = $CI->init_model->get_setting('site_description');
		}
		// Check the body exists
		$data['body'] = $this->load_body($template, $dir, $data, $user_agent,$is_shiftjis);
		
        // Now check the layout exists
		$this->load_layout($dir, $data, $user_agent,$is_shiftjis);
		// finally show the last hook
		$CI->core_events->trigger('display_template');	
	}


	/**
	 * Load Body Template
	 *
	 */
	function load_body($template, $dir='front', $data, $user_agent, $is_shiftjis)
	{
	    $CI =& get_instance();
		$data['settings']=$CI->init_model->settings;
		
		if ($dir=='admin')
		{
			$body_file = $dir.'/'.$data['settings']['admin_template'].'/'.$template.'.php';
		}
		else
		{
			$body_file = $dir.'/'.$data['settings']['template'].'/'.$template.'.php';
		}
		
		if ($CI->init_model->test_exists($body_file))
		{			
        	if($is_shiftjis){
            	return $this->convertUtfToShiftjis($CI->load->view($body_file, $data, true));
			}else{
				return $CI->load->view($body_file, $data, true);
			}
		}
		else
		{
        	if ($is_shiftjis){
            	return $this->convertUtfToShiftjis($CI->load->view($dir.'/default/'.$template, $data, true));
			}else{
				return $CI->load->view($dir.'/default/'.$template, $data, true);
			}
		}
	}

	
	// ------------------------------------------------------------------------
	
	/**
	 * Load layout Template
	 *
	 */
	function load_layout($dir='front', $data, $user_agent, $is_shiftjis)
	{
	    $CI =& get_instance();
		$data['settings']=$CI->init_model->settings;
		
		if (defined('IN_ADMIN'))
		{
			$layout_file = $dir.'/'.$data['settings']['admin_template'].'/layout.php';
		}
		else
		{
			if($this->isMobile($user_agent)){
			$layout_file = $dir.'/'.$data['settings']['template'].'/mobilelayout.php';
			}else{
			$layout_file = $dir.'/'.$data['settings']['template'].'/layout.php';
			}
		}
		if ($CI->init_model->test_exists($layout_file))
		{
        	if ($is_shiftjis){
				//set charset SHIFT-JIS
				$data['charset'] = "SHIFT-JIS";
				return $this->convertUtfToShiftjis($CI->load->view($layout_file, $data));			
			}else{
				//set charset SHIFT-JIS
				$data['charset'] = "UTF-8";
				return $CI->load->view($layout_file, $data);
			}
		}
		else
		{
        	if ($is_shiftjis){
				//set charset SHIFT-JIS
				$data['charset'] = "SHIFT-JIS";
				return $this->convertUtfToShiftjis($CI->load->view($dir.'/default/layout.php',$data));			
			}else{
				//set charset SHIFT-JIS
				$data['charset'] = "UTF-8";
				return $CI->load->view($dir.'/default/layout.php',$data);			
			}
		}
	}

	
	function show_thanks($data)
	{
        $CI =& get_instance();
        return $CI->init_model->display_template('thanks', $data);
	}


	function select_template_by_useragent($data)
	{
        $CI =& get_instance();
		$user_agent = $CI->input->user_agent();
		$is_shiftjis = $this->isShiftjis($user_agent);
		$this -> display_template($data,$user_agent,$is_shiftjis);
	}
	
	
	function isMobile($user_agent)
	{
        if ($this -> isDoCoMo($user_agent)) {
            return true;
        } elseif ($this -> isEZweb($user_agent)) {
            return true;
        } elseif ($this ->isSoftBank($user_agent)) {
            return true;
        } elseif ($this ->isWillcom($user_agent)) {
            return true;
        }
	        return false;
    }

     function isShiftjis($user_agent)
	{
        if ($this -> isDoCoMo($user_agent)) {
            return true;
        } elseif ($this -> isEZweb($user_agent)) {
            return false;
        } elseif ($this ->isSoftBank($user_agent)) {
            return true;
        } elseif ($this ->isWillcom($user_agent)) {
            return true;
        }
	 return false;
    }

    function isDoCoMo($user_agent = null)
    {
        if (is_null($user_agent)) {
	        $CI =& get_instance();
			$$user_agent = $CI->input->user_agent();
        }

        if (preg_match('!^DoCoMo!', $user_agent)) {
            return true;
        }

        return false;
    }

    function isEZweb($user_agent = null)
    {
        if (is_null($user_agent)) {
	        $CI =& get_instance();
			$$user_agent = $CI->input->user_agent();
        }

        if (preg_match('!^KDDI-!', $user_agent)) {
            return true;
        } elseif (preg_match('!^UP\.Browser!', $user_agent)) {
            return true;
        }

        return false;
    }

    function isSoftBank($user_agent = null)
    {
        if (is_null($user_agent)) {
	        $CI =& get_instance();
			$user_agent = $CI->input->user_agent();
        }

        if (preg_match('!^SoftBank!', $user_agent)) {
            return true;
        } elseif (preg_match('!^Semulator!', $user_agent)) {
            return true;
        } elseif (preg_match('!^Vodafone!', $user_agent)) {
            return true;
        } elseif (preg_match('!^Vemulator!', $user_agent)) {
            return true;
        } elseif (preg_match('!^MOT-!', $user_agent)) {
            return true;
        } elseif (preg_match('!^MOTEMULATOR!', $user_agent)) {
            return true;
        } elseif (preg_match('!^J-PHONE!', $user_agent)) {
            return true;
        } elseif (preg_match('!^J-EMULATOR!', $user_agent)) {
            return true;
        }

        return false;
    }

    function isWillcom($user_agent = null)
    {
        if (is_null($user_agent)) {
	        $CI =& get_instance();
			$user_agent = $CI->input->user_agent();
        }

        if (preg_match('!^Mozilla/3\.0\((?:DDIPOCKET|WILLCOM);!', $user_agent)) {
            return true;
        }

        return false;
    }
	
    /**
     * Checks whether or not the user agent is Willcom by a given user agent string.
     */
    function convertUtfToShiftjis($data)
    {
		return mb_convert_encoding($data, "SJIS-win", "UTF-8");
    }


	/**
	 * Get Categories By Parent.
	 *
	 * Get an array of categories that have the
	 * same parent.
	 *
	 * @access	public
	 * @param	int	the parent id
	 * @return	array
	 */
	function get_categories_by_parent_with_useragent($parent)
	{
	       $arr = array();
	       $CI =& get_instance();
	       $CI->db->distinct();
	       $CI->db->from('categories');
	       $CI->db->join('categories2fujisan', 'categories.cat_id = categories2fujisan.category_id', 'left');
	       $CI->db->orderby('cat_order', 'DESC')->orderby('cat_name', 'asc')->where('cat_parent', $parent)->where('cat_display', 'Y');
		$user_agent = $CI->input->user_agent();
		if($this->isMobile($user_agent) == false)
		{
		   $CI->db->where('site_id', 1);
		}else {
		   $CI->db->where('site_id', 2);
		}
		$query = $CI->db->get();
		return $query;
	}

	function get_cat_by_uri_with_useragent($uri,$user_agent=''){
	    $CI =& get_instance();
		$CI->db->from('categories');
	    $CI->db->join('categories2fujisan', 'categories.cat_id = categories2fujisan.category_id', 'left');
		if($user_agent == '')
		{
			$user_agent = $CI->input->user_agent();
		}
		if($this->isMobile($user_agent) == false)
		{
		   $CI->db->where('site_id', 1);
		}else {
		   $CI->db->where('site_id', 2);
		}
		$CI->db->where('cat_uri', $uri)->where('cat_display', 'Y');
		$query = $CI->db->get();
		$data = $query->row();
		$query->free_result();
		return  $data;
	}
	
	function get_articles_by_catid_with_useragent($id, $limit=0, $current_row = 0, $show_count=FALSE,$user_agent='')
	{
	    $CI =& get_instance();
		$id = (int)$id;
		$CI->db->from('articles');
		$CI->db->join('article2cat', 'articles.article_id = article2cat.article_id', 'left');
		$CI->db->join('articles2fujisan', 'articles.article_id = articles2fujisan.article_id', 'left');
		$CI->db->where('category_id', $id);
		$CI->db->where('article_display', 'Y');
		if($user_agent == '')
		{
			$user_agent = $CI->input->user_agent();
		}
		if($this->isMobile($user_agent) == false)
		{
		   $CI->db->where('site_id', 1);
		}else {
		   $CI->db->where('site_id', 2);
		}
		if ($show_count)
		{
			return $CI->db->count_all_results();
		}
		if ($limit > 0)
		{
			$CI->db->limit($limit, $current_row);
		}
		$query = $CI->db->get();
		return $query;
	}
/**
	function display_template($data,$user_agent,$is_shiftjis, $dir='front')
	{
		$template = 'all';
        $CI =& get_instance();
		$data['settings']=$CI->init_model->settings;
		
		// check directory
		if ($dir=='admin')
		{
			define('IN_ADMIN', TRUE);
		}
		else
		{
			$dir='front';
			// are we caching?
			if ($CI->init_model->get_setting('cache_time') > 0)
			{
				$CI->output->cache($CI->init_model->get_setting('cache_time'));
			}
		}
		
		// meta content
		if ( ! isset($data['title']))
		{
			$data['title'] = $CI->init_model->get_setting('site_name');
		}
		if ( ! isset($data['meta_keywords']))
		{
			$data['meta_keywords'] = $CI->init_model->get_setting('site_keywords');
		}
		if ( ! isset($data['meta_description']))
		{
			$data['meta_description'] = $CI->init_model->get_setting('site_description');
		}
		
		// Check the body exists
		$data['body'] = $this->load_body($template, $dir, $data, $user_agent,$is_shiftjis);
		
        // Now check the layout exists
		$this->load_layout($dir, $data, $user_agent,$is_shiftjis);
		// finally show the last hook
		$CI->core_events->trigger('display_template');	
	}
*/


	
}

/* End of file events.php */
/* Location: ./upload/my-modules/fujisan/events.php */ 
