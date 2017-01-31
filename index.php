<?php require'inc/header.php'; ?>

<?php

// Get cURL resource
$ch = curl_init();

// Get today's date
$today = date(Ymd);
//$today = '20170126';


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

		<h1>Worth Watching</h2>

		<p>Today is <?php echo $today;  ?></p>

		<h2>NHL Games</h2>

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
			} elseif ( $numberOfPeriods == 4 ) {

				echo 'class="worth-watching ended overtime">';

			// If over 7 goals have been scored
			} elseif ( $goalsScored >= 8 ) {

				if ( $game['isCompleted'] == 'false' && $currentPeriod > 1 ) {
					echo 'class="worth-watching in-progress goals-scored">';
				} else {
					echo 'class="worth-watching ended goals-scored">';
				}

			} else {
				echo '>'; // Close the tag if no conditions met
			}

			echo $away['City'] .' '.
				$away['Name'] .
				'<span class="away-score">(' . $game['awayScore'] . ')</span>' .
				' @ '. $home['City'] .' '.
				$home['Name'] .
				'<span class="home-score">(' . $game['homeScore'] . ')</span>'
				/* .
				' - '. date('m/d/Y', strtotime($game['game']['date'])) .
				' - '. $game['game']['time']*/ ;

			// Check the game progress
			if ( $game['isCompleted'] == 'false' && currentPeriod == 1 ) {

				echo ' 1st Period';

			} elseif ( $game['isCompleted'] == 'false' && currentPeriod == 2 ) {

				echo ' 2nd Period';

			} elseif ( $game['isCompleted'] == 'false' && currentPeriod == 3 ) {

				echo ' 3rd Period';

			} elseif ( $game['isCompleted'] == 'false' && currentPeriod == 4 ) {

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

			echo '</li>'; // End of the list item

        } // End of the loop
        ?>
        </ul>

		<br><br><br><br><br><br>

		<p>API Output:</p>
        <pre><?php print_r($data); ?></pre>

    </div>
</div>

<?php require'inc/footer.php'; ?>
