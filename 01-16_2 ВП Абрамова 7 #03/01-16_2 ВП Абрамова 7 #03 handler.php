<?
  require_once "classTable.php";
  require_once "connection.php";

  $oTable = new Table();


  // подключаемся к серверу
  $link = mysqli_connect($host, $user, $password, $database) 
	or exit("Ошибка " . mysqli_error($link));

  // выполняем операции с базой данных

  $query ="SELECT * FROM config";

  $result = mysqli_query($link, $query) or exit("Ошибка " . mysqli_error($link)); 
  if ($result)
  {
     $rows = mysqli_num_rows($result); // количество полученных строк
     if ($rows>0)
     {
       while ($row = mysqli_fetch_assoc($result)) 
       {
         $oTable->nRows=$row["rows"];
         $oTable->nCols=$row["cols"];
       }
     } 
     // очищаем результат
     mysqli_free_result($result);
  }
  else
  {
     echo "Нет подключения";
  };


  // Прочитаем полученные входные данные
  $data = json_decode(file_get_contents('php://input'), true);

  // Выполним действия с соответствии с полученными входными данными
  if ($data["Command"] !== "Init")
  {
    switch ($data["Command"]) 
    {
      case "IncRows":
           $oTable->setnRows($oTable->nRows+1);
           break;
      case "DecRows":
           $oTable->setnRows($oTable->nRows-1);
           break;
      case "IncCols":
           $oTable->setnCols($oTable->nCols+1);
           break;
      case "DecCols":
           $oTable->setnCols($oTable->nCols-1);
           break;
    };
    // Занесем новые данные в таблицу
    $query ="UPDATE config SET rows='$oTable->nRows', cols='$oTable->nCols'";
    $result = mysqli_query($link, $query) or exit("Ошибка " . mysqli_error($link)); 
  }

  // закрываем подключение
  mysqli_close($link);


  // Формируем ответ клиенту
  $answer = $oTable->getHTML();

  // Сформированный ответ посылаем клиенту
  echo $answer;

?>