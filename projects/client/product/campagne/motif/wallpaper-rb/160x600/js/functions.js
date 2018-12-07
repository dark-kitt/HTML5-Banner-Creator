window.onload = function() {

    var unusedCSS = gid('unsued_js');

    var counter = 0,
        repeat = 10000,
        maxLoops = 1;

    var play = function() {

        fade({
            id: 'sequence-one',
            delay: 5000,
            fps: 120,
            calcStep: 0.02,
            direction: 'out',
        });

        fade({
            id: 'sequence-two',
            delay: 5300,
            fps: 120,
            calcStep: 0.02,
            direction: 'in',
        });

        animationTimeout(function() {
            addClass(gid('cta'), {
                class: 'cta-ani',
                remove: 2000
            });
        }, 6000);

        counter++;
        if (counter <= maxLoops) {
                setTimeout(function(){

                    fade({
                        id: 'sequence-one',
                        delay: 0,
                        fps: 120,
                        calcStep: 0.02,
                        direction: 'in',
                    });

                    fade({
                        id: 'sequence-two',
                        delay: 300,
                        fps: 120,
                        calcStep: 0.02,
                        direction: 'out',
                    });

                    play();
                }, repeat);
            }
        };
    play();

};
