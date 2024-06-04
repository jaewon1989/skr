mxPanningHandler.prototype.tx = 0;
mxPanningHandler.prototype.ty = 0;

(function(original) {
	mxPanningHandler.prototype.start = function (me) {
		original.call(this, me);
		
		this.startX += this.tx;
		this.startY += this.ty;
	};
})(mxPanningHandler.prototype.start);

(function(original) {
	mxPanningHandler.prototype.mouseUp = function (sender, me) {
		original.call(this, sender, me);
		
		this.tx = 0;
		this.ty = 0;
	};
})(mxPanningHandler.prototype.mouseUp);
