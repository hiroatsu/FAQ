	<div id="tabs">
		<ul>
			<li><a href="<?php echo site_url('admin/stats/');?>"><span><?php echo $this->lang->line('kb_summary'); ?></span></a></li>
			<li><a href="<?php echo site_url('admin/stats/viewed');?>"><span><?php echo $this->lang->line('kb_most_viewed'); ?></span></a></li>
			<li><a href="<?php echo site_url('admin/stats/searchlog');?>"><span><?php echo $this->lang->line('kb_search_log'); ?></span></a></li>
			<li><a href="<?php echo site_url('admin/stats/rating');?>"><span>Rating</span></a></li>
			<li><a href="<?php echo site_url('admin/modules/show/report');?>"class="active"><span>Search Rating</span></a></li>
		</ul>
	</div>
<br /><br />
<?php
$CI =& get_instance();
?>
<form method="post" action="<?php echo site_url('/admin/modules/show/report'); ?>">
	<table width="100%" cellspacing="0">
		<tr align="center">
        <th>SiteInfo</th>
        <th>Article_id</th>
        <th>Category_id</th>
        <th>Month</th>
        <th>Year</th>
        <th>Start_date</th>
        <th>End_date</th>
		</tr>
		<tr>
	     <?php $CI->core_events->trigger('show_site_id_selection'); ?>
		 <td class="row"><input type="text" size="10" name="a_article_id" id="a_article_id" value="" />
         </td>
		 <td class="row"><input type="text" size="10" name="a_category_id" id="a_category_id" value="" />
         </td>
		 <td class="row"><input type="text" size="10" name="a_month" id="a_month" value="" />
         </td>
		 <td class="row"><input type="text" size="10" name="a_year" id="a_year" value="" />
         </td>
         <td>
         <select name= "a_start_year">
         <?php
		 $today = getdate();
		 $year= (int)$today['year']; 
		 	for( $year - 5;$year<=(int)$today['year'];$year++){
			if((int)$year == (int) $today['$year']){
			echo '<option value="'.$year.'"  selected="selected" >'.$year.'</opion>';
			}else{
			echo '<option value="'.$year.'" >'.$year.'</opion>';
			}
		}	
		 ?>
		 </select>
         -
         <select name= "a_start_month">
         <?php
		 $today = getdate(); 
		 	for($month=1;$month<=12;$month++){
			if((int)$month == (int) $today['mon']){
			echo '<option value="'.$month.'"  selected="selected" >'.$month.'</opion>';
			}else{
			echo '<option value="'.$month.'" >'.$month.'</opion>';
			}
		}	
		 ?>
		 </select>
         -
         <select name= "a_start_day">
         <?php 
		 $today = getdate(); 
		 for($day=1;$day<=31;$day++){
			if((int)$day == (int) $today['mday']){
			echo '<option value="'.$day.'" selected="selected" >'.$day.'</opion>';
			}else{
			echo '<option value="'.$day.'" >'.$day.'</opion>';
			}
		 }
		 ?>
		 </select>
         </td>
		 <td class="row">
         <select name= "a_end_year">
         <?php
		 $today = getdate();
		 $year= (int)$today['year']; 
		 	for( $year - 5;$year<=(int)$today['year'];$year++){
			if((int)$year == (int) $today['$year']){
			echo '<option value="'.$year.'"  selected="selected" >'.$year.'</opion>';
			}else{
			echo '<option value="'.$year.'" >'.$year.'</opion>';
			}
		}	
		 ?>
		 </select>
         -
         <select name= "a_end_month">
         <?php 
		 for($month=1;$month<=12;$month++){
			if((int)$month == (int) $today['mon']){
			echo '<option value="'.$month.'"  selected="selected" >'.$month.'</opion>';
			}else{
			echo '<option value="'.$month.'" >'.$month.'</opion>';
			}
			}	
		 ?>
		 </select>
         -
         <select name= "a_end_day">
         <?php 
		 $today = getdate(); 
		 for($day=1;$day<=31;$day++){
			if((int)$day == (int) $today['mday'] +1){
			echo '<option value="'.$day.'" selected="selected" >'.$day.'</opion>';
			}else{
			echo '<option value="'.$day.'" >'.$day.'</opion>';
			}
		}	
		 ?>
		 </select>
         </td>
		</tr>
	</table>
	<input type="hidden" name="a_search_active" id="search_active" value="y" /></p>
	<p><input type="submit" name="submit" class="save" value="Submit" /></p>
</form>


<?php

$CI =& get_instance();
if(isset($_POST['a_search_active']) && $_POST['a_search_active'] == 'y'){
$CI->db->select('articles.article_id,category_id,article_title,site_id')->from('articles');
	$CI->db->join('article2cat', 'articles.article_id = article2cat.article_id', 'left');
	$commment="search term:";
	if(isset($_POST['a_site_id']) && $_POST['a_site_id'] !== ''){
		$CI->db->join('articles2site', 'articles.article_id = articles2site.article_id', 'left');				
		$site_id = (int)$_POST['a_site_id'];
		if($site_id == 0){
			$commment.=" SiteInfo:ALL";
		}else{
			$CI->db->where('site_id', (int) $_POST['a_site_id']);
			$commment.=" SiteInfo:".$_POST['a_site_id']."(1:PC 2:Moble)";
		}
		if(isset($_POST['a_article_id']) && $_POST['a_article_id'] !== ''){
		$CI->db->where('articles.article_id', (int) $_POST['a_article_id']);
			$commment.=" Article_id:".$_POST['a_article_id'];
		}
		if(isset($_POST['a_category_id']) && $_POST['a_category_id'] !== ''){
		$CI->db->where('category_id', (int) $_POST['a_category_id']);
			$commment.=" Category_id:".$_POST['a_category_id'];
		}
		$start_datetime = $_POST['a_start_year'].'-'.$_POST['a_start_month'].'-'.$_POST['a_start_day'].' 00:00:00';
		$end_datetime = $_POST['a_end_year'].'-'.$_POST['a_end_month'].'-'.$_POST['a_end_day'].' 00:00:00';

		if(isset($_POST['a_year']) && $_POST['a_year'] !== '' && isset($_POST['a_month']) && $_POST['a_month'] !== ''){
			$start_datetime = $_POST['a_year'].'-'.$_POST['a_month'].'-01 00:00:00';
			$end_datetime = $_POST['a_year'].'-'.$_POST['a_month'].'-31 00:00:00';
		}else{
			if(isset($_POST['a_year']) && $_POST['a_year'] !== ''){
				$start_datetime = $_POST['a_year'].'-01-01 00:00:00';
				$end_datetime = $_POST['a_year'].'-12-31 00:00:00';
			}
			if(isset($_POST['a_month']) && $_POST['a_month'] !== ''){
				$start_datetime = $_POST['a_start_year'].'-'.$_POST['a_month'].'-01 00:00:00';
				$end_datetime = $_POST['a_start_year'].'-'.$_POST['a_month'].'-31 00:00:00';
			}
		}
	}
	
	
	$CI->db->where('article_display', 'Y')->orderby('article_id', 'ASC');
	$query = $CI->db->get();
	
	$commment.=" start:".$start_datetime." end:".$end_datetime;
	echo '<p>'.$commment.'</p>';
	echo '<table width="100%" cellspacing="0">'."\n";
	echo '<tr align="center">'."\n";
	echo '<th width="3%">A_ID</th>';
	echo '<th width="30%">article_title</th>';
	echo '<th width="3%">C_ID</th>';
	echo '<th width="27%">category</th>';
	echo '<th width="3%">PV</th>';
	echo '<th width="3%"> YES</th>';
	echo '<th width="3%">NO</th>';
	echo '<th width="7%">Total/PV</th>';
	echo '<th width="7%">YES/Total</th>';
	echo '<th width="7%">NO/Total</th>';
	echo '<th width="7%">Site</th>';
	//echo '<th>referrer</th>';
	echo '</tr>'."\n";

	if ($query->num_rows() > 0)
	{
		foreach ($query->result() as $row){
		$param = array(
			'article_id'=>$row->article_id,
			'start_datetime' =>$start_datetime,
			'end_datetime' =>$end_datetime
		);
		
		echo '<tr align="center">'."\n";
		echo '<td>'.$row->article_id.'</td>'."\n";
		echo '<td align="left">'.$row->article_title.'</td>'."\n";
		echo '<td>'.$row->category_id.'</td>'."\n";;
	    echo $CI->core_events->trigger('get_category_name_by_category_id',$row->category_id);
		echo '<td>'."\n";
		$pv = $CI->core_events->trigger('get_count_clickinfo_by_article_id',$param);
		echo $pv;
		echo '</td>'."\n";
		echo '<td>'."\n";
		$t_count = $CI->core_events->trigger('get_true_count_rating_log_by_article_id',$param);
		echo $t_count;
		echo '</td>'."\n";
		echo '<td>'."\n";
		$f_count = $CI->core_events->trigger('get_false_count_rating_log_by_article_id',$param);
		echo $f_count;
		echo '</td>'."\n";
		$answer_count = (int)$t_count + (int)$f_count;
		echo '<td>'."\n";
		if((int) $pv == 0){
		echo "N/A";
		}else{
		$percentage = ceil((int)$answer_count / (int) $pv * 100);
		echo $percentage.'%';
		}
		echo '</td>'."\n";
		echo '<td>'."\n";
		if((int) $answer_count == 0){
		echo "N/A";
		}else{
		$t_percentage = ceil((int)$t_count / (int) $answer_count * 100);
		echo $t_percentage.'%';
		}
		echo '</td>'."\n";
		echo '<td>'."\n";
		if((int) $answer_count == 0){
		echo "N/A";
		}else{
		$f_percentage = ceil((int)$f_count / (int) $answer_count * 100);
		echo $f_percentage.'%';
		}
		echo '</td>'."\n";
		echo '<td>'."\n";
		echo $CI->core_events->trigger('show_siteinfo_on_articles',$row->article_id);
		echo '</td>'."\n";
		echo '</tr>'."\n";
		}
	}
	echo '</table>'."\n";
}
?>
