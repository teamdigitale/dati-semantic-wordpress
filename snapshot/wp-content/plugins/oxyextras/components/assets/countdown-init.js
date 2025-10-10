jQuery(document).ready(oxygen_init_countdown_timer);
function oxygen_init_countdown_timer($) {

  let extrasCountdown = function ( container ) {

    $(container).find('.oxy-countdown-timer').each(function(i, OxyCountdownTimer) {

        let $timer = $(OxyCountdownTimer).children('.oxy-countdown-timer_inner');
        let timerID = $(OxyCountdownTimer).attr('ID');
        let finalDate = $timer.data('countdown');
        let mode = $timer.data('mode');
        let remove = $timer.data('remove');
        let reset = $timer.data('reset');
        let labels = $timer.data('labels');
        let localnow = now
        let endtime, newstart;

        var now = luxon.DateTime.now();

        let remainingWeeks = '%!w';
        let remainingDays = (true === $timer.data('show-weeks') || true === remove) ? '%!d' : '%!D';
        let remainingHours = (true === $timer.data('show-days') || true === remove) ? '%!H' : '%!I';
        let remainingMinutes = (true === $timer.data('show-hours') || true === remove) ? '%!M' : '%!N';
        let remainingSeconds = (true === $timer.data('show-minutes') || true === remove) ? '%!S' : '%!T';

        let weekLabel = true === labels ? '<div class="oxy-countdown-timer_label"> '+ remainingWeeks + ':'+ $timer.data('week') +','+ $timer.data('week-plural') +'; </div>' : '';
        let dayLabel = true === labels ? '<div class="oxy-countdown-timer_label"> '+ remainingDays + ':'+ $timer.data('day') +','+ $timer.data('day-plural') +'; </div>' : '';
        let hourLabel = true === labels ? '<div class="oxy-countdown-timer_label"> '+ remainingHours + ':'+ $timer.data('hour') +','+ $timer.data('hour-plural') +'; </div>' : '';
        let minuteLabel = true === labels ? '<div class="oxy-countdown-timer_label"> '+ remainingMinutes + ':'+ $timer.data('minute') +','+ $timer.data('minute-plural') +'; </div>' : '';
        let secondLabel = true === labels ? '<div class="oxy-countdown-timer_label"> '+ remainingSeconds + ':'+ $timer.data('second') +','+ $timer.data('second-plural') +'; </div>' : '';

        let seperator = $timer.data('seperator') != null ? '<div class="oxy-countdown-timer_seperator">'+ $timer.data('seperator') +'</div>' : '';
        
        // Evergreen
        if ('evergreen' == mode) {

          if (localStorage.getItem('oxy' + timerID + '-last-time') === null || (true === reset) ) {
                endtime = now.plus({ weeks: $timer.data('weeks'), days: $timer.data('days'), hours: $timer.data('hours'), minutes: $timer.data('minutes'), seconds: $timer.data('seconds') }).toFormat("x");
                if( localStorage ) {
                  localStorage.setItem('oxy' + timerID + '-last-time', now);
                };
          } else {
           let lasttime = localStorage.getItem('oxy' + timerID + '-last-time');
              endtime = luxon.DateTime.fromISO(lasttime).plus({ weeks: $timer.data('weeks'), days: $timer.data('days'), hours: $timer.data('hours'), minutes: $timer.data('minutes'), seconds: $timer.data('seconds') }).toFormat("x");
          }

          doCountdown(endtime);

        } else if ('recurring' == mode) { 

          let start =  luxon.DateTime.fromISO(new Date($timer.data('start').replace(/-/g, '/').replace('T', ' ')).toISOString()).setZone($timer.data('timezone'), { keepLocalTime: true });
          let end = start.plus({ weeks: $timer.data('weeks'), days: $timer.data('days'), hours: $timer.data('hours'), minutes: $timer.data('minutes'), seconds: $timer.data('seconds') });
          endtime = end.toFormat("x");

          let recurringDays = $timer.data('recurring-days');

          // before start time, waiting with interval text
          if (now.toFormat("x") < start.toFormat("x") ) {
            $timer.html( $timer.data('interval-text') );
            $timer.addClass('oxy-countdown-timer_inner-visible');
          } 
          
          // is after end time.
          else if ( now.toFormat("x") > endtime ) {

            let difference = (now.toFormat("x") - start) / 60 / 60 / 24 / 1000;

            if ( Number.isInteger(Math.round(difference) / recurringDays) ) {
              newstart = start.plus({ days: Math.round(difference) });
              endtime = newstart.plus({ weeks: $timer.data('weeks'), days: $timer.data('days'), hours: $timer.data('hours'), minutes: $timer.data('minutes'), seconds: $timer.data('seconds') }).toFormat("x");
            
              if (now.toFormat("x") < newstart.toFormat("x") ) {
                $timer.html( $timer.data('interval-text') );
                $timer.addClass('oxy-countdown-timer_inner-visible');
              } else {
                doCountdown(endtime);
              }

            } 

          } 
          
          // is during countdown
          else {

            doCountdown(endtime);
          }
          
        } 
        
        else {

          //endtime = luxon.DateTime.fromISO(new Date($timer.data('countdown')).toISOString()).setZone($timer.data('timezone'), { keepLocalTime: true }).toFormat("x");
          endtime = luxon.DateTime.fromISO(new Date($timer.data('countdown').replace(/-/g, '/').replace('T', ' ')).toISOString()).setZone($timer.data('timezone'), { keepLocalTime: true }).toFormat("x");
          doCountdown(endtime);

        }

        function doCountdown(endtime) {

          let $elapse = ( 'count' === $timer.data('action') ) ? true : false;

          $timer.countdown(endtime, {defer: true, elapse: $elapse})
          .on('update.countdown', oxyCountdownUpdate)
          .on('finish.countdown', oxyCountdownFinish)
          .countdown('update') //
          .countdown('start');

        }


        function oxyCountdownUpdate(event) {

          let weeksOutput = (true === $timer.data('show-weeks') || true === remove ) ? '<div class="oxy-countdown-timer_item oxy-countdown-timer_weeks"><div class="oxy-countdown-timer_digits">%w</div>' + weekLabel + '</div>' + seperator : '';
          let daysDigit = (true === $timer.data('show-weeks') || true === remove) ? '%-d' : '%D';
          
          let daysOutput = (true === $timer.data('show-days') || true === remove) ? '<div class="oxy-countdown-timer_item oxy-countdown-timer_days"><div class="oxy-countdown-timer_digits">' + daysDigit + '</div>' + dayLabel + '</div>' + seperator : '';
          let hoursDigit = (true === $timer.data('show-days') || true === remove) ? '%H' : event.offset.totalHours;

          let hoursOutput = (true === $timer.data('show-hours') || true === remove) ? '<div class="oxy-countdown-timer_item oxy-countdown-timer_hours"><div class="oxy-countdown-timer_digits">'+ hoursDigit +'</div>' + hourLabel + '</div>' + seperator : '';
          let minutesDigit = (true === $timer.data('show-hours') || true === remove) ? '%M' : event.offset.totalMinutes;

          let minutesOutput = (true === $timer.data('show-minutes') || true === remove) ? '<div class="oxy-countdown-timer_item oxy-countdown-timer_minutes"><div class="oxy-countdown-timer_digits">' + minutesDigit + '</div>' + minuteLabel +  '</div>' + seperator : '';
          let secondsDigit = (true === $timer.data('show-minutes') || true === remove) ? '%S' : event.offset.totalSeconds;

          let secondsOutput = (true === $timer.data('show-seconds') || true === remove) ? '<div class="oxy-countdown-timer_item oxy-countdown-timer_seconds"><div class="oxy-countdown-timer_digits">' + secondsDigit + '</div>' + secondLabel + '</div>' : '';


            if (true === remove) {
                weeksOutput = event.offset.weeks == 0 ? '' : weeksOutput;
                daysOutput = event.offset.totalDays == 0 ? '' : daysOutput;
                hoursOutput = event.offset.totalHours == 0 ? '' : hoursOutput;
                minutesOutput = event.offset.totalMinutes == 0 ? '' : minutesOutput;
                secondsOutput = event.offset.totalSeconds == 0 ? '' : secondsOutput;
           }

            $timer.html(event.strftime(''
            + weeksOutput
            + daysOutput
            + hoursOutput
            + minutesOutput
            + secondsOutput
            ));

            $timer.addClass('oxy-countdown-timer_inner-visible');

        }

        function oxyCountdownFinish(event) {

          $('.oxy-countdown-timer_seconds .oxy-countdown-timer_digits').html('00');

          if ( 'redirect' === $timer.data('action') ) {
            if ( $timer.data('redirect') && ('recurring' !== mode) ) {
              window.location.href = $timer.data('redirect');
            }
          } else if ( 'hide' === $timer.data('action') ) {
            $timer.hide();
          } else if ( 'text' === $timer.data('action') ) {
            $timer.html( $timer.data('text') );
            $timer.addClass('oxy-countdown-timer_inner-visible');
          } else if ( 'alert' === $timer.data('action') ) {
            if (typeof extrasShowAlert == 'function') {
              extrasShowAlert( $($timer.data('selector'))[0] );
            }
          } else if ( 'offcanvas' === $timer.data('action') ) {
            if (typeof extrasOpenOffcanvas == 'function') {
              extrasOpenOffcanvas($($timer.data('selector'))[0]);
            }
          }
          
        }
       

    });

  }


  extrasCountdown('body');
                
  // Expose function
  window.extrasCountdown = extrasCountdown;

}    