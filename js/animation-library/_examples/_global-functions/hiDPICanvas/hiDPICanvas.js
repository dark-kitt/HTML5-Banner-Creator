var pixelRatio = (function() {
    var canvas = document.createElement('canvas').getContext('2d'),
        dpr = window.devicePixelRatio || 1,
        bsr = canvas.webkitBackingStorePixelRatio ||
        canvas.mozBackingStorePixelRatio ||
        canvas.msBackingStorePixelRatio ||
        canvas.oBackingStorePixelRatio ||
        canvas.backingStorePixelRatio || 1;
    return dpr / bsr;
})(),
hiDPICanvas = function(id, width, height, pixelRatio) {
    var canvas;
    if (document.getElementById(id)) {
        canvas = document.getElementById(id);
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        canvas.getContext('2d').setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    } else {
        canvas = document.createElement('canvas');
        if (canvas !== null) {
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            canvas.width = width * pixelRatio;
            canvas.height = height * pixelRatio;
            canvas.setAttribute('id', id);
            canvas.getContext('2d').setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
            return canvas;
        }
    }
},
clearCanvas = function(can) {
    ctx = can.getContext('2d');
    ctx.clearRect(0, 0, can.width, can.height);
},
startAnimation = function(can, ctx, objs, loop, request, events) {
    return window.requestAnimationFrame(function() {
        loop(can, ctx, objs, request, events);
    });
},
stopAnimation = function(req) {
    window.cancelAnimationFrame(req);
};
