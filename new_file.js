if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
};

// ajax запрос к серверу 100500
function createRequest(param){
  
  if (this.name == 'sendcom') {
    if (author.value.trim() == '') {alert('Укажите автора'); return}; 
	if (textcom.value.trim() == '') {alert('Введите текст комментария'); return}; 
    param ='sendcom';
  };

  var params;  
  if (param =='loadpage')  // начальная загрузка страницы
    {params = "author=" + author.value + "&textcom=" + textcom.value + "&param=" + param};
  if (param =='sendcom')  // запрос на добавление комментария
	{params = "author=" + author.value + "&textcom=" + textcom.value + "&param=" + param + "&cap=" + captcha.value};
  if (param[0]=='id')  // запрос на удаление комментария
    {params = "author=" + author.value + "&textcom=" + textcom.value + "&param=" + param[0] + "&id=" + param[1]};
  if (param == 'captcha') {params = "&param=" + param};  // вывод капчи
  
  var request = new XMLHttpRequest();
  var url = "comment.php";

  request.open("POST", url, true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
 
  request.onreadystatechange= function() {
    if (request.readyState === 4 && request.status === 200) {  
	    switch (param) {    // обработка ответа сервера 
          case 'loadpage': // вывод капчи и вставка всех сохраненных комментариев из базы данных 
            {createRequest('captcha'); insertComment(request.responseText, param)};
            break;
          case 'sendcom':  // вставка нового комментария и вывод капчи
            {insertComment(request.responseText, param); createRequest('captcha')}; 
            break;
		  case 'captcha':  // отображение капчи на экране
	        {captchaImage.style.backgroundImage = 'url(1.jpg?'+Math.random()+')'};
		    break;
		};  
    };
  };
  request.send(params);   // запрос к серверу

};
  
// встака комментариев
function insertComment(response, param){
  if (response=='No') {
    alert('Код неправильный'); 
	createRequest('captcha');
	return;
  };
  comment.insertAdjacentHTML('afterEnd', response); // вставка комментария
  if (param=='loadpage') {                                    
    var comments=document.querySelectorAll('.delcomment');  
    for(var i=0; i<comments.length; i++){
      comments[i].onclick=function(){            // добавление события на удаление комментария по кнопке "Удалить"
        if (confirm('Удалить комментарий?')) {   // для всех комментариев загруженных из базы данных   
	      var node=this.parentNode;               
	      node=node.parentNode;
	      createRequest(['id',node.parentNode.id]); 
	      node.parentNode.remove();
		};
	  };  
    };
  };
  if (param=='sendcom') {    
    var lastComment=comment.nextSibling;    
	var node=lastComment.firstElementChild;
	node=node.lastElementChild;
	node=node.firstElementChild;
    node.onclick=function(){      // добавление события на удаление комментария по кнопке "Удалить" для нового комментария
      if (confirm('Удалить комментарий?')) { 	
	    createRequest(['id',lastComment.id]); 
	    lastComment.remove();
      };
	};
    author.value='';                
	textcom.value='';
	captcha.value='';
    };
};

var comment = document.querySelector('#comment');  // блок ввода комментария
var author = document.querySelector('#comment input[name="author"]');  // поле ввода автора
var textcom = document.querySelector('#comment textarea');  //  поле вводе текста комментария
var captcha = document.querySelector('#comment input[name="captcha"]');  // поле ввода проверочного кода
var captchaImage=document.querySelector('#comment div');  // изображение проверочного кода (капча)

createRequest('loadpage');  // ajax запрос при начальной загрузке страницы

var sendcom = document.querySelector('input[name="sendcom"]');
sendcom.onclick = createRequest;

