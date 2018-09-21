/*create custom functions to handle the animation*/
var start = function() {
        var canvas = document.getElementById('canvas');
        canvasAnimations.initBall.running = true;
        /*the startAnimation function is defined in the global-functions.js*/
        startAnimation(
            canvas,
            canvas.getContext('2d'),
            canvasAnimations.initBall.objs,
            canvasAnimations.initBall.loop
        );
    },
    stop = function() {
        canvasAnimations.initBall.running = false;
        /*the stopAnimation function is defined in the global-functions.js*/
        stopAnimation(canvasAnimations.initBall.request);
    };

window.onload = function() {
    /*create your canvas aniamtion*/
    var initBall = function(parent, append) {
        /*the hiDPICanvas function is defined in the global-functions.js*/
        var canvas = hiDPICanvas('canvas', window.innerWidth, window.innerHeight);

        if (append) {
            parent.append(canvas);
        } else {
            parent.prepend(canvas);
        }

        var can = document.getElementById('canvas'),
            ctx = can.getContext('2d');

        /*define your object*/
        var ball = {
            x: 100,
            y: 100,
            mx: 'right', // move direction x
            my: 'down', // move direction y
            ms: 5, // move speed
            radius: 25,
            color: randomRGBA(100, 200, (randomInt(0, 100) / 100)),
            draw: function(can, ctx) {
                ctx.beginPath();

                if (this.x + this.ms < (can.width / pixelRatio) && this.mx === 'right') {
                    this.x += this.ms; // move right
                } else {
                    this.mx = 'left';
                }

                if (this.x - this.ms > 0 && this.mx === 'left') {
                    this.x -= this.ms; // move left
                } else {
                    this.mx = 'right';
                }

                if (this.y + this.ms < (can.height / pixelRatio) && this.my === 'up') {
                    this.y += this.ms; // move up
                } else {
                    this.my = 'down';
                }

                if (this.y - this.ms > 0 && this.my === 'down') {
                    this.y -= this.ms; // move down
                } else {
                    this.my = 'up';
                }

                ctx.arc(this.x, this.y, this.radius, 0, (Math.PI * 2), true);
                ctx.closePath();
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        };

        var startTime = (!window.performance.now) ? new Date().getTime() : performance.now(),
            fps = 60,
            interval = fixValue(1000 / fps, 0),
            request = {
                id: null
            },
            loop = function(can, ctx, objs) {

                var currentTime = (!window.performance.now) ? new Date().getTime() : performance.now(),
                    difference = currentTime - startTime;

                if (difference >= interval) {
                    startTime = currentTime - (difference % interval);

                    /*the clearCanvas function is defined in the global-functions.js*/
                    clearCanvas(can);
                    objs.draw(can, ctx);
                }

                request.id = startAnimation(can, ctx, objs, loop);

                canvasAnimations.initBall.can = can;
                canvasAnimations.initBall.ctx = ctx;
                canvasAnimations.initBall.objs = objs;
                canvasAnimations.initBall.request = request.id;
                canvasAnimations.initBall.running = true;
            },
            events = function(can, ctx, objs, loop) {
                /*add your events*/
                can.addEventListener('mousemove', function(event) {
                    if (!canvasAnimations.initBall.running) {
                        clearCanvas(can);
                        objs.x = event.clientX;
                        objs.y = event.clientY;
                        objs.draw(can, ctx);
                    }
                });

                can.addEventListener('click', function() {
                    if (!canvasAnimations.initBall.running) {
                        canvasAnimations.initBall.running = true;
                        startAnimation(can, ctx, objs, loop);
                    } else {
                        canvasAnimations.initBall.running = false;
                        stopAnimation(canvasAnimations.initBall.request);
                    }
                });
            };

        /*its important to set your values in the global canvasAnimations object*/
        canvasAnimations.initBall = {
            can: can,
            ctx: ctx,
            objs: ball,
            loop: loop,
            request: request.id,
            events: events,
            running: false,
        };

        /*trigger your events*/
        events(can, ctx, ball, loop);

    };

    /*define the parent and set prepend or append <- true*/
    initBall(document.body, true);

    var buttonStart = document.getElementById('button-start');
    buttonStart.addEventListener('click', start);

    var buttonStop = document.getElementById('button-stop');
    buttonStop.addEventListener('click', stop);

    /*if you destroy the canvas (e.g remove) don't forget to register your events again!*/

};

window.onresize = function() {

    if (document.getElementById('canvas') !== null) {

        var canvas = document.getElementById('canvas');

        /*global handling*/
        canvasAnimations.initBall.running = false;
        /*the stopAnimation function is defined in the global-functions.js*/
        stopAnimation(canvasAnimations.initBall.request);

        hiDPICanvas('canvas', window.innerWidth, window.innerHeight);
        canvasAnimations.initBall.obj.draw(canvas, canvas.getContext('2d'));
    }

};
