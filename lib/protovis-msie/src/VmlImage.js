
//
pv.VmlScene.image = function(scenes) {
  var e = scenes.$g.firstChild;
  for (var i = 0; i < scenes.length; i++) {
    var s = scenes[i];

    /* visible */
    if (!s.visible) continue;

    /* fill */
    e = this.fill(e, scenes, i);

    /* image */
    if ( s.image ) {
      // There is no canvas support in MSIE
    }
    else {
      e = new Image();
      e.src = s.url;
      var st = e.style;
      st.position = 'absolute';
      st.top = s.top;
      st.left = s.left;
      st.width = s.width;
      st.height = s.height;
      st.cursor = s.cursor;
      st.msInterpolationMode = 'bicubic';
    }
    e = this.append(e, scenes, i);

    /* stroke */
    e = this.stroke(e, scenes, i);
  }
  return e;
};


