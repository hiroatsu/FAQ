<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * report Events File
 *
 */
class report_events
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
     $core_events->register('get_category_name_by_category_id', $this, 'get_category_name_by_category_id');
     $core_events->register('get_category_id_by_article_id', $this, 'get_category_id_by_article_id');
     $core_events->register('get_count_clickinfo_by_article_id', $this, 'get_count_clickinfo_by_article_id');
     $core_events->register('get_true_count_rating_log_by_article_id', $this, 'get_true_count_rating_log_by_article_id');
     $core_events->register('get_false_count_rating_log_by_article_id', $this, 'get_false_count_rating_log_by_article_id');
	}


    function insert_rating_log($data){
		$CI =& get_instance();
		$article_id = (int) $data['article_id'];
		$article_rating = (int) $data['rating'];
		$param = array(
				'article_id' => $article_id, 
				'datetime' => date('Y-m-d H:i:s',time()),
				'article_rating'=> $article_rating
		);
		$CI->db->insert('rating_log', $param);
        if ($CI->db->affected_rows() > 0){
        	$CI->db->cache_delete_all();
        }
		if((int) $article_rating == 1){
			$CI->core_events->trigger('thankyou');
			return "Done";
			//$CI->load->helper('url');
		}else{
				redirect('http://mobile.fms-alpha.com'); 
		}
	    //return "Done"; 
	}
	function get_true_count_rating_log_by_article_id($data){
		//$article_id= (int)$data['article_id'];
		return $this->get_count_rating_log_by_article_id($data,1);
	}

	function get_false_count_rating_log_by_article_id($data){
		//$article_id= (int)$data['article_id'];
		return $this->get_count_rating_log_by_article_id($data,-1);
	}


	function get_count_rating_log_by_article_id($data,$rating){
		$article_id = (int) $data['article_id'];
		$start_datetime = $data['start_datetime'];
		$end_datetime = $data['end_datetime'];
	    $CI =& get_instance();
		$CI->db->select('id')->from('rating_log');
		$CI->db->where('article_id', $article_id);
		$CI->db->where('article_rating', (int)$rating);
		$CI->db->where('datetime >=', $start_datetime);
		$CI->db->where('datetime <', $end_datetime);
		return $CI->db->count_all_results();
	}

	function get_count_clickinfo_by_article_id($data){
	    
		$CI =& get_instance();
		//$article_id = (int) $article_id;
		$article_id = (int) $data['article_id'];
		$start_datetime = $data['start_datetime'];
		$end_datetime = $data['end_datetime'];
		
		$CI->db->select('article_id')->from('clickinfo');
		$CI->db->where('article_id', (int)$article_id);
		$CI->db->where('visit_datetime >=', $start_datetime);
		$CI->db->where('visit_datetime <', $end_datetime);
		return $CI->db->count_all_results();
	}

    function get_category_id_by_article_id($article_id)
	{
	    $CI =& get_instance();
		$CI->db->select('category_id')->from('article2cat');
		$CI->db->where('article_id', (int)$article_id);
		$query = $CI->db->get();
		echo "<td>";
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row){
				echo $row->category_id;
			}
		}else{
		echo "N/A";
		}
		echo "<td>";
	}

    function get_category_name_by_category_id($category_id)
	{
	    $CI =& get_instance();
		$CI->db->select('cat_name')->from('categories');
		$CI->db->where('cat_id', (int)$category_id);
		$query = $CI->db->get();
		echo "<td>";
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row){
				echo $row->cat_name;
			}
		}else{
		echo "N/A";
		}
		echo "</td>";
	}
}

/* End of file events.php */
/* Location: ./upload/my-modules/fujisan/events.php */ 
