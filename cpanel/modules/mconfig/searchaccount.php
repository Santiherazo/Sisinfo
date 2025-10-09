<h1 class="page-header">Search Account</h1>
<form class="form-inline" role="form" method="post">
	<div class="form-group">
		<input type="text" class="form-control" id="input_1" name="search_request" placeholder="Account username"/>
	</div>
	<button type="submit" class="btn btn-primary" name="search_account" value="ok">Search</button>
</form>
<br />
<?php
	if(isset($_POST['search_account']) && isset($_POST['search_request'])) {
		try {
			if(!Validator::Length($_POST['search_request'], 11, 2)) throw new Exception("The username can be 3 to 10 characters long.");
			$searchRequest = '%'.$_POST['search_request'].'%';
			$searchResults = $dB->query_fetch("SELECT * FROM webengine_u_core WHERE unom LIKE ?", array($searchRequest));
			if(!$searchResults) throw new Exception("No results found.");
			
			if(is_array($searchResults)) {
				echo '<div class="row">';
				echo '<div class="col-md-6">';
				echo '<table class="table table-striped table-condensed table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th colspan="2">Search Results for <span style="color:red;"><i>'.$_POST['search_request'].'</i></span></th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
				foreach($searchResults as $account) {
					echo '<tr>';
						echo '<td>'.$account['username'].'</td>';
						echo '<td style="text-align:right;">';
							echo '<a href="'.admincp_base("accountinfo&id=".$account['uid']).'" class="btn btn-xs btn-default">Account Information</a>';
						echo '</td>';
					echo '</tr>';
				}
					echo '</tbody>';
				echo '</table>';
				echo '</div>';
				echo '<div class="col-md-6"></div>';
				echo '</div>';
			}
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
?>