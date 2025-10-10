jQuery(document).ready(function () {
    jQuery("#njt-fs-review a").on("click", function () {
        const thisElement = this;
        const fieldValue = jQuery(thisElement).attr("data");
        const freeLink = "https://wordpress.org/support/plugin/filester/reviews/#new-post";
        let hidePopup = false;
        if (fieldValue == "rateNow") {
            window.open(freeLink, "_blank");
        } else {
            hidePopup = true;
        }

        jQuery
        .ajax({
          dataType: 'json',
          url: wpDataFs.admin_ajax,
          type: "post",
          data: {
            action: "njt_fs_save_review",
            field: fieldValue,
            nonce: wpDataFs.nonce,
          },
        })
        .done(function (result) {
            if (hidePopup == true) {
                jQuery( "#njt-fs-review .notice-dismiss" ).trigger( "click" );
            }
        })
        .fail(function (res) {
            if (hidePopup == true) {
                console.log(res.responseText);
                jQuery( "#njt-fs-review .notice-dismiss" ).trigger( "click" );
            }
        });
    })
})