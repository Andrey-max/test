<?php

session_start();

// Создание комментария
function createDiv($id, $author, $datetime, $textcom){
$_monthsList = array(
  "01" => "января",
  "02" => "февраля",
  "03" => "марта",
  "04" => "апреля",
  "05" => "мая",
  "06" => "июня",
  "07" => "июля",
  "08" => "августа",
  "09" => "сентября",
  "10" => "октября",
  "11" => "ноября",
  "12" => "декабря"
);

$datetime = strtotime($datetime);
$_month = date("m", $datetime);
$datetime = date('d '.$_monthsList[$_month].' Y в H:i:s', $datetime);
  	
 $div = "<div class='comment' id='".$id."'>".
           "<div class='header_comment'>".
              "<div class='author'>".$author."</div>".
              "<div class='datetime'>".
		         $datetime.
		         "<input type='button' value='Удалить' class='delcomment'>".
		      "</div>".
		    "</div>".
		    "<div class='textcom'>".
	          $textcom.
		    "</div>".
        "</div>";
  return $div;
};

$host = "localhost";
$user = "root";
$password = "";
$database = "mydb";

// Добавление нового комментария по кнопке "Добавить комментарий"
if (isset($_POST["param"]) && $_POST["param"]=="sendcom"){
  if (isset($_POST["author"]) && isset($_POST["textcom"]) && isset($_POST["cap"]) && isset($_SESSION["cap"])) {
    if ($_POST["cap"]==$_SESSION["cap"]) {  // если введенный код равен значению капчи, то сохранение комментария
	  $link = @mysqli_connect($host, $user, $password, $database) or die("Соединение не удалось");	
      $query = "INSERT INTO `comment`(`author`, `textcom`, `datetime`) VALUES ('".$_POST["author"]."','".$_POST["textcom"]."',NOW())";
      mysqli_query($link, $query);
      $query = "SELECT * FROM `comment` WHERE `id`=".mysqli_insert_id($link);
      $result = mysqli_query($link, $query); 
      for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
      echo createDiv($data[0]["id"], $data[0]["author"], $data[0]["datetime"], $data[0]["textcom"]);
	  mysqli_close($link); 
    } else 
    echo 'No';
  };
};

// Вывод комментариев из базы данных при загрузке страницы
if (isset($_POST["param"]) && $_POST["param"]=="loadpage") {
  $link = @mysqli_connect($host, $user, $password, $database) or die("Соединение не удалось");
  $query = "SELECT * FROM `comment` ORDER BY `datetime` DESC";
  $result = mysqli_query($link, $query); 
  for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
  $div="";
  for ($i=0; $i<count($data); ++$i) {
    $div=$div." ".createDiv($data[$i]["id"], $data[$i]["author"], $data[$i]["datetime"], $data[$i]["textcom"]);
  };
  mysqli_close($link); 
  echo $div;
};

// Удаление комментария
if (isset($_POST["param"]) && $_POST["param"]=="id") {
  if (isset($_POST["id"])) {
	$link = @mysqli_connect($host, $user, $password, $database) or die("Соединение не удалось");  
    $query = "DELETE FROM `comment` WHERE `id`=".$_POST["id"];
    mysqli_query($link, $query);
	mysqli_close($link); 
  }
};

// Формирование капчи
if (isset($_POST["param"]) && $_POST["param"]=="captcha") {

  $captcha = '';
  $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
  for ($i = 0; $i < 4; $i++) {
    $captcha .= $keys[array_rand($keys)];
  };
  
  $_SESSION['cap'] = $captcha;  // Сохранение значения капчи на сервере
  
  $im = imagecreate(41, 18);
  $bg = imagecolorallocate($im, 255, 255, 255);
  $textcolor = imagecolorallocate($im, 0, 0, 255);
  imagestring($im, 5, 3, 1, $captcha, $textcolor);
  header('Content-type: image/png');
  ImageJpeg($im, "1.jpg");
  imagedestroy($im);
  
};


 ?>