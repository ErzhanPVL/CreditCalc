<html>

<head>
	<title>Table</title>
	<meta charset="utf-8">
</head>

<body>
<?php

//Проверка на правильность ввода почты
$email=$_GET["clientmail"];
if(filter_var($email, FILTER_VALIDATE_EMAIL))
{
	$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");//Запишем в локальный файл наши GET параметры, для обработки в get_table.php
	$txt = $_SERVER['argv'][0];
	fwrite($myfile, $txt);
	fclose($myfile);
	$hash = md5(microtime());//Сгенерируем псевдослучайное значение
	mail($email, "Table", "<a href='http://".$_SERVER['HTTP_HOST']."/get_table.php?hash=".$hash."'>Link of your table</a>");//Отправка почты с уникальной ссылкой
}

//Объявим переменные для каждого значений из формы, полученной методом GET
$price=$_GET["price"];
$startcap=$_GET["startcap"];
$period=$_GET["period"];
$yearper=$_GET["yearper"];

$correct=1;//Проверка на пустые поля

	if(empty($price) or empty($startcap) or empty($period) or empty($yearper))
	{
		echo "Заполните пустые поля";
		$correct=0;
	}

if($correct==1)
{
	$permonth=1/12*$_GET["yearper"]/100; //преобразовываем годовую ставку в месячную
	$credit=$price*(1-$startcap/100); //Реальная сумма долга с вычетом первоначального взноса
	$x=$credit*($permonth+($permonth/(  pow(1+$permonth,$period) -1 ))); //Аннуитетный платеж
	$overpay=$x*$period-$credit; //Переплата

	//Перед таблицей покажем некоторые данные
	echo "Первоначальный взнос: ",round($price*$startcap/100)," тг.</br>
	Вступительный взнос: ",round($price*5/100)," тг. </br>
	Ежемесячный платеж: ",round($x)," тг. </br>
	Общая переплата: ",round($overpay+$price*5/100)," тг. </br>
	Переплата с учетом вступительного взноса: ",round(($overpay+$price*5/100)/$credit*100),"% </br>
	Переплата без учета вступительного взноса: ",round($overpay/$credit*100),"% </br>";
}
?>

<table border="1">
	<tbody>
	<tr>
		<th>Номер месяца</th>
		<th>Остаток</th>
		<th>Начисленные проценты</th>
		<th>Основной долг</th>
		<th>Ежемесячный платеж</th>
	</tr>
		<?php
		if($correct == 1)
		{
			// функция для добавления одной строки в БД по всем параметрам, кроме месяца (полагаем уникальным идентификатором)
			function intab($a, $b, $c, $d)
			{
		
				try
				{
					$pdo=new PDO('mysql:host=localhost;dbname=credit;charset=utf8','root','');
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$sql="INSERT INTO `paymethod` (`leftdebt`,`commission`,`indebt`,`pay`) VALUES($a,$b,$c,$d)";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					echo $e->getMessage();
				}

			}

			
			$debt=$credit;//Остаток долга
			$commis=$debt*$permonth;//Начисленные проценты в тенге
			$payment=$x-$commis;//Основной долг
			
			//Пробежимся циклом по всем месяцам
				for($i=1; $i<=$period; $i++)
				{
					//Добавляем в таблицу БД строку, соответствующей текущей итерации цикла 
					intab(round($debt),round($commis),round($payment),round($x));
					
					//Добавляем в таблицу на странице строку, соответствующей текущей итерации цикла
					echo "<tr>
						<td>$i</td>
						<td>",round($debt),"</td>
						<td>",round($commis),"</td>
						<td>",round($payment),"</td>
						<td>",round($x),"</td>
						</tr>";
					$debt=$debt-$payment;
					$commis=$debt*$permonth;
					$payment=$x-$commis;
				}

			//Добавляем в таблицу на странице итоговую строку
			echo "<tr>
				<td><b>Итого</b></td>
				<td>0</td>
				<td>",round($overpay),"</td>
				<td>",round($credit),"</td>
				<td>",round($x*$period),"</td>";
		}
		?>
	</tbody>
</table>
</body>
</html>