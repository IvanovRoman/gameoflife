$.ajaxSetup(
  { async: true,
    timeout: 10000,
    type: "POST"
  }
);

// ����� ������������� ������� ������
addEventListener("keydown", EventHandler);

// ��������� ������� ������
function EventHandler(oEvent) {
  var Command = "None";
  switch (oEvent.key) 
  {
    case "ArrowDown":
         Command = "DecRows";
         break;
    case "ArrowUp":
         Command = "IncRows";
         break;
    case "ArrowLeft":
         Command = "DecCols";
         break;
    case "ArrowRight":
         Command = "IncCols";
         break;
  };

  // �������� ������� �� ������
  var SendData = {
                   "Command": Command
                 };
  SendData = JSON.stringify(SendData);
  $.post("handler.php", SendData, WhatDo);
};

// ��������� ������ �� �������
function WhatDo(ServerAnswer, RequestStatus) {
  $("#idTable").html(ServerAnswer);
};
