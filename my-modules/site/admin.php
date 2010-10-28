<h1>SITEINFO</h1>

<p>Below modify form for SITEINFO</p>

<h2>Site Info Modify Form</h2>

<form method="post" action="<?php echo site_url('/admin/modules/show/site'); ?>">
	<table width="100%" cellspacing="0">
		<tr>
			<td class="row1"><label for="site_id">Site_id:</label></td>
			<td class="row1"><input type="text" size="3" name="site_id" id="site_id" value="" /></td>
		</tr>
		<tr>
			<td class="row2"><label for="shortname">shortname:</label></td>
			<td class="row2"><input type="text" size="10" name="shortname" id="shortname" value="" /></td>
		</tr>
		<tr>
			<td class="row1"><label for="name">name:</label></td>
			<td class="row1"><input type="text" size="50" name="name" id="name" value="" /></td>
		</tr>
		<tr>
			<td class="row2"><label for="url">url:</label></td>
			<td class="row2"><input type="text" size="50" name="url" id="url" value="" /></td>
		</tr>
		<tr>
			<td class="row1"><label for="template">template:</label></td>
			<td class="row1"><input type="text" size="50" name="template" id="template" value="" /></td>
		</tr>
		<tr>
			<td class="row1"><label for="title">title:</label></td>
			<td class="row1"><input type="text" size="50" name="title" id="title" value="" /></td>
		</tr>
		<tr>
			<td class="row1"><label for="keywords">keywords:</label></td>
			<td class="row1"><textarea tabindex="4" name="keywords" id="keywords" cols="50" rows="4" class="inputtext"></textarea></td>
		</tr>
		<tr>
			<td class="row1"><label for="description">description:</label></td>
			<td class="row1"><textarea tabindex="4" name="description" id="description" cols="50" rows="4" class="inputtext"></textarea></td>
		</tr>
	</table>
	<p><input type="submit" name="submit" class="save" value="Submit" /></p>
</form>

<h2>SiteInfo</h2>
<p>Below will be information of siteinfo table:</p>
<?php
$CI =& get_instance();
$CI->db->from('siteinfo');
$query = $CI->db->get();
echo '<table>';
echo '<tr>';
echo '<th>site_id</th>';
echo '<th>shortname</th>';
echo '<th>name</th>';
echo '<th>uri</th>';
echo '<th>template</th>';
echo '<th>title</th>';
echo '<th>keywords</th>';
echo '<th>description</th>';
echo '</tr>';
if ($query->num_rows() > 0)
{
	foreach ($query->result() as $row)
	{
	echo '<tr>';
	echo '<td>'.$row->site_id.'</td>';
	echo '<td>'.$row->shortname.'</td>';
	echo '<td>'.$row->name.'</td>';
	echo '<td>'.$row->url.'</td>';
	echo '<td>'.$row->template.'</td>';
	echo '<td>'.$row->title.'</td>';
	echo '<td>'.$row->keywords.'</td>';
	echo '<td>'.$row->description.'</td>';
	echo '</tr>';
	}
	
}
echo '</table>';
?>

<?php 
	    $CI =& get_instance();
		if(isset($_POST['site_id']) && $_POST['site_id'] !== ''){
			$id =	$_POST['site_id'];				
			if(isset($_POST['shortname']) && $_POST['shortname'] !== ''){
				$data = array(
					'shortname' => $_POST['shortname']
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
				echo "shortname updated ".$_POST['shortname']."<br/>";	

        		}
			}
			if(isset($_POST['name']) && $_POST['name'] !== ''){
				$data = array(
				'name' => $_POST['name']
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
					echo "name updated ".$_POST['name']."<br/>";	
        		}
			}
			if(isset($_POST['url']) && $_POST['url'] !== ''){
				$data = array(
				'url' => $_POST['url']
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
					echo "url updated ".$_POST['url']."<br/>";	
        		}
			}
			if(isset($_POST['template']) && $_POST['template'] !== ''){
				$data = array(
				'template' => $_POST['template'] 
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
					echo "template updated ".$_POST['template']."<br/>";	
        		}
			}
			if(isset($_POST['title']) && $_POST['title'] !== ''){
				$data = array(
				'title' => $_POST['template']
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
					echo "title updated ".$_POST['title']."<br/>";	
        		}
			}
			if(isset($_POST['keywords']) && $_POST['keywords'] !== ''){
				$data = array(
				'keywords' => $_POST['keywords'] 
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
					echo "keywords updated ".$_POST['keywords']."<br/>";	
        		}
			}
			if(isset($_POST['description']) && $_POST['description'] !== ''){
				$data = array(
				'description' => $_POST['description']
				);
				$CI->db->where('site_id', $id);
        		$CI->db->update('siteinfo', $data);
        		if ($CI->db->affected_rows() > 0){
	        		$CI->db->cache_delete_all();
					echo "description updated ".$_POST['description']."<br/>";	
        		}
			}
		}
		?>

