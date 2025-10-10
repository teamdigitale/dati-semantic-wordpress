(($) => {
	$('document').ready((event) => {
		var coll = document.getElementsByClassName("collapsible");
		var i;

		
		let timedelay = 0;

		if($._data( $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').get(0), "events" ) &&
			$._data( $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').get(0), "events" ).click &&
			$._data( $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').get(0), "events" ).click.length > 0) {

			$._data( $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').get(0), "events" ).click.forEach(function(callback, index) {
				if(callback.handler && callback.handler.toString().indexOf(`animate({scrollTop:e.offset().top}`) > 0) {
					
					//smoothscroll = $._data( $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').get(0), "events" ).click[0].handler;
					$._data( $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').get(0), "events" ).click.splice(index, 1);
				}
			})
		}

		let smoothscript = false;
		let stickywidthscript = false;

		$('script').each(function() {
			if(smoothscript !== false && stickywidthscript !== false) {
				return;
			}
			if($(this).html().indexOf(`animate({scrollTop:e.offset().top`) > 0) {
				smoothscript = $(this).html();
			}
			if($(this).html().indexOf(`jQuery(window).width() >= `) > 0) {
				stickywidthscript = $(this).html();
			}
		})

		let stickminwidth = 1121;
		if(stickywidthscript !== false) {
			let matches = stickywidthscript.match(/jQuery\(window\)\.width\(\) \>\= (\d*)/);

			if(matches[1]) {
				stickminwidth = parseInt(matches[1]);
			}
		}
			
		if(smoothscript !== false) {
			let matches = smoothscript.match(/scrollTop\:e\.offset\(\)\.top[^\,]*\,(\d*)/);
			
			if(matches[1]) {
				timedelay = parseInt(matches[1]);
			}
		}
		

		$('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(t) {


			let heightoffset= 0;
			let stickyheader = $('.oxy-sticky-header');
			if(stickyheader.length > 0 && $(window).width() >= stickminwidth) {
				heightoffset += stickyheader.height();
			}
			if($('#wpadminbar').length > 0 && $(window).width() >= 601) {
				heightoffset += $('#wpadminbar').height();
			}
			
			
		
			if (location.pathname.replace(/^\//, "") == this.pathname.replace(/^\//, "") && location.hostname == this.hostname) {
		        var e = jQuery(this.hash);
		        (e = e.length ? e : jQuery("[name=" + this.hash.slice(1) + "]")).length && (t.preventDefault(), jQuery("html, body").animate({
		            scrollTop: e.offset().top-heightoffset
		        }, timedelay))
		    }

			
		});
		

		for (i = 0; i < coll.length; i++) {
			
		
			if(coll[i].classList.contains("active")) {
				coll[i].nextElementSibling.style.maxHeight = coll[i].nextElementSibling.scrollHeight + "px";
			}
				
			
		  coll[i].addEventListener("click", function() {
		    this.classList.toggle("active");
		    var content = this.nextElementSibling;
		   
		    if (content.style.maxHeight && !this.classList.contains("active")){
		      content.style.maxHeight = null;
		    } else {
		      content.style.maxHeight = content.scrollHeight + "px";
		    }
		  });
		}
	});
})(jQuery)