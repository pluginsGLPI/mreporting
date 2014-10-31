pv.VmlScene = {
  
  // The pre-multipled scale, based on any enclosing transforms.
  scale: 1,

  // The set of supported events.
  events: [
    "mousewheel",
    "mousedown",
    "mouseup",
    "mouseover",
    "mouseout",
    "mousemove",
    "click",
    "dblclick"
  ],

  // implicit values are not used for VML, assigned render faster and we have
  // no desire to keep the DOM clean here - only to make it work!
  implicit: { css: {} },

  copy_functions: function ( obj ) {
    for ( var name in obj ) {
      if ( typeof obj[name] === 'function' && !(name in pv.VmlScene) ) {
        pv.VmlScene[ name ] = obj[ name ];
      }
    }
  }

};


// copy helper methods from SvgScene onto our new Scene
pv.VmlScene.copy_functions( pv.SvgScene );
pv.Scene = pv.VmlScene;


pv.VmlScene.expect = function (e, type, attr, style) {
  style = style || {};
  var helper = vml.elm_defaults[type] || {}, 
      _type = helper.rewrite || type;

  if ( e ) {
    if ( e.tagName.toUpperCase() !== _type.toUpperCase() ) {
      var n = vml.createElement( type );
      e.parentNode.replaceChild( n, e );
      e = n;
    }
  }
  else {
    e = vml.createElement( type );
  }
  
  if ( 'attr' in helper ) {
    helper.attr( attr, style, e );
  }

  if ( attr.cursor in vml.cursorstyles ) {
    var curs = vml.cursorstyles[attr.cursor];
    style.cursor = ( curs === 1 ) ? attr.cursor : curs;
  }

  for (var name in style) {
    var value = style[name];
    if (value == null) e.style.removeAttribute(name);   // cssText 
    else e.style[name] = value;
  }
  
  return e;
};


pv.VmlScene.append = function(e, scenes, index) {
  // FIXME: hooks the scene onto the element --- this is probably hemorrhaging memory in MSIE
  // it is only ever used by the envent displatcher so it should probably be stored in a cache
  e.$scene = {scenes:scenes, index:index};
  // attach a title to element
  e = this.title(e, scenes[index]);
  if ( !e.parentNode || e.parentNode.nodeType === 11 ) {  // 11 == documentFragment
    scenes.$g.appendChild( e );
  }
  return e.nextSibling;
};


pv.VmlScene.title = function(e, s) {
  e.title = s.title || "";
  return e;
};



