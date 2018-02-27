<?
  require_once "classTable.php";
  require_once "connection.php";

  // Инициализация сессии
  session_start(); 
  if (!isset($_SESSION['Data'])) $_SESSION['Data'] = array();
  if (!isset($_SESSION['width'])) $_SESSION['width'] = 0;
  if (!isset($_SESSION['height'])) $_SESSION['height'] = 0;

  $data = $_SESSION['Data'];
  $width = $_SESSION['width'];
  $height = $_SESSION['height'];
  $_SESSION['width'] = 64;
  $_SESSION['height'] = 64;
  
 // $oTable = new Table();

  // Подключаемся к серверу
  $link = mysqli_connect($host, $user, $password, $database) or exit("Ошибка" . mysqli_error($link));
  
  // Выполняем операции с базой данных
  $query = "SELECT * FROM config";

  $result = mysqli_query($link, $query) or exit("Ошибка" . mysqli_error($link));
  if ($result)
  {
 
  }
  else
  {
    echo "Нет подключения";
  }
 
  $data_point = array();
  $array_answer = array(); //Массив для ответа
  $str_answer = "";
  // Прочитаем полученные входные данные
  $data_input = json_decode(file_get_contents('php://input'), true);
  if (array_key_exists("GetContent", $data_input))
  {
    $name_session = $data_input["GetContent"];
    // Выполняем операции с базой данных
    $query = "SELECT content FROM sessions WHERE name_session = '". $name_session ."';";
    $result = mysqli_query($link, $query) or exit("Ошибка " . mysqli_error($link));
    if ($result)
    {
      $rows = mysqli_num_rows($result); // количество полученных строк
     if ($rows > 0)
     {
       while ($row = mysqli_fetch_assoc($result)) 
       {
        $array_answer[] = $row;
       }
     } 
     // очищаем результат
   //  mysqli_free_result($rows);
    }
    $answer = $array_answer[0]["content"];
    // Сформированный ответ посылаем клиенту
    echo $answer;
  }
  if (array_key_exists("GetGame", $data_input)) 
  { 
    // Выполняем операции с базой данных
    $query = "SELECT name_session, time FROM sessions";
    $result = mysqli_query($link, $query) or exit("Ошибка " . mysqli_error($link));
    if ($result)
    {
      $rows = mysqli_num_rows($result); // количество полученных строк
     if ($rows > 0)
     {
       while ($row = mysqli_fetch_assoc($result)) 
       {
        $array_answer[] = $row;
      //  $str_answer .= $row["name_session"]. $row["time"];
       }
     } 
     // очищаем результат
   //  mysqli_free_result($rows);
    }
    $answer = json_encode($array_answer);
    // Сформированный ответ посылаем клиенту
    echo $answer;
  }
  else
  {
    // Выполним действия с соответствии с полученными входными данными
    if (array_key_exists("NewGame", $data_input)) 
    { 
      // Выполняем операции с базой данных
      $query = "INSERT INTO `life`.`sessions` (`name_session`, `time`, `ID_player`, `content`) VALUES ('" . $data_input["NewGame"] . "', '09.16.17', '1', '".json_encode($data_input["NewPos"])."');";
      $result = mysqli_query($link, $query) or exit("Ошибка " . mysqli_error($link)); 
    }

    if (array_key_exists("PosCell", $data_input) && ($data_input["PosCell"] != 0))
    {
      $data = $data_input;
      foreach ($data as $key) {
        $data_point = array_merge($data_point, $key);
        $array_answer = FlipToPoints($data_point);

        $answer = json_encode($array_answer);
        $_SESSION['Data'] = $array_answer;
        // Сформированный ответ посылаем клиенту
       echo $answer;
      }
    }
    else
    {
      if (array_key_exists("PosCell", $data_input))
      {
        $data_point = $data;
        $array_answer = FlipToPoints($data_point);
        $answer = json_encode($array_answer);
        $_SESSION['Data'] = $array_answer;
        // Сформированный ответ посылаем клиенту
        echo $answer;
      }
    }
  }


  // Выполним действия с соответствии с полученными входными данными
  /*
  switch ($data["PosCell"]) 
  {
    case "IncRows":
         $RowNew += 1;
         break;
    default:
    break;
  };
  */

  function FlipToPoints($data_point)
  {
    $pointsToFlip = $data_point;
    $width = $_SESSION['width'];
    $height = $_SESSION['height'];
    // Цикл для всех точек
    for ($x = 0; $x < $width; $x++)
    {
      for ($y = 0; $y < $height; $y++)
      {
        $neibours = FindNeighbours($x, $y, $data_point);
        // Применение основных правил игры
        if ($pointsToFlip[$x][$y]["active"] && $neibours < 2) {
          $pointsToFlip[$x][$y]["active"] = false;
        }
        if ($pointsToFlip[$x][$y]["active"] && $neibours > 3)
        {
          $pointsToFlip[$x][$y]["active"] = false;
        }
        if (!$pointsToFlip[$x][$y]["active"] && $neibours == 3)
        {
          $pointsToFlip[$x][$y]["active"] = true;
        }
        if ($pointsToFlip[$x][$y]["active"] && ($neibours == 2  || $neibours == 3))
        {
          $pointsToFlip[$x][$y]["active"] = true;
        }
      }
    }
    return $pointsToFlip;
  }
  
  function FindNeighbours($x, $y, $data_point)
  {
    $neighbours = array();
    $width = $_SESSION['width'];
    $height = $_SESSION['height'];
    // Проверка границ и получение всех 8 ячеек около текущей ячейки
    // ABC
    // DXE
    // FGH
    if ($x >= 1) { //D
      $neighbours = array_merge($neighbours, array(array("x" => $x - 1, "y" => $y))); 
      if ($y <= $height - 2) { //A
        $neighbours = array_merge($neighbours, array(array("x" => $x - 1, "y" => $y + 1)));
      } 
      if ($y >= 1) { //F
        $neighbours = array_merge($neighbours, array(array("x" => $x - 1, "y" => $y - 1)));
      }
    }
    if ($x <= $width - 2) { //E
      $neighbours = array_merge($neighbours, array(array("x" => $x + 1, "y" => $y)));
      if (($x <= $width - 2) && ($y <= $height - 2)) //C
      { 
        $neighbours = array_merge($neighbours, array(array("x" => $x + 1, "y" => $y + 1)));
      }
      if (($x <= $width - 2) && ($y >= 1)) //H
      {
        $neighbours = array_merge($neighbours, array(array("x" => $x + 1, "y" => $y - 1)));
      }
    }
    if ($y >= 1) //G
    { 
      $neighbours = array_merge($neighbours, array(array("x" => $x, "y" => $y - 1)));
    }
    if ($y <= $height - 2) //B
    { 
      $neighbours = array_merge($neighbours, array(array("x" => $x, "y" => $y + 1)));
    }

    $count = 0;
    for ($i = 0; $i < count($neighbours); $i++)
    {
      $x = $neighbours[$i]["x"];
      $y = $neighbours[$i]["y"];

      if (array_key_exists("active", $data_point[$x][$y]))
      {
        $active_true = $data_point[$x][$y]["active"];
      }
      else
      {
        $active_true = false;
      }

      if ($neighbours[$i] != null && $active_true)
      {
        $count++;
      }
    }
    return $count;
  }
  // Закрываем подключение
  mysqli_close($link);
  
?>