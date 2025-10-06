<h1 class="page-header">Manage News</h1>
<?php
$News = new News();

if($News->isNewsDirWritable()) {

	# News detele
	if(isset($_REQUEST['delete'])) {
		$deleteNews = $News->removeNews($_REQUEST['delete']);
		$News->cacheNews();
		$News->updateNewsCacheIndex();
		if($deleteNews) {
			redirect(1, 'admincp/?module=managenews');
		} else {
			message('error','Invalid news ID');
		}
	}
	
	# News cache
	if(isset($_REQUEST['cache']) && $_REQUEST['cache'] == 1) {
		$cacheNews = $News->cacheNews();
		$News->updateNewsCacheIndex();
		if($cacheNews) {
			message('success','News successfully cached');
		} else {
			message('error','There are no news to cache.');
		}
	}
	
	$news_list = $News->retrieveNews();
	if(is_array($news_list)) {
		
		foreach($news_list as $row) {
			
			$News->setId($row['news_id']);
			
			echo '<div class="panel panel-default">';
				echo '<div class="panel-heading">';
					echo '<a href="'.__BASE_URL__.'news/'.$row['news_id'].'/" target="_blank">'.$row['news_title'].'</a>';
					echo '<a class="btn btn-danger btn-xs pull-right" href="'.admincp_base("managenews&delete=".$row['news_id']).'"><i class="fa fa-trash"></i> delete</a>';
					echo '<a class="btn btn-warning btn-xs pull-right" style="margin-right:5px;" href="'.admincp_base("editnews&id=".$row['news_id']).'"><i class="fa fa-edit"></i> edit</a>';
				echo '</div>';
				echo '<div class="panel-body">';
					echo '<div class="row">';
						echo '<div class="col-xs-6">';
							echo '<table class="table">';
								echo '<tr>';
									echo '<th>News Id:</th>';
									echo '<td>'.$row['news_id'].'</td>';
								echo '</tr>';
								echo '<tr>';
									echo '<th>Author:</th>';
									echo '<td>'.$row['news_author'].'</td>';
								echo '</tr>';
								echo '<tr>';
									echo '<th>Date:</th>';
									echo '<td>'.date("Y-m-d H:i",$row['news_date']).'</td>';
								echo '</tr>';
							echo '</table>';
						echo '</div>';
						echo '<div class="col-xs-6">';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		}
		
	}
	
	echo '<a class="btn btn-success" href="'.admincp_base("managenews&cache=1").'">UPDATE NEWS CACHE</a>';

} else {
	message('error','The news cache folder is not writable.');
}

?>