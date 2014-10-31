// mostly the same code as pv.SvgScene.panel, but with less MSIE crashing...
pv.VmlScene.panel = function(scenes) {
  var g = scenes.$g, e = g && g.firstChild;
  for (var i = 0; i < scenes.length; i++) {
    var s = scenes[i];

    /* visible */
    if (!s.visible) continue;

    /* svg */
    if (!scenes.parent) {
      s.canvas.style.display = "inline-block";
      s.canvas.style.zoom = 1;
      if (g && (g.parentNode != s.canvas)) {
        g = s.canvas.firstChild;
        e = g && g.firstChild;
      }
      if ( !g ) {
        vml.init(); // turn VML on if it isn't allready
        g = s.canvas.appendChild( vml.createElement( "svg" ) );
        for (var j = 0; j < this.events.length; j++) {
          g.addEventListener
              ? g.addEventListener(this.events[j], this.dispatch, false)
              : g.attachEvent("on" + this.events[j], this.dispatch);
        }
        e = g.firstChild;
      }
      scenes.$g = g;
      var w = (s.width + s.left + s.right),
          h = (s.height + s.top + s.bottom);
      g.style.width  = w + 'px';
      g.style.height = h + 'px';
      g.style.clip = "rect(0px " + w + "px " + h + "px 0px)";
    }

    /* fill */
    e = this.fill( e, scenes, i );

    /* transform (push) */
    var k = this.scale,
        t = s.transform,
        x = s.left + t.x,
        y = s.top + t.y;
    this.scale *= t.k;

    /* children */
    for (var j = 0; j < s.children.length; j++) {
      s.children[j].$g = e = this.expect(e, "g", {
          "transform": "translate(" + x + "," + y + ")" + (t.k != 1 ? " scale(" + t.k + ")" : "")
        });
      this.updateAll(s.children[j]);
      if ( !e.parentNode || e.parentNode.nodeType === 11 ) {
        g.appendChild(e);
        var helper = vml.elm_defaults[ e.svgtype ];
        if ( helper && typeof helper.onappend === 'function' ) {
          helper.onappend( e, scenes[i] );
        }
      }
      e = e.nextSibling;
    }

    /* transform (pop) */
    this.scale = k;

    /* stroke */
    e = this.stroke( e, scenes, i );

  }
  return e;
};


pv.VmlScene.fill = function(e, scenes, i) {
  var s = scenes[i], fill = s.fillStyle;
  if (fill.opacity || s.events == "all") {
    e = this.expect(e, "div", {}, {
        "cursor": s.cursor,
        "left": s.left,
        "top": s.top,
        "width": s.width,
        "height": s.height,
        "border": 'none',
        "background": vml.color( fill.color ),
        'position': 'absolute'
      });
    e = this.append(e, scenes, i);
  }
  return e;
};


pv.VmlScene.stroke = function(e, scenes, i) {
  var s = scenes[i], stroke = s.strokeStyle;
  if (stroke.opacity || s.events == "all") {
    var linew = Math.round(s.lineWidth / this.scale);
    e = this.expect(e, "div", {}, {
        "cursor": s.cursor,
        "left": s.left - (linew/2),
        "top": s.top - (linew/2),
        "width": Math.max(1E-10, s.width) - linew,
        "height": Math.max(1E-10, s.height) - linew,
        "border": linew + 'px solid ' + vml.color( stroke.color ),
        'zoom': 1,
        'position': 'absolute'
      });
    e = this.append(e, scenes, i);
  }
  return e;
};
