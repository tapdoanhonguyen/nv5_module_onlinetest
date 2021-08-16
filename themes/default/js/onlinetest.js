(function($){

  var timer;

  var Timer = function(targetElement){
    this.targetElement = targetElement;
    return this;
  };

  Timer.start = function(options, targetElement){
    timer = new Timer(targetElement);
    return timer.start(options);
  };

  Timer.prototype.start = function(options) {

    var createSubDivs = function(timerBoxElement){
      var seconds = document.createElement('span');
      seconds.className = 'seconds';

      var minutes = document.createElement('span');
      minutes.className = 'minutes';

      // var hours = document.createElement('span');
      // hours.className = 'hours';

      var clearDiv = document.createElement('div');
      clearDiv.className = 'clearDiv';

      return timerBoxElement.
        // append(hours).
        append(minutes).
        append(seconds).
        append(clearDiv);
    };

    this.targetElement.each(function(_index, timerBox) {
      var timerBoxElement = $(timerBox);
      var cssClassSnapshot = timerBoxElement.attr('class');

      timerBoxElement.on('complete', function() {
        clearInterval(timerBoxElement.intervalId);
      });

      timerBoxElement.on('complete', function() {
        timerBoxElement.onComplete(timerBoxElement);
      });

      timerBoxElement.on('complete', function(){
        timerBoxElement.addClass('timeout');
      });

      timerBoxElement.on('complete', function(){
        if(options && options.loop === true) {
          timer.resetTimer(timerBoxElement, options, cssClassSnapshot);
        }
      });

      createSubDivs(timerBoxElement);
      return this.startCountdown(timerBoxElement, options);
    }.bind(this));
  };

  /**
   * Resets timer and add css class 'loop' to indicate the timer is in a loop.
   * $timerBox {jQuery object} - The timer element
   * options {object} - The options for the timer
   * css - The original css of the element
   */
  Timer.prototype.resetTimer = function($timerBox, options, css) {
    var interval = 0;
    if(options.loopInterval) {
      interval = parseInt(options.loopInterval, 10) * 1000;
    }
    setTimeout(function() {
      $timerBox.trigger('reset');
      $timerBox.attr('class', css + ' loop');
      timer.startCountdown($timerBox, options);
    }, interval);
  }

  Timer.prototype.fetchSecondsLeft = function(element){
    var secondsLeft = element.data('seconds-left');
    var minutesLeft = element.data('minutes-left');

    if(secondsLeft){
      return parseInt(secondsLeft, 10);
    } else if(minutesLeft) {
      return parseFloat(minutesLeft) * 60;
    }else {
      throw 'Missing time data';
    }
  };

  Timer.prototype.startCountdown = function(element, options) {
    options = options || {};

    var intervalId = null;
    var defaultComplete = function(){
      clearInterval(intervalId);
      return this.clearTimer(element);
    }.bind(this);

    element.onComplete = options.onComplete || defaultComplete;

    var secondsLeft = this.fetchSecondsLeft(element);

    var refreshRate = options.refreshRate || 1000;
    var endTime = secondsLeft + this.currentTime();
    var timeLeft = endTime - this.currentTime();

    this.setFinalValue(this.formatTimeLeft(timeLeft), element);

    window.myTimer = intervalId = setInterval((function() {
      timeLeft = endTime - this.currentTime();
      this.setFinalValue(this.formatTimeLeft(timeLeft), element);
	  element.attr('data-timeout', timeLeft);
    }.bind(this)), refreshRate);

    element.intervalId = intervalId;
  };

  Timer.prototype.clearTimer = function(element){
    element.find('.seconds').text('00');
    element.find('.minutes').text('00:');
    element.find('.hours').text('00:');
  };

  Timer.prototype.currentTime = function() {
    return Math.round((new Date()).getTime() / 1000);
  };

  Timer.prototype.formatTimeLeft = function(timeLeft) {

    var lpad = function(n, width) {
      width = width || 2;
      n = n + '';

      var padded = null;

      if (n.length >= width) {
        padded = n;
      } else {
        padded = Array(width - n.length + 1).join(0) + n;
      }

      return padded;
    };

    var hours, minutes, remaining, seconds;
    remaining = new Date(timeLeft * 1000);
    hours = remaining.getUTCHours();
    minutes = remaining.getUTCMinutes() + ( hours * 60 );
    seconds = remaining.getUTCSeconds();
    if (+hours === 0 && +minutes === 0 && +seconds === 0) {
      return [];
    } else {
      return [lpad(hours), lpad(minutes), lpad(seconds)];
    }
  };

  Timer.prototype.setFinalValue = function(finalValues, element) {

    if(finalValues.length === 0){
      this.clearTimer(element);
      element.trigger('complete');
      return false;
    }

    element.find('.seconds').text(finalValues.pop());
    element.find('.minutes').text(finalValues.pop() + ':');
    element.find('.hours').text(finalValues.pop() + ':');
  };


  $.fn.startTimer = function(options) {
    Timer.start(options, this);
    return this;
  };
})(jQuery);

(function($) {
	function Autofill(element, options) {
		this.element = element;
		this.options = options;
		this.timer = null;
		this.items = new Array();

		$(element).attr('autocomplete', 'off');
		$(element).on('focus', $.proxy(this.focus, this));
		$(element).on('blur', $.proxy(this.blur, this));
		$(element).on('keydown', $.proxy(this.keydown, this));

		$(element).after('<ul class="dropdown-menu template scrollable-menu" role="menu"></ul>');
		$(element).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));
	}

	Autofill.prototype = {
		focus: function() {
			this.request();
			 
		},
		blur: function() {
			setTimeout(function(object) {
				object.hide();
			}, 200, this);
		},
		click: function(event) {
			event.preventDefault();
			console.log(3);
			value = $(event.target).parent().attr('data-value');

			if (value && this.items[value]) {
				this.options.select(this.items[value]);
			}
			this.hide();
			
		},
		keydown: function(event) {
 
			switch(event.keyCode) {
				case 27: // escape
					this.hide();
					break;
				case 188: // comma
					break;
				default:
					this.request();
					break;
			}
		},
		show: function() {
 
			var pos = $(this.element).position();

			$(this.element).siblings('ul.dropdown-menu').css({
				top: pos.top + $(this.element).outerHeight(),
				left: pos.left
			});

			$(this.element).siblings('ul.dropdown-menu').show();
		},
		hide: function() {
 
			$(this.element).siblings('ul.dropdown-menu').hide();
		},
		request: function() {
 
			clearTimeout(this.timer);

			this.timer = setTimeout(function(object) {
				object.options.source($(object.element).val(), $.proxy(object.response, object));
			}, 200, this);
		},
		response: function(json) {
	 
			html = '';
			if ( json.length ) {
				for (i = 0; i < json.length; i++) {
					this.items[json[i]['value']] = json[i];
				}

				for (i = 0; i < json.length; i++) {
					if (!json[i]['category']) {	
						//var content = json[i]['label'].replace(new RegExp(this.element.value, "gi"), '<strong>$1</strong>');	
						html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a>';
						if( json[i]['level_id'] != undefined )
						{
							html += '<span class="level' + json[i]['level_id'] + '">' + json[i]['level'] + '</span>';
						}
						html += '</li>';
					}
				}

				// Get all the ones with a categories
				var category = new Array();

				for (i = 0; i < json.length; i++) {
					if (json[i]['category']) {
						if (!category[json[i]['category']]) {
							category[json[i]['category']] = new Array();
							category[json[i]['category']]['name'] = json[i]['category'];
							category[json[i]['category']]['item'] = new Array();
						}

						category[json[i]['category']]['item'].push(json[i]);
					}
				}

				for (i in category) {
					html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

					for (j = 0; j < category[i]['item'].length; j++) {
						html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
					}
				}
				 
			}

			if (html) {
				this.show();
			} else {
				this.hide();
			}

			$(this.element).siblings('ul.dropdown-menu').html(html);
		}
	};

	$.fn.autofill = function(option) {
		return this.each(function() {
			var data = $(this).data('autofill');

			if (!data) {
				data = new  Autofill(this, option);

				$(this).data('autofill', data);
			}
		});
	}
})(window.jQuery);

function getData(b){var c={},d=/^data\-(.+)$/;$.each(b.get(0).attributes,function(b,a){if(d.test(a.nodeName)){var e=a.nodeName.match(d)[1];c[e]=a.nodeValue}});return c};

function createEditor(element) {
	CKEDITOR.replace( element, {
		width: '100%',
		height: '100px',
		toolbarGroups:[
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
			{ name: 'forms', groups: [ 'forms' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'links', groups: [ 'links' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'paragraph' ] },
			{ name: 'insert', groups: [ 'insert' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
			{ name: 'styles', groups: [ 'styles' ] },
			{ name: 'colors', groups: [ 'colors' ] },
			{ name: 'tools', groups: [ 'tools' ] },
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'others', groups: [ 'others' ] },
			{ name: 'about', groups: [ 'about' ] }
		],
		removePlugins: 'autosave,gg,switchbar',
		removeButtons: 'Templates,Googledocs,Sourse,NewPage,Preview,Print,Save,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Blockquote,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,About,Anchor,BidiRtl,CreateDiv,Indent,BulletedList,NumberedList,Outdent,ShowBlocks,Youtube,Video' 
			
	});
	CKEDITOR.add;
	
	
}
  
function strip_tags(input, allowed) {
  
  allowed = (((allowed || '') + '')
    .toLowerCase()
    .match(/<[a-z][a-z0-9]*>/g) || [])
    .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '')
    .replace(tags, function($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}
  
$.fn.centerDiv  = function() {
 
    this.css({
        'position': 'absolute',
        'left': '50%',
        'top': '50%'
    });
    this.css({
        'margin-left': -this.outerWidth() / 2 + 'px',
        'margin-top':  -this.outerHeight() / 2 + 'px'
    });

    return this;
}

$.fn.center  = function() {
 
    this.css({
        'position': 'absolute',
        'left': '50%',
        'top': '50%'
    });
    this.css({
        'margin-left': -this.outerWidth() / 2 + 'px',
        'margin-top': -( $(window).height() / 2 + 100 ) + 'px'
    });

    return this;
}

function showQuestion(question_id) {
   $('.question-item').show();
   if($(window).width() < 767){
	   $('.question-item').hide();
	   $('.question-item').css('position','relative');
	   $('#'+question_id).show();
	   $('#'+question_id).css('position','relative');
	   $('html,body').animate({
		   scrollTop: $('.answer_solution').offset().top
	   }, 500);
   }else {
	   $('.question-item').show();
	   $('html,body').animate({
		   scrollTop: $('#'+question_id).offset().top - 40
	   },500);
   }

}

function scrollDiv()
{
	var $sticky = $('.sticky');
	if (!!$sticky.offset()) {  
		var stickOffset = 0;		

		var wbox = $('#scrolldiv').width();
		var stickyTop = $sticky.offset().top;

		$(window).scroll(function(){ 
			var generalSidebarHeight = $sticky.innerHeight();

			var $stickyrStopper = $('.sticky-stopper');
			var stickyStopperPosition = $stickyrStopper.offset().top;
			var stopPoint = stickyStopperPosition - generalSidebarHeight - stickOffset;
			
			var diff = stopPoint - $('#scrolldiv').offset().top + 10;// 10px padding //
			var windowTop = $(window).scrollTop(); // returns number
			if (stopPoint < windowTop){	
				
				stickyStopperPosition = $stickyrStopper.offset().top;
				$sticky.css({ position: 'absolute', top: diff, width: wbox + 'px' });			
			} 
			else if (stickyTop < windowTop+stickOffset) {
			
				$sticky.css({ position: 'fixed', top: stickOffset, width: wbox + 'px' });	
			} 
			else{
				$sticky.css({position: 'absolute', top: 'initial'});
			}
		});
	}
}

function download_exam ( history_id, token ){
	
	$.ajax({
		url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=download&second=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data :{
			action: 'is_download',
			history_id: history_id,
			token: token
		},
		beforeSend: function() {
			$('.download i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
			$('.download').prop('disabled', true);

		},
		complete: function() {
			$('.download i').replaceWith('<i class="fa fa-download"></i>');
			$('.download').prop('disabled', false);
		},
		success: function(json) {
			
			if( json['error'] ) alert( json['error'] );  		
			
			if( json['link'] && json['linkin'] == 0 )
			{
				window.open(json['link'], "_blank");
			}
			else if( json['link'] && json['linkin'] == 1 )
			{
				window.location = json['link']; 
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			 
		}
	});
}

function makeid(length) {
   var result           = '';
   var characters       = '0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function video_analyzed ( video_type, videourl, imageurl, title ){
	
	var item = '';
	var _makeid  = makeid(5);
	if( video_type == 'youtube' )
	{
		//item+= '<iframe style="width:100%" height="506" src="https://www.youtube.com/embed/'+ videourl +'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
	
		item+= ' <video';
		item+= '	id="onlinetest-player-'+_makeid+'" class="video-js vjs-theme-city"';
		item+= '	controls';
		item+= '	autoplay';
		item+= '	width="100%" height="264"';
		item+= '	data-setup=\'{ "techOrder": ["youtube", "html5"], "sources": [{ "type": "video/youtube", "src": "https://www.youtube.com/watch?v='+ videourl +'"}] }\'';
		item+= '  >';
	}
	else
	{
		item+= '<video id="onlinetest-player-'+_makeid+'" class="video-js vjs-theme-city" controls preload="auto" poster="'+ imageurl +'" data-setup=\'{}\'>';
		item+= '  <source src="'+videourl+'" type="video/mp4"></source>';
		item+= '</video>';
	}
	
 
	 
	$('#ModalAddList .modal-title').html( 'VIDEO BÀI GIẢNG ' + title ); 
	$('#ModalAddList .modal-body').html( item ); 
	$('#ModalAddList').modal('show');
	var player = videojs('onlinetest-player-'+_makeid+'');
	
	
}

function CKupdate(){
    for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
}


$('#ModalAddList').on('hidden.bs.modal', function (e) {
	$('#ModalAddList .modal-title').empty(); 
	$('#ModalAddList .modal-body').empty(); 
})

$(document).ready(function() {
	
	$("#ModalAddList").on('hide.bs.modal', function(){
		$('#ModalAddList .modal-title').empty( ); 
		$('#ModalAddList .modal-body').empty(); 
	});
	
	$(document).on('click', '#formReport ul.dropdown-menu li', function(){
		
		$('#reportTitle').val( $(this).text() );
		$(this).parent().hide();
	})
	$(document).on('focus', '#reportTitle', function(){
		
		$(this).next().show();
	})
	$(document).on('input propertychange', '#reportTitle', function(){
		
		if( $(this).val().length > 0 )
		{
			$(this).next().hide();
		}
		else
		{
			$(this).next().show();
		}
	})
 
	
	$('body').on('click', 'a.report2', function(){
		var essay_id = $(this).attr('data-essay_id');
		var token = $(this).attr('data-token');
		var question = $('#question' + essay_id ).html();
		var login = $(this).attr('data-login');
		if( login != '' )
		{
			alert( login );
		}else{
			var temp='';
			temp+='<div id="ModalReport" class="modal fade" role="dialog">';
			temp+='  <div class="modal-dialog">';
			temp+='	<div class="modal-content">';
			temp+='	<form id="formReport" class="form-horizontal" role="form">';
			temp+='	  <div class="modal-header">';
			temp+='		<button type="button" class="close" data-dismiss="modal">&times;</button>';
			temp+='		<h4 class="modal-title" style="font-size: 14px;text-transform: uppercase;">Phản hồi câu hỏi</h4>';
			temp+='	  </div>';
			temp+='	  <div class="modal-body">';
			temp+='          <div class="question">'+ question +'</div>';
			temp+='          <div class="form-group">';
			temp+='            <label class="col-sm-4 control-label" for="reportTitle">Tiêu đề</label>';
			temp+='            <div class="col-sm-20">';
			temp+='					<input type="hidden" name="token" value="'+ token +'"/>';
			temp+='					<input type="hidden" name="question_id" value="'+ essay_id +'"/>';
			temp+='					<input type="hidden" name="type" value="1"/>';
			temp+='					<input type="text" class="form-control" name="reportTitle" id="reportTitle" placeholder="Lời dẫn sai, sai đáp án...."/>';				
			temp+='            		<ul class="dropdown-menu template scrollable-menu animated faster fadeInUp" role="menu">';
			temp+=' 					<li><a href="javascript:void(0);">Sai lỗi chính tả</a></li>';
			temp+=' 					<li><a href="javascript:void(0);">Sai đáp án, lời giải</a></li>';
			temp+=' 					<li><a href="javascript:void(0);">Sai đề bài</a></li>';
			temp+=' 				</ul>';
			temp+='            </div>';
			temp+='          </div>';
			temp+='          <div class="form-group">';
			temp+='            <label class="col-sm-4 control-label" for="reportNote" >Ý kiến đóng góp của bạn</label>';
			temp+='            <div class="col-sm-20">';
			temp+='                <textarea style="height:100px" class="form-control" id="reportNote" name="reportNote" placeholder="Nội dung"></textarea>';
			temp+='            </div>';
			temp+='          </div>';             
			temp+='	  </div>';
			temp+='	  <div class="modal-footer">';
			temp+='	    <button id="submitReport" type="submit" class="btn btn-primary"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> Gửi đi</button>';
			temp+='		<button type="button" class="btn btn-default" data-dismiss="modal">Đóng lại</button>';
			temp+='	  </div>';
			temp+='	</form>';
			temp+='	</div>';
			temp+='	</div>';
			temp+='</div>';
			$('body').append(temp);
			$('#ModalReport').modal();
		}
		
	})
	
	$('body').on('click', 'a.report', function(){
		var question_id = $(this).attr('data-question_id');
		var token = $(this).attr('data-token');
		var question = $('#question' + question_id ).html();
		var login = $(this).attr('data-login');
		if( login != '' )
		{
			alert( login );
		}else{
			var temp='';
			temp+='<div id="ModalReport" class="modal fade" role="dialog">';
			temp+='  <div class="modal-dialog">';
			temp+='	<div class="modal-content">';
			temp+='	<form id="formReport" class="form-horizontal" role="form">';
			temp+='	  <div class="modal-header">';
			temp+='		<button type="button" class="close" data-dismiss="modal">&times;</button>';
			temp+='		<h4 class="modal-title" style="font-size: 14px;text-transform: uppercase;">Phản hồi câu hỏi</h4>';
			temp+='	  </div>';
			temp+='	  <div class="modal-body">';
			temp+='          <div class="question">'+ question +'</div>';
			temp+='          <div class="form-group">';
			temp+='            <label class="col-sm-4 control-label" for="reportTitle">Tiêu đề</label>';
			temp+='            <div class="col-sm-20">';
			temp+='					<input type="hidden" name="token" value="'+ token +'"/>';
			temp+='					<input type="hidden" name="question_id" value="'+ question_id +'"/>';
			temp+='					<input type="text" class="form-control" name="reportTitle" id="reportTitle" placeholder="Lời dẫn sai, sai đáp án...."/>';				
			temp+='            		<ul class="dropdown-menu template scrollable-menu animated faster fadeInUp" role="menu">';
			temp+=' 					<li><a href="javascript:void(0);">Sai lỗi chính tả</a></li>';
			temp+=' 					<li><a href="javascript:void(0);">Sai đáp án, lời giải</a></li>';
			temp+=' 					<li><a href="javascript:void(0);">Sai đề bài</a></li>';
			temp+=' 				</ul>';
			temp+='            </div>';
			temp+='          </div>';
			temp+='          <div class="form-group">';
			temp+='            <label class="col-sm-4 control-label" for="reportNote" >Ý kiến đóng góp của bạn</label>';
			temp+='            <div class="col-sm-20">';
			temp+='                <textarea style="height:100px" class="form-control" id="reportNote" name="reportNote" placeholder="Nội dung"></textarea>';
			temp+='            </div>';
			temp+='          </div>';             
			temp+='	  </div>';
			temp+='	  <div class="modal-footer">';
			temp+='	    <button id="submitReport" type="submit" class="btn btn-primary"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> Gửi đi</button>';
			temp+='		<button type="button" class="btn btn-default" data-dismiss="modal">Đóng lại</button>';
			temp+='	  </div>';
			temp+='	</form>';
			temp+='	</div>';
			temp+='	</div>';
			temp+='</div>';
			$('body').append(temp);
			$('#ModalReport').modal();
		}
		
	})

	$('body').on('click', 'a.analyzes', function(e){
		var question_id = $(this).attr('data-question_id');
		var token = $(this).attr('data-token');
		var login = $(this).attr('data-login');
		var obj = $(this);
		if( login != '' )
		{
			alert( login );
		}else{
			if( $('#analyzesList-' + question_id).hasClass('show') ) 
			{
				$('#analyzesList-' + question_id).removeClass('show').addClass('hide');
				
			}else  
			{
				$('#analyzesList-' + question_id).addClass('show').removeClass('hide');
			}
			
		}
		
 
		e.preventDefault() ;
 
	})
	
	$('body').on('click', 'a.comment', function(e){
		var question_id = $(this).attr('data-question_id');
		var token = $(this).attr('data-token');
		var obj = $(this);
		
		if( obj.hasClass('disabled') && $('#commentList-' + question_id).hasClass('hide')) 
		{
			return false;
		}else if( $('#commentList-' + question_id).hasClass('show') ) 
		{
			$('#commentList-' + question_id).removeClass('show').addClass('hide');
			return false;
		}else if( $('#commentList-' + question_id).hasClass('isload')  && $('#commentList-' + question_id).hasClass('hide') ) 
		{
			$('#commentList-' + question_id).addClass('show').removeClass('hide');
			return false;
		}

		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: { action:'getComment', question_id : question_id, token : token } ,
			beforeSend: function() {
				obj.find('i').show();
				obj.prop('disabled', true).addClass('disabled');

			},
			complete: function() {
				obj.find('i').hide();
				setTimeout(function() {
					obj.prop('disabled', false).removeClass('disabled');
				}, 5000);
			},
			success: function(json) {
				
				if( json['comment'] )
				{
					$('#commentList-' + question_id ).append( json['comment'] ).removeClass('hide').addClass('show isload');
					createEditor('comment-'+question_id); 
					if(typeof MathJax !== 'undefined') {MathJax.Hub.Queue(["Typeset",MathJax.Hub]);setTimeout(function(){$('.math-tex').show()}, 400)}
					 
				}else if( json['error'] )
				{
					alert( json['error'] );
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				obj.prop('disabled', false);
			}
		});
		e.preventDefault() ;
		
		
		
	})

	$(document).on('click', '.insertComment', function(e){
		
		var obj = $(this).parent().parent();
		var id = obj.attr('id');
		var question_id = obj.attr('data-id');
		var comment =  CKEDITOR.instances['comment-'+question_id+''].getData();  
		if( strip_tags( comment, '<img>' ).length < 10 )
		{
			alert('Nội dung bình luận quá ngắn');
			return false;
		}
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data :{
				action: 'insertComment',
				comment_id: $('#' + id + ' input[name="comment_id"]').val(),
				question_id: $('#' + id + ' input[name="question_id"]').val(),
				token: $('#' + id + ' input[name="token"]').val(),
				lastcomment: $('#' + id + ' input[name="lastcomment"]').val(),
				comment: comment 	
			},
			beforeSend: function() {
				obj.find('i').show();
				obj.find('.insertComment').addClass('disabled');

			},
			complete: function() {
				obj.find('i').hide();
				setTimeout(function() {
					obj.find('.insertComment').removeClass('disabled');
				}, 2000);
			},
			success: function(json) {
				
				if( json['comment'] )
				{
					
					$('#commentList-' + question_id).find('ul.comment-list').append( json['comment'] ); 
					$('#insertComment-' + question_id).find('input[name="lastcomment"]').val( json['lastcomment'] ); 
					if( json['total_comment'] )
					{ 
						var getcomment = $('#getcomment-' + question_id ).text();
						getcomment = intval( getcomment ) + intval(json['total_comment']);
						$('#getcomment-' + question_id ).html( getcomment );
					}
					CKEDITOR.instances['comment-'+question_id+''].setData(''); 
					 
				}else if( json['update'] )
				{
					$('#group-' + json['comment_id'] ).find('.contentComment').html( json['update'] );
					$('#' + id + ' input[name="comment_id"]').val(0),
					CKEDITOR.instances['comment-'+question_id+''].setData(''); 
				}
				else if( json['error'] )
				{
					alert( json['error'] );
				}
				
				if(typeof MathJax !== 'undefined') {MathJax.Hub.Queue(["Typeset",MathJax.Hub]);setTimeout(function(){$('.math-tex').show()}, 400)}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				obj.find('.insertComment').removeClass('disabled');
			}
		});
		e.preventDefault() ;
		
		
		
	})

	$('body').on('click', 'a.loadMoreComment', function(e){
		var question_id = $(this).attr('data-question_id');
		var token = $(this).attr('data-token');
		var page = $(this).attr('data-page');
		var obj = $(this);
	 
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: { action : 'getOnlyComment', question_id : question_id, page : page, token : token } ,
			beforeSend: function() {
				obj.find('i').show();
				obj.prop('disabled', true).addClass('disabled');

			},
			complete: function() {
				obj.find('i').hide();
				setTimeout(function() {
					obj.prop('disabled', false).removeClass('disabled');
				}, 2000);
			},
			success: function(json) {
				if( json['comment'] )
				{
					$('#commentList-' + question_id ).find('.comment-list').append( json['comment'] );
					if( json['page'] )
					{
						obj.attr('data-page', json['page']);
					}
					if( json['loadMore'] == 0 )
					{
						$('#commentList-' + question_id).find('.loadmore').remove();
					}		

					if(typeof MathJax !== 'undefined') {MathJax.Hub.Queue(["Typeset",MathJax.Hub]);setTimeout(function(){$('.math-tex').show()}, 400)}
				}
				if( json['total_comment'] )
				{
					$('#getcomment-' + question_id ).html( json['total_comment'] );
				}
				if( json['error'] )
				{
					alert( json['error'] );
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				obj.prop('disabled', false);
			}
		});
		e.preventDefault() ;
		
		
		
	})

	$('body').on('click', 'a.canEdit', function(e){
		var question_id  = $(this).attr('data-question_id');
		var comment_id = $(this).attr('data-comment_id');
		var token = $(this).attr('data-token');
		var obj = $(this);
	 
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: { action : 'canEdit', comment_id : comment_id, question_id : question_id, token : token },
			beforeSend: function() {
				obj.find('i').replaceWith('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
				obj.prop('disabled', true).addClass('disabled');

			},
			complete: function() {
				obj.find('i').replaceWith('<i class="fa fa-edit fa-1x fa-fw"></i>');
				setTimeout(function() {
					obj.prop('disabled', false).removeClass('disabled');
				}, 2000);
			},
			success: function(json) {
				if( json['comment'] )
				{
					CKEDITOR.instances['comment-'+question_id+''].setData( json['comment'] );   
					$('#insertComment-'+question_id+'').find('input[name="comment_id"]').val( comment_id );
				}
				 
				if( json['error'] )
				{
					alert( json['error'] );
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				obj.find('i').replaceWith('<i class="fa fa-edit fa-1x fa-fw"></i>');
				obj.prop('disabled', false);
			}
		});
		e.preventDefault() ;
		
		
		
	})
	
	$('body').on('click', 'a.canDelete', function(e){
		var question_id  = $(this).attr('data-question_id');
		var comment_id = $(this).attr('data-comment_id');
		var token = $(this).attr('data-token');
		var obj = $(this);
		if( confirm('Bạn có chắc chắn xóa bình luận này không ?') )
		{
		
			$.ajax({
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&second=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: { action : 'canDelete', comment_id : comment_id, question_id : question_id, token : token },
				beforeSend: function() {
					obj.find('i').replaceWith('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
					obj.prop('disabled', true).addClass('disabled');

				},
				complete: function() {
					obj.find('i').replaceWith('<i class="fa fa-trash fa-1x fa-fw"></i>');
					setTimeout(function() {
						obj.prop('disabled', false).removeClass('disabled');
					}, 2000);
				},
				success: function(json) {
					if( json['success'] )
					{			 
						$('#group-'+json['comment_id']+'').remove();
						$('#insertComment-' + question_id + ' input[name="comment_id"]').val(0);
					}		 
					else if( json['error'] )
					{
						alert( json['error'] );
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					obj.find('i').replaceWith('<i class="fa fa-trash fa-1x fa-fw"></i>');
					obj.prop('disabled', false);
				}
			});
		}
		e.preventDefault() ;
	 
	})

	$(document).on('submit', '#formReport', function(e){
		formdata = $( this ).serializeArray();
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: formdata ,
			beforeSend: function() {
				$('#submitReport i').show();
				$('#submitReport').prop('disabled', true);

			},
			complete: function() {
				$('#submitReport i').hide();
				setTimeout(function() {
					$('#submitReport').prop('disabled', false);
				}, 2000);
			},
			success: function(json) {
				if( json['success'] )
				{
					$('#ModalReport .modal-body').html( '<div class="success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '</div>' );
					$('#submitReport').remove();
				}
				if( json['error'] )
				{
					$('#ModalReport .modal-body').html( '<div class="error"><i class="fa fa-exclamation-circle"></i> ' + json['error']+ '</div>' );
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('#submitReport').prop('disabled', false);
			}
		});
		e.preventDefault() ;

	})

	$(document).on("hidden.bs.modal", '#ModalReport', function () {
		$('#ModalReport').remove();
	});

	$('#sureOpen').on('click', function(){
		
		var type_exam_id = $('#sureOpenTypeExamId').val();
		var token = $('#sureOpenToken').val();
		
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=getquestion&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: {type_exam_id : type_exam_id, token : token },
			beforeSend: function() {
				$('#sureOpen i').show();
				$('#sureOpen').prop('disabled', true);

			},
			complete: function() {
				$('#sureOpen i').hide();
				setTimeout(function() {
					$('#sureOpen').prop('disabled', false);
				}, 2000);
			},
			success: function(json) {
				if( json['question'] )
				{
					var temp = '';
					$.each( json['question'], function(i, item){
						
						 temp+= '<li class="q_'+ item['question_id'] +'"><a class="" onclick="showQuestion(\'question'+ item['question_id'] +'\')" href="javascript:void(0);">'+ item['num'] +'</a></li>';
					})
					$('.list-question-number ul').html( temp );
					
				}
				if( json['answers_list'] )
				{
					$('#changetoanswer').html( json['answers_list'] );
				}
				if( json['template'] )
				{
					$('#showQuestion').html( json['template'] ).show();
					$('#openTest').remove();
					// code hiển thị thời gian
					$('<div class="summury-info text-center ">Thời gian còn lại: <div class="timer" data-seconds-left="1"></div></div>').prependTo('#OnlineTestDoTest .OnlineTestDoTestbox');
					document.getElementById("thoigian").style.display = "none";
					
					$('.timer').attr('data-seconds-left', json['time_test']).empty();
					$('.timer').startTimer({
						onComplete: function(element){
							$('#is_sended').val(1);
							$('#submitform').trigger('click');
							alert('ĐÃ HẾT THỜI GIAN LÀM BÀI');
						}
					 }) 
					scrollDiv();
 
				}else if( json['error'] )
				{
					alert( json['error'] );
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('#sureOpen').prop('disabled', false);
			}
		});

	})

	$('#sureOpenEssay').on('click', function(){
		
		var essay_exam_id = $('#sureOpenTypeExamId').val();
		var token = $('#sureOpenToken').val();
		
		$.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=getquestion&action=essay&second=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: {essay_exam_id : essay_exam_id, token : token },
			beforeSend: function() {
				$('#sureOpenEssay i').show();
				$('#sureOpenEssay').prop('disabled', true);

			},
			complete: function() {
				$('#sureOpenEssay i').hide();
				setTimeout(function() {
					$('#sureOpenEssay').prop('disabled', false);
				}, 2000);
			},
			success: function(json) {
				if( json['question'] )
				{
					var temp = '';
					$.each( json['question'], function(i, item){
						
						 temp+= '<li class="q_'+ item['essay_id'] +'"><a class="" onclick="showQuestion(\'question'+ item['essay_id'] +'\')" href="javascript:void(0);">'+ item['num'] +'</a></li>';
					})
					$('.list-question-number ul').html( temp );
					
				}
				if( json['answers_list'] )
				{
					$('#changetoanswer').html( json['answers_list'] );
				}
				if( json['template'] )
				{
					$('#showQuestion').html( json['template'] ).show();
					$('#openTest').remove();

					
					
					// $('<div class="onlinetest-clock">Thời gian còn lại: <div class="timer" data-seconds-left="1"></div></div>').prependTo('body');	
				
					$('.timer').attr('data-seconds-left', json['time_test']).empty();
					$('.timer').startTimer({
						onComplete: function(element){
							$('#is_sended').val(1);
							$('#submitform').trigger('click');
							alert('ĐÃ HẾT THỜI GIAN LÀM BÀI');
						}
					 }) 
					scrollDiv();
 
				}else if( json['error'] )
				{
					alert( json['error'] );
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('#sureOpenEssay').prop('disabled', false);
			}
		});

	})
 
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
 
	$('button[type=\'submit\']').on('click', function() {
		$("form[id*='form-']").submit();
	});
 
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
	
	$('body').on('click', '.alert i.fa-times', function(){
		$(this).parent().slideUp( "slow", function() {
			$(this).remove();
		}); 
	})
});
