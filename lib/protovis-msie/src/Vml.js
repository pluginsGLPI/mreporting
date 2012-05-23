var vml = {

  round: function(n){ return Math.round( n * 21.6 ); },

  styles: null,

  pre: '<v:',
  post: ' class="msvml">',

  block: { 'group':1, 'shape':1, 'shapetype':1, 'line':1,
           'polyline':1, 'curve':1, 'rect':1, 'roundrect':1,
           'oval':1, 'arc':1, 'image':1 },
  ends: { 'butt':'flat','round':'round','square':'square','flat':'flat'},
  joins: { 'bevel':'bevel','round':'round','miter':'miter'},
  cursorstyles: {
    'hand': 'pointer',
    'crosshair': 1, 'pointer': 1, 'move': 1, 'text': 1,
    'wait': 1, 'help': 1, 'progress': 1,
    'n-resize': 1, 'ne-resize': 1, 'nw-resize': 1, 's-resize': 1,
    'se-resize': 1, 'sw-resize': 1, 'e-resize': 1, 'w-resize': 1
  },

  text_shim: null,
  _textcache: {},
  text_dims: function ( text, font ) {
    if ( !(font in vml._textcache) ) {
      vml._textcache[ font ] = {};
    }
    if ( text in vml._textcache[ font ] ) {
      return vml._textcache[ font ][ text ];
    }
    var shim = vml.text_shim;
    shim.style.font = font;
    shim.innerText = text;
    return (vml._textcache[ font ][ text ] = {
      fontsize: parseInt( shim.style.fontSize, 10 ),
      height: shim.offsetHeight,
      width: shim.offsetWidth
    });
  },

  d2r: Math.PI * 2 / 360,  // is this used more than once?

  get_dim: function ( attr, target ) {
    var o = target || {};
    // reformat the most common attributes
    o.translate_x = 0;
    o.translate_y = 0;
    if ( attr.transform ) {
      var t = /translate\((\d+(?:\.\d+)?)(?:,(\d+(?:\.\d+)?))?\)/.exec( attr.transform );
      if ( t && t[1] ) { o.translate_x = parseFloat( t[1] ); }
      if ( t && t[2] ) { o.translate_y = parseFloat( t[2] ); }
      var r = /rotate\((\d+\.\d+|\d+)\)/.exec( attr.transform );
      if ( r ) { o.rotation = parseFloat( r[1] ) % 360; }
      // var scale_x = 1, scale_y = 1,
      // var s = /scale\((\d+)(?:,(\d+))?\)/i.exec( value );
      // if ( s && s[1] ) { scale[0] = parseInt( s[1], 10 ); }
      // if ( s && s[2] ) { scale[1] = parseInt( s[2], 10 ); }
    }
    o.x = parseFloat( attr.x||0 );
    o.y = parseFloat( attr.y||0 );
    if ( 'width' in attr ) {
      o.width = parseInt( attr.width, 10 );
    }
    if ( 'height' in attr ) { 
      o.height = parseInt( attr.height, 10 );
    }
    return o;
  },

  elm_defaults: {

    "g": {
      rewrite: 'span',
      attr: function ( attr, style, elm ) {
        var d = vml.get_dim( attr );
        elm.style.cssText = "position:absolute;zoom:1;left:"+
                        (d.translate_x + d.x)+"px;top:"+
                        (d.translate_y + d.y)+"px;";
      }
    },

    "line": {
      rewrite: 'shape',
      attr: function ( attr, style, elm ) {
        var x1 = parseFloat( attr.x1 || 0 ),
            y1 = parseFloat( attr.y1 || 0 ),
            x2 = parseFloat( attr.x2 || 0 ),
            y2 = parseFloat( attr.y2 || 0 ),
            r = vml.round;
        elm.coordorigin = "0,0";
        elm.coordsize = "21600,21600";
        vml.path( elm ).v = 'M '+ r(x1) + ' ' + r(y1) + ' L ' + r(x2) + ' ' + r(y2) + ' E';
        vml.stroke( elm, attr );
      },
      css: "top:0px;left:0px;width:1000px;height:1000px"
    },

    "rect": {
      rewrite: 'shape',
      attr: function ( attr, style, elm ) {
        var d = vml.get_dim( attr ),
            p = vml.path( elm ),
            r = vml.round;
        elm.coordorigin = "0,0";
        elm.coordsize = "21600,21600";
        var x = r(d.translate_x + d.x),
            y = r(d.translate_y + d.y),
            w = r(d.width),
            h = r(d.height);
        p.v = 'M ' + x + ' ' + y + 
             ' L ' + (x + w) + ' ' + y + 
             ' L ' + (x + w) + ' ' + (y + h) + 
             ' L ' + x + ' ' + (y + h) + 
             ' x';
        vml.stroke( elm, attr );
        vml.fill( elm, attr );
      },
      css: "top:0px;left:0px;width:1000px;height:1000px"
    },

    "path": {
      rewrite: 'shape',
      attr: function ( attr, style, elm ) {
        var d = vml.get_dim( attr ),
            es = elm.style;
        es.left = (d.translate_x + d.x) + "px";
        es.top = (d.translate_y + d.y) + "px";
        elm.coordorigin = "0,0";
        elm.coordsize = "21600,21600";
        vml.path( elm, attr.d );
        vml.fill( elm, attr );
        vml.stroke( elm, attr );
      },
      css: "top:0px;left:0px;width:1000px;height:1000px"
    },

    "circle": {
      /* This version of circles is crisper but seems slower
      rewrite: 'shape',
      attr: function ( attr, style, elm ) {
        var d = vml.get_dim( attr ),
            r = vml.round( parseFloat( attr.r || 0 ) ),
            cx = parseFloat( attr.cx || 0 ),
            cy = parseFloat( attr.cy || 0 ),
            es = elm.style;
        es.left = (d.translate_x + d.x + cx + 0.3) + "px";
        es.top  = (d.translate_y + d.y + cy + 0.3) + "px";
        elm.coordorigin = "0,0";
        elm.coordsize = "21600,21600";
        vml.path( elm ).v = "ar-" + r + ",-" + r + "," + r + "," + r + ",0,0,0,0x";
        vml.fill( elm, attr );
        vml.stroke( elm, attr );
      },
      css: "top:0px;left:0px;width:1000px;height:1000px"
      */
      rewrite: 'oval',
      attr: function ( attr, style, elm ) {
        var d  = vml.get_dim( attr ),
            es = elm.style,
            cx = parseFloat( attr.cx || 0 ) + 0.7,
            cy = parseFloat( attr.cy || 0 ) + 0.7,
            r  = parseFloat( attr.r  || 0 ) + 0.5;
        es.top = ( d.translate_y + cy - r ) + "px";
        es.left = ( d.translate_x + cx - r ) + "px";
        es.width = ( r * 2 ) + "px";
        es.height = ( r * 2 ) + "px";
        vml.fill( elm, attr );
        vml.stroke( elm, attr );
      }
    },

    "text": {
      rewrite: 'span'
    },

    "svg": {
      rewrite: 'span',
      css: 'position:relative;overflow:hidden;display:inline-block;~display:block;'
    },

    // this allows reuse of the createElement function for actual VML
    "vml:path": { rewrite: 'path' },
    "vml:stroke": { rewrite: 'stroke' },
    "vml:fill": { rewrite: 'fill' }

  },

  // cloning elements is a lot faster than creating them
  _elmcache: {
    'span': document.createElement( 'span' ), 
    'div': document.createElement( 'div' )
  },

  createElement: function ( type, reformat ) {
    var elm,
        cache = vml._elmcache,
        helper = vml.elm_defaults[ type ] || {};
    var tagName = helper.rewrite || type;
    if ( tagName in cache ) {
      elm = cache[ tagName ].cloneNode( false );
    }
    else {
      cache[ tagName ] = document.createElement( vml.pre + tagName + vml.post );
      if ( tagName in vml.block ) {
        cache[ tagName ].className += ' msvml_block';
      }
      elm = cache[ tagName ].cloneNode( false );
    }
    helper.css && (elm.style.cssText = helper.css);
    return elm;
  },


  // hex values lookup table
  _hex: pv.range(0,256).map(function(i){ return pv.Format.pad("0",2,i.toString(16)); }),
  _colorcache: { 'none': 'transparent' },
  color: function ( value, rgb ) {
    // TODO: deal with opacity here ?
    if ( !(value in vml._colorcache) && (rgb = /^rgb\((\d+),(\d+),(\d+)\)$/i.exec( value )) ) {
      vml._colorcache[value] = '#' + vml._hex[rgb[1]] + vml._hex[rgb[2]] + vml._hex[rgb[3]];
    }
    return vml._colorcache[ value ] || value;
  },


  fill: function ( elm, attr ) {
    var fill = elm.getElementsByTagName( 'fill' )[0];
    if ( !fill ) {
      fill = elm.appendChild( vml.createElement( 'vml:fill' ) );
    }
    if ( !attr.fill || attr.fill === 'none' ) {
      fill.on = false;
    }
    else {
      fill.on = 'true';
      fill.color = vml.color( attr.fill );
      fill.opacity = parseFloat( attr['fill-opacity'] || '1' ) || '1';
    }
  },


  stroke: function ( elm, attr ) {
    var stroke = elm.getElementsByTagName( 'stroke' )[0];
    if ( !stroke ) {
      stroke = elm.appendChild( vml.createElement( 'vml:stroke' ) );
    }
    if ( !attr.stroke || attr.stroke === 'none' ) {
      stroke.on = 'false';
      stroke.weight = '0';
    }
    else {
      stroke.on = 'true';
      stroke.weight = parseFloat( attr['stroke-width'] || '1' ) / 1.25;
      stroke.color = vml.color( attr.stroke ) || 'black';
      stroke.opacity = parseFloat( attr['stroke-opacity'] || '1' ) || '1';
      stroke.joinstyle = vml.joins[ attr['stroke-linejoin'] ] || 'miter';
    }
  },

  path: function ( elm, svgpath ) {
    var p = elm.getElementsByTagName( 'path' )[0];
    if ( !p ) {
      p = elm.appendChild( vml.createElement( 'vml:path' ) );
    }
    if ( arguments.length > 1 ) {
      p.v = vml.rewritePath( svgpath );
    }
    return p;
  },


  init: function () {
    if ( !vml.text_shim ) {
      vml.text_shim = document.getElementById('pv_vml_text_shim') || document.createElement('span');
      vml.text_shim.id = 'protovisvml_text_shim';
      vml.text_shim.style.cssText = "position:absolute;left:-9999em;top:-9999em;padding:0;margin:0;line-height:1;display:inline-block;white-space:nowrap;";
      document.body.insertBefore( vml.text_shim, document.body.firstChild );
    }
    if ( !vml.styles ) {
      vml.styles = document.getElementById('protovisvml_styles') || document.createElement("style");
      if ( vml.styles.id !== 'protovisvml_styles' ) {
        vml.styles.id = 'protovisvml_styles';
        document.documentElement.firstChild.appendChild( vml.styles );
        vml.styles.styleSheet.addRule( '.msvml', 'behavior:url(#default#VML);' );
        vml.styles.styleSheet.addRule( '.msvml_block', 'position:absolute;top:0;left:0;' );
      }
      try {
        if ( !document.namespaces.v ) { document.namespaces.add( 'v', 'urn:schemas-microsoft-com:vml' ); }
      }
      catch (e) {
        vml.pre  = '<';
        vml.post = ' class="msvml" xmlns="urn:schemas-microsoft.com:vml">';
      }
    }
  },

  // SVG->VML path conversion - This converts a SVG path to a VML path
  //
  // Things that are missing:
  //  - Multiple sets of coords. 
  //    Some commands (lineto,curveto,..) can take multiple sets of coords.
  //    Because Protovis always supplies the command between arguments, this isn't
  //    implemented, but it would be trivial to complete this.
  // - ARCs need solving
   _pathcache: {},
  rewritePath:function ( p, deb ) {
    var x = 0, y = 0, round = vml.round;

    if ( !p ) { return p; }
    if ( p in vml._pathcache ) { return vml._pathcache[p]; }

    // clean up overly detailed fractions (8.526512829121202e-148) 
    p = p.replace( /(\d*)((\.*\d*)(e ?-?\d*))/g, "$1");

    var bits = p.match( /([MLHVCSQTAZ][^MLHVCSQTAZ]*)/gi );
    var np = [], lastcurve = [];
    for ( var i=0,bl=bits.length; i<bl; i++ ) {
      var itm  = bits[i],
          op   = itm.charAt( 0 ),
          args = itm.substring( 1 ).split( /[, ]/ );

      switch ( op ) {

        case 'M':  // moveto (absolute)
          op = 'm';
          x = round( args[0] );
          y = round( args[1] );
          args = [ x, y ];
          break;
        case 'm':  // moveto (relative)
          op = 'm';
          x += round( args[0] );
          y += round( args[1] );
          args = [ x, y ];
          break;

        case "A": // TODO: arc (absolute):
          // SVG: rx ry x-axis-rotation large-arc-flag sweep-flag x y
          // VML: http://www.w3.org/TR/NOTE-VML
          /*var rx = round( args[0] ), 
              ry = round( args[1] ), 
              xrot = round( args[2] ), 
              lrg = round( args[3] ), 
              sweep = round( args[4] );*/
          op = 'l';
          args = [ (x = round( args[5] )),
                   (y = round( args[6] )) ];
          break;

        case "L": // lineTo (absolute)
          op = 'l';
          args = [ (x = round( args[0] )),
                   (y = round( args[1] )) ];
          break;
        case "l": // lineTo (relative)
          op = 'l';
          args = [ (x = x + round( args[0] )),
                   (y = y + round( args[1] )) ];
          break;

        case "H": // horizontal lineto (absolute)
          op = 'l';
          args = [ (x = round( args[0] )), y ];
          break;
        case "h": // horizontal lineto (relative)
          op = 'l';
          args = [ (x = x + round( args[0] )), y ];
          break;

        case "V": // vertical lineto (absolute)
          op = 'l';
          args = [ x, (y = round( args[0] )) ];
          break;
        case "v": // vertical lineto (relative)
          op = 'l';
          args = [ x, (y = y + round( args[0] )) ];
          break;

        case "C": // curveto (absolute)
          op = 'c';
          lastcurve = args = [
            round(args[0]), round(args[1]),
            round(args[2]), round(args[3]),
            (x = round( args[4] )),
            (y = round( args[5] ))
          ];
          break;
        case "c": // curveto (relative)
          op = 'c';
          lastcurve = args = [
            x + round(args[0]),
            y + round(args[1]),
            x + round(args[2]),
            y + round(args[3]),
            (x = x + round( args[4] )),
            (y = y + round( args[5] ))
          ];
          break;

        case "S": // shorthand/smooth curveto (absolute)
          op = 'c';
          lastcurve = args = [
            lastcurve[4] + (lastcurve[4] - lastcurve[2]),
            lastcurve[5] + (lastcurve[5] - lastcurve[3]),
            round(args[0]),
            round(args[1]),
            (x = round( args[2] )),
            (y = round( args[3] ))
          ];
          break;
        case "s":  // shorthand/smooth curveto (relative)
          op = 'c';
          lastcurve = args = [
            lastcurve[4] + (lastcurve[4] - lastcurve[2]),
            lastcurve[5] + (lastcurve[5] - lastcurve[3]),
            x + round(args[0]),
            y + round(args[1]),
            (x = x + round( args[2] )),
            (y = y + round( args[3] ))
          ];
          break;

        case "Q": // quadratic Bézier curveto (absolute)
          op = 'c';
          var x1 = round( args[0] ),
              y1 = round( args[1] ),
              x2 = round( args[2] ),
              y2 = round( args[3] );
          args = [
            ~~(x + (x1 - x) * 2 / 3),
            ~~(y + (y1 - y) * 2 / 3),
            ~~(x1 + (x2 - x1) / 3),
            ~~(y1 + (y2 - y1) / 3),
            (x = x2),
            (y = y2)
          ];
          break;
        case "q": // TODO: quadratic Bézier (relative)
          op = 'l';
          x += round( args[2] );
          y += round( args[3] );
          args = [ x, y ];
          break;

        // TODO: T/t (Shorthand/smooth quadratic Bézier curveto)

        case "Z":
        case "z":
          op = 'xe';
          args = [];
          break;

        default:
          // unsupported path command
          op = '';
          args = [];
      }
      np.push( op, args.join(',') );
    }
    return ( vml._pathcache[p] = (np.join('') + 'e') );
  }

};

