<?php

class Calendar {

	public $weekstartday = 0;      // week start day (0-6 e.g. 0 = Sunday, 1 = Monday, etc.)
	public $monthselector = true;    // month/year select box (true=show selector,false=show month name)
	public $yearoffset = 5;     // monthselector range of years (int)
	public $weeknumbers = false;    // adds a column for week numbers (left,right,false)
	public $weeknumrotate = true;    // rotate weeknumbers 90 degrees *currently only firefox is supported* (true,false)
	public $weeknames = 2;      // controls how weekdays are displayed. (1=full,2=abbrev,3=single char)
	public $monthnames = 1;     // controls how months are displayed. (1=full,2=abbrev)
	public $basecolor = '7D9AC0';    // base color for color scheme (hex)
	public $color = 'blue';    //  color for color scheme (hex)
	public $minilinkbase = '';     // base url for links on mini calendar (blank=disabled)
	public $eventlistbg = '10738B';   // event list view bg color (hex)
	public $eventemptytext = 'No additional details for this event.'; // default text in event view when details is empty (string)
	public $dateformat = 'Y.m.d';    // default date format (passed to php date() public function)
	public $timeformat = 'H:i';    // default time format (passed to php date() public function)
	public $font = '"Lucida Grande","Lucida Sans Unicode",sans-serif'; // font used to display the calendar (any css supported value)
	public $linktarget = 'parent';    // link target frame or window (e.g. 'parent.frameName'. Use '_blank' for new window/tab)
	public $listlimit = false;     // limit the number of events in list and mini-list (false or int e.g. 10)
	public $listtitle = ''; //'Event List';   // Title shown when displaying full event list
	public $widget = 0;
	public $search_params = array();
	public $categories = array();
	public $tags = array();
	public $venues = array();
	public $organizers = array();
	public $displays = array( 'full', 'list', 'week', 'day', 'map', '4day', 'posterboard' );
	public $filters = array( 'categories', 'tags', 'venues', 'organizers' );
	public $event_search = 'yes';
	public $displaysName;
//--------------------------------------------------------------------------------------------
// Weekday names/abbreviations (array must start with Sunday=0)
//--------------------------------------------------------------------------------------------

	public $weekdate;
	public $weekdays = array(
		"Sunday",
		"Monday",
		"Tuesday",
		"Wednesday",
		"Thursday",
		"Friday",
		"Saturday"
	);
	public $abbrevweekdays = array(
		"Sun",
		"Mon",
		"Tue",
		"Wed",
		"Thu",
		"Fri",
		"Sat"
	);
	public $weekdayschar = array(
		"Su",
		"Mo",
		"Tu",
		"We",
		"Th",
		"Fr",
		"Sa"
	);
//--------------------------------------------------------------------------------------------
// Other publics used. (No need to edit below here)
//--------------------------------------------------------------------------------------------

	public $jd, $year, $month, $day, $displaytype, $numdaysinmonth, $monthstartday, $monthname, $previousmonth, $nextmonth;
	public $events = array();

	// types: mini,full,list,mini-list, map, 4day, week, posterboard (default=full)
	public function __construct( $type = 'full', $date = '', $staticdisplaytype = false, $widget = 0, $limit = false, $page = 1, $displays = null, $filters = null, $event_search = 'yes' ) {
// static displaytype
		global $ecwd_options;
		if ( isset( $ecwd_options['date_format'] ) && $ecwd_options['date_format'] != '' ) {
			if ( isset( $ecwd_options['date_format'] ) ) {
				$this->dateformat = $ecwd_options['date_format'];
			}
		}
		if ( isset( $ecwd_options['time_format'] ) && $ecwd_options['time_format'] != '' ) {
			$this->timeformat = $ecwd_options['time_format'];
		}
		if ( isset( $ecwd_options['week_starts'] ) && $ecwd_options['week_starts'] != '' ) {
			$this->weekstartday = $ecwd_options['week_starts'];
		}
		$this->widget = $widget;
		$this->page   = $page;

		if ( $displays && is_array( $displays ) && $displays[0] != '' ) {
			$displays = array_unique( $displays );
			if ( ( $key = array_search( 'none', $displays ) ) !== false ) {
				unset( $displays[ $key ] );
			}
			$this->displays = $displays;
		}
		if ( $filters && is_array( $filters ) && $filters[0] != '' ) {
			$filters = array_unique( $filters );
			if ( ( $key = array_search( 'none', $filters ) ) !== false ) {
				unset( $filters[ $key ] );
			}
			$this->filters = $filters;
		}
		if ( $staticdisplaytype === true ) {
			$this->displaytype = $type;
		} else {
// calendar display type
			if ( $type != '' && ! isset( $_REQUEST['t'] ) ) {
				$this->displaytype = $type; // if type is not set in querystring set type to passed value
			} else {
				$this->displaytype = $_REQUEST['t']; // else set to type passed in querystring
			}
		}

// calendar date
		if ( $date == '' && ! isset( $_REQUEST['date'] ) ) {
			$date = date( 'Y-n-j' );
		} // set to todays date if no value is set
		if ( isset( $_REQUEST['date'] ) && $_REQUEST['date'] != '' ) {
			$date = $_REQUEST['date'];
		} // check if date is in the querystring
		$date      = date( 'Y-n-j', strtotime( $date ) ); // format the date for parsing
		$date_part = explode( '-', $date ); // separate year/month/day
		$year      = $date_part[0];
		$month     = $date_part[1];
		$day       = $date_part[2];
		if ( isset( $_REQUEST['y'] ) && $_REQUEST['y'] != '' ) {
			$year = $_REQUEST['y'];
		} // if year is set in querystring it takes precedence
		if ( isset( $_REQUEST['m'] ) && $_REQUEST['m'] != '' ) {
			$month = $_REQUEST['m'];
		} // if month is set in querystring it takes precedence
		if ( isset( $_REQUEST['d'] ) && $_REQUEST['d'] != '' ) {
			$day = $_REQUEST['d'];
		} // if day is set in querystring it takes precedence
// make sure we have year/month/day as int
		if ( $year == '' ) {
			$year = date( 'Y' );
		}
		if ( $month == '' ) {
			$month = date( 'n' ); // set to january if year is known
		}
		if ( $day == '' ) {
			$day = date( 'j' ); // set to the 1st is year and month is known
		}

		$this->date  = $date;
		$this->month = (int) $month;
		$this->year  = (int) $year;
		$this->day   = (int) $day;

// find out the number of days in the month
		$this->numdaysinmonth = cal_days_in_month( CAL_GREGORIAN, $this->month, $this->year );

// create a calendar object
		$this->jd = cal_to_jd( CAL_GREGORIAN, $this->month, date( 1 ), $this->year );

// get the month start day as an int (0 = Sunday, 1 = Monday, etc)
		$this->monthstartday = jddayofweek( $this->jd, 0 );

// get the month as a name
		$this->monthname = __( jdmonthname( $this->jd, 1 ), 'ecwd' );

		//get weekdate
		$this->weekdate = $this->getWeekFirstDayDate( $this->date );

		//get month date
		$this->monthdate = $this->getMonthDate( $this->date );

		//get day date
		$this->daydate = $this->getDayDate( $this->date );

		$this->displaysName = array(
			'full'        => array( 'name' => __( 'Month', 'ecwd' ), 'date' => $this->monthdate ),
			'mini'        => array( 'name' => __( 'Month', 'ecwd' ), 'date' => $this->monthdate ),
			'list'        => array( 'name' => __( 'List', 'ecwd' ), 'date' => $this->monthdate ),
			'week'        => array(
				'name' => __( 'Week', 'ecwd' ),
				'date' => $this->weekdate
			),
			'day'         => array(
				'name' => __( 'Day', 'ecwd' ),
				'date' => $this->daydate
			)
		);
		$this->event_search = $event_search;
		$this->listlimit = $limit;
	}

// header area for all displaytypes

	public function month_selector() {
		$html = '';


		return $html;
	}


	public function add_terms( $type = 'categories', $terms ) {
		if ( is_array( $terms ) ) {
			$this->$type = $terms;
		}
	}

	public function addEvent( $arr ) {


		$this->events[] = $arr;
	}

// next month link

	public function dateDiff( $beginDate, $endDate ) {
		if ( $endDate == '' ) {
			return 0;
		}
		$fromDate = date( 'Y-n-j', strtotime( $beginDate ) );
		$toDate   = date( 'Y-n-j', strtotime( $endDate ) );
		// echo $fromDate.'----'.$toDate.'<br />';
		$date_parts1 = explode( '-', $fromDate );
		$date_parts2 = explode( '-', $toDate );


		$start_date = gregoriantojd( $date_parts1[1], $date_parts1[2], $date_parts1[0] );
		$end_date   = gregoriantojd( $date_parts2[1], $date_parts2[2], $date_parts2[0] );

		return $end_date - $start_date;
	}

// previous month link

	public function showcal() {
		global $cal_ID;
		$html = '';
		$html .= $this->calendar_head(); // set table head
		$this->seted_days = array();
		if ( ! in_array( $this->displaytype, array(
			"list",
			"mini-list",
			"week",
			'day',
			'4day',
			'map',
			'posterboard'
		) )
		) { // mini and full cal
			$html .= '
<tr>';

// render week number on left
			if ( $this->weeknumbers == 'left' && $this->monthstartday != $this->weekstartday ) {
				$html .= '<td class="week-number"><span>' . date( 'W', strtotime( $this->year . '-' . $this->month ) ) . '</span></td>';
			}

// render previous month cells
			$emptycells = 0;

			$numinrow = 7;

// adjust for weekstartdays
			$weekstartadjust = $this->monthstartday - $this->weekstartday;
			if ( $weekstartadjust < 0 ) {
				$weekstartadjust = $weekstartadjust + $numinrow;
			}

			for ( $counter = 0; $counter < $weekstartadjust; $counter ++ ) {
				if ( $counter == 0 ) {
					$thisclass = 'day-without-date week-start';
				} // only on first
				else {
					$thisclass = 'day-without-date';
				}
				if ( $this->displaytype == 'full' ) {
					$html .= $this->calendar_cell( __($this->previousmonth, 'ecwd'), $thisclass );
				} else {
					$html .= $this->calendar_cell( '&nbsp;', $thisclass );
				}
				$emptycells ++;
			}

// render days
			$rowcounter    = $emptycells;
			$weeknumadjust = $numinrow - ( $this->monthstartday - $this->weekstartday );

			for ( $counter = 1; $counter <= $this->numdaysinmonth; $counter ++ ) {

				$date = $this->year . '-' . $this->month . '-' . $counter;
// render week number on left
				if ( $this->weeknumbers == 'left' && $this->weekstartday == $this->getDay( $date, 0 ) ) {
					$adjustweek = $this->calcDate( $date, '+' . $weeknumadjust, 'day' );
					$adjustweek = $adjustweek['year'] . '-' . $adjustweek['month'] . '-' . $adjustweek['day'];
					$html .= '<td class="week-number"><span>' . date( 'W', strtotime( $adjustweek ) ) . '</span></td>';
				}
				$rowcounter ++;
				$html .= $this->calendar_cell( $counter, 'day-with-date', $date );
				if ( $rowcounter % $numinrow == 0 ) {
// render week number on right
					if ( $this->weeknumbers == 'right' ) {
						$html .= '<td class="week-number"><span>' . date( 'W', strtotime( $date ) ) . '</span></td>';
					}
					$html .= "</tr>";
					if ( $counter < $this->numdaysinmonth ) {
						$html .= "<tr>";
					}
					$rowcounter = 0;
				}
			}

// render next month cells
			$numcellsleft = $numinrow - $rowcounter;
			if ( $numcellsleft != $numinrow ) {
				for ( $counter = 0; $counter < $numcellsleft; $counter ++ ) {
					if ( $this->displaytype == 'full' ) {
						$html .= $this->calendar_cell( $this->nextmonth, 'day-without-date' );
					} else {
						$html .= $this->calendar_cell( '&nbsp;', 'day-without-date' );
					}
					$emptycells ++;
				}
			}

// render week number on right
			if ( $this->weeknumbers == 'right' && $numcellsleft != 7 ) {
				$html .= '<td class="week-number" style="border-bottom:1px solid #' . $this->bordercolor . ';"><span>' . date( 'W', strtotime( $date ) ) . '</span></td>';
			}

			$html .= '</tr>';

		} elseif ( $this->displaytype == 'week' ) {
			$html .= '<ul class="week-event-list">';
			$currentWeek = $this->rangeWeek( $this->year . '-' . $this->month . '-' . $this->day );
			$date        = $currentWeek['start'];

			while ( strtotime( $date ) <= strtotime( $currentWeek['end'] ) ) {
				$html .= '<li>' . $this->calendar_cell( date( 'd', strtotime( $date ) ), 'day-with-date', date( 'Y-n-j', strtotime( $date ) ) ) . '</li>';
				$date = date( "Y-m-d", strtotime( "+1 day", strtotime( $date ) ) );
			}
			$html .= '</ul>';

		} elseif ( $this->displaytype == 'day' ) {
			$html .= '<ul class="day-event-list">';
			$html .= '<li>' . $this->calendar_cell( date( 'd', strtotime( $this->year . '-' . $this->month . '-' . $this->day ) ), 'day-with-date', date( 'Y-n-j', strtotime( $this->year . '-' . $this->month . '-' . $this->day ) ) ) . '</li>';
			$html .= '</ul>';
		} elseif ( $this->displaytype == '4day' ) {
			$html .= '<ul class="day4-event-list">';
			$days = $this->range4Days( $this->year . '-' . $this->month . '-' . $this->day );
			$date = $days['start'];
			while ( strtotime( $date ) <= strtotime( $days['end'] ) ) {
				$html .= '<li>' . $this->calendar_cell( date( 'd', strtotime( $date ) ), 'day-with-date', date( 'Y-n-j', strtotime( $date ) ) ) . '</li>';
				//$this->
				$date = date( "Y-m-d", strtotime( "+1 day", strtotime( $date ) ) );
			}
			$html .= '</ul>';

		} else { // event list and map
			if ( count( $this->events ) > 0 ) {
				//$this->events = $this->arraySort($this->events, 'from');
			}
			if ( $this->displaytype == 'map' ) {
				$map_events = array();

				if ( count( $this->events ) > 0 ) {
					foreach ( $this->events as $i => $event ) {
						if ( strtotime( $event['from'] ) >= strtotime( $this->year . '-' . $this->month . '-1' ) &&
						     strtotime( $event['from'] ) <= strtotime( date( 'Y-m-t', strtotime( $this->date ) ) )
						) {
							if ( $event['latlong'] != '' ) {
								if ( $event['from'] != '' ) {
									$map_events[ $i ]['from'] = date( $this->dateformat, strtotime( $event['from'] ) );
								}
								if ( $event['to'] != '' && $event['from'] != $event['to'] ) {
									$map_events[ $i ]['to'] = date( $this->dateformat, strtotime( $event['to'] ) );
								}
								if ( isset( $event['all_day_event'] ) && $event['all_day_event'] == 1 ) {
									$map_events[ $i ]['starttime'] = __( 'All day', 'ecwd' );
								} else {
									if ( $event['starttime'] != '' ) {
										$map_events[ $i ]['starttime'] = date( $this->timeformat, strtotime( $event['starttime'] ) );
									}
									if ( $event['endtime'] != '' ) {
										$map_events[ $i ]['endtime'] = date( $this->timeformat, strtotime( $event['endtime'] ) );
									}
								}

								if ( $event['location'] != '' ) {
									$map_events[ $i ]['location'] = $event['location'];
								}
								if ( $event['link'] != '' ) {
									$map_events[ $i ]['link'] = $event['link'];
								}
								$map_events[ $i ]['latlong'] = explode( ',', $event['latlong'] );

								$map_events[ $i ]['infow'] = '<div class="ecwd_map_event">';
								if ( $event['permalink'] ) {
									$map_events[ $i ]['infow'] .= '<a href="' . $event['permalink'] . '"';
									if ( isset( $event['color'] ) && $event['color'] !== '' ) {
										$map_events[ $i ]['infow'] .= ' style= "color:' . $event['color'] . '"';
									}
									$map_events[ $i ]['infow'] .= '>' . $event['title'] . '</a>';
								} else {
									$map_events[ $i ]['infow'] .= '<span class="title"';
									if ( isset( $event['color'] ) && $event['color'] !== '' ) {
										$map_events[ $i ]['infow'] .= ' style= "color:' . $event['color'] . '"';
									}
									$map_events[ $i ]['infow'] . '>' . $event['title'] . '<span>';
								}
								$map_events[ $i ]['infow'] .= '<span class="date">';
								if ( isset( $event['all_day_event'] ) && $event['all_day_event'] == 1 ) {
									$map_events[ $i ]['infow'] .= date( $this->dateformat, strtotime( $event['from'] ) ) . ' - ' . ( ( strtotime( $event['to'] ) != '' && strtotime( $event['from'] ) != strtotime( $event['to'] ) ) ? date( $this->dateformat, strtotime( $event['to'] ) ) : "" ) . ' ' . __( 'All day', 'ecwd' );
								} else {
									if ( $event['to'] != '' && $event['from'] != $event['to'] ) {
										$map_events[ $i ]['infow'] .= date( $this->dateformat, strtotime( $event['from'] ) ) . ' ' . date( $this->timeformat, strtotime( $event['starttime'] ) );
										$map_events[ $i ]['infow'] .= ' - ';
										$map_events[ $i ]['infow'] .= date( $this->dateformat, strtotime( $event['to'] ) ) . ' ' . date( $this->timeformat, strtotime( $event['endtime'] ) );
									} else {
										$map_events[ $i ]['infow'] .= date( $this->dateformat, strtotime( $event['from'] ) ) . ' ' . date( $this->timeformat, strtotime( $event['starttime'] ) );
									}
								}
								$map_events[ $i ]['infow'] .= '</span>';

								$map_events[ $i ]['infow'] .= '<span class="location">' . $event['location'] . '</span>';
								$map_events[ $i ]['infow'] .= '</div>';
							}
						}
					}

					$markers = json_encode( $map_events );

					$html .= '<div class="ecwd_map_div" style="width: 100%; height: 100%; min-height: 300px;" ></div>';
					$html .= '<textarea class="ecwd_markers" class="hidden" style="display: none;">' . $markers . '</textarea>';
				} else {
					$html .= '<div class="event-main-content no-events">' . __( 'No events', 'ecwd' ) . '</div>';
				}
			} elseif ( $this->displaytype == 'posterboard' ) {

				$html .= '<div  class="ecwd-poster-board">';
				if ( $this->events ) {
					foreach ( $this->events as $i => $event ) {
						if ( ! isset( $event['color'] ) ) {
							$event['color'] = '#ffffff';
						}
						if ( ! isset( $event['terms']['ecwd_taxonomy_image'] ) ) {
							$event['terms']['ecwd_taxonomy_image'] = '';
						}

						if ( strtotime( $event['from'] ) >= strtotime( $this->year . '-' . $this->month . '-1' ) &&
						     strtotime( $event['from'] ) <= strtotime( date( 'Y-m-t', strtotime( $this->date ) ) )
						) {
							$datenumber1 = substr( date( 'd', strtotime( $event['from'] ) ), 0, 1 );
							$datenumber2 = substr( date( 'd', strtotime( $event['from'] ) ), 1, 1 );
							$dates       = '<span class="weekday-block" itemprop="startDate" content="' . date( 'Y-m-d', strtotime( $event['from'] ) ) . 'T' . date( 'H:i', strtotime( $event['starttime'] ) ) . '"><span class="weekday">' . date( 'D', strtotime( $event['from'] ) ) . '</span> ';
							$dates .= '<span class="month">' . date( 'M', strtotime( $event['from'] ) ) . '</span></span> ';
							$dates .= '<span class="datenumber1">' . $datenumber1 . '</span> ';
							$dates .= '<span class="datenumber2">' . $datenumber2 . '</span> ';


							$html .= '<div class="ecwd-poster-item" itemscope itemtype="http://schema.org/Event">';
							$image = $this->getAndReplaceFirstImage( $event['details'] );
							if ( get_the_post_thumbnail( $event['id'] ) ) {
								$html .= '<div class="ecwd-poster-img">' . get_the_post_thumbnail( $event['id'] ) . '</div>';
							} elseif ( $image['image'] != null ) {
								$html .= '<div class="ecwd-poster-img"><img src="' . $image['image'] . '" /></div>';
								$event['details'] = $image['content'];
							}

							$html .= '<div class="ecwd-event-content"><div class="ecwd-event-details">';
							$html .= '<div class="ecwd-event-header"><div class="date">' . $dates . '</div>';
							if ( $event['permalink'] !== '' ) {
								$html .= '<h2 itemprop="name" ><a href="' . $event['permalink'] . '" style="color: ' . $event['color'] . '">' . $event['title'] . '</a></h2>';
							} else {
								$html .= '<h2 itemprop="name" style="color: ' . $event['color'] . '">' . $event['title'] . '</h2>';
							}
							$html .= '</div>';

							if ( isset( $event['organizers'] ) && count( $event['organizers'] ) > 0 ) {
								$html .= '<div class="event-organizers">';
								foreach ( $event['organizers'] as $organizer ) {
									$html .= '<div class="event-organizer" itemprop="organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
								}
								$html .= '</div>';
							}
							if ( isset( $event['location'] ) && $event['location'] !== '' ) {
								$html .= '<div class="event-venue" itemprop="location" itemscope itemtype="http://schema.org/Place">
                                    <span itemprop="name">';
								if ( isset( $event['venue']['name'] ) && $event['venue']['name'] !== '' ) {
									$html .= '<a href="' . $event['venue']['permalink'] . '">' . $event['venue']['name'] . '</a>';
								}

								$html .= '</span>
                                    <div class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                     ' . $event['location'] . '
                                    </div>
                                  </div>';
							}
							$html .= '</div>';
							$html .= '
                    <div class="post-descr">
                        <div class="post-' . $event['id'] . '">
                             ' . $event['details'] . '
                        </div>
                     </div>';

							$html .= '</div></div>';

						}
					}
				} else {
					$html .= '<div class="event-main-content no-events">' . __( 'No events', 'ecwd' ) . '</div>';
				}

				$html .= '</div>';
			} else {
				$pages = 0;
				$page  = $this->page;
				$html .= '<ul class="ecwd_list">';
				if ( $this->listlimit !== false ) {
					$offset       = ( $page - 1 ) * $this->listlimit;
					$pages        = round( count( $this->events ) / $this->listlimit );
					$this->events = array_slice( $this->events, $offset, $this->listlimit );
				}
				foreach ( $this->events as $event ) {
					if ( $this->displaytype == 'list' ) { // full event list
						if ( strtotime( $event['from'] ) >= strtotime( $this->year . '-' . $this->month . '-1' ) &&
						     strtotime( $event['from'] ) <= strtotime( date( 'Y-m-t', strtotime( $this->date ) ) )
						) {
							$image_class = '';
							$image       = $this->getAndReplaceFirstImage( $event['details'] );
							if ( ! has_post_thumbnail( $event['id'] ) && $image['image'] == "" ) {
								$image_class = "ecwd-no-image";
							}
							$html .= '<li class="' . $image_class . '" itemscope itemtype="http://schema.org/Event">';
							if ( ! $this->widget ) {
								$html .= '<div class="ecwd-list-date resp" itemprop="startDate" content="' . date( 'Y-m-d', strtotime( $event['from'] ) ) . 'T' . date( 'H:i', strtotime( $event['starttime'] ) ) . '">' . __(date( 'd', strtotime( $event['from'] ) ), 'ecwd') . '</div>';
								$html .= '<div class="ecwd-list-img"><div class="ecwd-list-img-container"><div class="ecwd-list-date web">' . date( 'd', strtotime( $event['from'] )).  '.'.   __( date( 'F', strtotime( $event['from'] )), 'ecwd')  .'.'.   __( date( 'l', strtotime( $event['from'] )), 'ecwd') . '</div>';

								$html .= '<div class="ecwd-img">';
								if ( get_the_post_thumbnail( $event['id'] ) ) {
									$html .= get_the_post_thumbnail( $event['id'] );
								} elseif ( $image['image'] != null ) {
									$html .= '<img src="' . $image['image'] . '" />';
									$event['details'] = $image['content'];
								}
								$html .= '</div></div></div>';
							} else {
								$html .= '<div class="ecwd-list-date"  itemprop="startDate" content="' . date( 'Y-m-d', strtotime( $event['from'] ) ) . 'T' . date( 'H:i', strtotime( $event['starttime'] ) ) . '">' . __(date( 'd', strtotime( $event['from'] ) ), 'ecwd') . '</div>';
							}
							$html .= '<div class="event-main-content">';
							if ( $event['permalink'] != '' ) {
								$html .= '<h3 class="event-title"  itemprop="name"><a href="' . $event['permalink'] . '" itemprop="url" style="color:' . $event['color'] . ';">' . $event['title'] . '</a></h3>';
							} else {
								$html .= '<h3 class="event-title" style="color:' . $event['color'] . ';" itemprop="name">' . $event['title'] . '</h3>';
							}
							$html .= '<div class="ecwd-list-date-cont">';

							if ( isset( $event['all_day_event'] ) && $event['all_day_event'] == 1 ) {
								$eventtime = '<div class="ecwd-time"><span class="metainfo"> ' . __( 'All day', 'ecwd' );
								$eventtime .= '</span>';
								$eventtime .= '</div>';
							} else {
								if ( $event['starttime'] != '' ) { // event details - hidden until clicked (full)
									$eventtime = '<div class="ecwd-time"><span class="metainfo"> ' . date( $this->timeformat, strtotime( $event['starttime'] ) );
									if ( $event['endtime'] != '' && strtotime( $event['endtime'] )!= strtotime( $event['starttime'] ) ) {
										$eventtime .= "-" . date( $this->timeformat, strtotime( $event['endtime'] ) );
									}
									$eventtime .= '</span>';
									$eventtime .= '</div>';

								}
							}
							$html .= $eventtime;
							if ( $event['from'] != '' ) { // event details - hidden until clicked (full)
								$eventdate = '<div class="ecwd-date"><span class="metainfo"> ' . date( $this->dateformat, strtotime( $event['from'] ) );
								if ( $event['to'] != '' && strtotime( $event['to'] ) !== strtotime( $event['from'] )) {
									$eventdate .= "-" . date( $this->dateformat, strtotime( $event['to'] ) );
								}
								$eventdate .= '</span>';
								$eventdate .= '</div>';
								$html .= $eventdate;
							}
							$html .= '</div>';
							if ( isset( $event['organizers'] ) && count( $event['organizers'] ) > 0 ) {
								$html .= '<div class="event-organizers">';
								foreach ( $event['organizers'] as $organizer ) {
									$html .= '<div class="event-organizer" itemprop="organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
								}
								$html .= '</div>';
							}

							if ( $event['location'] !== '' ) {
								$html .= '<div class="event-venue" itemprop="location" itemscope itemtype="http://schema.org/Place">
                                            <span itemprop="name">';
								if ( isset( $event['venue']['name'] ) ) {
									$html .= '<a href="' . $event['venue']['permalink'] . '">' . $event['venue']['name'] . '</a>';
								}

								$html .= '</span>
                                            <div class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                              ' . ( $event['location'] ? $event['location'] : "" ) . '
                                            </div>
                                          </div>';
							}
							$html .= '<div class="event-content" itemprop="description">' . ( $event['details'] ? $event['details'] : $this->eventemptytext ) . '</div></div>';

						} else { // mini event list
							$html .= '<li style="background:' . $event['color'] . ';"';
							if ( $this->minilinkbase !== false ) { // enable link (good for linking to full calendar)
								$html .= ' class="event-link"';
								if ( $this->linktarget == '_blank' ) {
									$html .= ' onClick="window.open(\'' . $this->minilinkbase . '\', \'_blank\')"';
								} else {
									$html .= ' onClick="' . $this->linktarget . '.location=\'' . $this->minilinkbase . '\'"';
								}
							} else if ( $event['link'] != '' ) { // enable link (links to event url)
								$html .= ' class="event-link"';
								if ( $this->linktarget == '_blank' ) {
									$html .= 'onClick="window.open(\'' . $event['link'] . '\', \'_blank\')"';
								} else {
									$html .= ' onClick="' . $this->linktarget . '.location=\'' . $event['link'] . '\'"';
								}

							}
						}
						$html .= '</li>';
					}
				}
				if ( count( $this->events ) <= 0 ) { // if events array is empty
					$html .= '<li >';
					$html .= '<div class="event-content">' . __( 'No Events', 'ecwd' ) . '</div>';
					$html .= '</li>';
				}

				$html .= '</ul>';

				///PAGINATION////
				if ( $pages > 1 ) {
					$html .= '<div class="ecwd-pagination">';
					if ( $this->page > 1 ) {
						$html .= '<span class="cpage-span"><a href="?date=' . $this->year . '-' . $this->month . '&t=' . $this->displaytype . '&cpage=' . ( $this->page - 1 ) . '" class="cpage">...</a></span>';
					}
					for ( $i = ( $this->page - 3 ); $i < ( $this->page + 4 ); $i ++ ) {
						if ( $i <= $pages and $i >= 1 ) {
							if ( $this->page == $i ) {
								$html .= '<span class="cpage-span"><span class="page">' . $i . '</span></span>';
							} else {
								$html .= '<span class="cpage-span"><a href="?date=' . $this->year . '-' . $this->month . '&t=' . $this->displaytype . '&cpage=' . $i . '" class="cpage">' . $i . '</a></span>';
							}
						}
					}
					if ( $this->page < $pages ) {
						$html .= '<span class="cpage-span"><a href="?date=' . $this->year . '-' . $this->month . '&t=' . $this->displaytype . '&cpage=' . ( $this->page + 1 ) . '" class="cpage">...</a></span>';
					}
					$html .= '</div>';
				}
			}
		}
		$html .= $this->calendar_foot();

// remove tabs, line breaks, vertical tabs, null-byte
		$html = $this->stripWhitespace( $html );

		return $html;
	}

// month/year select box

	public function calendar_head( $content = '' ) {
		global $cal_ID;

		$html = '';
		$html .= '<div class="calendar-head ecwd_calendar_prev_next">';
		$html .= $this->cal_previous(); // previous month link
		$previous_year      = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '-1', 'year' );
		$previous_dateparam = $previous_year['year'] . '-' . $previous_year['month'] . '-' . $previous_year['day'];

		$next_year      = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '+1', 'year' );
		$next_dateparam = $next_year['year'] . '-' . $next_year['month'] . '-' . $next_year['day'];
		if ( $this->displaytype == 'week' ) {
			$previous_dateparam = $this->getWeekFirstDayDate( $previous_year['year'] . '-' . $previous_year['month'] . '-' . $previous_year['day'] );
			$next_dateparam     = $this->getWeekFirstDayDate( $next_year['year'] . '-' . $next_year['month'] . '-' . $next_year['day'] );
		}
		$html .= '<div class="current-month">
					<a href="?date=' . $previous_dateparam . '&t=' . $this->displaytype . '"><</a>&nbsp;
					' . $this->year . '
				&nbsp;<a href="?date=' . $next_dateparam . '&t=' . $this->displaytype . '">></a>
					<div>';
		if ( $this->displaytype !== 'week' && $this->displaytype !== '4day' && $this->displaytype !== 'day' ) {

			$html .= __( $this->monthname, 'ecwd' );
		} else {
			if ( $this->displaytype !== 'day' ) {
				if ( $this->displaytype == 'week' ) {
					$currentDays = $this->rangeWeek( $this->year . '-' . $this->month . '-' . $this->day );
				} else {
					$currentDays = $this->range4Days( $this->year . '-' . $this->month . '-' . $this->day );
				}

				$html .= __( $this->getMonth( $currentDays['start'] ), 'ecwd' ) . ' ' . date( 'd', strtotime( $currentDays['start'] ) ) . ' - ' . __( $this->getMonth( $currentDays['end'] ), 'ecwd' ) . ' ' . date( 'd', strtotime( $currentDays['end'] ) );
			} else {
				$html .= __( $this->monthname, 'ecwd' ) . ' ' . $this->day;
			}
		}


		$html .= '		</div>
				</div>';


		$html .= $this->cal_next(); // next month link
		$html .= '</div>';
		$html .= $this->cal_viewmode();

		if ( ! in_array( $this->displaytype, array(
			"list",
			"mini-list",
			'day',
			'week',
			'4day',
			'map',
			'posterboard'
		) )
		) { // mini and full cal
			$html .= '
<table class="ecwd_calendar_container ' . $this->displaytype . ' cal_' . $this->color . '" cellpadding="0" cellspacing="0" border="0">

<tr>';
// render week number on left
			if ( $this->weeknumbers == 'left' ) {
				$html .= '<td rowspan="2" class="week-number">&nbsp;</td>';
			}

// render week number on right
			if ( $this->weeknumbers == 'right' ) {
				$html .= '<td rowspan="2" class="week-number">&nbsp;</td>';
			}
			$html .= '
</tr>
<tr>';
			if ( $this->weeknames == 1 ) {
				$weekdays = $this->weekdays; // full
			} else if ( $this->weeknames == 3 || $this->displaytype == 'mini' ) {
				$weekdays = $this->weekdayschar; // single char
			} else {
				$weekdays = $this->abbrevweekdays; // 3 char
			}
			for ( $i = 0; $i < count( $weekdays ); $i ++ ) {
				$di      = ( $i + $this->weekstartday ) % 7;
				$weekday = $weekdays[ $di ];
				if ( $i == 0 ) {
					$thisclass = 'normal-day-heading week-start';
				} else {
					$thisclass = 'normal-day-heading';
				}
				$html .= $this->calendar_cell( __($weekday, 'ecwd'), $thisclass ); // calendar cells for full & mini
			}
			$html .= '
</tr>
';
		} else { // event list
			$html .= '<div class="ecwd_calendar_container ' . $this->displaytype . '">';

			if ( $this->displaytype == 'list' ) {
				if ( $this->listtitle != '' ) {
					$html .= '<h2 class="list-title">' . $this->listtitle . '</h2>';
				}

			} elseif ( $this->displaytype == 'day' ) {
				//$this->day = ;

			} elseif ( $this->displaytype == 'week' ) {
			} elseif ( $this->displaytype == '4day' ) {
			} elseif ( $this->displaytype == 'map' ) {
			} elseif ( $this->displaytype == 'posterboard' ) {
			}

			//$html .= '</div>';
		}

		return $html;
	}

// calendar cells for mini and full displaytypes

	public function cal_previous() {
		global $cal_ID;
		$previous  = $this->calcDate( $this->year . '-' . $this->month, '-1', 'month' );
		$dateparam = $previous['year'] . '-' . $previous['month'];
		$prev_date = '';
		if ( $this->displaytype == 'day' ) {
			$previous  = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '-1', 'day' );
			$dateparam = $previous['year'] . '-' . $previous['month'] . '-' . $previous['day'];
			$prev_date = $previous['day'];
		} elseif ( $this->displaytype == '4day' ) {
			$previous  = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '-4', 'day' );
			$dateparam = $previous['year'] . '-' . $previous['month'] . '-' . $previous['day'];
			$prev_date = $previous['day'];
		} elseif ( $this->displaytype == 'week' ) {
			$previous  = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '-1', 'week' );
			$dateparam = $previous['year'] . '-' . $previous['month'] . '-' . $previous['day'];
			$prev_date = $previous['day'];
		}

		$this->previousmonth = ( $this->getMonth( $previous['year'] . '-' . $previous['month'], $this->monthnames ) );
		if ( $this->widget == 1 ) {
			$previoustext = '<span><</span>';
		} else {
			$previoustext = '<span><</span><span class="month-name"> ' . __($this->previousmonth, 'ecwd') . ' ' . $prev_date . '</span>';
		}

		$html = '<div class="previous"><a href="?date=' . $dateparam . '&t=' . $this->displaytype . '">' . $previoustext . '</a></div>';

		return $html;
	}

// calendar footer for all displaytypes

	public function calcDate( $startdate, $increment, $unit ) {
		$date    = date( "Y-n-j", strtotime( date( "Y-n-j", strtotime( $startdate ) ) . " " . $increment . " " . $unit ) );
		$date    = explode( '-', $date );
		$newdate = array(
			'year'  => $date[0],
			'month' => $date[1],
			'day'   => $date[2]
		);

		return $newdate;
	}


	public function getWeekFirstDayDate( $date ) {
		if(strtotime($date)==strtotime(date('Y-m-d'))) {
			return date( 'Y-n-j', strtotime( strtolower( $this->weekdays[ $this->weekstartday ] ) . " this week", strtotime( $date ) ) );
		}else{
			$first_monday_of_month =  strtotime('First '.$this->weekdays[ $this->weekstartday ].' of '.date('F o', strtotime($date)));
			if(date('j', $first_monday_of_month)>1){
				$previous_monday = strtotime("-1 week",$first_monday_of_month);
				return date( 'Y-n-j',$previous_monday);
			}else{
				return date( 'Y-n-j',$first_monday_of_month);
			}

		}
	}

	public function getMonthDate($date){
		if($this->displaytype=='week' && strtotime($date)!==strtotime(date('Y-m-d'))){
			return date( 'Y-n-j',strtotime("+1 week",strtotime($date)));
		}elseif($this->displaytype=='4day' && strtotime($date)!==strtotime(date('Y-m-d'))){
			return date( 'Y-n-j',strtotime("+3 day",strtotime($date)));
		}
		return $date;
	}
	public function getDayDate($date){
		if(strtotime($date)==strtotime(date('Y-m-d'))){
			return $date;
		}else{
			if($this->displaytype=='full' || $this->displaytype=='month' || $this->displaytype=='mini' || $this->displaytype=='map' || $this->displaytype=='posterboard'|| $this->displaytype=='list') {
				return date( 'Y-n-j',strtotime(date('Y-m-1'),strtotime($date)));
			}elseif($this->displaytype=='week'){
				return date( 'Y-n-j', strtotime( strtolower( $this->weekdays[ $this->weekstartday ] ) . " this week", strtotime( $date ) ) );
			}else{
				return $date;
			}
		}

	}

// add an event to the events array

	public function getMonth( $date, $type = 1 ) {
		$date       = date( 'Y-n-j', strtotime( $date ) );
		$date_parts = explode( '-', $date );
		$jd         = cal_to_jd( CAL_GREGORIAN, $date_parts[1], $date_parts[2], $date_parts[0] );

		return __(jdmonthname( $jd, $type ));
	}

// pulls everything together and returns the calendar for all displaytypes

	public function cal_next() {
		global $cal_ID;
		$html = '';

		$next      = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '+1', 'month' );
		$next_date = '';
		$dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
		if ( $this->displaytype == 'day' ) {
			$next      = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '+1', 'day' );
			$dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
			$next_date = $next['day'];
		} elseif ( $this->displaytype == '4day' ) {
			$next      = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '+4', 'day' );
			$dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
			$next_date = $next['day'];
		} elseif ( $this->displaytype == 'week' ) {
			$next      = $this->calcDate( $this->year . '-' . $this->month . '-' . $this->day, '+1', 'week' );
			$dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
			$next_date = $next['day'];
		}
		$this->nextmonth = ( $this->getMonth( $next['year'] . '-' . $next['month'], $this->monthnames ) );

		if ( $this->displaytype == 'mini' || $this->displaytype == 'mini-list' || $this->widget == 1 ) {
			$nexttext = '<span>></span>';

		} else {
			$divider = $this->monthselector === false ? '&nbsp;|&nbsp;' : '';
			$nexttext = '<span class="month-name">' . $next_date . ' ' . $divider . __($this->nextmonth, 'ecwd') . ' </span><span>></span>';
		}
		$html = '<div class="next"><a href="?date=' . $dateparam . '&t=' . $this->displaytype . '">' . $nexttext . '</a></div>';

		return $html;
	}

//--------------------------------------------------------------------------------------------
// Helper Functions
//--------------------------------------------------------------------------------------------
//

	public function cal_viewmode() {
		$monthType = ( $this->widget == 1 ) ? 'mini' : 'full';

		if ( $this->widget == 1 ) {
			$html = ' <div class="ecwd_calendar_view_dropdown cal_tabs_' . $this->color . '" ><a class="ecwd-dropdown-toggle" data-toggle="ecwd-dropdown">';
			$html .= $this->displaysName[ $this->displaytype ]['name'];
			$html .= '</a>';

			$widgetDisplays = array('mini', 'list', 'week', 'day');

			$html .= '<div class="ecwd-dropdown-menu">';
			foreach ( $widgetDisplays as $display ) {
				if ( $display !== 'none' && isset($this->displaysName[ $display ])) {
					$html .= '<div class="type">' . '<a href="?date=' . $this->displaysName[ $display ]['date'] . '&t=' . $display . '">' . $this->displaysName[ $display ]['name'] . '</a>' . '</div>';
				}
			}

			$html .= '</div></div>';

		} else {
			$html = ' <div class="ecwd_calendar_view_tabs cal_tabs_' . $this->color . '" >';
			$html .= '<div class="filter-container">
                        <div class="filter-arrow-left">&laquo;</div>
                        <div class="filter-arrow-right">&raquo;</div>';


			if ( count( $this->displays ) > 1 ) {
				$html .= '<ul>';
				foreach ( $this->displays as $display ) {
					if ( $display !== 'none' && isset($this->displaysName[ $display ])) {
						$html .= '<li class="type';
						if ( $this->displaytype == $display ) {
							$html .= ' ecwd-selected-mode';
							$html .= '">' . '<a>' . $this->displaysName[ $display ]['name'] . '</a>' . '</li>';
						}else {
							$html .= '">' . '<a href="?date=' . $this->displaysName[ $display ]['date'] . '&t=' . $display . '">' . $this->displaysName[ $display ]['name'] . '</a>' . '</li>';
						}
					}
				}
				$html .= '</ul>';
			}
			$html .= '</div>';
			if ( $this->event_search == 'yes' ) {
				$svalue = '';
				if ( isset( $this->search_params['query'] ) && $this->search_params['query'] ) {
					$svalue = $this->search_params['query'];
				}
				$html .= '<div class="ecwd-search">
                        <input class="ecwd-search" name="search" type="text" placeholder="' . __( 'What\'re we looking for ?', 'ecwd' ) . '" value="' . $svalue . '">'
				         . '<button class="btn btn-default ecwd-search-submit"><span class="glyphicon glyphicon-search"></span><i class="fa fa-search"></i></button>
                </div>';
			}
			$html .= '</div>';

		}

		return $html;
	}


// returns month from passed date (string), $type: 0=number,1=full(January,February,etc),2=abbreviation(Jan,Feb,etc)

	public function calendar_cell( $day, $class, $date = '', $style = '' ) {
		global $cal_ID;

		$addclass = '';

		if ( strpos( $class, 'normal-day-heading' ) !== false ) {
			$tag = 'th';
		} else {
			$tag = 'td';
		}

		if ( $day != '' ) {
			$bgColor    = '';
			$cellevents = array();

			if ( is_array( $this->events ) ) { // events array populated from addEvent()
				foreach ( $this->events as $event ) {
					$color         = $event['color'];
					$title         = $event['title'];
					$link          = $event['link'];
					$eventdate     = $event['date'];
					$from          = strtotime( $event['from'] );
					$to            = strtotime( $event['to'] );
					$starttime     = $event['starttime'];
					$endtime       = $event['endtime'];
					$details       = $event['details'];
					$location      = $event['location'];
					$venue         = $event['venue'];
					$organizers    = $event['organizers'];
					$terms         = $event['terms'];
					$all_day_event = $event['all_day_event'];
					$permalink     = $event['permalink'];

					$id = $event['id'];


					if ( $date == date( 'Y-n-j', strtotime( $eventdate ) ) ) {
						$cellevents[] = array(
							'color'         => $color,
							'title'         => $title,
							'link'          => $link,
							'date'          => $eventdate,
							'from'          => $from,
							'to'            => $to,
							'id'            => $id,
							'starttime'     => $starttime,
							'endtime'       => $endtime,
							'details'       => $details,
							'location'      => $location,
							'all_day_event' => $all_day_event,
							'terms'         => $terms,
							'venue'         => $venue,
							'organizers'    => $organizers,
							'permalink'     => $permalink
						);
					}
				}
			}

// sort by starttime for the cell
			if ( count( $cellevents ) > 0 ) {
				$cellevents = $this->arraySort( $cellevents, 'starttime' );
			}

			if ( $date == date( 'Y-n-j' ) ) {
				$addclass .= ' current-day'; // if processing the current day
			}

			if ( in_array( $this->getDay( $date ), array(
				'Saturday',
				'Sunday'
			) ) ) {
				$addclass .= ' weekend'; // if a weekend
			}

			if ( $this->getDay( $date, 0 ) == $this->weekstartday ) {
				$addclass .= ' week-start'; // if the weekstartday
			}

			if ( $this->arraySearch( $date, $cellevents ) !== false ) {
				$addclass .= ' has-events'; // if the date has events
			} else if ( strpos( $class, 'normal-day-heading' ) === false ) {
				$addclass .= ' no-events'; // no events
			}

			$combinedclass = $class . $addclass; // combine all classes

			$html = '<' . $tag . ' class="' . $combinedclass . '" data-date="' . $date . '"';

// check/set links for mini calendar
			if ( $this->minilinkbase != '' && $this->displaytype == 'mini' ) {
				if ( $this->linktarget == '_blank' ) {
					$html .= ' onClick="window.open(\'' . $this->minilinkbase . '?date=' . $date . '\', \'_blank\')"';
				} else {
					$html .= ' onClick="' . $this->linktarget . '.location=\'' . $this->minilinkbase . '?date=' . $date . '\'"';
				}
			}
		}
		if ( $style ) {
			$html .= ' style="' . $style . '"';
		}
		if ( $this->displaytype == 'mini' ) {
			$content = $day;
		} elseif ( $this->widget && $this->displaytype != 'mini' ) {
			$content = '<div class="ecwd-week-date">' . date( 'd', strtotime( $date ) ) . '</div>';
		} elseif ( ( $this->displaytype == 'week' || $this->displaytype == 'day' || $this->displaytype == '4day' ) && ! $this->widget ) {
			$content = '<div class="ecwd-week-date resp" itemprop="startDate" content="' . date( 'Y-m-d', strtotime( $date ) ) . '">' . date( 'd', strtotime( $date )) . '</div><div class="ecwd-week-date web"">'   .date( 'd', strtotime( $date )).  '.'.   __( date( 'F', strtotime( $date )), 'ecwd')  .'.'.   __( date( 'l', strtotime( $date )), 'ecwd'). '</div>';
		} else {
			$content = '<div class="day-number">' . $day . '</div>'; // day number or prev/next month cell content
		}


		if ( count( $cellevents ) > 0 ) {
			$content .= '<ul class="events">';
			$eventcontent = '';
			foreach ( $cellevents as $i => $cellevent ) {
				//echo $cellevent['date'].'-------'.$cellevent['title'].'<br />';
				//if (!array_key_exists($cellevent['id'], $this->seted_days)) {
				$li_class = '';
				if ( $i > 2 && $this->displaytype !== 'mini' ) {
					$li_class = 'inmore';
				}

				$eventcontent .= '<li itemscope itemtype="http://schema.org/Event" style="';
				if ( is_array( $cellevent['terms'] ) ) {
					if ( isset( $cellevent['color'] ) && $cellevent['color'] !== '' ) {
						$eventcontent .= 'background-color: ' . $cellevent['color'] . '; ';
					}
				}
				$eventcontent .= '" class="' . $li_class . ' ' . $i;
				if ( isset( $cellevent['terms']['ecwd_taxonomy_image'] ) && $cellevent['terms']['ecwd_taxonomy_image'] == '' ) {
					$eventcontent .= ' no-cat-image ';
				}
				$eventcontent .= '">';
				if ( $this->displaytype != 'mini' ) {
					if ( isset( $cellevent['terms']['ecwd_taxonomy_image'] ) && $this->displaytype != 'mini' ) {
						if ( $cellevent['terms']['ecwd_taxonomy_image'] != '' ) {
							$eventcontent .= '<img  class="ecwd-event-cat-icon" src="' . $cellevent['terms']['ecwd_taxonomy_image'] . '" />';
						}/*elseif (isset($cellevent['color'])){
                            $eventcontent .= ' <span class="event-metalabel" style="background:' . $cellevent['color'] . '"></span>';
                        }*/
					}
					if ( $cellevent['permalink'] ) {
						$eventcontent .= '<a href="' . $cellevent['permalink'] . '"><span itemprop="name">' . $cellevent['title'] . '</span></a>';
					} else {
						$eventcontent .= '<span itemprop="name">' . $cellevent['title'] . '</span>';
					}

				}
				$this->seted_days[ $cellevent['id'] ] = $date;

				$eventcontent .= '<div class="event-details-container"><div class="ecwd-event-arrow"></div><div class="event-details">';

				if ( $cellevent['title'] != '' ) {
					$eventcontent .= '<div class="event-details-title">';

					if ( isset( $cellevent['color'] ) && $cellevent['color'] !== '' ) {
						$eventcontent .= ' <span class="event-metalabel" style="background:' . $cellevent['color'] . '"></span>
                                         <h5 style="color:' . $cellevent['color'] . '" itemprop="name">';
						if ( isset( $cellevent['permalink'] ) && $cellevent['permalink'] !== '' ) {
							$eventcontent .= '<a href="' . $cellevent['permalink'] . '" style="color: ' . $cellevent['color'] . '">' . $cellevent['title'] . '</a>';
						} else {
							$eventcontent .= $cellevent['title'];
						}
						$eventcontent .= '</h5>
                                         ';
					} else {
						$eventcontent .= ' <span class="event-metalabel"></span>
                                         <h5 itemprop="name">';
						if ( isset( $cellevent['permalink'] ) && $cellevent['permalink'] !== '' ) {
							$eventcontent .= '<a href="' . $cellevent['permalink'] . '">' . $cellevent['title'] . '</a>';
						} else {
							$eventcontent .= $cellevent['title'];
						}
						$eventcontent .= '</h5>';
					}
					$eventcontent .= ' </div>';
				}
				if ( isset( $cellevent['all_day_event'] ) && $cellevent['all_day_event'] == 1 ) {

					$eventtime = '<div class="ecwd-time"><span class="metainfo"  itemprop="startDate" content="' . date( 'Y-m-d', $cellevent['from'] ) . 'T' . date( 'H:i', strtotime( $cellevent['starttime'] ) ) . '"> ' . __( 'All day', 'ecwd' );
					$eventtime .= '</span>';
					$eventtime .= '</div>';
					$eventcontent .= $eventtime;
				} else {
					if ( $cellevent['starttime'] != '' ) { // event details - hidden until clicked (full)
						$eventtime = '<div class="ecwd-time"><span class="metainfo"  itemprop="startDate" content="' . date( 'Y-m-d', $cellevent['from'] ) . 'T' . date( 'H:i', strtotime( $cellevent['starttime'] ) ) . '"> ' . date( $this->timeformat, strtotime( $cellevent['starttime'] ) );
						if ( $cellevent['endtime'] != '' && strtotime($cellevent['endtime'])!==strtotime($cellevent['starttime'])) {
							$eventtime .= "-" . date( $this->timeformat, strtotime( $cellevent['endtime'] ) );
						}
						$eventtime .= '</span>';
						$eventtime .= '</div>';
						$eventcontent .= $eventtime;
					}
				}
				if ( $cellevent['from'] != '' ) { // event details - hidden until clicked (full)
					$eventdate = '<div class="ecwd-date"><span class="metainfo"> ' . date( $this->dateformat, $cellevent['from'] );
					if ( $cellevent['to'] != '' && $cellevent['to']!=$cellevent['from']) {
						$eventdate .= "-" . date( $this->dateformat, $cellevent['to'] );
					}
					$eventdate .= '</span>';
					$eventdate .= '</div>';
					$eventcontent .= $eventdate;
				}
				if ( isset( $cellevent['organizers'] ) && count( $cellevent['organizers'] ) > 0 ) {
					$eventcontent .= '<div class="event-organizers">';
					foreach ( $cellevent['organizers'] as $organizer ) {
						$eventcontent .= '<div class="event-organizer" itemprop="organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
					}
					$eventcontent .= '</div>';
				}
				if ( $cellevent['location'] !== '' ) {
					$eventcontent .= '<div class="event-venue" itemprop="location" itemscope itemtype="http://schema.org/Place">';
					if ( isset( $cellevent['venue']['name'] ) ) {
						$eventcontent .= '<div class="ecwd-venue" ><span itemprop="name"><a href="' . $cellevent['venue']['permalink'] . '">' . $cellevent['venue']['name'] . '</a></span></div>';
					}
					if ( isset( $cellevent['location'] ) && $cellevent['location'] != '' ) {
						$eventcontent .= '<div class="ecwd-location" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span>' . $cellevent['location'] . '</span></div>';
					}
					$eventcontent .= '</div>';
				}
				if ( isset( $cellevent['link'] ) && $cellevent['link'] != '' ) {
					$eventcontent .= '<div  class="ecwd-link"> <a href="' . $cellevent['link'] . '"  itemprop="url">' . $cellevent['link'] . '</a></div>';
				}
				$image = $this->getAndReplaceFirstImage( $cellevent['details'] );
				if ( $cellevent['details'] != '' || has_post_thumbnail( $cellevent['id'] ) ) {
					$eventcontent .= '<div  class="ecwd-detalis" itemprop="description">';
					if ( get_the_post_thumbnail( $cellevent['id'] ) ) {
						$eventcontent .= get_the_post_thumbnail( $cellevent['id'], 'thumbnail' );
					} elseif ( $image['image'] != null ) {
						$eventcontent .= '<img src="' . $image['image'] . '" />';
						$cellevent['details'] = $image['content'];
					}
					$eventcontent .= $cellevent['details'] . '</div>';
				}

				$eventcontent .= '</div><div class="ecwd-event-arrow-right"></div>';

				$eventcontent .= '</li> ';


//                } else {
//                    //$eventcontent .= $cellevent['title'];
//                }

			}
			$content .= $eventcontent;

			if ( $i > 2 && $this->displaytype !== 'mini' ) {
				$content .= '<li class="ecwd-calendar-more-event">
                     <span class="ecwd-calendar-event-add"><a href="#more_events_' . $date . '">' . __( 'More events', 'ecwd' ) . '</a></span>
                    <div id="more_events_' . $date . '" class="ecwd-modal">
                        <div class="ecwd-excluded-events ">
                            <a href="#ecwd-close" title="Close" class="ecwd-close"><i class="fa fa-times"></i></a>
                             <div class="modal-header">
                                    <h4 class="modal-title">' . date( $this->dateformat, strtotime( $date ) ) . '</h4>
                                  </div>
                                <div class="modal-body">
                                     <ul class="events more_events">
                                        ' . $eventcontent . '
                                        </ul>
                                  </div>
                                   <div class="modal-footer">
                                  </div>
                        </div>
                    </div>
                 </li>';

			}

			$content .= '</ul>';


			if ( $this->displaytype == 'week' || $this->displaytype == 'day' || $this->displaytype == '4day' ) {
				if ( count( $cellevents ) > 0 ) {
					$html = '';
					if ( ! $this->widget ) {
						$html = '<div class="ecwd-week-date resp"  style="background:#' . $this->eventlistbg . '" itemprop="startDate" content="' . date( 'Y-m-d', strtotime( $event['from'] ) ) . 'T' . date( 'H:i', strtotime( $event['starttime'] ) ) . '">' . date( 'd', strtotime( $date ) ) . '</div><div class="ecwd-week-date web"">' . date( 'd', strtotime( $date )).  '.'.   __( date( 'F', strtotime( $date )), 'ecwd')  .'.'.   __( date( 'l', strtotime( $date )), 'ecwd') . '</div>';
					} else {
						$html = '<div class="ecwd-week-date">' . date( 'd', strtotime( $date ) ) . '</div>';
					}
					$html .= '<div class="event-main-content">';
					foreach ( $cellevents as $cellevent ) {

						$image_class = '';
						$image       = $this->getAndReplaceFirstImage( $cellevent['details'] );
						if ( ! has_post_thumbnail( $cellevent['id'] ) && $image['image'] == "" ) {
							$image_class = "ecwd-no-image";
						}
						$html .= '<div class="event-container ' . $image_class . '" itemprop="event">';
						if ( ! $this->widget ) {
							$html .= '<div class="ecwd-list-img"><div class="ecwd-list-img-container">';
							$html .= '<div class="ecwd-img">';
							if ( get_the_post_thumbnail( $cellevent['id'] ) ) {
								$html .= get_the_post_thumbnail( $cellevent['id'] );
							} elseif ( $image['image'] != null ) {
								$html .= '<img src="' . $image['image'] . '" />';
								$cellevent['details'] = $image['content'];
							}
							$html .= '</div></div></div>';
						}

						if ( $cellevent['permalink'] != '' ) {
							$html .= '<h3 class="event-title" itemprop="name"> <a href="' . $cellevent['permalink'] . '"';
							if ( isset( $cellevent['color'] ) && $cellevent['color'] !== '' ) {
								$html .= ' style="color:' . $cellevent['color'] . ';"';
							}
							$html .= '>' . $cellevent['title'] . '</a></h3>';

						} else {
							$html .= '<h3 class="event-title" itemprop="name"';

							if ( isset( $cellevent['color'] ) && $cellevent['color'] !== '' ) {
								$html .= 'style="color:' . $cellevent['color'] . ';"';
							}
							$html .= '>' . $cellevent['title'] . '</h3>';
						}
						$html .= '<div class="ecwd-list-date-cont">';
						if ( isset( $cellevent['all_day_event'] ) && $cellevent['all_day_event'] == 1 ) {
							$eventtime = '<div class="ecwd-time"><span class="metainfo event-time" itemprop="startDate" content="' . date( 'Y-m-d', $cellevent['from'] ) . 'T' . date( 'H:i', strtotime( $cellevent['starttime'] ) ) . '"> ' . __( 'All day', 'ecwd' );
							$eventtime .= '</span>';
							$eventtime .= '</div>';
							$html .= $eventtime;
						} else {

							if ( $cellevent['starttime'] != '' ) { // event details - hidden until clicked (full)
								$eventtime = '<div class="ecwd-time"><span class="metainfo event-time" itemprop="startDate" content="' . date( 'Y-m-d', $cellevent['from'] ) . 'T' . date( 'H:i', strtotime( $cellevent['starttime'] ) ) . '"> ' . date( $this->timeformat, strtotime( $cellevent['starttime'] ) );
								if ( $cellevent['endtime'] != '' &&  $cellevent['endtime']!= $cellevent['starttime']) {
									$eventtime .= "-" . date( $this->timeformat, strtotime( $cellevent['endtime'] ) );
								}
								$eventtime .= '</span>';
								$eventtime .= '</div>';
								$html .= $eventtime;
							}
						}
						if ( $cellevent['from'] != '' ) {
							$eventdate = '<div class="ecwd-date"><span class="metainfo" itemprop="startDate" content="' . date( 'Y-m-d', $cellevent['from'] ) . 'T' . date( 'H:i', strtotime( $cellevent['starttime'] ) ) . '"> ' . date( $this->dateformat, $cellevent['from'] );
							if ( $cellevent['to'] != '' && $cellevent['to']!= $cellevent['from']) {
								$eventdate .= "-" . date( $this->dateformat, $cellevent['to'] );
							}
							$eventdate .= '</span>';
							$eventdate .= '</div>';
							$html .= $eventdate;
						}
						$html .= '</div>';
						if ( isset( $cellevent['organizers'] ) && count( $cellevent['organizers'] ) > 0 ) {
							$eventcontent .= '<div class="event-organizers">';
							foreach ( $cellevent['organizers'] as $organizer ) {
								$eventcontent .= '<div class="event-organizer" itemprop="organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
							}
							$eventcontent .= '</div>';
						}
						if ( $cellevent['location'] != '' ) {
							$html .= '<div class="event-venue" itemprop="location" itemscope itemtype="http://schema.org/Place">
                                        <span itemprop="name">';
							if ( isset( $cellevent['venue']['name'] ) ) {
								$html .= '<a href="' . $cellevent['venue']['permalink'] . '">' . $cellevent['venue']['name'] . '</a>';
							}

							$html .= '</span>
                                        <div class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                          <span itemprop="streetAddress">' . $cellevent['location'] . '</span>
                                        </div>
                                      </div>';
						}
						$html .= '<div class="event-content" itemprop="description">' . ( $cellevent['details'] ? $cellevent['details'] : $this->eventemptytext ) . '</div></div>';
					}
					$html .= '</div>';

					return $html;
				}
			}

		} else {
			if ( $this->displaytype == 'week' || $this->displaytype == 'day' || $this->displaytype == '4day' ) {
				$content .= '<div class="event-main-content no-events">' . __( 'No events', 'ecwd' ) . '</div>';
			}
		}

		$html .= '>' . $content . '</td>';

		return $html;
	}


	public function arraySort( $a, $subkey ) {
		foreach ( $a as $k => $v ) {
			$b[ $k ] = strtolower( $v[ $subkey ] );
		}
		asort( $b );
		foreach ( $b as $key => $val ) {
			$c[] = $a[ $key ];
		}

		return $c;
	}

// add/subtract day,week,month,days from startdate

	public function getDay( $date, $type = 1 ) {
		$date       = date( 'Y-n-j', strtotime( $date ) );
		$date_parts = explode( '-', $date );
		$jd         = cal_to_jd( CAL_GREGORIAN, $date_parts[1], $date_parts[2], $date_parts[0] );

		return jddayofweek( $jd, $type );
	}

// recursive array search returns the key of occurance (int) or false if not found

	public function arraySearch( $needle, $haystack, $index = null ) {
		$aIt = new RecursiveArrayIterator( $haystack );
		$it  = new RecursiveIteratorIterator( $aIt );
		while ( $it->valid() ) {
			if ( ( ( isset( $index ) AND ( $it->key() == $index ) ) OR ( ! isset( $index ) ) ) AND ( $it->current() == $needle ) ) {
				return $aIt->key();
			}
			$it->next();
		}

		return false;
	}

	public function getKey( $array, $member, $value ) {
		foreach ( $array as $k => $v ) {
			if ( $v->$member == $value ) {
				return $k;
			}
		}

		return false;
	}

	//return current week start and end dates

	public function rangeWeek( $datestr ) {
		date_default_timezone_set( date_default_timezone_get() );
		$res['start'] = date( "Y-m-d", strtotime( $datestr ) );
		$res['end']   = date( "Y-m-d", strtotime( "+6 day", strtotime( $datestr ) ) );

		return $res;
	}

	public function range4Days( $date ) {
		date_default_timezone_set( date_default_timezone_get() );
		$res['start'] = date( "Y-m-d", strtotime( $date ) );
		$res['end']   = date( "Y-m-d", strtotime( "+3 day", strtotime( $date ) ) );

		return $res;
	}

	public function calendar_foot() {
		if ( in_array( $this->displaytype, array(
			'full',
			'mini'
		) ) ) {
			$html = '</table>';
		} else {
			$html = '</div>';
		}

		return $html;
	}

// Removes tabs, line breaks, vertical tabs, null-byte. Everything but a regular space.

	public function stripWhitespace( $c ) {
		$c = str_replace( array(
			"\n",
			"\r",
			"\t",
			"\o",
			"\xOB"
		), '', $c );

		return trim( $c );
	}

// sorts an associative array by values of passed key

	public function hex2RGB( $hexStr, $returnAsString = false, $seperator = ',' ) {
		$hexStr   = preg_replace( "/[^0-9A-Fa-f]/", '', $hexStr ); // check hex string
		$rgbArray = array();
		if ( strlen( $hexStr ) == 6 ) { // if a proper hex code e.g. #RRGGBB
			$colorVal          = hexdec( $hexStr );
			$rgbArray['red']   = 0xFF & ( $colorVal >> 0x10 );
			$rgbArray['green'] = 0xFF & ( $colorVal >> 0x8 );
			$rgbArray['blue']  = 0xFF & $colorVal;
		} elseif ( strlen( $hexStr ) == 3 ) { // if shorthand notation e.g #RGB
			$rgbArray['red']   = hexdec( str_repeat( substr( $hexStr, 0, 1 ), 2 ) );
			$rgbArray['green'] = hexdec( str_repeat( substr( $hexStr, 1, 1 ), 2 ) );
			$rgbArray['blue']  = hexdec( str_repeat( substr( $hexStr, 2, 1 ), 2 ) );
		} else {
			return false; // invalid hex color code
		}

// returns the rgb string or the associative array
		return $returnAsString ? implode( $seperator, $rgbArray ) : $rgbArray;
	}

	public function getAndReplaceFirstImage( $content ) {
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
		if ( isset( $matches [1] [0] ) ) {
			$first_img = $matches [1] [0];
		}

		if ( empty( $first_img ) ) { //Defines a default image
			return false;
		} else {
			//preg_replace('/<img[^>]+\>/i', '', $content);
			$content = $this->replaceFirstImage( $content );
		}

		return array(
			'image'   => $first_img,
			'content' => $content
		);
	}

	public function replaceFirstImage( $content ) {
		$content = preg_replace( "/<img[^>]+\>/i", " ", $content, 1 );

		return $content;
	}
}


// end class