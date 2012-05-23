// Much of the event rewriting code is copyed and watered down
// from the jQuery library's event hander. We have the luxury
// of knowing that we're on MSIE<9 so we can despense with some
// fixes for other browsers.
(function(){
    
  var returnTrue  = function () { return true;  };
  var returnFalse = function () { return false; };
  var _event_props = ["altKey","attrChange","attrName","bubbles","button",
                      "cancelable","charCode","clientX","clientY","ctrlKey",
                      "currentTarget","data","detail","eventPhase","fromElement",
                      "handler","keyCode","layerX","layerY","metaKey",
                      "newValue","offsetX","offsetY","pageX","pageY","prevValue",
                      "relatedNode","relatedTarget","screenX","screenY",
                      "shiftKey","srcElement","target","toElement","view","wheelDelta","which"];

  function IEvent ( src ) {
    if ( src && src.type ) {
      this.originalEvent = src;
      this.type = src.type;
      this.isDefaultPrevented = returnFalse;
      if (src.defaultPrevented || src.returnValue === false || src.getPreventDefault && src.getPreventDefault()) {
        this.isDefaultPrevented = returnTrue;
      }
    }
    else {
      this.type = src;
    }
    this.timeStamp = Date.now();
  }
  IEvent.prototype = {
    preventDefault: function() {
      this.isDefaultPrevented = returnTrue;
      var e = this.originalEvent;
      if ( !e ) { return; }
      // if preventDefault exists run it on the original event
      if ( e.preventDefault ) {
        e.preventDefault();
        // otherwise set the returnValue property of the original event to false (IE)
      }
      else {
        e.returnValue = false;
      }
    },
    stopPropagation: function() {
      this.isPropagationStopped = returnTrue;

      var e = this.originalEvent;
      if ( !e ) {
        return;
      }
      // if stopPropagation exists run it on the original event
      if ( e.stopPropagation ) {
        e.stopPropagation();
      }
      // otherwise set the cancelBubble property of the original event to true (IE)
      e.cancelBubble = true;
    },
    stopImmediatePropagation: function() {
      this.isImmediatePropagationStopped = returnTrue;
      this.stopPropagation();
    },
    isDefaultPrevented: returnFalse,
    isPropagationStopped: returnFalse,
    isImmediatePropagationStopped: returnFalse
  };


  vml.fixEvent = function ( ev ) {

    // store a copy of the original event object
    // and "clone" to set read-only properties
    var originalEvent = ev;
    ev = new IEvent( originalEvent );

    for (var i=0,l=_event_props.length; i<l; i++) {
      var prop = _event_props[i];
      ev[ prop ] = originalEvent[ prop ];
    }

    // Fix target property, if necessary
    if ( !ev.target ) {
      ev.target = ev.srcElement || document;
    }

    // Add relatedTarget, if necessary
    if ( !ev.relatedTarget && ev.fromElement ) {
      ev.relatedTarget = (ev.fromElement === ev.target)
                ? ev.toElement
                : ev.fromElement;
    }

    // Calculate pageX/Y if missing and clientX/Y available
    if ( ev.pageX == null && ev.clientX != null ) {
      var doc = document.documentElement,
         body = document.body;
      ev.pageX = ev.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);
      ev.pageY = ev.clientY + (doc && doc.scrollTop  || body && body.scrollTop  || 0) - (doc && doc.clientTop  || body && body.clientTop  || 0);
    }

    // Add which for key events
    if ( ev.which == null && (ev.charCode != null || ev.keyCode != null) ) {
      ev.which = ev.charCode != null
              ? ev.charCode
              : ev.keyCode;
    }

    // Add metaKey to non-Mac browsers (use ctrl for PC's and Meta for Macs)
    if ( !ev.metaKey && ev.ctrlKey ) {
      ev.metaKey = ev.ctrlKey;
    }

    // Add which for click: 1 === left; 2 === middle; 3 === right
    // Note: button is not normalized, so don't use it
    if ( !ev.which && ev.button !== undefined ) {
      ev.which = (ev.button & 1 ? 1 : ( ev.button & 2 ? 3 : ( ev.button & 4 ? 2 : 0 ) ));
    }

    // Mousewheel delta
    if ( ev.type === "mousewheel" ) {
      ev.wheel = ev.wheelDelta;
    }

    return ev;
  }

})();



// replace the listener with something a little more elaborate
pv.listener = function(f, target) {
  return f.$listener || (f.$listener = function(e) {
    try {
      pv.event = vml.fixEvent( e || window.event );
      return f.call( this, pv.event );
    }
    catch (e) {
      pv.error(e);
    }
    finally {
      delete pv.event;
    }
  });
};


pv.listen = function(target, type, listener) {
  listener = pv.listener(listener, target);
  if ( target === window ) {
    target = document.documentElement;
  }
  return target.addEventListener
      ? target.addEventListener(type, listener, false)
      : target.attachEvent("on" + type, listener);
};



pv.VmlScene.dispatch = pv.listener(function(e) {
  var t = e.target.$scene;
  if ( t && pv.Mark.dispatch(e.type, t.scenes, t.index) ) {
    e.preventDefault();
  }
});
