/*!
 * Protovis MSIE/VML addon
 * Copyright (C) 2011 by DataMarket <http://datamarket.com>
 * Dual licensed under the terms of the MIT or GPL Version 2 software licenses.
 * 
 * This software includes code from jQuery, http://jquery.com/
 * jQuery is licensed under the MIT or GPL Version 2 license.
 * 
 * This software includes code from the Protovis, http://mbostock.github.com/protovis/
 * Protovis is licensed under the BSD license.
 * 
 */

// detect SVG support
pv.have_SVG = !!( 
  document.createElementNS && 
  document.createElementNS( 'http://www.w3.org/2000/svg', 'svg' ).createSVGRect 
);

// detect VML support
pv.have_VML = (function (d,a,b) {
  a = d.createElement('div');
  a.innerHTML = '<v:shape adj="1" />';
  b = a.firstChild;
  b.style.behavior = 'url(#default#VML)';
  return b ? typeof b.adj === 'object' : true;
})(document);

// MSIE does not support indexOf on arrays
if ( !Array.prototype.indexOf ) {
  Array.prototype.indexOf = function (s, from) {
    var n = this.length >>> 0,
        i = (!isFinite(from) || from < 0) ? 0 : (from > this.length) ? this.length : from;
    for (; i < n; i++) { if ( this[i] === s ) { return i; } }
    return -1;
  };
}

// only run if we need to
if ( !pv.have_SVG && pv.have_VML ){(function(){

if ( typeof Date.now !== 'function' ) {
  Date.now = function () { return new Date() * 1; };
}


