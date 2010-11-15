<h1>Article Log</h1>
</br>
<?php
$CI =& get_instance();
$specified_article_id =$CI->uri->segment(5, 0);
?>
<?php
	$CI->load->view('admin/default/modules/view/management_articles_search.php');
	$CI->load->view('admin/default/modules/view/management_articles_result.php');
?>

