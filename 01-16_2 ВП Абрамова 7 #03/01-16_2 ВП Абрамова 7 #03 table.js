$.ajaxSetup(
  { async: true,
    timeout: 10000,
    type: "POST"
  }
);

// Выведем таблицу в первоначальном виде
var SendData = {
                 "Command": "Init"
               };
SendData = JSON.stringify(SendData);
$.post("handler.php", SendData, WhatDo);


// Будем перехватывать нажатия клавиш
addEventListener("keydown", EventHandler);

// Обработка нажатий клавиш
function EventHandler(oEvent) {
  var Command = "None";
  switch (oEvent.key) 
  {
    case "ArrowDown":
         Command = "IncRows";
         break;
    case "ArrowUp":
         Command = "DecRows";
         break;
    case "ArrowLeft":
         Command = "DecCols";
         break;
    case "ArrowRight":
         Command = "IncCols";
         break;
  };

  // Посылаем команду на сервер
  var SendData = {
                   "Command": Command
                 };
  SendData = JSON.stringify(SendData);
  $.post("handler.php", SendData, WhatDo);
};

// Обработка ответа от сервера
function WhatDo(ServerAnswer, RequestStatus) {
  //console.log(ServerAnswer);
  $("#idTable").html(ServerAnswer);
};


function Func4ButtonAdd() {
  console.log("Нажата Add");
};
