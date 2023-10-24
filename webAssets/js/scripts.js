(function($) {
    'use strict';

    //======================
    // Preloder
    //======================
    $(window).on('load', function() {
        $('#preloader').fadeOut();
    });


    //======================
    // Mobile menu 
    //======================
    $('#mobile-menu-toggler').on('click', function(e) {
        e.preventDefault();
        $('nav.navbar > ul').slideToggle();
    })
    $('.coupon-btn').on('click', function(e) {
        e.preventDefault();
        $('#couponBox').slideToggle();
    })
    $('.has-menu-child').append('<i class="menu-dropdown ti-angle-down"></i>');
    $('.langbtn').on('click', function(e) {


    })
    $('.langbtn').on('click', function(e) {
        if ($(this).find('i').hasClass('ti-angle-down')) {
            $(this).find('i').removeClass('ti-angle-down')
            $(this).find('i').addClass('ti-angle-up')
            console.log('wo');
        } else {
            $(this).find('i').addClass('ti-angle-down')
            $(this).find('i').removeClass('ti-angle-up')
        }
        $('ul.list-unstyled.dropdown-menu').slideToggle();
        e.preventDefault();
    });
    setTimeout(function() {
        $('.profile-btn').on('click', function(e) {
            $('.profile-drop').slideToggle();
            e.preventDefault();
        });
    }, 1000)

    $('.mbtn').on('click', function(e) {
        e.preventDefault();
    });

    if ($('nav').length > 0) {
        $('nav').coreNavigation({
            menuPosition: "right",
            container: true,
            animated: false,
            animatedIn: 'fadeIn',
            animatedOut: 'fadeOut',
            responsideSlide: true,
            mode: 'sticky',
            dropdownEvent: 'hover'
        });
    }
    // if ($('.header-3').length > 0) {
    //     $('.header-3').coreNavigation({
    //         layout: 'sidebar-toggle',  //default, brand-center, fullscreen, sidebar, sidebar-toggle, section, side-icon
    //         // menuPosition: "right",
    //         container: true,
    //         responsideSlide: true,
    //         mode: 'fixed',
    
    //     });
    // }



    function mobileProfile() {
        if ($(window).width() < 992) {

            if ($('.profile-rcv *').length === 0) {
                $('.dropdown.profile').clone().appendTo('.profile-rcv');
                $('.wrap-core-nav-list .dropdown.profile').remove();
            } else {}
        }
    }
    $(window).on('resize load', function() {
        mobileProfile()
    });



    //======================
    // Custom filter script
    //======================
    var modalbtn = $('.view-plans');
    $(modalbtn).on('click', function() {
        $('.modalBody').show();
        $('.modalBody').show();
        $('body').addClass('modal-open');
    })

    $('.close-button').on('click', function() {
        $('.modalBody').hide();
        $('body').removeClass('modal-open');
    })

    $('.op-logo').on('click', function() {
        $('.op-logo').removeClass('selected');
        $(this).addClass('selected');
    })

    var flteritems = $('.data-menu');
    var dataPakage = $('.data-pakage');
    $(flteritems).on('click', function() {
        $(flteritems).removeClass('active');
        $(this).addClass('active');
        $(dataPakage).hide();
        var data = $(this).attr('data-value');
        $('.' + data).show();

    })

    //======================
    // More Item function
    //======================
    function moreItem(selector, receiver) {
        var tele = document.getElementById(selector),
            rec = document.getElementById(receiver);
        // window.onresize = resize;
        // resize();

        $(window).on('resize', function() {
            resize();
        })
        resize();

        function resize() {
            const rChildren = rec.children;
            let numW = 0;

            [...rChildren].forEach(item => {
                item.outHTML = '';
                tele.appendChild(item);
            })

            const teleW = tele.offsetWidth,
                tChildren = tele.children;

            [...tChildren].forEach(item => {
                numW += item.offsetWidth;

                if (numW > teleW) {
                    item.outHTML = '';
                    rec.appendChild(item);
                }
            });
        }
    }

    if ($('#teleporter').length > 0) {
        moreItem('teleporter', 'receiver')
    }
    // moreItem('teleporter2', 'receiver2')

    //======================
    // Partners carousel
    //======================
    function sliderHeight() {
        var sliderContainer = $('.owl-stage-outer').height();
        var slideHeightLg = sliderContainer + 0;
        var slideHeightSm = sliderContainer + 0;

        function sliderHeightre() {
            var windowWidght = $(window).width();
            if (windowWidght > 992) {
                $('.hero-slider .item').css('height', slideHeightLg)
            } else {
                $('.hero-slider .item').css('height', slideHeightSm)
            }
        }
        $(window).on('resize load', function() {
            sliderHeightre()
        });
        // console.log(sliderContainer);

    }
 sliderHeight();
    setTimeout(function(){
        sliderHeight();
    },100)

    $('.hero-slider').owlCarousel({
        autoplay: true,
        dots: false,
        animateOut: 'fadeOut',
        items: 4,
        loop: true,
        margin: 1,
        nav: false,
        smartSpeed: 500,
        responsive: {
            0: {
                items: 1
            },
            400: {
                items: 1
            },
            768: {
                items: 1
            }
        }
    });

    //======================
    // Offer carousel
    //======================
    function offerCaro(selector) {
        var quantity = $(selector).attr('data-slide');
        var navicon = $(selector).data('return');

        $(selector).owlCarousel({
            autoplay: false,
            dots: true,
            items: quantity,
            loop: true,
            margin: 10,
            nav: navicon,
            smartSpeed: 1000,
            responsive: {
                0: {
                    items: 1
                },
                400: {
                    items: 2
                },
                768: {
                    items: 2
                },
                992: {
                    items: quantity
                }
            }
        });
    }
    offerCaro('.fc-1');
    offerCaro('.fc-2');
    offerCaro('.fc-3');


    //======================
    // Partners carousel
    //======================
    $('.partners-caro').owlCarousel({
        autoplay: true,
        dots: false,
        items: 4,
        loop: true,
        margin: 1,
        nav: false,
        smartSpeed: 500,
        responsive: {
            0: {
                items: 2,
                margin: 10
            },
            400: {
                items: 3,
                margin: 20
            },
            768: {
                items: 4,
                margin: 30
            }
        }
    });

    //======================
    // Testimonial
    //======================
    $('.test-caro').owlCarousel({
        dots: false,
        items: 1,
        nav: false,
        margin: 30,
        navText: ['<i class="ti-arrow-left"></i>', '<i class="ti-arrow-right"></i>'],
        smartSpeed: 500,
        responsive: {
            992: {
                items: 2
            }

        }
    });

    // ====================================
    //  Common carousel - owl carousel
    // ====================================
    $(".cmn-carousel").owlCarousel({
        autoplay: false,
        loop: true,
        dots: false,
        nav: true,
        // navText: ['<i class="ti-arrow-left"></i>', '<i class="ti-arrow-right"></i>'],
        margin: 10,
        responsive: {
            992: {
                items: 4
            },
            576: {
                nav: false,
                items: 2
            },
            0: {
                nav: false,
                items: 1
            }
        }
    });

    function tabFunction(mainArea, batton, tabContent, id) {
        $(mainArea + ' ' + batton).first().addClass('active');
        $(mainArea + ' ' + batton).click(function() {
            var t = $(this).data(id);
            if ($(this).hasClass('active')) {
                $(mainArea + ' ' + batton).addClass('inactive');
            } else {
                $(mainArea + ' ' + batton).removeClass('active');
                $(this).addClass('active');
                /*content*/
                $(tabContent).removeClass('active');
                $('#' + t + 'C').addClass('active')
            }
        });

        var owlS = $(mainArea);
        owlS.owlCarousel();
        owlS.on('changed.owl.carousel', function(event) {
            setTimeout(function() {
                var activeB = $(mainArea + ' .owl-item.active').first().find(batton).data(id);

                /*TAB BUTTON*/
                $(mainArea + ' ' + batton).removeClass('active');
                $("[data-tag=" + activeB + "]").addClass('active');

                /*CONTENT*/
                $(tabContent).removeClass('active');
                $('#' + activeB + 'C').addClass('active');

            }, 100);
        })

        var block = $('.caroBtn');
        var maxHeight = 0;

        $(block).each(function() {
            if ($(this).height() > maxHeight) {
                maxHeight = $(this).height();
            }
        });
        // console.log(maxHeight);
        $(block).height(maxHeight);
    }
    tabFunction(".cmn-carousel", ".caroBtn", ".tcontent", "tag");



    if ($(window).width() <= 991) {
        $('.menu-dropdown').on('click', function() {
            $(this).prev().slideToggle('slow');
            $(this).toggleClass('ti-angle-down ti-angle-up')
        })



    }

    //======================
    // TOOLTIP
    //======================
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    //======================
    // COUNTER
    //======================
    if ($('#counter').length > 0) {
        var a = 0;
        $(window).scroll(function() {

            var oTop = $('#counter').offset().top - window.innerHeight;
            if (a == 0 && $(window).scrollTop() > oTop) {
                $('.counter-value').each(function() {
                    var $this = $(this),
                        countTo = $this.attr('data-count');
                    $({
                        countNum: $this.text()
                    }).animate({
                            countNum: countTo
                        },

                        {

                            duration: 2000,
                            easing: 'swing',
                            step: function() {
                                $this.text(Math.floor(this.countNum));
                            },
                            complete: function() {
                                $this.text(this.countNum);
                                //alert('finished');
                            }

                        });
                });
                a = 1;
            }

        });
    }

    //======================
    // Clock
    //======================
    if ($('#clock').length > 0) {
        $('#clock').countdown('2020/10/10').on('update.countdown', function(event) {
            var $this = $(this).html(event.strftime('' +
                '<p>week%!w <span>%-w</span> </p>' +
                '<p>day%!d <span>%-d</span> </p>' +
                '<p>hr <span>%H</span> </p>' +
                '<p>min <span>%M</span> </p>' +
                '<p>sec <span>%S</span> </p>'));
        });
    }
    /*
     * accordion
     * */
    var pbtn = $('.pbtn');
    $(pbtn).on('click', function(e) {
        $(pbtn).removeClass('disabled')
        $(this).addClass('disabled')
        var battr = $(this).attr('data-id');
        $('.accord').slideUp();
        $('#' + battr).slideDown();
        e.preventDefault();
    });
    $('.close').on('click', function(e) {
        $(pbtn).removeClass('disabled')
        $('.accord').slideUp();
        e.preventDefault();
    });
    $('.load-more-btn').on('click', function(e) {
        e.preventDefault();
        $(this).toggleClass('active')
        $('.trancstion-more').slideToggle();
        $(this).find('i').toggleClass('fa-chevron-up');
        if($(this).hasClass('active')){
            $(this).find('span').text('Show less');
        }else{
            $(this).find('span').text('Show more');
        }
    });

    if ($('#birthDate').length > 0) {
        $('#birthDate').daterangepicker({
            singleDatePicker: true,
            "showDropdowns": true,
            autoUpdateInput: false,
            maxDate: moment().add(0, 'days'),
        }, function(chosen_date) {
            $('#birthDate').val(chosen_date.format('MM-DD-YYYY'));
        });
    }

    /*
     * date range
     * */

    if ($('#custom-date').length > 0) {
        $(function() {
            var start = moment().subtract(29, 'days');
            var end = moment();

            function customDateFunction(start, end) {
                $('#custom-date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#custom-date').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, customDateFunction);
            customDateFunction(start, end);
        });
    }

    if ($('#paymentDue').length > 0) {
        $('#paymentDue').daterangepicker({
            singleDatePicker: true,
            minDate: moment(),
            autoUpdateInput: false,
        }, function(chosen_date) {
            $('#paymentDue').val(chosen_date.format('MM-DD-YYYY'));
        });
    }


    //======================
    // GOOGLE MAP SCRIPT
    //======================
    function init() {
        var locations = [
            ['Baridhara', 23.803164, 90.419431, 4],
            ['Gulshan', 23.779812, 90.417055, 5],
            ['Badda', 23.793321, 90.442621, 3],
            ['Mirpur', 23.810912, 90.360432, 2],
            ['Uttara', 23.875147, 90.400267, 1],
            ['Motijil', 23.726422, 90.421692, 6]
            // ['Parramatta River', -33.821770, 151.079346, 7]
        ];

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: new google.maps.LatLng(23.810331, 90.412521),
            mapTypeId: google.maps.MapTypeId.ROADMAP,


            styles: [{
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#193341"
                }]
            }, {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#2c5a71"
                }]
            }, {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#29768a"
                }, {
                    "lightness": -37
                }]
            }, {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#406d80"
                }]
            }, {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#406d80"
                }]
            }, {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "on"
                }, {
                    "color": "#3e606f"
                }, {
                    "weight": 2
                }, {
                    "gamma": 0.84
                }]
            }, {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#ffffff"
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry",
                "stylers": [{
                    "weight": 0.6
                }, {
                    "color": "#1a3541"
                }]
            }, {
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#2c5a71"
                }]
            }]
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: '../images/marker.png',
            });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }


    }
    if ($('#map').length > 0) {
        google.maps.event.addDomListener(window, 'load', init);
    }


    //======================
    // RANGE SLIDER
    //======================

    (function($) {
        "use strict";
        var DEBUG = false, // make true to enable debug output
            PLUGIN_IDENTIFIER = "RangeSlider";

        var RangeSlider = function(element, options) {
            this.element = element;
            this.options = options || {};
            this.defaults = {
                output: {
                    prefix: '', // function or string
                    suffix: 'M', // function or string
                    format: function(output) {
                        return output;
                    }
                },
                change: function(event, obj) {}
            };
            // This next line takes advantage of HTML5 data attributes
            // to support customization of the plugin on a per-element
            // basis.
            this.metadata = $(this.element).data('options');
        };

        RangeSlider.prototype = {

            ////////////////////////////////////////////////////
            // Initializers
            ////////////////////////////////////////////////////

            init: function() {
                if (DEBUG && console) console.log('RangeSlider init');
                this.config = $.extend(true, {}, this.defaults, this.options, this.metadata);

                var self = this;
                // Add the markup for the slider track
                this.trackFull = $('<div class="track track--full"></div>').appendTo(self.element);
                this.trackIncluded = $('<div class="track track--included"></div>').appendTo(self.element);
                this.inputs = [];

                $('input[type="range"]', this.element).each(function(index, value) {
                    var rangeInput = this;
                    // Add the ouput markup to the page.
                    rangeInput.output = $('<output>').appendTo(self.element);
                    // Get the current z-index of the output for later use
                    rangeInput.output.zindex = parseInt($(rangeInput.output).css('z-index')) || 1;
                    // Add the thumb markup to the page.
                    rangeInput.thumb = $('<div class="slider-thumb">').prependTo(self.element);
                    // Store the initial val, incase we need to reset.
                    rangeInput.initialValue = $(this).val();
                    // Method to update the slider output text/position
                    rangeInput.update = function() {
                        if (DEBUG && console) console.log('RangeSlider rangeInput.update');
                        var range = $(this).attr('max') - $(this).attr('min'),
                            offset = $(this).val() - $(this).attr('min'),
                            pos = offset / range * 100 + '%',
                            transPos = offset / range * -100 + '%',
                            prefix = typeof self.config.output.prefix == 'function' ? self.config.output.prefix.call(self, rangeInput) : self.config.output.prefix,
                            format = self.config.output.format($(rangeInput).val()),
                            suffix = typeof self.config.output.suffix == 'function' ? self.config.output.suffix.call(self, rangeInput) : self.config.output.suffix;

                        // Update the HTML
                        $(rangeInput.output).html(prefix + '' + format + '' + suffix);
                        $(rangeInput.output).css('left', pos);
                        $(rangeInput.output).css('transform', 'translate(' + transPos + ',0)');

                        // Update the IE hack thumbs
                        $(rangeInput.thumb).css('left', pos);
                        $(rangeInput.thumb).css('transform', 'translate(' + transPos + ',0)');

                        // Adjust the track for the inputs
                        self.adjustTrack();
                    };

                    // Send the current ouput to the front for better stacking
                    rangeInput.sendOutputToFront = function() {
                        $(this.output).css('z-index', rangeInput.output.zindex + 1);
                    };

                    // Send the current ouput to the back behind the other
                    rangeInput.sendOutputToBack = function() {
                        $(this.output).css('z-index', rangeInput.output.zindex);
                    };

                    ///////////////////////////////////////////////////
                    // IE hack because pointer-events:none doesn't pass the 
                    // event to the slider thumb, so we have to make our own.
                    ///////////////////////////////////////////////////
                    $(rangeInput.thumb).on('mousedown', function(event) {
                        // Send all output to the back
                        self.sendAllOutputToBack();
                        // Send this output to the front
                        rangeInput.sendOutputToFront();
                        // Turn mouse tracking on
                        $(this).data('tracking', true);
                        $(document).one('mouseup', function() {
                            // Turn mouse tracking off
                            $(rangeInput.thumb).data('tracking', false);
                            // Trigger the change event
                            self.change(event);
                        });
                    });

                    // IE hack - track the mouse move within the input range
                    $('body').on('mousemove', function(event) {
                        // If we're tracking the mouse move
                        if ($(rangeInput.thumb).data('tracking')) {
                            var rangeOffset = $(rangeInput).offset(),
                                relX = event.pageX - rangeOffset.left,
                                rangeWidth = $(rangeInput).width();
                            // If the mouse move is within the input area
                            // update the slider with the correct value
                            if (relX <= rangeWidth) {
                                var val = relX / rangeWidth;
                                $(rangeInput).val(val * $(rangeInput).attr('max'));
                                rangeInput.update();
                            }
                        }
                    });

                    // Update the output text on slider change
                    $(this).on('mousedown input change touchstart', function(event) {
                        if (DEBUG && console) console.log('RangeSlider rangeInput, mousedown input touchstart');
                        // Send all output to the back
                        self.sendAllOutputToBack();
                        // Send this output to the front
                        rangeInput.sendOutputToFront();
                        // Update the output
                        rangeInput.update();
                    });

                    // Fire the onchange event 
                    $(this).on('mouseup touchend', function(event) {
                        if (DEBUG && console) console.log('RangeSlider rangeInput, change');
                        self.change(event);
                    });

                    // Add this input to the inputs array for use later
                    self.inputs.push(this);
                });

                // Reset to set to initial values
                this.reset();

                // Return the instance
                return this;
            },

            sendAllOutputToBack: function() {
                $.map(this.inputs, function(input, index) {
                    input.sendOutputToBack();
                });
            },

            change: function(event) {
                if (DEBUG && console) console.log('RangeSlider change', event);
                // Get the values of each input
                var values = $.map(this.inputs, function(input, index) {
                    return {
                        value: parseInt($(input).val()),
                        min: parseInt($(input).attr('min')),
                        max: parseInt($(input).attr('max'))
                    };
                });

                // Sort the array by value, if we have 2 or more sliders
                values.sort(function(a, b) {
                    return a.value - b.value;
                });

                // call the on change function in the context of the rangeslider and pass the values
                this.config.change.call(this, event, values);
            },

            reset: function() {
                if (DEBUG && console) console.log('RangeSlider reset');
                $.map(this.inputs, function(input, index) {
                    $(input).val(input.initialValue);
                    input.update();
                });
            },

            adjustTrack: function() {
                if (DEBUG && console) console.log('RangeSlider adjustTrack');
                var valueMin = Infinity,
                    rangeMin = Infinity,
                    valueMax = 0,
                    rangeMax = 0;

                // Loop through all input to get min and max
                $.map(this.inputs, function(input, index) {
                    var val = parseInt($(input).val()),
                        min = parseInt($(input).attr('min')),
                        max = parseInt($(input).attr('max'));

                    // Get the min and max values of the inputs
                    valueMin = (val < valueMin) ? val : valueMin;
                    valueMax = (val > valueMax) ? val : valueMax;
                    // Get the min and max possible values
                    rangeMin = (min < rangeMin) ? min : rangeMin;
                    rangeMax = (max > rangeMax) ? max : rangeMax;
                });

                // Get the difference if there are 2 range input, use max for one input.
                // Sets left to 0 for one input and adjust for 2 inputs
                if (this.inputs.length > 1) {
                    this.trackIncluded.css('width', (valueMax - valueMin) / (rangeMax - rangeMin) * 100 + '%');
                    this.trackIncluded.css('left', (valueMin - rangeMin) / (rangeMax - rangeMin) * 100 + '%');
                } else {
                    this.trackIncluded.css('width', valueMax / (rangeMax - rangeMin) * 100 + '%');
                    this.trackIncluded.css('left', '0%');
                }
            }
        };

        RangeSlider.defaults = RangeSlider.prototype.defaults;

        $.fn.RangeSlider = function(options) {
            if (DEBUG && console) console.log('$.fn.RangeSlider', options);
            return this.each(function() {
                var instance = $(this).data(PLUGIN_IDENTIFIER);
                if (!instance) {
                    instance = new RangeSlider(this, options).init();
                    $(this).data(PLUGIN_IDENTIFIER, instance);
                }
            });
        };

    })(jQuery);


    var rangeSlider = $('#length, #price');
    if (rangeSlider.length > 0) {
        rangeSlider.RangeSlider({
            output: {
                format: function(output) {
                    return output.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                },
                suffix: function(input) {
                    return parseInt($(input).val()) == parseInt($(input).attr('max')) ? this.config.maxSymbol : '';
                }
            }
        });
    }


    var bsbtn = $('.bstn');
    var aSearch = $('.a-search');
    $(bsbtn).on('click', function(e) {
        $(bsbtn).removeClass('active');
        $(this).addClass('active')
        e.preventDefault();
    });
    $(aSearch).on('click', function(e) {
        $('.advance-search-area').slideToggle()
        e.preventDefault();
    });




    /*
     *
     * COMPARE
     *
     * */
    var tiems = $('.p-body li');
    var itembox = $('.p-body');
    $(tiems).on('mouseenter', function() {
        var index = $(this).index();
        $('.p-body li').css('background', 'transparent')
        var i;
        for (i = 0; i < itembox.length; i++) {
            $('.p-body').eq(i).find('li').eq(index).css('background', '#f3f3f3')
        }
    })
    $(tiems).on('mouseout', function() {
        $('.p-body li').css('background', 'transparent')
    })

    /*
     *
     * TITLE BAR ANIMATION
     *
     * */
    if ($('.page-feature').length > 0) {
        setTimeout(function() {
            var x = 0;
            var y = 0;
            var banner = jQuery(".page-feature");
            banner.css('backgroundPosition', x + 'px');
            window.setInterval(function() {
                banner.css("backgroundPosition", x + 'px');
                x--;
                // y++;
            }, 70);

        }, 1000);
    }

    /*
     *
     * ISOTOPE IN Portfolio
     *
     * */
    $(window).on('load', function() {
        //CHECK THE GRID
        if ($('.portGrid').length > 0) {
            var $container = $('.portGrid');
            $container.isotope({
                filter: '*',
                animationOptions: {
                    duration: 750,
                    easing: 'linear',
                    queue: false
                }
            });

            // Fillter button for Mobile
            function filltermbButton() {
                var buttonTxt = $('.is-checked').text();
                var mbutton = $("<button class='filterMbBtn ti-angle-down'></button>").text(buttonTxt);
                // $('.filters').before(mbutton);   
                function ftbtn() {
                    var mbuttonClass = $('.filterMbBtn')
                    if ($(window).width() < 992) {
                        $('.button-group').css('display', 'none');
                        $(mbuttonClass).show();
                        if ($('.filters').hasClass('mb-disable')) {
                            $('.filters').before(mbutton);
                            $('.filterMbBtn').on('click', function() {
                                if ($(this).hasClass('ti-angle-down')) {
                                    $(this).removeClass('ti-angle-down')
                                    $(this).addClass('ti-angle-up')
                                    console.log('wo');
                                } else {
                                    $(this).addClass('ti-angle-down')
                                    $(this).removeClass('ti-angle-up')
                                }
                                $('.button-group').slideToggle();
                            });
                        }
                        $('.filters').removeClass('mb-disable')
                    } else {
                        $(mbuttonClass).hide();
                        $('.button-group').css('display', 'flex');
                    }
                }
                ftbtn()
                $(window).on('resize', function() {
                    ftbtn()
                });
            }
            filltermbButton();


            $('.filters button').on('click', function() {
                $('.filters .is-checked').removeClass('is-checked');
                $(this).addClass('is-checked');
                var buttonTxt = $(this).text();
                $('.filterMbBtn').text(buttonTxt)

                var selector = $(this).attr('data-filter');
                $container.isotope({
                    filter: selector,
                    animationOptions: {
                        duration: 750,
                        easing: 'linear',
                        queue: false
                    }
                });
                return false;
            });
        }

    });


    if ($('#mobilenumber').length > 0) {
        $("#mobilenumber").intlTelInput({
            separateDialCode: true,
            customContainer: "input-lg", //Add this(input-lg) css class When use large input(form-control-lg).
            preferredCountries: ["us", "bd", "gb"], // the countries at the top of the list.
        });
        $("#mobilenumber2").intlTelInput({
            separateDialCode: true,
            customContainer: "input-number2", //Add this(input-lg) css class When use large input(form-control-lg).
            preferredCountries: ["us", "bd", "gb"], // the countries at the top of the list.
        });

    }


    $('.part-icon, .single-payment').on('click', function(e) {
        $('.part-icon, .single-payment').removeClass('selected');
        $(this).addClass('selected');
        e.preventDefault();
    })
    $('.pakbtn').on('click', function(e) {
        $('.pakbtn').removeClass('selected');
        $(this).addClass('selected');
        e.preventDefault();
    })

    /*
     *
     * SWITCHER CONFIG
     *
     * */
    var imported = document.createElement('script');
    var styleE = document.createElement('link');
    imported.src = 'inc/switcher/js/switcher.js';
    styleE.href = 'inc/switcher/css/switcher.css';
    styleE.rel = 'stylesheet';
    document.head.appendChild(imported);
    document.head.appendChild(styleE);


})(jQuery)