pv.VmlScene.label = function(scenes) {
  var e = scenes.$g.firstChild,
      round = Math.round;
  for (var i = 0; i < scenes.length; i++) {
    var s = scenes[i];

    // visible
    if (!s.visible) continue;
    var fill = s.textStyle;
    if (!fill.opacity || !s.text) continue;

    var attr = {};
    if ( s.cursor ) { attr.cursor = s.cursor; }

    // measure text
    var txt = s.text.replace( /\s+/g, '\xA0' );
    var label = vml.text_dims( txt, s.font );

    var dx = 0, dy = 0;

    if ( s.textBaseline === 'middle' ) {
      dy -= label.fontsize / 2;
    }
    else if ( s.textBaseline === 'top' ) {
      dy += s.textMargin;
    }
    else if ( s.textBaseline === 'bottom' ) {
      dy -= s.textMargin + label.fontsize;
    }

    if ( s.textAlign === 'center' ) {
      dx -= label.width / 2; 
    }
    else if ( s.textAlign === 'right' ) {
      dx -= label.width + s.textMargin; 
    }
    else if ( s.textAlign === 'left' ) {
      dx += s.textMargin; 
    }

    e = this.expect(e, "text", attr, {
      "font": s.font,
      // "text-shadow": s.textShadow,
      "textDecoration": s.textDecoration,
      'top': Math.round( s.top + dy ) + 'px',
      'left': Math.round( s.left + dx ) + 'px',
      'position': 'absolute',
      'display': 'block',
      'lineHeight': 1,
      'whiteSpace': 'nowrap',
      'zoom': 1,
      'cursor': 'default',
      'color': vml.color( fill.color ) || 'black'
    });
    e.innerText = txt;

    // Rotation is broken in serveral different ways:
    // 1. it looks REALLY ugly
    // 2. it is incredibly slow
    // 3. rotated text is offset completely wrong and it takes a ton of math to correct it
    // when text is rotated we need to switch to a VML textpath solution
    var rotation = 180 * s.textAngle / Math.PI;
    if ( rotation ) {
      var r = (~~rotation % 360) * vml.d2r,
          ct = Math.cos(r),
          st = Math.sin(r);
      e.style.filter = ['progid:DXImageTransform.Microsoft.Matrix(',
                    'M11=',  ct.toFixed( 8 ), ',',
                    'M12=', -st.toFixed( 8 ), ',',
                    'M21=',  st.toFixed( 8 ), ',',
                    'M22=',  ct.toFixed( 8 ), ',sizingMethod=\'auto expand\')";'].join('');
    }
    else {
      e.style.filter = '';
    }

    e = this.append(e, scenes, i);
  }
  return e;
};
