/* Definition of BlueBox (popup) */
(function($){
	"use strict";
	var BlueBox = function(id){
		this.id = typeof id != 'undefined' ? id : '#undefined';
		this.window = window;
		this.window['Bluebox'+id] = this;
		this.width = this.window.innerWidth*0.65;
		this.height = this.window.innerHeight*0.75;
		this.handler = false;//would be equal to 'iframe'
		this.stack = {};
		this.css = {};
		this.subject = '.bluebox-subject';
		this.makeCopy = false;
		this._clear = false;
	};
	
	BlueBox.prototype = {
		init: function(subject, options){
			this.subject = subject || this.subject;
			var options = typeof options == 'object' ? options : {};
			
			//if( this._clear ){
				//this.clear();
			//}
			//alert('init');
			this.setOptions(options);
			this.createBox();
			
			$(window).resize(function(){
				//
			});
			
			return this;
		},
		setOptions: function(options, callback){
			var $this = this;
			$.each(options, function(i,o){
				$this[i] = o;
			});
			
			if( typeof this.needle == 'undefined' ){
				this.needle = '#bluebox-btn';
			}
			
			this.document = this.window.document;
			this._clear = true;
			
			if( typeof callback == 'function' ){
				callback();
			}
			//console.log(this.width,this.height);
			return this;
		},
		set: function(key, value){
			this[key] = value;
			return this;
		},
		listen: function(Event, needle){
			var $this = this;
			var selector;
			if( typeof needle == 'undefined' ){
				selector = this.needle;
			}
			
			$(document).on(Event, selector, function(e){
				e.preventDefault();
				$this.open(needle, $this.subject);
			});
			
			return this;
		},
		specify: function(elem){
			if( typeof elem != 'undefined' ){
				var element;
				if( elem.jquery != 'undefined' ){
					element = elem;
				}
				else if( $(elem).length > 0 ){
					element = $(elem);
				}
				
				return $(element);
			}
			else{
				return null;
			}
			
		},
		createBox: function(){
			var background = this.background || '#FFF';
			this.background = background;
			var box = '';
			box += '<div class="bluebox-bg" style="position:fixed; left:0; top:0; right:0; bottom:0; z-index: 1; background-color: #222; opacity: 0.6;"></div>';
			box += '<div class="bluebox-content"><div class="content-wrapper"></div></div>';
			box = '<div class="bluebox" id="'+this.id.replace('#','')+'" style="position: fixed; top:0; left:0; z-index: 999999; width:100%; height: 100%; display:none">' + box + '</div>';

			$(this.document).find('body').append(box);
			this.blueBox = $(this.document).find(this.id);
			this.blueBoxContent = this.blueBox.find('.bluebox-content');
			this.contentWrapper = this.blueBoxContent.find('.content-wrapper');
			
			this.blueBox.css({
				'position': 'fixed', 'top': 0, 'left': 0, 'z-index': 999999, 'width': '100%', 'height': '100%', 'display':'none'
			});
			this.blueBoxContent.css({
				'width'	   : this.width,
				'height'   : this.height,
				'position' : 'relative',
				'left'	   : (this.window.innerWidth - this.width)/2,
				'top' 	   : (this.window.innerHeight - this.height)/2,
				'z-index'  : 2,
				'overflow' : 'auto',
				'padding'  : '5px',
				'background-color': this.background,
				'border'   : this.border,
				'border-radius' : this.border_radius
			});
			if( this.styleBg ){
				this.blueBox.find('.bluebox-bg').css(this.styleBg);
			}
			
			return this;
		},
		addEvent: function(Event, selector, callback){
			if( !this.stack[Event] ){
				this.stack[Event] = {};
			}
			this.stack[Event][selector] = callback;
			return this;
		},
		triggerEvents: function(){
			var $this = this;
			$.each(this.stack, function(Event, Obj){
				$.each(Obj, function(selector){
					$this.triggerEvent(Event, selector);
				});
			});
			
			return this;
		},
		triggerEvent: function(Event, selector){
			if( !(this.stack[Event]) ){
				return this;
			}
			
			if( typeof selector != 'undefined' ){
				 if( this.stack[Event][selector] ){
				 	var callback = this.stack[Event][selector];
				 	this.trigger(Event, selector, callback);
				 }
			}
			else{
				var $this = this;
				$.each(this.stack[Event], function(selector, callback){
					$this.trigger(Event, selector, callback);
				});
			}
			
			return this;
		},
		trigger: function(Event, selector, callback){
			var $this = this;
			if( this.specify(selector) ){
				$(this.document).find(selector).unbind(Event).bind(Event, function(e){
					e.preventDefault();
					if( typeof callback == 'function' ){
						callback();
					}
					else if( typeof callback == 'string' ){
						eval(callback);
					}
				});
			}
			
			return this;
		},
		open: function(needle, subject){
			var $this = this;
			needle = this.specify(needle) || this.needle;
			subject = this.specify(subject) || $(this.subject);
			//this.addEvent('click', this.id+' .bluebox-bg', function(){ $this.close(); });
			this.triggerEvents();
			//this.clear();
			if( $this.makeCopy ){
				this.clear();
				$this.contentWrapper.append(subject.html());
			}
			else{
				$this.contentWrapper.append(subject);
			}
			$this.blueBox.fadeIn(200);
			$(this.id).find('.content-wrapper > *').show();
			this.bindEscape();
			//this.trigger('keypress', document, function(e){ $this.escape(); });
			return $this;
		},
		close: function(fast){
			if( !fast ){
				this.blueBox.fadeOut(100);
			}
			else{
				this.blueBox.hide();
			}
		},
		bindEscape: function(){
			$(document).on('keypress', function(e){
				var keycode = e.charCode || e.keyCode;
				if( keycode == 27 ){
					$this.close();
				}
			});
		},
		clear: function(elem){
			var Elem = elem;
			var $this = this;
			
			if( elem ){
				elem = this.specify(elem);
				if( elem.length < 1 ){
					elem = $(this.document).find(Elem);
				}
				
				elem.empty();
			}
			else{
				$this.contentWrapper.empty();
			}
			
			return this;
		},
		styleBackground: function(bg){
			this.styleBg = bg;
			return this;
		},
		issetElem: function(selector){
			return ( $(this.document).find(selector).length > 0 );
		},
		addNode: function(elem, parent){
			if( elem && parent ){
				$(this.document).find(parent).append(elem);
			}
			
			return this;
		}
	};
	
	//var Bluebox = new BlueBox();
	$.fn.inbox = function(id, options){
		if( ! window['Bluebox'+id] ){
			new BlueBox(id);
			window['Bluebox'+id].init(this, options);
		}
		
		return window['Bluebox'+id];
	};
	
})(jQuery);

/* Actions add events */
jQuery(document).ready(function($){
	//
});
