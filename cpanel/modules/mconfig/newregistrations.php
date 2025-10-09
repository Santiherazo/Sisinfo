<?php
echo '<h1 class="page-header">New Registrations</h1>';
	$newRegs = $dB->query_fetch("SELECT ID, username, email FROM webengine_user ORDER BY ID DESC");
	
	if(is_array($newRegs)) {
		echo '<table id="new_registrations" class="table display">';
			echo '<thead>';
			echo '<tr>';
				echo '<th>Id</th>';
				echo '<th>Username</th>';
				echo '<th>Email</th>';
				echo '<th></th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($newRegs as $thisReg) {
				echo '<tr>';
					echo '<td>'.$thisReg['ID'].'</td>';
					echo '<td>'.$thisReg['username'].'</td>';
					echo '<td>'.$thisReg['email'].'</td>';
					echo '<td style="text-align:right;"><a href="'.admincp_base("accountinfo&id=".$thisReg['ID']).'" class="btn btn-xs btn-default">Account Information</a></td>';
				echo '</tr>';
			}
			echo '</tbody>';
		echo '</table>';
	}
?>