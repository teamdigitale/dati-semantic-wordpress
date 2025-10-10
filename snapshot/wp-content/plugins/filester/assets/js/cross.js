jQuery(document).ready(function () {
    const fbv_cross = `<div id="filebird_cross" class="fbv-cross-wrap">
    <div class="fbv-cross-popup">
      <div class="fbv-cross-icon-wrap">
        <i class="fbv-icon fbv-i-folder"></i>
        <i class="dashicons dashicons-no-alt"></i>
      </div>
      <div class="fbv-cross-sub">
        <span>Organize your files</span>
      </div>
    </div>
    <div class="fbv-cross-window">
      <div class="fbv-cross-window-mess">
        <h3>Your WordPress media library is messy?</h3>
        <span>Start using FileBird to organize your files into folders by drag and drop.</span>
      </div>
      <div class="fbv-cross-window-img-wrap">
        <img src="https://ps.w.org/filebird/assets/screenshot-2.gif" alt="screenshot_demo">
      </div>
      <div class="fbv-cross-window-btn">
        <div><a class="button button-primary fbv-cross-install" href="javascript:;"><i class="dashicons dashicons-wordpress-alt"></i>Install for free</a></div>
        <div><a class="fbv-cross-link fbv-cross-hide-popup" href="javascript:;" rel="noopener noreferrer">Don't display again</a></div>
      </div>
    </div>
  </div>`

  const install_failed = `<div class="fbv-noti-install-failed"><div class="fbv-label-error">Oops! Installation failed.</div><div>Please try <a href="${njtCross.filebird_install_url}">manual installation</a>.</div></div>`

  jQuery.fn.exists = function (callback) {
    var args = [].slice.call(arguments, 1)
    if (this.length) {
      callback.call(this, args)
    }
    return this
  }
  jQuery('body.upload-php #wpfooter').exists(function () {
    njtCross.show_popup && this.append(fbv_cross)
  })
  jQuery('.fbv-cross-popup').click(function () {
    jQuery(this).parent().toggleClass('fbv-cross-popup-open')
  })
  jQuery('.fbv-cross-link.fbv-cross-hide-popup').click(function () {
    const a = jQuery('#filebird_cross')

    jQuery.ajax({
      url: ajaxurl,
      method: 'POST',
      data: {
        action: 'njt_filebird_cross_hide',
        nonce: njtCross.nonce,
        type: 'popup'
      },
      beforeSend: function(){
        a.removeClass('fbv-cross-popup-open').addClass('fbv_permanent_hide')
      },
      success: function(){
        // a.removeClass('fbv-cross-popup-open').addClass('fbv_permanent_hide')
        setTimeout(function () {
          a.remove()
        }, 2000)
      }
    })
    // const a = jQuery('#filebird_cross')
    // a.removeClass('fbv-cross-popup-open').addClass('fbv_permanent_hide')
    // setTimeout(function () {
    //   a.remove()
    // }, 2000)
  })
  jQuery('.fbv-cross-link.fbv-cross-hide-notification').click(function(){
    jQuery.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'njt_filebird_cross_hide',
        nonce: njtCross.nonce,
        type: 'notification'
      }
    }).done(function(result) {
      if (result.success) {
        jQuery('#njt-ads-wrapper button.notice-dismiss').click()
      } else {
        console.log("Error", result.data.status)
      }
    });
  })
  jQuery('.fbv-cross-install:not(.fbv_installing)').click(function (e) {
    e.preventDefault()
    const normal = '<i class="dashicons dashicons-wordpress-alt"></i>Install for free'
    const loading = '<i class="dashicons dashicons-update-alt"></i>Installing<span class="text-dots"><span>.<span></span>'
    const done = '<i class="dashicons dashicons-saved"></i>Installed! Organize files now'
    const error = '<i class="dashicons dashicons-warning"></i>Install failed. Retry'
    const a = jQuery(this)
    
    jQuery.ajax({
      url: ajaxurl,
      method: 'POST',
      data: {
          action: 'njt_filebird_cross_install',
          nonce: njtCross.nonce
      },
      beforeSend: function(){
        a.focusout()
        a.addClass('fbv_installing')
        a.html(loading)
      },
      success: function(response){
        if (response.success) {
          a.removeClass('fbv_installing').addClass('fbv_done')
          a.html(done)
          a.off('click')
          a.click(()=> { window.location.href = njtCross.media_url })
        } else {
          a.removeClass('fbv_installing').addClass('fbv_error')
          a.parent().after(install_failed)
          a.html(error)
        }
      },
      error: function(response){
        a.removeClass('fbv_installing').addClass('fbv_error')
        a.parent().after(install_failed)
        a.html(error)
      }
    })
    // setTimeout(function () {
    //   a.removeClass('fbv_installing').addClass('fbv_done')
    //   a.html(done)
    // }, 3000)
  })
})
