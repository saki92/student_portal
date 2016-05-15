<html>
<head>
    <title><?php echo $type; ?></title>
</head>
<body>
    <center>
	<?php echo $sem_gpa;
	echo form_open('guest/table/'.$type); ?>
	<select name="sem">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
	</select><br>
	<?php echo $dept; ?>
	<select name="dept">
		<option value="civil">Civil</option>
		<option value="aeronautical">Aeronautical</option>
	</select>
	<br><br>
	<input type="submit">
	</form>
	</center>
</body>
</html>