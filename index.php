<?php require'inc/header.php'; ?>

<?php
$version = '1.0.1';

// Get cURL resource
$ch = curl_init();

$requestedDate = $_GET['date'];

// Get today's date
if ($requestedDate != '') {

	$today = $requestedDate;
	$adjustedDay = strtotime($requestedDate);

} else {

	$adjustedDay = date(Ymd);
	//$adjustedDay = '20170131';
	$today = $adjustedDay;

}

// Set variables for previous/next day
$previousDay = strtotime ('-1 day', strtotime ($today));
$previousDay = date('Ymd', $previousDay);

$nextDay = strtotime ('+1 day', strtotime ($today));
$nextDay = date('Ymd', $nextDay);

// Set url
curl_setopt($ch, CURLOPT_URL, 'https://www.mysportsfeeds.com/api/feed/pull/nhl/2016-2017-regular/scoreboard.json?fordate=' . $today);

// Set method
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// Set options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	"Authorization: Basic " . base64_encode("luskyj89" . ":" . "Ifgtsts987")
]);

// Send the request & save response to $resp
$resp = curl_exec($ch);

if (!$resp) {
	die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}
// } else {
// 	echo "Response HTTP Status Code : " . curl_getinfo($ch, CURLINFO_HTTP_CODE);
// 	echo "\nResponse HTTP Body : " . $resp;
// }

// Close request to clear up some resources
curl_close($ch);

$data = json_decode($resp, true);

?>

<div id="content">
    <div class="wrap">

		<h1>Games That Don't Suck <span>v.<?php echo $version;?></span></h1>

		<p>What games might be worth watching according to our algorithm?*</p>

		<h2>NHL Games</h2>

		<h3><?php
				if ( $requestedDate == '') {

					echo date('l, F j\<\s\u\p\>S\<\/\s\u\p\> Y');

				} else {

					echo date('l, F j\<\s\u\p\>S\<\/\s\u\p\> Y', $adjustedDay);

				}?>
		</h3>

		<a class="date-select-btn" href="?date=<?php echo $previousDay; ?>">Previous Day</a>

		<a class="date-select-btn <?php if ( $today == date(Ymd) ) { echo 'active'; } ?>" href="?date=<?php echo date(Ymd); ?>">Today</a>

		<a class="date-select-btn" href="?date=<?php echo $nextDay; ?>">Next Day</a>

    	<ul id="nhl-games" class="scoreboard">
        <?php
        foreach ($data['scoreboard']['gameScore'] as $game) {
			// Private vars
			$away = $game['game']['awayTeam'];
        	$home = $game['game']['homeTeam'];
			$awayShots = $game['awayShots'];
			$homeShots = $game['homeShots'];
			$awayScore = $game['awayScore'];
			$homeScore = $game['homeScore'];
			$currentPeriod = $game['currentPeriod'];
			$goalDifferential = ($homeScore - $awayScore);
			$goalsScored = ($awayScore + $homeScore);
			$numberOfPeriods = count( $game['periodSummary']['period'] );

        	echo '<li ';

			// If there have been a lot of shots, the game might have a lot going on.
			if ( ($awayShots > 25 &&  $homeShots > 25) && $game['isCompleted'] == 'false' ) {

				echo 'class="worth-watching in-progress shots-taken">';

			// Did the game end with a high shot total?
			} elseif ( $awayShots > 35 &&  $homeShots > 35 && $game['isCompleted'] == 'true' ) {

				echo 'class="worth-watching ended shots-taken">';

			// Is the score of the game close after at least one period?
			} elseif ( $goalDifferential <= 1 && $goalDifferential >= -1 && $goalsScored >= 4) {

				if ( $game['isCompleted'] == 'false' && $currentPeriod > 1 ) {
					echo 'class="worth-watching in-progress close-game">';
				} else {
					echo 'class="worth-watching ended close-game">';
				}

			// Is the game in OT?
			} elseif ( $currentPeriod > 3 ) {

				echo 'class="worth-watching in-progress overtime">';

			// Did the game end in OT?
		} elseif ( $numberOfPeriods >= 4 ) {

				echo 'class="worth-watching ended overtime">';

			// If over 9 goals have been scored
			} elseif ( $goalsScored >= 10 ) {

				if ( $game['isCompleted'] == 'false' && $currentPeriod > 1 ) {
					echo 'class="worth-watching in-progress goals-scored">';
				} else {
					echo 'class="worth-watching ended goals-scored">';
				}

			} else {
				echo '>'; // Close the tag if no conditions met
			}

			echo $away['City'] .' <span class="away-team-name">'. $away['Name'] . '</span>';

			if ( $game['isUnplayed'] == 'false') {
				echo '<span class="away-score">' . $game['awayScore'] . '</span>';
			}

			echo ' @ '. $home['City'] .' <span class="home-team-name">'. $home['Name'] . '</span>';

			if ( $game['isUnplayed'] == 'false') {
				echo '<span class="home-score">' . $game['homeScore'] . '</span>';
			}

				/* .
				' - '. date('m/d/Y', strtotime($game['game']['date'])) .
				' - '. $game['game']['time']*/ ;

			echo '<span class="game-status">';

			// Check the game progress
			if ( $game['isCompleted'] == 'false' && $currentPeriod == 1 ) {

				echo ' 1st Period';

			} elseif ( $game['isCompleted'] == 'false' && $currentPeriod == 2 ) {

				echo ' 2nd Period';

			} elseif ( $game['isCompleted'] == 'false' && $currentPeriod == 3 ) {

				echo ' 3rd Period';

			} elseif ( $game['isCompleted'] == 'false' && $currentPeriod == 4 ) {

				echo ' OT';

			}

			// Is the score final?
			if ( $game['isCompleted'] == 'true') {

				if ( $numberOfPeriods >= 4 ) {
					echo ' Final/OT';
				} else {
					echo ' Final';
				}

			} else {

				echo ' ' . $game['game']['time'];

			}

			echo '</span>';

			echo '</li>'; // End of the list item

        } // End of the loop
        ?>
        </ul>

		<p class="legal">*I will accept no responsibility if the game does in fact suck. This algorithm is experimental and needs more development. Any games that the Pittsburgh Penguins are winning or have won suck and I apologize if said games were misrepresented by the application. More leagues might be added eventually.</p>

		<p class="legal">Created by <a href="http://johnlusky.com/" target="_blank">John Lusky</a></p>

		<br><br><br><br><br><br>

		<p>API Output:</p>
        <pre><?php print_r($data); ?></pre>

    </div>
</div>

<?php require'inc/footer.php'; ?>
