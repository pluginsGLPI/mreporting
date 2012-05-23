pv.VmlScene.wedge = function(scenes) {
  var e = scenes.$g.firstChild,
      round = vml.round;
  for (var i = 0; i < scenes.length; i++) {
    var s = scenes[i];

    // visible
    if (!s.visible) continue;
    var fill = s.fillStyle, stroke = s.strokeStyle;
    if (!fill.opacity && !stroke.opacity) continue;

    // create element sans path
    e = this.expect(e, "path", {
      "pointer-events": s.events,
      "cursor": s.cursor,
      "transform": "translate(" + s.left + "," + s.top + ")",
      "d": '', // we deal with the path afterwards
      "fill": fill.color,
      "fill-rule": "evenodd",
      "fill-opacity": fill.opacity || null,
      "stroke": stroke.color,
      "stroke-opacity": stroke.opacity || null,
      "stroke-width": stroke.opacity ? s.lineWidth / this.scale : null
    });
    
    // add path
    var p = e.getElementsByTagName( 'path' )[0];
    if ( !p ) {
      p = vml.make( 'path' );
      e.appendChild( p );
    }

    // Arc path from bigfix/protovis
    var r1 = round(s.innerRadius),
        r2 = round(s.outerRadius),
        d;
    if (s.angle >= 2 * Math.PI) {
      if (r1) {
        d = "AE0,0 " + r2 + "," + r2 + " 0 23592960"
          + "AL0,0 " + r1 + "," + r1 + " 0 23592960";
      }
      else {
        d = "AE0,0 " + r2 + "," + r2 + " 0 23592960";
      }
    }
    else {
      var sa = Math.round(s.startAngle / Math.PI * 11796480),
           a = Math.round(s.angle / Math.PI * 11796480);
      if (r1) {
        d = "AE 0,0 " + r2 + "," + r2 + " " + -sa + " " + -a
          + " 0,0 " + r1 + "," + r1 + " " + -(sa + a) + " " + a
          + "X";
      }
      else {
        d = "M0,0"
          + "AE0,0 " + r2 + "," + r2 + " " + -sa + " " + -a
          + "X";
      }
    }
    p.v = d;

    e = this.append(e, scenes, i);

  }
  return e;
};
