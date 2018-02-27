<?
 
  // Таблица
  class Table
  {
    // Свойства класса 
    /*
       Обратите внимание:
         Некоторые свойства объявлены public, а некоторые - private. Почему? 
    */

    // Кол-во строк в таблице по умолчанию
    public $nRows = 3;
    // Максимальное кол-во строк в таблице
    private $nRowsMax = 30;
    // Минимальное кол-во строк в таблице
    private $nRowsMin = 2;

    // Кол-во столбцов в таблице по умолчанию
    public $nCols = 5;
    // Максимальное кол-во столбцов в таблице
    private $nColsMax = 70;
    // Минимальное кол-во столбцов в таблице
    private $nColsMin = 2;

    // Параметры оформления таблицы
    public $bgcolor="#00ffff";
    public $border="1";
    public $bordercolor="#000000";
    public $cellspacing="0";


    public function setnRows($nNewRows) 
    // Установить кол-во строк в таблице
    /* 
       Параметры:
          $nNewRows  - новое кол-во строк в таблице (разрешенный диапазон = $this->nRowsMin..$this->nRowsMax)
                       При выходе значения за разрешенный диапазон оно приравнивается к ближайшей границе диапазона.
    */
    {
      // Ограничим диапазон изменения строк
      /*
         Обратите внимание: 
           - обращения к свойствам идет без знака $ впереди
           - switch используется нестандартным образом
      */
      switch ($nNewRows)
      {
        case ($nNewRows > $this->nRowsMax):
             $this->nRows = $this->nRowsMax;
             break;
        case ($nNewRows < $this->nRowsMin):
             $this->nRows = $this->nRowsMin;
             break;
        default:
             $this->nRows = $nNewRows;
      }
    }


    public function setnCols($nNewCols) 
    // Установить кол-во столбцов в таблице
    /* 
       Параметры:
          $nNewCols  - новое кол-во столбцов в таблице (разрешенный диапазон = $this->nColsMin..$this->nColsMax)
                       При выходе значения за разрешенный диапазон оно приравнивается к ближайшей границе диапазона.
    */
    {
      // Ограничим диапазон изменения столбцов
      switch ($nNewCols)
      {
        case ($nNewCols > $this->nColsMax):
             $this->nCols = $this->nColsMax;
             break;
        case ($nNewCols < $this->nColsMin):
             $this->nCols = $this->nColsMin;
             break;
        default:
             $this->nCols = $nNewCols;
      }
    }


    // Получить HTML-код таблицы
    public function getHTML() 
    {
      $result = '<table bgcolor="'.$this->bgcolor.'" border="'.$this->border.'" bordercolor="'.$this->bordercolor.'" cellspacing="'.$this->cellspacing.'">';
     
      // Для всех строк
      for ($row=1; $row <= $this->nRows; $row++) 
      {
        $result .= "<tr>";
        // Для всех столбцов внутри текущей строки
        for ($col=1; $col <= $this->nCols; $col++) 
        {
          $result .= "<td>Х</td>";
        };
        $result .= "</tr>";
      };
      $result .= "</table>";
      return $result;
    }
  }


?>