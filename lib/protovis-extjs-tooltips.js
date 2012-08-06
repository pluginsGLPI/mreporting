pv.Behavior.extjsTooltips = function(opts) {
  var tip;
  var tt;

  /**
   * @private When the mouse leaves the root panel, trigger a mouseleave event
   * on the tooltip span. This is necessary for dimensionless marks (e.g.,
   * lines) when the mouse isn't actually over the span.
   */
  function trigger() {
    if (tip) {
      tip.parentNode.removeChild(tip);
      tip = null;
      tt.hide();
    }
  }

  return function(d) {
      /* Compute the transform to offset the tooltip position. */
      var t = pv.Transform.identity, p = this.parent;
      do {
        t = t.translate(p.left(), p.top()).times(p.transform());
      } while (p = p.parent);

      /* Create and cache the tooltip span to be used by tipsy. */
      if (!tip) {
        var c = this.root.canvas();
        c.style.position = "relative";
        //$(c).mouseleave(trigger);

        tip = c.appendChild(document.createElement("div"));

        tip.style.position = "absolute";
        tip.style.pointerEvents = "none"; // ignore mouse events

      }

      /* Propagate the tooltip text. */
      tip.title = this.title() || this.text();

      /*
       * Compute bounding box. TODO support area, lines, wedges, stroke. Also
       * note that CSS positioning does not support subpixels, and the current
       * rounding implementation can be off by one pixel.
       */

      x = Math.floor(this.left() * t.k + t.x);
      y = Math.floor(this.top() * t.k + t.y);

      if (this.properties.width) {
        tip.style.width = Math.ceil(this.width() * t.k) + 1 + "px";
        tip.style.height = Math.ceil(this.height() * t.k) + 1 + "px";

      } else if (this.properties.shapeRadius) {
        var r = this.shapeRadius();
        t.x -= r;
        t.y -= r;
        tip.style.height = tip.style.width = Math.ceil(2 * r * t.k) + "px";

      } else if( this.properties.outerRadius){
        // Wedge
        var angle = this.endAngle() - this.angle()/2
        var radius = this.outerRadius() - (this.outerRadius() - this.innerRadius())*0.3;
        x = Math.floor(this.left() + Math.cos(angle)*radius + t.x);
        y = Math.floor(this.top() + Math.sin(angle)*radius + t.y);
      }

      tip.style.left = x + "px";
      tip.style.top = y + "px";

      /*
       * Cleanup the tooltip span on mouseout. Immediately trigger the tooltip;
       * this is necessary for dimensionless marks. Note that the tip has
       * pointer-events disabled (so as to not interfere with other mouse
       * events, such as "click"); thus the mouseleave event handler is
       * registered on the event target rather than the tip overlay.
       */
      if (tt) tt.hide();

      tip.id = 'pv_tooltip';

      
      tt = new Ext.ToolTip({
        //target: tip,
        anchor: 'top',
        anchorToTarget: false,
        html: d.nodeName,
        targetXY: getPosition(tip.id),
        maxWidth:500
      });
      tt.show();
    };
};


function getPosition(element)
{
  var left = 0;
  var top = 0;
  /*On récupère l'élément*/
  var e = document.getElementById(element);
  /*Tant que l'on a un élément parent*/
  while (e.offsetParent != undefined && e.offsetParent != null)
  {
    /*On ajoute la position de l'élément parent*/
    left += e.offsetLeft + (e.clientLeft != null ? e.clientLeft : 0);
    top += e.offsetTop + (e.clientTop != null ? e.clientTop : 0);
    e = e.offsetParent;
  }
  return new Array(left,top);
}