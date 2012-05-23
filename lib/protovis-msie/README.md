# Protovis MSIE-VML compatibility layer

A compatibility layer for [Protovis][pv] that adds support for [VML][vml] compatible browsers (Internet Explorer 7 and 8).

## How to use this:

As you include Protovis as you normally would, include protovis-msie.js __after__ it:

    <script src="protovis.min.js"></script>
    <script src="protovis-msie.min.js"></script>

The software should kick in on browsers that support VML, but don't support SVG, and procede to translate the visualization into VML.

There is no harm in including the file on other browsers but you can still choose to conditionally include the MSIE support:

    <!--[if lte IE 8]><script src="protovis-msie.min.js"></script><![endif]-->

This saves non-VML browsers the trouble of downloading the code.


## What can it do

The shim can translate lines, areas, panels, rules, labels and most basic things into VML at a fairly acceptable running speed. It has trouble with massive/complicated visualizations but most simple charts work fine.


## Where does it fail?

Things known not to work are:

* Polar interpolation for lines is missing.
* Rotated labels are incorrectly positioned.
* Label shadow is missing.
* Label text does not support opacity.
* Zoom property is not supported.

The VML layer works quite well for visualizations that are static or not overly complex. However MSIE versions before 9 are slow and it will likely never be viable to run large and complex animated Protovis visualizations on those browsers.

Other than that you will simply have to experiment.



[pv]: http://mbostock.github.com/protovis/
[vml]: http://www.w3.org/TR/NOTE-VML
