<html>

<head>
	<title>Credit calculator</title>
	<meta charset="utf-8">
</head>

<body>
<form action="table.php" method="get">
	email: <input type="text" name="clientmail" size="30"/><br/>
	Cтоимость недвижимости (в тенге): <input type="number" name="price" size="10"/><br/>
	Первоначальный взнос (в процентах): <input type="number" name="startcap" min="0" max="100" step="0.1" size="3"><br/>
	Годовая ставка (в процентах): <input type="number" name="yearper" min="0" max="100" step="0.1" size="3"><br/>
	Срок рассрочки (в месяцах): <input type="number" name="period" size="3"><br/>
	<input type="submit" value="Рассчитать">
</form>
</body>
</html>