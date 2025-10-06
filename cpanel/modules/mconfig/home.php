Left Panel
      <div class="lg:col-span-2 grid gap-6">
        <div class="bg-white p-6 rounded-2xl shadow">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h2 class="text-xl font-semibold">Income Tracker</h2>
              <p class="text-gray-500 text-sm">Track changes in income over time and access detailed data</p>
            </div>
            <button class="bg-gray-100 px-3 py-1 rounded-md text-sm">Week</button>
          </div>
          <div class="flex items-end justify-between h-40">
            <div class="flex flex-col items-center">
              <div class="w-1 h-12 bg-gray-300"></div>
              <span class="text-sm mt-2">S</span>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-1 h-16 bg-gray-300"></div>
              <span class="text-sm mt-2">M</span>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-1 h-32 bg-primary"></div>
              <span class="text-sm mt-2">T</span>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-1 h-20 bg-gray-300"></div>
              <span class="text-sm mt-2">W</span>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-1 h-24 bg-gray-300"></div>
              <span class="text-sm mt-2">T</span>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-1 h-18 bg-gray-300"></div>
              <span class="text-sm mt-2">F</span>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-1 h-14 bg-gray-300"></div>
              <span class="text-sm mt-2">S</span>
            </div>
          </div>
          <div class="mt-4 text-sm text-green-500 font-semibold">+20%</div>
          <p class="text-gray-500 text-sm">This week's income is higher than last week's</p>
        </div>
        <div class="flex flex-col gap-4 md:flex-row">
          <div class="bg-white p-6 rounded-2xl shadow flex-1">
            <h3 class="text-lg font-semibold mb-4">Let's Connect</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gray-300"></div>
                  <div>
                    <p class="font-semibold">Randy Gouse</p>
                    <span class="text-sm text-red-500">Senior</span>
                  </div>
                </div>
                <button class="bg-primary text-white px-2 py-1 rounded">+</button>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gray-300"></div>
                  <div>
                    <p class="font-semibold">Giana Schleifer</p>
                    <span class="text-sm text-blue-500">Middle</span>
                  </div>
                </div>
                <button class="bg-primary text-white px-2 py-1 rounded">+</button>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow flex-1 flex flex-col justify-between">
            <h3 class="text-lg font-semibold mb-2">Unlock Premium Features</h3>
            <p class="text-sm text-gray-500">Get access to exclusive benefits and expand your freelancing opportunities</p>
            <button class="mt-4 bg-primary text-white py-2 px-4 rounded-lg">Upgrade now</button>
          </div>
        </div>
      </div>
      <div class="grid gap-6">
        <div class="bg-white p-6 rounded-2xl shadow">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Your Recent Projects</h3>
            <a href="#" class="text-sm text-primary">See all Project</a>
          </div>
          <div class="space-y-4">
            <div class="border-b pb-4">
              <div class="flex justify-between items-center">
                <p class="font-semibold">Web Development Project</p>
                <span class="bg-black text-white text-xs px-2 py-1 rounded">Paid</span>
              </div>
              <p class="text-sm text-gray-500">$10/hour</p>
              <div class="flex gap-2 mt-1">
                <span class="text-xs bg-gray-200 px-2 py-1 rounded">Remote</span>
                <span class="text-xs bg-gray-200 px-2 py-1 rounded">Part-time</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">Germany · 2h ago</p>
              <p class="text-sm text-gray-400 mt-1">This project involves frontend & backend functionalities, and API integrations.</p>
            </div>
            <div class="border-b pb-4">
              <div class="flex justify-between items-center">
                <p class="font-semibold">Copyright Project</p>
                <span class="bg-gray-300 text-gray-700 text-xs px-2 py-1 rounded">Not Paid</span>
              </div>
              <p class="text-sm text-gray-500">$10/hour</p>
            </div>
            <div>
              <div class="flex justify-between items-center">
                <p class="font-semibold">Web Design Project</p>
                <span class="bg-black text-white text-xs px-2 py-1 rounded">Paid</span>
              </div>
              <p class="text-sm text-gray-500">$10/hour</p>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Proposal Progress</h3>
            <span class="text-sm text-gray-500">April 11, 2024</span>
          </div>
          <div class="flex justify-between">
            <div class="text-center">
              <p class="text-xl font-bold">64</p>
              <p class="text-sm text-gray-500">Proposals sent</p>
            </div>
            <div class="text-center">
              <p class="text-xl font-bold">12</p>
              <p class="text-sm text-gray-500">Interviews</p>
            </div>
            <div class="text-center">
              <p class="text-xl font-bold">10</p>
              <p class="text-sm text-gray-500">Hires</p>
            </div>
          </div>
        </div>
      </div>

<?php
if(file_exists(__ROOT_DIR__ . 'install/')) {
	message('warning', 'Your WebEngine CMS <strong>install</strong> directory still exists, it is recommended that you rename or delete it.', 'WARNING');
}

echo '<div class="row">';
	echo '<div class="col-md-6">';
		echo '<div class="panel panel-primary">';
		echo '<div class="panel-heading">General Information</div>';
		echo '<div class="panel-body">';
			
			echo '<div class="list-group">';
				echo '<div class="list-group-item" target="_blank">';
					echo 'OS';
					echo '<span class="pull-right text-muted small">';
						echo '<em>'.PHP_OS.'</em>';
					echo '</span>';
				echo '</div>';
				echo '<div class="list-group-item" target="_blank">';
					echo 'PHP';
					echo '<span class="pull-right text-muted small">';
						echo '<em>'.phpversion().'</em>';
					echo '</span>';
				echo '</div>';
				echo '<a href="https://webenginecms.org/" class="list-group-item" target="_blank">';
					echo 'WebEngine';
					echo '<span class="pull-right text-muted small">';
						if(checkVersion()) echo '<span class="label label-danger">Update Available</span>  ';
						echo '<em>'.__WEBENGINE_VERSION__.'</em>';
					echo '</span>';
				echo '</a>';
			echo '</div>';
			
			echo '<div class="list-group">';
				
				$database = (config('SQL_USE_2_DB',true) ? $dB2 : $dB);
				
				// Total Accounts
				$totalAccounts = $database->query_fetch_single("SELECT COUNT(*) as result FROM MEMB_INFO");
				echo '<div class="list-group-item">';
					echo 'Registered Accounts';
					echo '<span class="pull-right text-muted small">'.number_format($totalAccounts['result']).'</span>';
				echo '</div>';
				
				// Banned Accounts
				$bannedAccounts = $database->query_fetch_single("SELECT COUNT(*) as result FROM MEMB_INFO WHERE bloc_code = 1");
				echo '<div class="list-group-item">';
					echo 'Banned Accounts';
					echo '<span class="pull-right text-muted small">'.number_format($bannedAccounts['result']).'</span>';
				echo '</div>';
				
				// Total Characters
				$totalCharacters = $dB->query_fetch_single("SELECT COUNT(*) as result FROM Character");
				echo '<div class="list-group-item">';
					echo 'Characters';
					echo '<span class="pull-right text-muted small">'.number_format($totalCharacters['result']).'</span>';
				echo '</div>';
				
				// Plugins Status
				$pluginStatus = (config('plugins_system_enable',true) ? 'Enabled' : 'Disabled');
				echo '<div class="list-group-item">';
					echo 'Plugin System';
					echo '<span class="pull-right text-muted small">'.$pluginStatus.'</span>';
				echo '</div>';
				
				// Scheduled Tasks
				$scheduledTasks = $database->query_fetch_single("SELECT COUNT(*) as result FROM ".WEBENGINE_CRON."");
				echo '<div class="list-group-item">';
					echo 'Scheduled Tasks (cron)';
					echo '<span class="pull-right text-muted small">'.number_format($scheduledTasks['result']).'</span>';
				echo '</div>';
				
				// Server Time
				echo '<div class="list-group-item">';
					echo 'Server Time (web)';
					echo '<span class="pull-right text-muted small">'.date("Y-m-d h:i A").'</span>';
				echo '</div>';
				
				// Admins
				$admincpUsers = implode(", ", array_keys(config('admins',true)));
				echo '<div class="list-group-item">';
					echo 'Admins';
					echo '<span class="pull-right text-muted small">'.$admincpUsers.'</span>';
				echo '</div>';
				
			echo '</div>';
		echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="col-md-6">';
		echo '<div class="panel panel-default">';
		echo '<div class="panel-body">';
			echo '<strong>WebEngine CMS Official Website:</strong><br>';
			echo '<a href="https://webenginecms.org/" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i> https://webenginecms.org/</a><br><br>';
			
			echo '<strong>Community Discord:</strong><br>';
			echo '<a href="https://webenginecms.org/discord/" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i> https://webenginecms.org/discord/</a><br><br>';
			
			echo '<strong>Facebook Page:</strong><br>';
			echo '<a href="https://webenginecms.org/facebook/" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i> https://webenginecms.org/facebook/</a><br><br>';
		echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';