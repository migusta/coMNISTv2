//*******************************************************
//Block, that contains function to draw letter and save it to server
//*******************************************************
// Variables to keep track of the mouse position and left-button status
//todo: make a db

// db_QUIZ=[{word_ru:"КОРОВА",word_en:"COW",img: "cow.jpg"},
// 		 {word_ru:"АББА",word_en:"ABBA",img: "abba.png"},
// 		 {word_ru:"НОГА",word_en:"LEG",img: "leg.jpg"},
// 		 {word_ru:"КОТ",word_en:"CAT",img: "cat.jpg"}];

var db_QUIZ=[];

function initQuiz(lessonid){
	//console.log(lessonid);
	$.ajax({
		type: "POST",
		url: "getwords.php?lessonid="+lessonid
	}).done(function (o) {
		db_QUIZ=JSON.parse(o);
		console.log(db_QUIZ);	
		// init first random word 
		var index=getRandomInt(0,db_QUIZ.length);
		showWordByIndex(index);
	//	$("#db-length").html(db_QUIZ.length);	
		$("#db-length").html(3);	
	});
}
// Возвращает случайное целое число между min (включительно) и max (не включая max)
function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
}

var mouseX, mouseY, mouseDown = 0;
// Variables to keep track of the touch position
var touchX, touchY;
// Variable storing the current letter to draw
var current_letter;
//draw path
function draw(x, y, fMove) {
	if (!fMove) {
		//$(svg).append($("<path id='cur_path'></path>"));
		$(svg).append(document.createElementNS("http://www.w3.org/2000/svg", "path"));
		path = $(svg).find("path:last-child");
		path.attr({
			"d": "M" + x + "," + y + "L" + x + "," + y,
			"stroke-width": 12,
			"fill": "transparent",
			"stroke": "black",
			"stroke-linecap": "round"
		});
	} else {
		path = $(svg).find("path:last-child");
		path.attr("d", path.attr("d") + "L" + x + "," + y);
	}
	//console.log(x+" "+y)
}
//get mouse position
function getMousePos(e) {

	if (!e)
		var e = event;
	if (e.offsetX) {
		mouseX = e.offsetX;
		mouseY = e.offsetY;
	} else if (e.layerX) {
		mouseX = e.layerX;
		mouseY = e.layerY;
	}

}

// Keep track of the mouse button being pressed and draw a dot at current location
function svg_mouseDown() {
	mouseDown = 1;
	draw(mouseX, mouseY);
}
// Keep track of the mouse button being released
function svg_mouseUp() {
	mouseDown = 0;
}
// Keep track of the mouse position and draw a dot if mouse button is currently pressed
function svg_mouseMove(e) {
	// Update the mouse co-ordinates when moved
	getMousePos(e);
	// Draw a dot if the mouse button is currently being pressed
	if (mouseDown == 1) {
		draw(mouseX, mouseY, true);
	}
}

// Get the touch position relative to the top-left of the svg
function getTouchPos(e) {
	if (!e)
		var e = event;
	if (e.originalEvent.touches) {
		if (e.originalEvent.touches.length == 1) { // Only deal with one finger
			var touch = e.originalEvent.touches[0]; // Get the information for finger #1
			touchX = touch.pageX - $("#draw-letter-area").offset().left; //-touch.target.offsetLeft;
			touchY = touch.pageY - $("#draw-letter-area").offset().top; //-touch.target.offsetTop;
		}
	}
}
function clearDrawingArea(){

	$('#draw-letter-area svg').empty();
	$('#result-image').removeAttr("src").hide();
	
}

//Initiation of the drawing area
function init(lessonid) {

	initQuiz(lessonid);
	//generate Random letter
	//generateLetter();
	initSvg();	
}

function initSvg(){
	// Get the specific canvas element from the HTML document
	svg = $("#draw-letter-area svg");

	// Check that we have a valid context to draw on/with before adding event handlers
	if (svg) {

		window.addEventListener('mouseup', svg_mouseUp, false);

		// React to touch events on the svg
		svg.bind('mousemove', function (e) {
			getMousePos(e);
			// Draw a dot if the mouse button is currently being pressed
			if (mouseDown == 1) {
				draw(mouseX, mouseY, true);
			}
		});

		svg.bind('mouseup', function (e) {
			mouseDown = 0;
		});

		svg.bind('mousedown', function (e) {
			mouseDown = 1;
			draw(mouseX, mouseY);

		});

		svg.bind('touchstart', function (e) {
			getTouchPos(e);
			draw(touchX, touchY);
			// Prevents an additional mousedown event being triggered
			event.preventDefault();

		});

		svg.bind('touchmove', function (e) {
			// Update the touch co-ordinates
			getTouchPos(e);
			// During a touchmove event, unlike a mousemove event, we don't need to check if the touch is engaged, since there will always be contact with the screen by definition.
			draw(touchX, touchY, true);
			// Prevent a scrolling action as a result of this touchmove triggering.
			event.preventDefault();
		});
	}
}
function validate_and_save(isPractice,actionid) {
	if ($("#draw-letter-area svg").html() != "") {
		send_to_engine(isPractice,actionid);
	} else {
		alert("You didn't draw anything")
	}
}


//Save svg to image function
function send_to_engine(isPractice,actionid) {
	
	//disable buttons to prevent user double click

	console.log("Send Image Event Begin");
	//get the svg
	var svg = $.trim(document.getElementById("draw-letter-area").innerHTML);

	//check if there is canvas
	var canvas = document.getElementById("canvas");

	if (canvas == null || canvas == undefined) {
		//create canvas
		var canvas = document.createElement('canvas');

		canvas.id = "canvas";
		canvas.height = "240"; //todo: get svg params
		canvas.width = "600";

		document.body.appendChild(canvas);
	}

	//Load the canvas element with our svg
	canvg(document.getElementById('canvas'), svg);

	var dataURL = canvas.toDataURL();
	var word=$("#current-word").attr("word");
	var lang=$("#quiz-lang option:selected").val();	

	xhr = new XMLHttpRequest();
	var url = "http://35.187.34.5:5002/api/word";
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json");
	xhr.onreadystatechange = function () { 
		if (xhr.readyState == 4 && xhr.status == 200) {
			var json = JSON.parse(xhr.responseText);
			json=JSON.parse(json);
			console.log("Expected word: "+word+", your word: "+json.word);
			//if(word==json.word){
				
			if(json.correct==1){
				$(".success").show().delay(500).fadeOut();
				if (isPractice==true){
					addUserScore();
					showNextImage();
				}
				else{
					redirectToPractice(actionid);
				}
			}
			else{
			ClearScore();
			$("#result-image").attr("src","data:image/png;base64,"+json.img);
			//hide the svg and show img
			$("#retrybutton").show();
			$("#submitbutton,#clearbutton").hide();
			
			$("#result-image").show();
			$("#draw-letter-area svg").hide();
		}
		}
	}
	var data = JSON.stringify({"img":dataURL,"word":word, "lang":lang, "nb_output":2});
	xhr.send(data);
	
}
function redirectToPractice(actionid){
	$.ajax({
		type: "POST",
		url: "finishlesson.php",
		data: {actionid: actionid}
	}).done(function (o) {
		// console.log(o);
		document.location='practice.php?lessonid='+o;
	
	});
	//document.location="practice.php?lessonid="+lessonid;
}

function enableDrawingControls(){
	$("#draw-letter-area svg").show();
	$("#retrybutton").hide();
	$("#submitbutton,#clearbutton").show();
}
function retryDrawing(){
	clearDrawingArea();
	enableDrawingControls();
}
function showNextImage(){
	clearDrawingArea();
	enableDrawingControls();
	
	// var index=parseInt($("#current-word").attr("index"));
	// showWordByIndex(index+1);
	showRandomWord();
}


function showRandomWord(){
	var index=getRandomInt(0,db_QUIZ.length);
	var lang=$("#quiz-lang option:selected").val();	
	if (db_QUIZ.length>index){
		switch(lang){
			case "ru": $("#current-word").attr({"word":db_QUIZ[index].word_ru});break;
			case "en": $("#current-word").attr({"word":db_QUIZ[index].word_en});break;
		}
		$("#current-word img").attr("src","images/"+db_QUIZ[index].img);	
	}
	if($("#hid-score").val()==3) {
		finishPractice();
	}
}

function showWordByIndex(index){
	var lang=$("#quiz-lang option:selected").val();	
	if (db_QUIZ.length>index){
		switch(lang){
			case "ru": $("#current-word").attr({"word":db_QUIZ[index].word_ru, "index":index});break;
			case "en": $("#current-word").attr({"word":db_QUIZ[index].word_en, "index":index});break;
		}
		$("#current-word img").attr("src","images/"+db_QUIZ[index].img);	
	}
	//else {
	if (db_QUIZ.length==index){
		//$("#draw-container").hide();
		// $("#nextbutton").attr("disabled","disabled").hide();		 
		 
		if($("#hid-score").val()<3) {initQuiz();
		}
		else {
			finishPractice();
		}
	}
	// if (db_QUIZ.length==index){
	// 	$("#draw-container").hide();
	// }
}
function finishPractice(){

	$("#draw-container").hide();
	$("#fin_practice").show();
	
	$.ajax({
		type: "POST",
		url: "finishpractice.php",
		data: {actionid: $("#hid-action").val(),
			 score: $("#hid-score").val()}
	}).done(function (o) {
		console.log(o);
		document.location='lesson.php?lessonid='+o;
	
	});

}
function ClearScore(){
	$("#user-score").html(0);
	$("#hid-score").val(0);
}
function addUserScore(){
	var curscore=parseInt($("#user-score").html());
	$("#user-score").html(curscore+1);
	$("#hid-score").val(curscore+1);
	
}
function activateTab(sClass){
	$(".nav-tabs li.active").removeClass("active");		
	$(".nav-tabs a").each(function(){
		if($(this).text().toLowerCase()==sClass) 
			$(this).parent().addClass("active");}
	);

	
	if(sClass=="all") $(".lesson").show();
	else {
			$(".lesson").hide();
			$("."+sClass).show();
		}
}


function changeLanguage(lang)
{
	var index=parseInt($("#current-word").attr("index"));
	switch(lang){
			case "ru": $("#current-word").attr({"word":db_QUIZ[index].word_ru});break;
			case "en": $("#current-word").attr({"word":db_QUIZ[index].word_en});break;
		}
}
