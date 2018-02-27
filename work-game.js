var game = new Game();
$(function() {
  GameInit();
});

var GameInit = function() {
  var $ = jQuery;

  this.record = function() {
    var content = "";
    for (var w = 0; w < 64; w++)
    {
      content += '<tr>';
      for (var h = 0; h < 64; h++)
      {
        content += '<td></td>';
      }
      content += '</tr>';
    }
    $('#game-grid').append(content);
    game.init($('#game-grid')[0]);

    $(game.gridNode).find('td').click(function() {
      var coord = {
        x: $(this)[0].parentNode.rowIndex,
        y: $(this)[0].cellIndex,
      };
      // Действие с точкой
      if (!game.points[coord.x][coord.y].active) {
        $(this).css("background", "#333333");
        game.enablePoint(coord.x, coord.y);
      }
      else
      {
        $(this).css("background", "#cccccc");
        game.disablePoint(coord.x, coord.y);
      }
    });
  }
};

//Отправка данных на сервер
$.ajaxSetup(
  { async: true,
    timeout: 10000,
    type: "POST"
  }
);

$.ajax({ 
  type: 'POST', 
  url: 'handler.php',
  data: '{ "GetGame": "" }',
  success: function(json_string) {    //здесь в json_string мы получаем именно строку со структурой в формате JSON (это ещё не объект)
    var records = JSON.parse(json_string);    //парсим строку и получаем объект
    $.each(records, function (key, val) {
      $('#ul').append($('<li class="item">')
        .text('Наименование: ' + val.name_session)
        .append($('<strong>')
        .text(' Дата: ' + val.time)));
    });
    $('body').append('<div id="blackout"></div>');

    $('li').click(function() {
      var hack = true;
      var scrollPos = $(window).scrollTop();
      /* Скрыть окно когда кликнут по элементу списка. */
      $('[id^=popup-box-]').hide();
      $('#blackout').hide();
      $('html,body').css("overflow", "auto");
      $('html').scrollTop(scrollPos);
      //Для передачи данных в теги h2 и h3
      var index = $('li').index(this);
      $('h2').text('Наименование игры: ' + records[index].name_session);
      $('h3').text('Время: ' + records[index].time);

      SendData = { "GetContent": records[index].name_session };
      SendData = JSON.stringify(SendData);
      $.post("handler.php", SendData, ViewPoints);

      function ViewPoints(ServerAnswer, RequestStatus) {
        var points = JSON.parse(ServerAnswer);
        for (var x = 0; x < 64; x++)
        {
          for (var y = 0; y < 64; y++)
          {
            var color = points[x][y].active;
            $('#game-grid')[0].rows[x].cells[y].style.background = (color == true) ? "#333333" : "#cccccc";
            if (color)
            {
              game.enablePoint(x, y);
            }
          }
        } 
      };
    });
  }
});

var SendData;
// Окно для выбора сохраненной игры из списка
$(document).ready(function() {

  //Первоначальная инициализация данных для сервера
  $('body').append('<div class="popup-box" id="popup-box-1"><div class="top"><h2>Список игр. Игрока с ID:45673</h2></div><div class="bottom"><ul id="ul"></ul></div><button class="new-game">Новая игра</button></div>');
    
$('body').append('<div id="blackout"></div>');
var boxWidth = 250;
function centerBox() {
  var winWidth = $(window).width();
  var winHeight = $(document).height();
  var scrollPos = $(window).scrollTop();

  // Вычисление позиций
  var disWidth = (winWidth - boxWidth) / 2;
  var disHeight = scrollPos + 150;

  // Задание css стилей
  $('.popup-box').css({'width' : boxWidth+'px', 'left' : disWidth+'px', 'top' : disHeight+'px'});
  $('#blackout').css({'width' : winWidth+'px', 'height' : winHeight+'px'});
  return false;
};

$(window).resize(centerBox);
$(window).scroll(centerBox);
centerBox();

  $(document).ready(function() {
    record();

    /* Get the id (the number appended to the end of the classes) */
    var name = $(this).attr('class');
  // var id = name[name.length - 1];
    var id = 1;
    var scrollPos = $(window).scrollTop();

    // /* Show the correct popup box, show the blackout and disable scrolling */
    // if (records != null)
    // {
    //   $('#popup-box-1').show();
    //   $('#blackout').show();
    //   $('html,body').css('overflow', 'hidden');
    // }

    /* Fixes a bug in Firefox */
    $('html').scrollTop(scrollPos);
  });

  $('#gameSave').click(function() {
    var nameGame = prompt('Название игры', '');
    var date = new Date();
    $('h2').text('Наименование игры: ' + nameGame);
    $('h3').text('Время: ' + date.getHours()+':'+date.getMinutes()+':'+date.getSeconds());

    SendData = { "NewGame" : nameGame, "NewPos" : game.points}; 
    SendData = JSON.stringify(SendData);
    $.post("handler.php", SendData, alert("Игра сохранена!"));
  });         
  $('.new-game').click(function() {
    var scrollPos = $(window).scrollTop();
    $('[id^=popup-box-]').hide();
    $('#blackout').hide();
    $("html,body").css("overflow", "auto");
    $('html').scrollTop(scrollPos);

    $('h2').text('Наименование игры: ' + newGame);
    var date = new Date();
    $('h3').text('Время: ' + date.getHours()+':'+date.getMinutes()+':'+date.getSeconds());
  });

  var newGame = true;
//Обработка клика по кнопке "Сделать ход"
$('#gameGo').click(function() {
  
  if (newGame == true)
  {
    newGame = false;
    SendData = { "PosCell" : game.points };
  }
  else
  {
    SendData = { "PosCell" : ""};
  }
  
  SendData = JSON.stringify(SendData);
  $.post("handler.php", SendData, WhatDo);
});

function WhatDo(ServerAnswer, RequestStatus) {
  var flipToPoints = JSON.parse(ServerAnswer);
  for (var x = 0; x < 64; x++)
  {
    for (var y = 0; y < 64; y++)
    {
      $('#game-grid')[0].rows[x].cells[y].style.background = "#cccccc";
    }
  }
  for (var x = 0; x < 64; x++)
  {
    for (var y = 0; y < 64; y++)
    {
      var color = flipToPoints[x][y].active;
      $('#game-grid')[0].rows[x].cells[y].style.background = (color == true) ? "#666" : "#cccccc";
    }
  } 
  console.log(flipToPoints);
  };
});