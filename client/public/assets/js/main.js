var SQUARE_SIZE = 0;
$(document).ready(function() {

	SQUARE_SIZE = $('#board .square').eq(0).width();

	$('#board').click(function() {
		$('.line.hover').removeClass('hover').addClass('filled');
	});

	$('#board .square').mousemove(function(event) {
		var square = $(event.currentTarget);
		var point = { x : event.offsetX, y : event.offsetY };

		var row = parseInt(square.data('row'));
		var column = parseInt(square.data('column'));

		$('.line.hover').removeClass('hover');

		if (pointInTriangle(point, {x:0,y:0}, {x:60,y:0}, {x:30,y:30})) {					
			if (row > 1) {
				$('#square-' + (row - 1) + '-' + column).parent().find('.bottom-line').addClass('hover');
			} else {
				square.parent().find('.top-line').addClass('hover');
			}
		} else if(pointInTriangle(point, {x:0,y:60}, {x:60,y:60}, {x:30,y:30})) {
			square.parent().find('.bottom-line').addClass('hover');
		} else if(pointInTriangle(point, {x:0,y:0}, {x:0,y:60}, {x:30,y:30})) {						
			if (column > 1) {
				$('#square-' + row + '-' + (column - 1)).parent().find('.right-line').addClass('hover');
			} else {
				square.parent().find('.left-line').addClass('hover');
			}
		} else if(pointInTriangle(point, {x:60,y:0}, {x:60,y:60}, {x:30,y:30})) {
			square.parent().find('.right-line').addClass('hover');
		}

	});
});

function pointInTriangle(point, v1, v2, v3) {
    var b1 = signTriangle(point, v1, v2) < 0;
    var b2 = signTriangle(point, v2, v3) < 0;
    var b3 = signTriangle(point, v3, v1) < 0;

	return ((b1 == b2) && (b2 == b3));
}

function signTriangle(p1, p2, p3) {
	return (p1.x - p3.x) * (p2.y - p3.y) - (p2.x - p3.x) * (p1.y - p3.y);   
}

var Server = new function() {
    this.socket = null;
    
    var $this = this;
    
    this.initialize = function() {
        
        if ($this.isConnected()) {
            return false;
        }
        
        this.socket = new WebSocket("ws://localhost:8080/");
        
        this.socket.onopen = function() {
            console.log('socket opened');
        };
        
        this.socket.onclose = function(event) {
            console.log('socket closed');
        };
        this.socket.onerror = function (error) {
            console.log(error);
        };
        this.socket.onmessage = function(event) {
            var messageData = JSON.parse(event.data);
            console.log(messageData);
        };
        
        return true;
    };
    
    this.isConnected = function() {
        return (this.socket != null && this.socket.readyState == 1);
    };
    
    this.close = function() {
        this.socket.close();
    }
    
    this.send = function(type, params) {
        this.socket.send(JSON.stringify({'type':type, 'params':params}));
    };
}; 