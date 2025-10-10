const njtFileManager = {
  sunriseCreateCookie(name, value, days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
  },

  sunriseReadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  },

  capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  },

  //Setting tab
  activeTabSetting() {
    var pagenow = "njt-fs-filemanager-settings-tab";
    jQuery("#njt-plugin-tabs a").click(function (event) {
      jQuery("#njt-plugin-tabs a").removeClass("nav-tab-active");
      jQuery(".njt-plugin-setting").hide();
      jQuery(this).addClass("nav-tab-active");
      if (jQuery(this).data('tab') == 'njt_fs_roles') {
        location.hash = "#user-role-restrictions";
      } else {
        var noHashURL = window.location.href.replace(/#.*$/, '');
        window.history.replaceState('', document.title, noHashURL)
      }

      // Show current pane
      jQuery(".njt-plugin-setting:eq(" + jQuery(this).index() + ")").show();
      njtFileManager.sunriseCreateCookie(pagenow + "_last_tab", jQuery(this).index(), 365);
    });

    //Auto-open tab by cookies
    if (njtFileManager.sunriseReadCookie(pagenow + "_last_tab") != null)
      jQuery("#njt-plugin-tabs a:eq(" + njtFileManager.sunriseReadCookie(pagenow + "_last_tab") + ")").trigger("click");
    // Open first tab by default
    else jQuery("#njt-plugin-tabs a:eq(0)").trigger("click");
  },

  themeSelector() {
    if (jQuery('input[name = "selected-theme"]')) {
      const selectedTheme = jQuery('input[name = "selected-theme"]').val()
      jQuery('#selector-themes').val(selectedTheme);
    }

    jQuery('select#selector-themes').on('change', function () {
      const themesValue = jQuery(this).val()
      const dataThemes = {
        'action': 'selector_themes',
        'themesValue': themesValue,
        'nonce': wpData.nonce,
      }
      jQuery.post(
        wpData.admin_ajax,
        dataThemes,
        function (response) {
          jQuery('link#themes-selector-css').attr('href', response.data)
        });
    });
  },

  actionSettingFormSubmit() {
    jQuery('.njt-settings-form-submit').on('click', function () {
      const arraylistUserAccess = [];
      jQuery('.fm-list-user-item').each(function () {
        if (jQuery(this).is(":checked")) {
          arraylistUserAccess.push(jQuery(this).val());
        }
      });
      if (!wpData.is_multisite) {
        arraylistUserAccess.push('administrator')
      }
      jQuery("#list_user_alow_access").val(arraylistUserAccess)
    })
  },

  userHasApproved() {
    const arrayUserHasApproved = jQuery('#list_user_has_approved').val() ? jQuery('#list_user_has_approved').val().split(",") : []
    for (itemUserHasApproved of arrayUserHasApproved) {
      if (!wpData.is_multisite) {
        if (itemUserHasApproved != 'administrator') {
          jQuery('input[name = ' + itemUserHasApproved + ']').prop('checked', true);
        }
      } else {
        jQuery('input[name = ' + itemUserHasApproved + ']').prop('checked', true);
      }
     
    }
  },

  actionSubmitRoleRestrictionst() {
    jQuery('#njt-form-user-role-restrictionst').on('click', function () {
      const arrayUserRestrictionsAccess = [];
      if (!jQuery('.njt-fs-list-user-restrictions').val()) {
        alert('Please select a User Role at Setings tab to use this option.')
        return false;
      }
      jQuery('.fm-list-user-restrictions-item').each(function () {
        if (jQuery(this).is(":checked")) {
          arrayUserRestrictionsAccess.push(jQuery(this).val());
        }
      });
      jQuery("#list_user_restrictions_alow_access").val(arrayUserRestrictionsAccess)

      if (jQuery("#hide_paths").val().trim().length > 0) {
        const valueHidePaths = jQuery("#hide_paths").val().trim().split("|")
        const newValueHidePaths = []
        for (const itemHidePath of valueHidePaths) {
          if (itemHidePath.trim().length > 0) {
            newValueHidePaths.push(itemHidePath.trim())
          }
        }
        jQuery("#hide_paths").val(newValueHidePaths.join("|"))
      }

      if (jQuery("#lock_files").val().trim().length > 0) {
        const valueLockFiles = jQuery("#lock_files").val().trim().split("|")
        const newValueLockFiles = []
        for (const itemLockFile of valueLockFiles) {
          if (itemLockFile.trim().length > 0) {
            newValueLockFiles.push(itemLockFile.trim())
          }
        }
        jQuery("#lock_files").val(newValueLockFiles.join("|"))
      }

    })
  },

  restrictionsHasApproved() {
    const arrayRestrictionsHasApproved = jQuery('#list_restrictions_has_approved').val() ? jQuery('#list_restrictions_has_approved').val().split(",") : []
    for (itemRestrictionsHasApproved of arrayRestrictionsHasApproved) {
      jQuery('input[name = ' + itemRestrictionsHasApproved + ']').prop('checked', true);
    }
  },

  ajaxRoleRestrictions() {
    jQuery('select.njt-fs-list-user-restrictions').on('change', function () {
      const valueUserRole = jQuery(this).val()
      const dataUserRole = {
        'action': 'get_role_restrictions',
        'valueUserRole': valueUserRole,
        'nonce': wpData.nonce,
      }
      jQuery.post(
        wpData.admin_ajax,
        dataUserRole,
        function (response) {
          const resRestrictionsHasApproved = response.data.disable_operations ? response.data.disable_operations.split(",") : []
          const resPrivateFolderAccess = response.data.private_folder_access ? response.data.private_folder_access : ''
          const resPrivateURLFolderAccess = response.data.private_url_folder_access ? response.data.private_url_folder_access : ''
          const resHidePaths = response.data.hide_paths ? response.data.hide_paths.replace(/[,]+/g, ' | ') : '';
          const resLockFiles = response.data.lock_files ? response.data.lock_files.replace(/[,]+/g, ' | ') : '';
          const resCanUploadMime = response.data.can_upload_mime ? response.data.can_upload_mime : '';
          jQuery('input.fm-list-user-restrictions-item').prop('checked', false);
          for (itemRestrictionsHasApproved of resRestrictionsHasApproved) {
            jQuery('input[name = ' + itemRestrictionsHasApproved + ']').prop('checked', true);
          }
          // Set value for textarea[name='private_folder_access']
          jQuery('textarea#private_folder_access').val(resPrivateFolderAccess)
          // Set value for textarea[name='private_url_folder_access']
          jQuery('textarea#private_url_folder_access').val(resPrivateURLFolderAccess)
          // Set value for textarea[name='hide_paths']
          jQuery('textarea#hide_paths').val(resHidePaths)
          // Set value for textarea[name='lock_files']
          jQuery('textarea#lock_files').val(resLockFiles)
          // Set value for textarea[name='can_upload_mime']
          jQuery('textarea#can_upload_mime').val(resCanUploadMime)
        });
    });
  },
  clickedCreatRootPath() {
    jQuery('.js-creat-root-path').on('click', function () {
      const valueRootPath = wpData.ABSPATH
      jQuery('textarea#private_folder_access').val(valueRootPath)
    })
  },

  ajaxSaveSettings() {
    jQuery('.njt-settings-form-submit').on('click', function () {
      const arraylistUserAccess = [];
      jQuery(this).addClass('njt-fs-updating-message');
      jQuery('.fm-list-user-item').each(function () {
        if (jQuery(this).is(":checked")) {
          arraylistUserAccess.push(jQuery(this).val());
        }
      });
      if (!wpData.is_multisite) {
        arraylistUserAccess.push('administrator')
      }
      jQuery("#list_user_alow_access").val(arraylistUserAccess)
      const list_user_alow_access = jQuery("#list_user_alow_access").val()
      const root_folder_path = jQuery("#root_folder_path").val()
      const root_folder_url = jQuery("#root_folder_url").val()
      const upload_max_size = jQuery("#upload_max_size").val()
      const fm_locale = jQuery("#fm_locale").val()
      const enable_htaccess = jQuery("#enable_htaccess").is(":checked")
      const enable_trash = jQuery("#enable_trash").is(":checked")
      const enable_sensitive_protection = jQuery("#enable_sensitive_protection").is(":checked")
      const data = {
        'nonce': wpData.nonce,
        'action': 'njt_fs_save_setting',
        'root_folder_path': root_folder_path,
        'root_folder_url': root_folder_url,
        'list_user_alow_access': list_user_alow_access,
        'upload_max_size': upload_max_size,
        'fm_locale': fm_locale,
        'enable_htaccess': enable_htaccess,
        'enable_trash': enable_trash,
        'enable_sensitive_protection': enable_sensitive_protection

      }
      const toastr_opt = {
        closeButton: true,
        showDuration: 300,
        hideDuration: 300,
        hideMethod: "fadeOut",
        positionClass: "toast-top-right njt-fs-toastr"
      }
      jQuery.post(
        wpData.admin_ajax,
        data,
        function (response) {
          const list_access = response.data.njt_fs_file_manager_settings.list_user_alow_access
          const index = list_access.indexOf('administrator');
          if (index > -1) {
            list_access.splice(index, 1);
          }

          if (list_access.length > 0) {
            jQuery('.njt-fs-list-user-restrictions').empty()
            list_access.forEach((item) => {
              jQuery('.njt-fs-list-user-restrictions').append(`<option value="${item}"> ${njtFileManager.capitalizeFirstLetter(item)} </option>`);
            });
            jQuery('.njt-text-error').hide()
          } else {
            jQuery('.njt-fs-list-user-restrictions').empty()
            jQuery('.njt-fs-list-user-restrictions').append('<option selected="" disabled="" hidden="">Nothing to choose</option>');
            jQuery('.njt-text-error').show()
          }
          jQuery('.njt-fs-list-user-restrictions').change()
          if (response.success) {
            jQuery('.njt-settings-form-submit').removeClass('njt-fs-updating-message');
            toastr.success('Changes Saved', '', toastr_opt)
          } else {
            jQuery('.njt-settings-form-submit').removeClass('njt-fs-updating-message');
            toastr.error('Please try again later', '', toastr_opt)
          }
        });
    })
  },
  ajaxSaveSettingsRestrictions() {
    jQuery('#njt-form-user-role-restrictionst').on('click', function () {
      jQuery(this).addClass('njt-fs-updating-message');
      const njt_fs_list_user_restrictions = jQuery(".njt-fs-list-user-restrictions").val()
      const list_user_restrictions_alow_access = jQuery("#list_user_restrictions_alow_access").val()
      const private_folder_access = jQuery("#private_folder_access").val()
      const private_url_folder_access = jQuery("#private_url_folder_access").val()
      const hide_paths = jQuery("#hide_paths").val()
      const lock_files = jQuery("#lock_files").val()
      const can_upload_mime = jQuery("#can_upload_mime").val()

      const data = {
        'nonce': wpData.nonce,
        'action': 'njt_fs_save_setting_restrictions',
        'njt_fs_list_user_restrictions': njt_fs_list_user_restrictions,
        'list_user_restrictions_alow_access': list_user_restrictions_alow_access,
        'private_folder_access': private_folder_access,
        'private_url_folder_access': private_url_folder_access,
        'hide_paths': hide_paths,
        'lock_files': lock_files,
        'can_upload_mime': can_upload_mime
      }
      const toastr_opt = {
        closeButton: true,
        showDuration: 300,
        hideDuration: 200,
        hideMethod: "fadeOut",
        positionClass: "toast-top-right njt-fs-toastr"
      }
      jQuery.post(
        wpData.admin_ajax,
        data,
        function (response) {
          if (response.success) {
            jQuery('#njt-form-user-role-restrictionst').removeClass('njt-fs-updating-message');
            toastr.success('Changes Saved', '', toastr_opt)
          } else {
            jQuery('#njt-form-user-role-restrictionst').removeClass('njt-fs-updating-message');
            toastr.error('Error! Please try again', '', toastr_opt)
          }
        });
    })
  },
  fsConnector() {
    jQuery('#njt-fs-file-manager').elfinder({
      url: ajaxurl,
      handlers : {
        dblclick : function(event, elfinderInstance) {
          event.preventDefault();
          elfinderInstance.exec('getfile')
          .done(function() { 
            try {
              elfinderInstance.exec('edit'); 
            } catch (e) {
              elfinderInstance.exec('quicklook'); 
            }
          })
          .fail(function() { elfinderInstance.exec('open');});
        }
      },
      // disable quicklook
      // bootCallback : function(fm) {
      //     fm.bind('init', function() {
      //         fm._commands.quicklook.getstate = function() {
      //             return -1;
      //         }
      //     });
      // },
      getFileCallback : function(files, fm) {
        return false;
      }, 
      contextmenu: {
        // current directory file menu
        files: ['getfile', '|', 'open', 'opennew', 'download', 'opendir', 'quicklook', 'email', '|', 'upload',
          'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', 'empty', 'hide', '|', 'rename', 'edit',
          'resize', '|', 'archive', 'extract', '|', 'selectall', 'selectinvert', '|', 'places', 'info', 'chmod',
          'netunmount'
        ],
        // navbarfolder menu
        navbar: ['open', 'opennew', 'download', '|', 'upload', 'mkdir', '|', 'copy', 'cut', 'paste', 'duplicate',
          '|', 'rm', 'empty', 'hide', '|', 'rename', '|', 'archive', '|', 'places', 'info', 'chmod',
          'netunmount'
        ],
        // current directory menu
        cwd: ['undo', 'redo', '|', 'back', 'up', 'reload', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|',
          'empty', 'hide', '|', 'view', 'sort', 'selectall', 'colwidth', '|', 'places', 'info', 'chmod',
          'netunmount',
          '|', 'fullscreen', '|'
        ],
      },
      uiOptions : {
        // toolbar configuration
        toolbar : [
          ['home', 'back', 'forward', 'up', 'reload'],
          ['netmount'],
          ['mkdir', 'mkfile', 'upload'],
          ['open', 'download', 'getfile'],
          ['undo', 'redo'],
          ['copy', 'cut', 'paste', 'rm', 'empty', 'hide'],
          ['duplicate', 'rename', 'edit', 'resize', 'chmod'],
          ['selectall', 'selectnone', 'selectinvert'],
          ['quicklook', 'info'],
          ['extract', 'archive'],
          ['search'],
          ['view', 'sort'],
          ['preference', 'help'],
          ['fullscreen']
        ],
        toolbarExtra : {
          defaultHides: [],
          // show Preference button into contextmenu of the toolbar (true / false)
          preferenceInContextmenu: false
        },
      },
      ui: ['toolbar', 'tree', 'path', 'stat'],
      customData: {
        action: 'fs_connector',
        nonce: wpData.nonce_connector,
      },
      lang: wpData.lang,
      requestType: 'get',
      width: 'auto',
      height: '600',
    });
  }
}

jQuery(document).ready(function () {
  if (jQuery("div").hasClass("njt-fs-file-manager")) {

    //set select value
    njtFileManager.themeSelector();
    // Start- Setting for `Select User Roles to access`
    njtFileManager.actionSettingFormSubmit();
    // Get value to prop checked for input checkbox
    njtFileManager.userHasApproved();
    //Setting tab
    njtFileManager.activeTabSetting();

    njtFileManager.actionSubmitRoleRestrictionst();
    // Get value to prop checked for input checkbox
    njtFileManager.restrictionsHasApproved();
    //Ajax change value
    njtFileManager.ajaxRoleRestrictions();
    //Creat root path default
    njtFileManager.clickedCreatRootPath();
    // End- Setting for `Select User Roles Restrictions to access`

    //Ajax settings
    njtFileManager.ajaxSaveSettings();
    njtFileManager.ajaxSaveSettingsRestrictions();
    if(jQuery(".elfinder-theme-ext") > 0) {
      jQuery(".elfinder-theme-ext").remove()
    }
    njtFileManager.fsConnector();

  }
});
