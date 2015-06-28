/* global jQuery, ecwd, ecwd_calendar */

/**
 * Public JS functions
 */
(function ($) {
    'use strict';
    var cwidth = $(".calendar_main .ecwd_calendar").width();
    var view_click = 1;
    var click = 1;
	$('a[href="#ecwd-modal-preview"]').click(function(){
		setTimeout(function () {
			showFilterSliderArrow();
		}, 1);
	});
    setTimeout(function () {
        show_filters(0);
        showMiniCalendarEventOnHover();
        showFullCalendarEventOnHover();
        showWidgetEventDesc();
        calendarFullResponsive();
        showFilterSliderArrow();
        createSearchForm();
        upcomingEventsSlider();
        doMasonry();
    }, 1);

    if ($('.ecwd_map_div').length > 0) {
        showMap();
    }
    jQuery('.ecwd_reset_filters').click(function () {
        $(this).closest('.ecwd_filters').find('input:checkbox').attr('checked', false);
    });

    jQuery('body').on('click', '.ecwd_calendar_prev_next .next, .ecwd_calendar_prev_next .previous, .ecwd_calendar .type, .cpage, .current-month a, .ecwd_filter, .ecwd_reset_filters', function (e) {
        var days = $('input[name="ecwd_weekdays[]"]:checked').map(function () {
            return this.value;
        }).get();
        var cats = $('input[name="ecwd_categories[]"]:checked').map(function () {
            return this.value;
        }).get();
        var tags = $('input[name="ecwd_tags[]"]:checked').map(function () {
            return this.value;
        }).get();
        var venues = $('input[name="ecwd_venues[]"]:checked').map(function () {
            return this.value;
        }).get();
        var organizers = $('input[name="ecwd_organizers[]"]:checked').map(function () {
            return this.value;
        }).get();
        var el = $(this);
        if (!$(this).is(':checkbox')) {
            e.preventDefault();
        }
        var navLink = $(this);
        if ((!navLink.attr('href') || navLink.attr('href') == 'undefined') && !navLink.is(':checkbox') && navLink.attr('class') != 'ecwd_reset_filters') {
            navLink = $(this).find('a');
        }
        var main_div = navLink.closest('.calendar_main');

        var calendar_ids_class = $(main_div).find('div.ecwd_calendar').find('div:first-child').attr('class').split('-');
        var display = $(main_div).find('div.ecwd_calendar').attr('class').split(' ')[0].split('-')[2];
        var calendar_ids = calendar_ids_class[2];
        var query = $(main_div).find('input.ecwd-search').val();
        var tag = $('.ecwd_tags').val();
        var venue = $('.ecwd_venues').val();
        var organizer = $('.ecwd_organizers').val();
        var category = $('.ecwd_categories').val();
        var displays = $(main_div).find('.ecwd_displays').val();
        var date = $(main_div).find('.ecwd_date').val();
        var page_items = $(main_div).find('.ecwd_page_items').val();
        var event_search = $(main_div).find('.event_search').val();
        var filters = $(main_div).find('.ecwd_filters').val();
        $(main_div).find('.ecwd_loader').show();
        jQuery.post(ecwd.ajaxurl, {
            action: 'ecwd_ajax',
            ecwd_calendar_ids: calendar_ids,
            ecwd_link: navLink.attr('href'),
            ecwd_type: calendar_ids_class[1],
            ecwd_query: query,
            ecwd_weekdays: days,
            ecwd_categories: cats,
            ecwd_tags: tags,
            ecwd_venues: venues,
            ecwd_organizers: organizers,
            ecwd_category: category,
            ecwd_tag: tag,
            ecwd_venue: venue,
            ecwd_organizer: organizer,
            ecwd_displays: displays,
            ecwd_prev_display: display,
            ecwd_page_items: page_items,
            ecwd_event_search: event_search,
            ecwd_filters: filters,
            ecwd_date: 1,
            ecwd_date_filter: date,
            ecwd_nonce: ecwd.ajaxnonce
        }, function (data) {
            $(main_div).find('div.ecwd_calendar').replaceWith(data);
            showMiniCalendarEventOnHover();
            showFullCalendarEventOnHover();
            upcomingEventsSlider();
            showWidgetEventDesc();
            calendarFullResponsive();
            show_filters(main_div);
            showFilterSliderArrow();
            createSearchForm();
            if ($('.ecwd_map_div').length > 0) {
                showMap();
            }
            if ($('.ecwd-poster-board').length > 0) {
                doMasonry();
            }


        })
        e.stopPropagation();
    });


    function createSearchForm() {
        var scinpt = document.getElementById("ecwd-search-submit");
        if (scinpt !== null) {
            //scinpt.addEventListener('focus', doSearch, false);
        }
        jQuery('.ecwd-search').on("keyup", function (e) {
            if (e.keyCode == 13) {
                doSearch(this);
            }
        });
        jQuery('.ecwd-search-submit').on("focus", function (e) {
            doSearch(this);
        });

        jQuery('.ecwd-tag-container .ecwd-dropdown-menu > div').click(function (e) {
            $('.ecwd_tags').val($(this).attr("data-term-tag"));
            doSearch(this);
        });
        jQuery('.ecwd-category-container .ecwd-dropdown-menu > div').click(function (e) {
            $('.ecwd_categories').val($(this).attr("data-term-category"));
            doSearch(this);
        });
        jQuery('.ecwd-venue-container .ecwd-dropdown-menu > div').click(function (e) {
            $('.ecwd_venues').val($(this).attr("data-term-venue"));
            doSearch(this);
        });
        jQuery('.ecwd-organizer-container .ecwd-dropdown-menu > div').click(function (e) {
            $('.ecwd_organizers').val($(this).attr("data-term-organizer"));
            doSearch(this);
        });
    }


    function doSearch(el) {

        var main_div = $(el).closest('.calendar_main');
        var navLink = $(main_div).find('.previous').find('a');
        var query = $(main_div).find('input.ecwd-search').val();
        var tag = $(main_div).find('.ecwd_tags').val();
        var venue = $(main_div).find('.ecwd_venues').val();
        var organizer = $(main_div).find('.ecwd_organizers').val();
        var category = $(main_div).find('.ecwd_categories').val();
        var calendar_ids_class = $(main_div).find('div.ecwd_calendar').find('div:first-child').attr('class').split('-');
        var calendar_ids = calendar_ids_class[2];
        var displays = $(main_div).find('.ecwd_displays').val();
        var page_items = $(main_div).find('.ecwd_page_items').val();
        var event_search = $(main_div).find('.event_search').val();
        var filters = $(main_div).find('.ecwd_filters').val();
        $(main_div).find('.ecwd_loader').show();
        $.post(ecwd.ajaxurl, {
            action: 'ecwd_ajax',
            ecwd_query: query,
            ecwd_category: category,
            ecwd_tag: tag,
            ecwd_venue: venue,
            ecwd_organizer: organizer,
            ecwd_displays: displays,
            ecwd_filters: filters,
            ecwd_page_items: page_items,
            ecwd_link: navLink.attr('href'),
            ecwd_calendar_ids: calendar_ids,
            ecwd_event_search: event_search,
            ecwd_date: 0,
            ecwd_type: calendar_ids_class[1],
            ecwd_nonce: ecwd.ajaxnonce
        }, function (data) {
            $(main_div).find('div.ecwd_calendar').replaceWith(data);
            showFullCalendarEventOnHover();
            upcomingEventsSlider();
            showWidgetEventDesc();
            calendarFullResponsive();
            show_filters(main_div);
            showFilterSliderArrow();
            createSearchForm();
            if ($('.ecwd_map_div').length > 0) {
                showMap();
            }
            if ($('.ecwd-poster-board').length > 0) {
                doMasonry();
            }

        });
        $('.ecwd-search-submit').blur();
    }

    function showMiniCalendarEventOnHover() {
    };

    var ulEvent, day;
    var ulEventFull, dayFull;
    jQuery('body').on('click', 'div.ecwd_calendar .has-events', function (e) {
        dayFull = $(this).attr('data-date').split('-');
        dayFull = dayFull[2];
        ulEventFull = $(this).find('ul.events');
        if (parseInt($(this).closest('.ecwd_calendar').width()) <= 300 || parseInt($(window).width()) <= 768 || $(this).closest('.ecwd_calendar').hasClass('ecwd-widget-mini')) {
            if (dayFull == $(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').attr('data-dayNumber')
                && $(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').is(':empty') == false) {
                $(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').html('');
            } else {
                showEvent(ulEventFull, this);
            }
            $(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').attr('data-dayNumber', dayFull);
        }
    });

    function showEvent(el, calendar) {
        if (el.parent().parent().parent().parent().attr('class').indexOf("full") != -1) {
            $(calendar).closest('.ecwd_calendar').find('.ecwd-events-day-details').html(el.find('.event-details').clone().css('display', 'block'));
        } else if (el.parent().parent().parent().parent().attr('class').indexOf("mini") != -1) {
            $(calendar).closest('.ecwd_calendar').find('.ecwd-events-day-details').html(el.clone());
        }

    }

    function showFullCalendarEventOnHover() {
        if (parseInt($(window).width()) >= 768) {
            $('div.ecwd-page-full .has-events ul.events:not(.more_events) > li:not(.ecwd-calendar-more-event)').on('mouseover', function (e) {
               //if()
                if($(window).width()-e.clientX>400){
					$(this).find('ul.events').show();
					$(this).find('div.event-details-container').show();
               } else{
			        $(this).find('ul.events').show();
					$(this).find('div.event-details-container').show();
			        $(this).find('div.event-details-container').css({
					    left:"auto",
					    right:parseInt($(this).width())+25
					});
					$(this).find('div.event-details-container .ecwd-event-arrow').css("display","none");
					$(this).find('div.event-details-container .ecwd-event-arrow-right').css("display","block");
			   }
            });

            $('div.ecwd-page-full .has-events ul.events:not(.more_events) > li:not(.ecwd-calendar-more-event)').on('mouseout', function (e) {
                $(this).find('div.event-details-container').hide();
            });
        }

        $('div.ecwd-page-full .has-events ul.more_events > li').on('click', function (e) {
		    $('div.ecwd-page-full .has-events ul.more_events > li').find('.event-details-container').slideUp();
            if($(this).find('.event-details-container').is(":visible"))
				$(this).find('.event-details-container').slideUp();
            else
				$(this).find('.event-details-container').slideDown();
        });
    }



    $('.ecwd-show-map-span').click(function () {
        $('.ecwd-show-map').show();
    });


    jQuery(window).resize(function () {
        jQuery(".ecwd-poster-board").masonry("reload");
        view_click = 0;
        cwidth = jQuery(".ecwd_calendar").width();
        jQuery('.ecwd_calendar').find('.ecwd-events-day-details').html('');
        upcomingEventsSlider();
        calendarFullResponsive();
        show_filters();
        showFilterSliderArrow();

    });

    function doMasonry() {
        var $container = $('.ecwd-poster-board');
        if($container.length && $('.ecwd-poster-board').find('.ecwd-poster-item').length>0) {
            $container.imagesLoaded(function () {
                $container.masonry({
                    itemSelector: '.ecwd-poster-item'
                });
            });
        }

    }

    function showFilterSliderArrow() {
        var li_position, li_width, last_child;
        $(".calendar_main:not([class^='ecwd_widget'] .calendar_main) .ecwd_calendar_view_tabs").each(function (key, element) {
            var cwidth = $(element).closest('.ecwd_calendar').width();
			//console.log(cwidth);
            if (cwidth == 0)
                cwidth = 600;
            if ($(this).find('.ecwd-search').length != 0)
                var ecwd_calendar_view_tabs_width = parseInt(cwidth) - 50;
            else
                var ecwd_calendar_view_tabs_width = parseInt(cwidth);
            if (parseInt(jQuery('body').width()) <= 768 || $(".calendar_full_content .ecwd_calendar").width() < 600) {
                var ecwd_calendar_view_visible_count = parseInt(ecwd_calendar_view_tabs_width / 110);
            } else if (parseInt(jQuery('body').width()) <= 500 || $(".calendar_full_content .ecwd_calendar").width() < 400) {
                var ecwd_calendar_view_visible_count = parseInt(ecwd_calendar_view_tabs_width / 90);
            } else {
                var ecwd_calendar_view_visible_count = parseInt(ecwd_calendar_view_tabs_width / 150);
            }
            $(element).find('.filter-container').width(ecwd_calendar_view_tabs_width);
            //var ecwd_view_item_width = $(element).find('.filter-container ul li').eq(0).width();
            var ecwd_view_item_width = ecwd_calendar_view_tabs_width/ecwd_calendar_view_visible_count;

            $(element).find('ul li').each(function (keyli, elementli) {
                if ($(elementli).hasClass('ecwd-selected-mode')) {
                    li_position = keyli;
                    li_width = $(elementli).width();
                }
            });

            if (!($(element).find('.filter-container').width() + (ecwd_view_item_width / 2.5) < ecwd_view_item_width * parseInt($(element).find('.filter-container ul li').length) && !($(element).find("ul li:nth-child(" + ($(element).find('.filter-container ul li').length) + ")").hasClass("ecwd-selected-mode"))))

                $(element).find('.filter-arrow-right').hide();
            else {
                $(element).find('.filter-arrow-right').show();
            }
            if($(element).find("ul li:last-child").hasClass("ecwd-selected-mode")) {
                last_child = 1;
            }

            if ($(element).find(".filter-arrow-right").css("display") == "block" || last_child == 1)
                $(element).find('.filter-container ul li').width((ecwd_calendar_view_tabs_width - 30) / ecwd_calendar_view_visible_count);
            else
                $(element).find('.filter-container ul li').width((ecwd_calendar_view_tabs_width) / ecwd_calendar_view_visible_count);
            if (ecwd_calendar_view_visible_count <= li_position && li_position!=0) {
                $(element).find('ul li').css({left: "-" + ((li_position + 1 - ecwd_calendar_view_visible_count) * $(element).find('.filter-container ul li').eq(0).width()) + "px"});
                $(element).find('.filter-arrow-left').show();
            }else
			    $(element).find('ul li').css({left: "0px"});
        });


        $('.ecwd_calendar_view_tabs .filter-arrow-right').click(function () {
            var view_filter_width = $(this).parent().find('ul li').eq(0).width();
            var view_filter_container_width = $(this).parent().width();
            var view_filter_count = parseInt($(this).parent().find('ul li').length);
            var events_item_width = $(this).parent().find('ul li').eq(0).width();
            if ($(this).parent().find('.filter-arrow-left').css('display') == 'none')
                $(this).parent().find('.filter-arrow-left').show();
            if (parseInt($(this).parent().find('ul li').css('left')) <= -(view_filter_width * (view_filter_count) - view_filter_container_width) + view_filter_width)
                $(this).hide();
            if (click && view_filter_container_width < view_filter_width * view_filter_count && parseInt($(this).parent().find('ul li').css('left')) >= -(view_filter_width * (view_filter_count) - view_filter_container_width)) {
                click = 0;
                $(this).parent().find('ul li').animate({left: "-=" + view_filter_width}, 400, function () {
                    click = 1
                });
            }

        });
        $('.ecwd_calendar_view_tabs .filter-arrow-left').click(function () {
            var view_filter_width = $(this).parent().find('ul li').eq(0).width();
            if ($(this).parent().find('.filter-arrow-right').css('display') == 'none')
                $(this).parent().find('.filter-arrow-right').show();
            if (parseInt($(this).parent().find('ul li').css('left')) == -view_filter_width)
                $(this).hide();
            if (click && parseInt($(this).parent().find('ul li').css('left')) < 0) {
                click = 0;
                $(this).parent().find('ul li').animate({left: "+=" + view_filter_width}, 400, function () {
                    click = 1
                });
            }

        });
    }

    function upcomingEventsSlider() {
        var current_date = Date.parse(Date());

        var upcoming_events_slider_main = $('.upcoming_events_slider').width();
        $('.upcoming_events_slider .upcoming_events_item').width(upcoming_events_slider_main);


        $('.upcoming_events_slider .upcoming_event_container').width(parseInt(upcoming_events_slider_main) - 80);
        $('.upcoming_events_slider > ul').width(upcoming_events_slider_main * $('.upcoming_events_slider .upcoming_events_item').length);

        if ($(".upcoming_events_slider").width() < $('.upcoming_events_slider > ul').width()) {
            $('.upcoming_events_slider .upcoming_events_slider-arrow-right').show();
        }


        var min = 0;
        $('.upcoming_events_slider .upcoming_events_item').each(function () {
            var item_date =  Date.parse($(this).data('date'));
            if(item_date<current_date){
                min++;
            }else{
                return false;
            }
        });

        if(min && min == $('.upcoming_events_slider .upcoming_events_item').length){
            min--;
        }

        $('.upcoming_events_slider .upcoming_events_item').css('left', -upcoming_events_slider_main*min);

        if (parseInt($('.upcoming_events_slider .upcoming_events_item').css('left'))< 0){
            $('.upcoming_events_slider').parent().find('.upcoming_events_slider-arrow-left').show();
        }
        if(parseInt($('.upcoming_events_slider .upcoming_events_item').css('left'))==-($('.upcoming_events_slider > ul').width()-upcoming_events_slider_main)){
            $('.upcoming_events_slider').parent().find('.upcoming_events_slider-arrow-right').hide();
        }

        $('.upcoming_events_slider .upcoming_events_slider-arrow-right').click(function () {
            var events_item_width = $(this).parent().find('ul li').eq(0).width();

            var events_item_count = parseInt($(this).parent().find('ul li').length);
            if ($(this).parent().find('.upcoming_events_slider-arrow-left').css('display') == 'none')
                $(this).parent().find('.upcoming_events_slider-arrow-left').show();
            if (click && upcoming_events_slider_main < events_item_width * events_item_count && parseInt($(this).parent().find('ul li').css('left')) >= -(events_item_width * (events_item_count) - upcoming_events_slider_main)) {
                click = 0;
                $(this).parent().find('ul li').animate({left: "-=" + events_item_width}, 400, function () {
                    click = 1
                });
            }
            if (parseInt($(this).parent().find('ul li').css('left')) <= -($('.upcoming_events_slider > ul').width() - (2 * events_item_width)))
                $(this).hide();
        });
        $('.upcoming_events_slider .upcoming_events_slider-arrow-left').click(function () {
            var events_item_width = $(this).parent().find('ul li').eq(0).width();
            if ($(this).parent().find('.upcoming_events_slider-arrow-right').css('display') == 'none')
                $(this).parent().find('.upcoming_events_slider-arrow-right').show();
            if (parseInt($(this).parent().find('ul li').css('left')) == -events_item_width)
                $(this).hide();
            if (click && parseInt($(this).parent().find('ul li').css('left')) < 0) {
                click = 0;
                $(this).parent().find('ul li').animate({left: "+=" + events_item_width}, 400, function () {
                    click = 1
                });
            }

        });
    }
    function showWidgetEventDesc() {
        $('.ecwd-widget-mini .event-container, .ecwd-widget-mini .ecwd_list .event-main-content').each(function () {
            if ($(this).find('.arrow-down').length == 0) {
                $(this).find('.ecwd-list-date-cont').append("<span class='arrow-down'>&nbsp</span>");
                $(this).find('.ecwd-list-date-cont').after("<div class='event_dropdown_cont'></div>");
                $(this).find('.event_dropdown_cont').append($(this).children(".event-venue,.event-content, .event-organizers"));

                $(this).find('.arrow-down').click(function () {
                    if ($(this).hasClass('open')) {
                        $(this).parent().parent().find('.event_dropdown_cont').slideUp(400);
                        $(this).removeClass('open');
                    } else {
                        $(this).parent().parent().find('.event_dropdown_cont').slideDown(400);
                        $(this).addClass('open');
                    }
                });
            }
        })
    }

    function calendarFullResponsive() {
        if ($(window).width() <= 500) {
            $('div[class^="ecwd-page"] .event-container, div[class^="ecwd-page"] .ecwd_list .event-main-content').each(function () {
                if ($(this).find('.arrow-down').length == 0) {
                    var content = $(this).find('.event-content').html();
                    if ($(this).hasClass("event-container")) {
                        $(this).find('.event-content').html($(this).find('.ecwd-list-img').html() + content);
                        $(this).find('.ecwd-list-img').remove();
                    } else {
                        var content = $(this).find('.event-content').html();
                        $(this).find('.event-content').html($(this).prev().html() + content);
                        $(this).prev().remove();

                    }
                    $(this).find('.ecwd-list-date-cont').append("<span class='arrow-down'>&nbsp</span>");
                    $(this).find('.ecwd-list-date-cont').after("<div class='event_dropdown_cont'></div>");
                    $(this).find('.event_dropdown_cont').append($(this).children(".event-venue,.event-content, .event-organizers"));
                    $(this).find('.arrow-down').each(function () {
                        $(this).click(function () {
                            if ($(this).hasClass('open')) {
                                $(this).parent().parent().find('.event_dropdown_cont').slideUp(400);
                                $(this).removeClass('open');
                            } else {
                                $(this).parent().parent().find('.event_dropdown_cont').slideDown(400);
                                $(this).addClass('open');
                            }
                        });
                    })
                }
            })
        } else if (jQuery(window).width() > 500) {
            $('div[class^="ecwd-page"] .event-container, div[class^="ecwd-page"] .ecwd_list .event-main-content').each(function () {
                if ($(this).find('.arrow-down').length != 0) {
                    //  $(this).css('height','auto');
                    if ($(this).hasClass("event-container")) {
                        $(this).find('.event-title').before('<div class="ecwd-list-img"><div class="ecwd-list-img-container">' + $(this).find('.ecwd-list-img-container').html() + '</div></div>');
                        $(this).find('.event-content .ecwd-list-img-container').remove();
                        $(this).find('.ecwd-list-date-cont').after($(this).find('.event_dropdown_cont').html());
                        $(this).find('.event_dropdown_cont').remove();
                    } else {
                        $(this).parent().find('.ecwd-list-date.resp').after('<div class="ecwd-list-img"><div class="ecwd-list-img-container">' + $(this).find('.ecwd-list-img-container').html() + '</div></div>');
                        $(this).find('.event-content .ecwd-list-img-container').remove();
                        $(this).find('.ecwd-list-date-cont').after($(this).find('.event_dropdown_cont').html());
                        $(this).find('.event_dropdown_cont').remove();
                    }

                    $(this).find('.arrow-down').remove();
                }
            })

        }

    }
    setTimeout(function () {
        if (parseInt($('body').width()) <= 768 || $(".calendar_full_content").width() <= 550) {
            $('.calendar_main').each(function (k, v) {
                $(this).find('.ecwd_show_filters').click(function () {
                    if ($(this).find('span').hasClass('open')) {
                        $(this).find('span').html($('.ecwd_show_filters_text').val());
                        $(this).next().hide();
                        $(this).find('span').removeClass('open');
                    } else {
                        $(this).find('span').html($('.ecwd_hide_filters_text').val());
                        $(this).next().show();
                        $(this).find('span').addClass('open');
                    }
                });
            });
        } else {
            $('.calendar_main').each(function () {
                $(this).find('.ecwd_show_filters span').click(function () {
                    if ($(this).hasClass('open')) {
                        $(this).html($('.ecwd_show_filters_text').val());
                        $(this).closest(".calendar_full_content").find(".ecwd_calendar").css({
                            "max-width": "100%",
                            "width": "100%"
                        });
                        $(this).parent().next().hide();
                        $(this).removeClass('open');
                        showFilterSliderArrow();
                        if ($('.ecwd-poster-board').length > 0) {
                            doMasonry();
                        }
                    } else {
                        $(this).html($('.ecwd_hide_filters_text').val());
                        $(this).closest(".calendar_full_content").find(".ecwd_filters").css({
                            "max-width": "27%",
                            "float": "left"
                        });
                        $(this).closest(".calendar_full_content").find(".ecwd_calendar").css({
                            "max-width": "71%",
                            "float": "left"
                        });
                        $(this).parent().next().show();
                        $(this).addClass('open');
                        showFilterSliderArrow();
                        if ($('.ecwd-poster-board').length > 0) {
                            doMasonry();
                        }
                    }
                });
            });
        }
        $('.ecwd_filter_item').each(function () {
            $(this).find('.ecwd_filter_heading').click(function () {
                if ($(this).hasClass('open')) {
                    $(this).next().slideUp(400);
                    $(this).removeClass('open');
                } else {
                    $(this).next().slideDown(400);
                    $(this).addClass('open');
                }
            });
        });
    }, 250);

    function show_filters(main_div) {

            if (parseInt($('body').width()) <= 768 || $(".calendar_full_content").width() <= 550) {
                $(".calendar_full_content .ecwd_calendar").css("max-width", "100%");
                $(".calendar_full_content .ecwd_filters, .calendar_full_content .ecwd_calendar").css({
                    "max-width": "100%",
                    "width": "100%",
                    "float": "none"
                });
                $(".ecwd_show_filters").removeClass('ecwd_show_filters_left').addClass('ecwd_show_filters_top');
                if (!main_div) {
                    $(".ecwd_show_filters span").html($('.ecwd_show_filters_text').val());
                    $(".ecwd_show_filters span").removeClass("open");
                    $(".ecwd_filters").hide();
                }
            }
            else {

                if (!main_div) {
                    $(".ecwd_show_filters").removeClass('ecwd_show_filters_top').addClass('ecwd_show_filters_left');
                } else {
                    if (main_div.find(".ecwd_calendar").hasClass('ecwd-widget-mini') === false) {
                    if (main_div.find(".ecwd_show_filters span").hasClass('open')) {
                        main_div.find(".ecwd_calendar").css({"max-width": "71%", "float": "left"});
                        main_div.find(".ecwd_filters").css({"max-width": "27%", "float": "left"});
                    } else {

                        main_div.find(".ecwd_filters").css({"max-width": "100%", "width": "100%", "float": "none"});

                        main_div.find(".ecwd_calendar").css({"max-width": "100%", "width": "100%", "float": "none"});
                    }
                }
            }
        }

    }

    function showMap() {
        var maps = [];
        jQuery(".ecwd_map_div").each(function (k, v) {
            maps[k] = this;
            var locations = JSON.parse($(maps[k]).next('textarea').val());
            var locations_len = Object.keys(locations).length;

            $(maps[k]).gmap3();

            var markers = [];
            var zoom = 17;
            if(locations_len > 0 && typeof locations[0] != 'undefined' && typeof locations[0]['zoom']!= 'undefined'){
                zoom = parseInt(locations[0]['zoom']);
            }else{
                zoom = 2;
            }
            for (var i = 0; i < locations_len; i++) {
                if (locations[i]) {

                    var marker = new Object();
                    marker.lat = locations[i].latlong[0];
                    marker.lng = locations[i].latlong[1];
                    marker.data = locations[i].infow;
                    marker.options = new Object();
                    markers.push(marker);
                }

            }
            $(maps[k]).gmap3({
                map:{
                    options: {
                        zoom: zoom,
                        zoomControl: true
                    }
                },
                marker: {
                    values: markers,
                    options: {
                        draggable: false
                    },
                    events: {
                        click: function (marker, event, context) {
                            var map = $(maps[k]).gmap3("get"),
                                infowindow = $(maps[k]).gmap3({get: {name: "infowindow"}});
                            if (infowindow) {
                                infowindow.open(map, marker);
                                infowindow.setContent(context.data);
                            } else {
                                $(maps[k]).gmap3({
                                    infowindow: {
                                        anchor: marker,
                                        options: {content: context.data}
                                    }
                                });
                            }
                        }

                    }
                },
                autofit: {maxZoom:zoom}
            });

        });

    }

}(jQuery));