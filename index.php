<?php  require_once('config.php') ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<title>Wo bleibt eigentlich Philipp?</title>

<style>
* {
	margin: 0;
	padding: 0;
}

body {
	font-family: Helvetica;
	font-size: 2em;
}

.content {
	margin: 2em 0;
	text-align: center;
}

h1 {
	margin-bottom: 80px;
}

p {	
	margin-bottom: 20px;
}

p.big {
	font-size: 2.3em;
}

p.footer {
	margin-top: 80px;
	font-size: 0.4em;
}

a:link, a:active, a:visited {
	color: inherit;
}

a:hover {
	text-decoration: none;
}

</style>
</head>

<body>

	<div class="content">
		<h1>Wo bleibt Philipp?</h1>

<?php if($result->status == Status::ASLEEP): ?>
		<p>Tja, sieht so aus als</p>
		<p class="big">schläft</p>
		<p>er noch :)</p>
<?php elseif($result->status == Status::WORKING): ?>
		<p>Schon seit</p>
		<p class="big"><?php echo date('H:i', $result->last_checkin->date) ?> Uhr</p>
		<p>im Büro!</p>
<?php elseif($result->status == Status::ON_THE_WAY): ?>
		<p>Unterwegs!</p>
		<p class="big" title="Start: <?php echo date('H:i', $result->last_checkin->date) ?>">ETA: <?php echo date('H:i', $result->eta) ?> Uhr</p>
		<p>(Angabe ohne Gewähr)</p>
<?php else: ?>
		<p>Mhh, irgendwas</p>
		<p class="big">stimmt</p>
		<p>da nicht.</p>
<?php endif ?>

		<p class="footer"><a href="http://knuspermagier.de">knuspermagier.de</a></p>
	</div>

</body>
</html>
