//*******************************************************
//Block, that contains function to draw letter and save it to server
//*******************************************************
// Variables to keep track of the mouse position and left-button status
//todo: make a db

db_QUIZ=[{word:"COW",lang:"en",img: "cow.jpg"},{word:"ABBA",lang:"en",img: "abba.png"},{word:"LEG",lang:"en",img: "leg.jpg"}];


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
			"stroke-width": 15,
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
	$('#result-image').removeAttr("src");
	
}

//Initiation of the drawing area
function init() {

	//generate Random letter
	//generateLetter();
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

	//init first word 
	showWordByIndex(0);
	$("#db-length").html(db_QUIZ.length);
	
}
function validate_and_save() {
	if ($("#draw-letter-area svg").html() != "") {
		send_to_engine();
	} else {
		alert("You didn't draw anything")
	}
}

//Save svg to image function
function send_to_engine() {
	
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
	var lang=$("#current-word").attr("lang");	

	xhr = new XMLHttpRequest();
	var url = "http://35.187.34.5:5002/api/word";
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json");
	xhr.onreadystatechange = function () { 
		if (xhr.readyState == 4 && xhr.status == 200) {
			var json = JSON.parse(xhr.responseText);
			json=JSON.parse(json);
			console.log("Expected word: "+word+", your word: "+json.word);
			if(word==json.word){
				alert("great job!");
				addUserScore();
				showNextImage();
			}
			else{
			$("#result-image").attr("src","data:image/png;base64,"+json.img);}
		}
	}
	var data = JSON.stringify({"img":dataURL,"word":word, "lang":lang});
	xhr.send(data);
	
}

function showNextImage(){
	//hide the image and show svg
	$("#result-image").hide();
	$("#draw-letter-area svg").show();
	
	clearDrawingArea();
	var index=parseInt($("#current-word").attr("index"));
	showWordByIndex(index+1);
}

function showWordByIndex(index){
	if (db_QUIZ.length>index){
	$("#current-word").attr({"word":db_QUIZ[index].word, "lang": db_QUIZ[index].lang, "index":index});
	$("#current-word img").attr("src","images/"+db_QUIZ[index].img);
}
	else {
		alert("no more words!");
		$("#nextbutton").attr("disabled","disabled");

	}
}

function addUserScore(){
	var curscore=parseInt($("#user-score").html());
	$("#user-score").html(curscore+1);
}

// function enableControls(){
// 	$("#submitbutton").removeAttr("disabled");
// 	$("#clearbutton").removeAttr("disabled");
// 	$("#finishbutton").removeAttr("disabled");
// }

//*******************************************************
//Block that contains functions to validate and send user data
//*******************************************************



