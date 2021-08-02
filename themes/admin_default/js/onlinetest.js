/* ===================================================
 *  jquery-sortable.js v0.9.13
 *  http://johnny.github.com/jquery-sortable/
 * ===================================================
 *  Copyright (c) 2012 Jonas von Andrian
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  * The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 *  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * ========================================================== */


!function ( $, window, pluginName, undefined){
  var containerDefaults = {
    // If true, items can be dragged from this container
    drag: true,
    // If true, items can be droped onto this container
    drop: true,
    // Exclude items from being draggable, if the
    // selector matches the item
    exclude: "",
    // If true, search for nested containers within an item.If you nest containers,
    // either the original selector with which you call the plugin must only match the top containers,
    // or you need to specify a group (see the bootstrap nav example)
    nested: true,
    // If true, the items are assumed to be arranged vertically
    vertical: true
  }, // end container defaults
  groupDefaults = {
    // This is executed after the placeholder has been moved.
    // $closestItemOrContainer contains the closest item, the placeholder
    // has been put at or the closest empty Container, the placeholder has
    // been appended to.
    afterMove: function ($placeholder, container, $closestItemOrContainer) {
    },
    // The exact css path between the container and its items, e.g. "> tbody"
    containerPath: "",
    // The css selector of the containers
    containerSelector: "ol, ul",
    // Distance the mouse has to travel to start dragging
    distance: 0,
    // Time in milliseconds after mousedown until dragging should start.
    // This option can be used to prevent unwanted drags when clicking on an element.
    delay: 0,
    // The css selector of the drag handle
    handle: "",
    // The exact css path between the item and its subcontainers.
    // It should only match the immediate items of a container.
    // No item of a subcontainer should be matched. E.g. for ol>div>li the itemPath is "> div"
    itemPath: "",
    // The css selector of the items
    itemSelector: "li",
    // The class given to "body" while an item is being dragged
    bodyClass: "dragging",
    // The class giving to an item while being dragged
    draggedClass: "dragged",
    // Check if the dragged item may be inside the container.
    // Use with care, since the search for a valid container entails a depth first search
    // and may be quite expensive.
    isValidTarget: function ($item, container) {
      return true
    },
    // Executed before onDrop if placeholder is detached.
    // This happens if pullPlaceholder is set to false and the drop occurs outside a container.
    onCancel: function ($item, container, _super, event) {
    },
    // Executed at the beginning of a mouse move event.
    // The Placeholder has not been moved yet.
    onDrag: function ($item, position, _super, event) {
      $item.css(position)
    },
    // Called after the drag has been started,
    // that is the mouse button is being held down and
    // the mouse is moving.
    // The container is the closest initialized container.
    // Therefore it might not be the container, that actually contains the item.
    onDragStart: function ($item, container, _super, event) {
      $item.css({
        height: $item.outerHeight(),
        width: $item.outerWidth()
      })
      $item.addClass(container.group.options.draggedClass)
      $("body").addClass(container.group.options.bodyClass)
    },
    // Called when the mouse button is being released
    onDrop: function ($item, container, _super, event) {
      $item.removeClass(container.group.options.draggedClass).removeAttr("style")
      $("body").removeClass(container.group.options.bodyClass)
    },
    // Called on mousedown. If falsy value is returned, the dragging will not start.
    // Ignore if element clicked is input, select or textarea
    onMousedown: function ($item, _super, event) {
      if (!event.target.nodeName.match(/^(input|select|textarea)$/i)) {
        event.preventDefault()
        return true
      }
    },
    // The class of the placeholder (must match placeholder option markup)
    placeholderClass: "placeholder",
    // Template for the placeholder. Can be any valid jQuery input
    // e.g. a string, a DOM element.
    // The placeholder must have the class "placeholder"
    placeholder: '<li class="placeholder"></li>',
    // If true, the position of the placeholder is calculated on every mousemove.
    // If false, it is only calculated when the mouse is above a container.
    pullPlaceholder: true,
    // Specifies serialization of the container group.
    // The pair $parent/$children is either container/items or item/subcontainers.
    serialize: function ($parent, $children, parentIsContainer) {
      var result = $.extend({}, $parent.data())

      if(parentIsContainer)
        return [$children]
      else if ($children[0]){
        result.children = $children
      }

      delete result.subContainers
      delete result.sortable

      return result
    },
    // Set tolerance while dragging. Positive values decrease sensitivity,
    // negative values increase it.
    tolerance: 0
  }, // end group defaults
  containerGroups = {},
  groupCounter = 0,
  emptyBox = {
    left: 0,
    top: 0,
    bottom: 0,
    right:0
  },
  eventNames = {
    start: "touchstart.sortable mousedown.sortable",
    drop: "touchend.sortable touchcancel.sortable mouseup.sortable",
    drag: "touchmove.sortable mousemove.sortable",
    scroll: "scroll.sortable"
  },
  subContainerKey = "subContainers"

  /*
   * a is Array [left, right, top, bottom]
   * b is array [left, top]
   */
  function d(a,b) {
    var x = Math.max(0, a[0] - b[0], b[0] - a[1]),
    y = Math.max(0, a[2] - b[1], b[1] - a[3])
    return x+y;
  }

  function setDimensions(array, dimensions, tolerance, useOffset) {
    var i = array.length,
    offsetMethod = useOffset ? "offset" : "position"
    tolerance = tolerance || 0

    while(i--){
      var el = array[i].el ? array[i].el : $(array[i]),
      // use fitting method
      pos = el[offsetMethod]()
      pos.left += parseInt(el.css('margin-left'), 10)
      pos.top += parseInt(el.css('margin-top'),10)
      dimensions[i] = [
        pos.left - tolerance,
        pos.left + el.outerWidth() + tolerance,
        pos.top - tolerance,
        pos.top + el.outerHeight() + tolerance
      ]
    }
  }

  function getRelativePosition(pointer, element) {
    var offset = element.offset()
    return {
      left: pointer.left - offset.left,
      top: pointer.top - offset.top
    }
  }

  function sortByDistanceDesc(dimensions, pointer, lastPointer) {
    pointer = [pointer.left, pointer.top]
    lastPointer = lastPointer && [lastPointer.left, lastPointer.top]

    var dim,
    i = dimensions.length,
    distances = []

    while(i--){
      dim = dimensions[i]
      distances[i] = [i,d(dim,pointer), lastPointer && d(dim, lastPointer)]
    }
    distances = distances.sort(function  (a,b) {
      return b[1] - a[1] || b[2] - a[2] || b[0] - a[0]
    })

    // last entry is the closest
    return distances
  }

  function ContainerGroup(options) {
    this.options = $.extend({}, groupDefaults, options)
    this.containers = []

    if(!this.options.rootGroup){
      this.scrollProxy = $.proxy(this.scroll, this)
      this.dragProxy = $.proxy(this.drag, this)
      this.dropProxy = $.proxy(this.drop, this)
      this.placeholder = $(this.options.placeholder)

      if(!options.isValidTarget)
        this.options.isValidTarget = undefined
    }
  }

  ContainerGroup.get = function  (options) {
    if(!containerGroups[options.group]) {
      if(options.group === undefined)
        options.group = groupCounter ++

      containerGroups[options.group] = new ContainerGroup(options)
    }

    return containerGroups[options.group]
  }

  ContainerGroup.prototype = {
    dragInit: function  (e, itemContainer) {
      this.$document = $(itemContainer.el[0].ownerDocument)

      // get item to drag
      var closestItem = $(e.target).closest(this.options.itemSelector);
      // using the length of this item, prevents the plugin from being started if there is no handle being clicked on.
      // this may also be helpful in instantiating multidrag.
      if (closestItem.length) {
        this.item = closestItem;
        this.itemContainer = itemContainer;
        if (this.item.is(this.options.exclude) || !this.options.onMousedown(this.item, groupDefaults.onMousedown, e)) {
            return;
        }
        this.setPointer(e);
        this.toggleListeners('on');
        this.setupDelayTimer();
        this.dragInitDone = true;
      }
    },
    drag: function  (e) {
      if(!this.dragging){
        if(!this.distanceMet(e) || !this.delayMet)
          return

        this.options.onDragStart(this.item, this.itemContainer, groupDefaults.onDragStart, e)
        this.item.before(this.placeholder)
        this.dragging = true
      }

      this.setPointer(e)
      // place item under the cursor
      this.options.onDrag(this.item,
                          getRelativePosition(this.pointer, this.item.offsetParent()),
                          groupDefaults.onDrag,
                          e)

      var p = this.getPointer(e),
      box = this.sameResultBox,
      t = this.options.tolerance

      if(!box || box.top - t > p.top || box.bottom + t < p.top || box.left - t > p.left || box.right + t < p.left)
        if(!this.searchValidTarget()){
          this.placeholder.detach()
          this.lastAppendedItem = undefined
        }
    },
    drop: function  (e) {
      this.toggleListeners('off')

      this.dragInitDone = false

      if(this.dragging){
        // processing Drop, check if placeholder is detached
        if(this.placeholder.closest("html")[0]){
          this.placeholder.before(this.item).detach()
        } else {
          this.options.onCancel(this.item, this.itemContainer, groupDefaults.onCancel, e)
        }
        this.options.onDrop(this.item, this.getContainer(this.item), groupDefaults.onDrop, e)

        // cleanup
        this.clearDimensions()
        this.clearOffsetParent()
        this.lastAppendedItem = this.sameResultBox = undefined
        this.dragging = false
      }
    },
    searchValidTarget: function  (pointer, lastPointer) {
      if(!pointer){
        pointer = this.relativePointer || this.pointer
        lastPointer = this.lastRelativePointer || this.lastPointer
      }

      var distances = sortByDistanceDesc(this.getContainerDimensions(),
                                         pointer,
                                         lastPointer),
      i = distances.length

      while(i--){
        var index = distances[i][0],
        distance = distances[i][1]

        if(!distance || this.options.pullPlaceholder){
          var container = this.containers[index]
          if(!container.disabled){
            if(!this.$getOffsetParent()){
              var offsetParent = container.getItemOffsetParent()
              pointer = getRelativePosition(pointer, offsetParent)
              lastPointer = getRelativePosition(lastPointer, offsetParent)
            }
            if(container.searchValidTarget(pointer, lastPointer))
              return true
          }
        }
      }
      if(this.sameResultBox)
        this.sameResultBox = undefined
    },
    movePlaceholder: function  (container, item, method, sameResultBox) {
      var lastAppendedItem = this.lastAppendedItem
      if(!sameResultBox && lastAppendedItem && lastAppendedItem[0] === item[0])
        return;

      item[method](this.placeholder)
      this.lastAppendedItem = item
      this.sameResultBox = sameResultBox
      this.options.afterMove(this.placeholder, container, item)
    },
    getContainerDimensions: function  () {
      if(!this.containerDimensions)
        setDimensions(this.containers, this.containerDimensions = [], this.options.tolerance, !this.$getOffsetParent())
      return this.containerDimensions
    },
    getContainer: function  (element) {
      return element.closest(this.options.containerSelector).data(pluginName)
    },
    $getOffsetParent: function  () {
      if(this.offsetParent === undefined){
        var i = this.containers.length - 1,
        offsetParent = this.containers[i].getItemOffsetParent()

        if(!this.options.rootGroup){
          while(i--){
            if(offsetParent[0] != this.containers[i].getItemOffsetParent()[0]){
              // If every container has the same offset parent,
              // use position() which is relative to this parent,
              // otherwise use offset()
              // compare #setDimensions
              offsetParent = false
              break;
            }
          }
        }

        this.offsetParent = offsetParent
      }
      return this.offsetParent
    },
    setPointer: function (e) {
      var pointer = this.getPointer(e)

      if(this.$getOffsetParent()){
        var relativePointer = getRelativePosition(pointer, this.$getOffsetParent())
        this.lastRelativePointer = this.relativePointer
        this.relativePointer = relativePointer
      }

      this.lastPointer = this.pointer
      this.pointer = pointer
    },
    distanceMet: function (e) {
      var currentPointer = this.getPointer(e)
      return (Math.max(
        Math.abs(this.pointer.left - currentPointer.left),
        Math.abs(this.pointer.top - currentPointer.top)
      ) >= this.options.distance)
    },
    getPointer: function(e) {
      var o = e.originalEvent || e.originalEvent.touches && e.originalEvent.touches[0]
      return {
        left: e.pageX || o.pageX,
        top: e.pageY || o.pageY
      }
    },
    setupDelayTimer: function () {
      var that = this
      this.delayMet = !this.options.delay

      // init delay timer if needed
      if (!this.delayMet) {
        clearTimeout(this._mouseDelayTimer);
        this._mouseDelayTimer = setTimeout(function() {
          that.delayMet = true
        }, this.options.delay)
      }
    },
    scroll: function  (e) {
      this.clearDimensions()
      this.clearOffsetParent() // TODO is this needed?
    },
    toggleListeners: function (method) {
      var that = this,
      events = ['drag','drop','scroll']

      $.each(events,function  (i,event) {
        that.$document[method](eventNames[event], that[event + 'Proxy'])
      })
    },
    clearOffsetParent: function () {
      this.offsetParent = undefined
    },
    // Recursively clear container and item dimensions
    clearDimensions: function  () {
      this.traverse(function(object){
        object._clearDimensions()
      })
    },
    traverse: function(callback) {
      callback(this)
      var i = this.containers.length
      while(i--){
        this.containers[i].traverse(callback)
      }
    },
    _clearDimensions: function(){
      this.containerDimensions = undefined
    },
    _destroy: function () {
      containerGroups[this.options.group] = undefined
    }
  }

  function Container(element, options) {
    this.el = element
    this.options = $.extend( {}, containerDefaults, options)

    this.group = ContainerGroup.get(this.options)
    this.rootGroup = this.options.rootGroup || this.group
    this.handle = this.rootGroup.options.handle || this.rootGroup.options.itemSelector

    var itemPath = this.rootGroup.options.itemPath
    this.target = itemPath ? this.el.find(itemPath) : this.el

    this.target.on(eventNames.start, this.handle, $.proxy(this.dragInit, this))

    if(this.options.drop)
      this.group.containers.push(this)
  }

  Container.prototype = {
    dragInit: function  (e) {
      var rootGroup = this.rootGroup

      if( !this.disabled &&
          !rootGroup.dragInitDone &&
          this.options.drag &&
          this.isValidDrag(e)) {
        rootGroup.dragInit(e, this)
      }
    },
    isValidDrag: function(e) {
      return e.which == 1 ||
        e.type == "touchstart" && e.originalEvent.touches.length == 1
    },
    searchValidTarget: function  (pointer, lastPointer) {
      var distances = sortByDistanceDesc(this.getItemDimensions(),
                                         pointer,
                                         lastPointer),
      i = distances.length,
      rootGroup = this.rootGroup,
      validTarget = !rootGroup.options.isValidTarget ||
        rootGroup.options.isValidTarget(rootGroup.item, this)

      if(!i && validTarget){
        rootGroup.movePlaceholder(this, this.target, "append")
        return true
      } else
        while(i--){
          var index = distances[i][0],
          distance = distances[i][1]
          if(!distance && this.hasChildGroup(index)){
            var found = this.getContainerGroup(index).searchValidTarget(pointer, lastPointer)
            if(found)
              return true
          }
          else if(validTarget){
            this.movePlaceholder(index, pointer)
            return true
          }
        }
    },
    movePlaceholder: function  (index, pointer) {
      var item = $(this.items[index]),
      dim = this.itemDimensions[index],
      method = "after",
      width = item.outerWidth(),
      height = item.outerHeight(),
      offset = item.offset(),
      sameResultBox = {
        left: offset.left,
        right: offset.left + width,
        top: offset.top,
        bottom: offset.top + height
      }
      if(this.options.vertical){
        var yCenter = (dim[2] + dim[3]) / 2,
        inUpperHalf = pointer.top <= yCenter
        if(inUpperHalf){
          method = "before"
          sameResultBox.bottom -= height / 2
        } else
          sameResultBox.top += height / 2
      } else {
        var xCenter = (dim[0] + dim[1]) / 2,
        inLeftHalf = pointer.left <= xCenter
        if(inLeftHalf){
          method = "before"
          sameResultBox.right -= width / 2
        } else
          sameResultBox.left += width / 2
      }
      if(this.hasChildGroup(index))
        sameResultBox = emptyBox
      this.rootGroup.movePlaceholder(this, item, method, sameResultBox)
    },
    getItemDimensions: function  () {
      if(!this.itemDimensions){
        this.items = this.$getChildren(this.el, "item").filter(
          ":not(." + this.group.options.placeholderClass + ", ." + this.group.options.draggedClass + ")"
        ).get()
        setDimensions(this.items, this.itemDimensions = [], this.options.tolerance)
      }
      return this.itemDimensions
    },
    getItemOffsetParent: function  () {
      var offsetParent,
      el = this.el
      // Since el might be empty we have to check el itself and
      // can not do something like el.children().first().offsetParent()
      if(el.css("position") === "relative" || el.css("position") === "absolute"  || el.css("position") === "fixed")
        offsetParent = el
      else
        offsetParent = el.offsetParent()
      return offsetParent
    },
    hasChildGroup: function (index) {
      return this.options.nested && this.getContainerGroup(index)
    },
    getContainerGroup: function  (index) {
      var childGroup = $.data(this.items[index], subContainerKey)
      if( childGroup === undefined){
        var childContainers = this.$getChildren(this.items[index], "container")
        childGroup = false

        if(childContainers[0]){
          var options = $.extend({}, this.options, {
            rootGroup: this.rootGroup,
            group: groupCounter ++
          })
          childGroup = childContainers[pluginName](options).data(pluginName).group
        }
        $.data(this.items[index], subContainerKey, childGroup)
      }
      return childGroup
    },
    $getChildren: function (parent, type) {
      var options = this.rootGroup.options,
      path = options[type + "Path"],
      selector = options[type + "Selector"]

      parent = $(parent)
      if(path)
        parent = parent.find(path)

      return parent.children(selector)
    },
    _serialize: function (parent, isContainer) {
      var that = this,
      childType = isContainer ? "item" : "container",

      children = this.$getChildren(parent, childType).not(this.options.exclude).map(function () {
        return that._serialize($(this), !isContainer)
      }).get()

      return this.rootGroup.options.serialize(parent, children, isContainer)
    },
    traverse: function(callback) {
      $.each(this.items || [], function(item){
        var group = $.data(this, subContainerKey)
        if(group)
          group.traverse(callback)
      });

      callback(this)
    },
    _clearDimensions: function  () {
      this.itemDimensions = undefined
    },
    _destroy: function() {
      var that = this;

      this.target.off(eventNames.start, this.handle);
      this.el.removeData(pluginName)

      if(this.options.drop)
        this.group.containers = $.grep(this.group.containers, function(val){
          return val != that
        })

      $.each(this.items || [], function(){
        $.removeData(this, subContainerKey)
      })
    }
  }

  var API = {
    enable: function() {
      this.traverse(function(object){
        object.disabled = false
      })
    },
    disable: function (){
      this.traverse(function(object){
        object.disabled = true
      })
    },
    serialize: function () {
      return this._serialize(this.el, true)
    },
    refresh: function() {
      this.traverse(function(object){
        object._clearDimensions()
      })
    },
    destroy: function () {
      this.traverse(function(object){
        object._destroy();
      })
    }
  }

  $.extend(Container.prototype, API)

  /**
   * jQuery API
   *
   * Parameters are
   *   either options on init
   *   or a method name followed by arguments to pass to the method
   */
  $.fn[pluginName] = function(methodOrOptions) {
    var args = Array.prototype.slice.call(arguments, 1)

    return this.map(function(){
      var $t = $(this),
      object = $t.data(pluginName)

      if(object && API[methodOrOptions])
        return API[methodOrOptions].apply(object, args) || this
      else if(!object && (methodOrOptions === undefined ||
                          typeof methodOrOptions === "object"))
        $t.data(pluginName, new Container($t, methodOrOptions))

      return this
    });
  };

}(jQuery, window, 'sortable');


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
 
function get_alias( mod, id ) {
	var title = strip_tags(document.getElementById('inputs-title').value);
	if (title != '') {
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=alias&nocache=' + new Date().getTime(), 'title=' + encodeURIComponent(title) + '&mod=' + mod + '&id=' + id, function(res) {
			if (res != "") {
				document.getElementById('input-alias').value = res;
			} else {
				document.getElementById('input-alias').value = '';
			}
		});
	}
	return false;
}
 
/* function validateUrl(url) {
	var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	(pattern.test(url)) ? return 1 : return 0;
}  */



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

$('#ModalAddList').on('hidden.bs.modal', function (e) {
	$('#ModalAddList .modal-title').empty(  ); 
	$('#ModalAddList .modal-body').empty(  ); 
})

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
			if( json['link'] ) window.location.href= json['link'];  
		},
		error: function(xhr, ajaxOptions, thrownError) {
			 
		}
	});
}

$(document).ready(function() {
	
	$('body').on('click', 'a.analyzes', function(e){
		var question_id = $(this).attr('data-question_id');
		var token = $(this).attr('data-token');
		var obj = $(this);
		
		if( $('#analyzesList-' + question_id).hasClass('show') ) 
		{
			$('#analyzesList-' + question_id).removeClass('show').addClass('hide');
			
		}else  
		{
			$('#analyzesList-' + question_id).addClass('show').removeClass('hide');
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
				}
				if( json['total_comment'] )
				{
					$('#getcomment-' + question_id ).html( json['total_comment'] );
				}
				if( json['error'] )
				{
					alert( json['error'] );
				}
				if(typeof MathJax !== 'undefined') {MathJax.Hub.Queue(["Typeset",MathJax.Hub]);setTimeout(function(){$('.math-tex').show()}, 400)}
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
				url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
			url: nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=comment&nocache=' + new Date().getTime(),
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
 