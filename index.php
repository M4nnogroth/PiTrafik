<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<?php
	header('Refresh: 30;'); //Consider using another method of refreshing the table
	include ('php/functions.php');
	$authKey = getAuthKey(); //Fetch a time based key
	$currentDate = date('y-m-d'); //Is used to calculate remaining time until departure
	$currentTime = date('H:i');	
	$friesId = getId('Doktor Fries Torg', $authKey)['LocationList']['StopLocation'][0]['id']; //Gets the specific ID for our stop. Navigation in the json decoded array is shown here
	$estridsId = getId('Syster Estrids Gata', $authKey)['LocationList']['StopLocation'][0]['id'];
	$sahlgrenskaId = getId('Sahlgrenska huvudentré', $authKey)['LocationList']['StopLocation'][0]['id'];	

	$fries = (getDeparture($friesId, $currentDate, $currentTime, $authKey)['DepartureBoard']); //Similar to ID but instead fetches all available departures for the gived ID. Some optimization could be used here
	$estrids = (getDeparture($estridsId, $currentDate, $currentTime, $authKey)['DepartureBoard']);
	$sahlgrenska = (getDeparture($sahlgrenskaId, $currentDate, $currentTime, $authKey)['DepartureBoard']);
	$stations = array($fries, $estrids, $sahlgrenska); //puts everything in one array. all departures on Doktor Fries Torg can be found in index 0 for example.	
	?>
	<title>PiTrafik 1.0</title>
</head>
<body>
	<div class="info"> 
			<div class="servertime"><?php 
				if (date('Y-m-d') == $stations[0]['serverdate']) {
					$day = date('l');
					$date = date('j');
					$month = date('F');

					//Could not get date() to properly display Swedish letters
					switch ($day) {
						case 'Monday':
						$day = 'Måndag';
						break;
						case 'Tuesday':	
						$day = 'Tisdag';
						break;
						case 'Wednesday':
						$day = 'Onsdag';
						break;
						case 'Thursday':
						$day = 'Torsdag';
						break;
						case 'Friday':
						$day = 'Fredag';
						break;
						case 'Saturday':
						$day = 'Lördag';
						break;
						case 'Sunday':
						$day = 'Söndag';
						break;
					}

					switch ($month) {
						case 'January':
						$month = 'Januari';
						break;
						case 'February':
						$month = 'Februari';
						break;
						case 'May':
						$month = 'Maj';
						break;
						case 'June':
						$month = 'Juni';
						break;
						case 'July':
						$month = 'Juli';
						break;
						case 'August':
						$month = 'Augusti';
						break;
						case 'October':
						$month = 'Oktober';
						break;
					}	
					echo $day.'<br>'.$date.' '.$month.'<br>'.$stations[0]['servertime'];
				}
				?></div>
				<p class="title">PiTrafik</p>
				<img class="logo" src="img/Raspberry_Pi_Logo.png">

			</div>
			<?php
			$serverTime = $stations[0]['servertime'];
			//3 big boxes
			for ($j=0; $j <sizeOf($stations); $j++) { 
				?>
				<div class="stationname">	
					<?php
					echo $stations[$j]['Departure'][0]['stop'];			 
					?>
				</div>

				<div class="station">
					<div class="pointers">
						<div class="linje">Linje</div>
						<div class="direction">Destination</div>
						<div class="departure">Avgår om (min)</div>
					</div>

					<?php
					//Info about the 7 next departures
					for ($i=0; $i < 7; $i++) { 
						$bgColor = $stations[$j]['Departure'][$i]['bgColor'];
						$fgColor = $stations[$j]['Departure'][$i]['fgColor'];
						?>			
						<div class="name" style="color:<?php echo $bgColor;?>; background-color: <?php echo $fgColor; ?>;">
							<?php echo $stations[$j]['Departure'][$i]['sname']; ?>
						</div>
						<div class="direction">
							<?php echo $stations[$j]['Departure'][$i]['direction']; ?>
						</div>	
						<div class="departure">
							<?php 
							if (isset($stations[$j]['Departure'][$i]['rtTime'])) {
								if (getRemainingTime($serverTime, $stations[$j]['Departure'][$i]['rtTime'])==0) { //If departure is now
									echo 'Nu ('.$stations[$j]['Departure'][$i]['rtTime'].')';
								}
								else {
									echo getRemainingTime($serverTime, $stations[$j]['Departure'][$i]['rtTime']).' ('.$stations[$j]['Departure'][$i]['rtTime'].')'; //print remaining time
								}		
							}
							else {
								echo getRemainingTime($serverTime, $stations[$j]['Departure'][$i]['time']).' ('.$stations[$j]['Departure'][$i]['time'].')';
							} 
							?>
						</div>	
						<?php
					}

					?>
				</div>
				<?php
			}
			?>
</body>
</html>
