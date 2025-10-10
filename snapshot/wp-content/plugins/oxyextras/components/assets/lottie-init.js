jQuery(document).ready(oxygen_init_lottie);
function oxygen_init_lottie($) {

    var config = {
        root: null,
        rootMargin: '0px 0px 300px 0px',
        threshold: 1
    };

    function callback(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {

                let $this = $(entry.target),
                    $lottie = $this.children('lottie-player'),
                    $lottieID = $lottie.attr('id'),
                    $lottieJSON = $lottie.data('src'),
                    $lottieDom = $lottie[0],
                    $trigger = $lottie.data('trigger'),
                    frameStart = $lottie.data('start'),
                    frameEnd = $lottie.data('end'),
                    $maybe_reverse = $lottie.data('reverse');

                if ($lottieJSON !== undefined) {
                    $lottieDom.load($lottieJSON);
                }

                let $lottieInt = $lottieDom.getLottie();

                if ($trigger === 'scroll') {

                    let containerSelector = $lottie.data('container');

                    if ($lottie.data('offset') == '1') {
                        offsetBottom = ($lottie.data('offset-bottom') / 100);
                        offsetTop = (1 - ($lottie.data('offset-top') / 100));
                    } else {
                        offsetBottom = '0';
                        offsetTop = '1';
                    }

                    LottieInteractivity.create({
                        mode: 'scroll',
                        player: '#' + $lottieID,
                        container: containerSelector,
                        actions: [{
                                visibility: [0, offsetBottom],
                                type: 'stop',
                                frames: [frameStart]
                            },
                            {
                                visibility: [offsetBottom, offsetTop],
                                type: 'seek',
                                frames: [frameStart, frameEnd]
                            },
                            {
                                visibility: [offsetTop, 1],
                                type: 'stop',
                                frames: [frameEnd]
                            },
                        ]

                    });

                }

                if ($trigger === 'cursor') {

                    let cursorSelector = $lottie.data('cursor-container');

                    LottieInteractivity.create({
                        mode: 'cursor',
                        player: '#' + $lottieID,
                        container: cursorSelector,
                        actions: [{
                            position: {
                                x: [0, 1],
                                y: [0, 1]
                            },
                            type: 'seek',
                            frames: [frameStart, frameEnd],
                        }, ],
                    });

                }

                if ($trigger === 'mouseover') {

                    let mouseoverSelector = $lottie.data('mouseover-container');

                    $lottieInt.goToAndStop(frameStart, true);

                    $(mouseoverSelector).hover(function() {

                        $lottieInt.playSegments([
                            [frameStart, frameEnd]
                        ], true);

                    }, function() {
                        $lottieInt.setDirection(-1);
                        $lottieInt.play();
                    });

                }

                if ($trigger === 'click') {

                    let triggerSelector = $lottie;

                    if ($lottie.data('click-trigger') === 'self') {
                        triggerSelector = $lottie;
                    } else {
                        triggerSelector = $($lottie.data('click-trigger'));
                    }

                    $lottieDom.seek(frameStart);

                    if ($maybe_reverse === true) {

                        triggerSelector.click(function(e) {
                            $lottieDom = !$lottieDom;
                            if ($lottieDom) {
                                $lottieInt.playSegments([
                                    [frameEnd, frameStart]
                                ], true);

                            } else {
                                $lottieInt.playSegments([
                                    [frameStart, frameEnd]
                                ], true);
                            }
                        });

                    } else {

                        triggerSelector.click(function(e) {
                            $lottieInt.playSegments([
                                [frameStart, frameEnd]
                            ], true);
                        })

                    }

                }

                observer.unobserve(entry.target);
            }
        })
    };

    var observer = new IntersectionObserver(callback, config);
    var counters = document.querySelectorAll('.oxy-lottie-animation');
    counters.forEach(counter => {
        observer.observe(counter);
    });

}