(function($) {

	$('document').ready(function() {

		
      //let cleanArea = $('<div class="oxy-cleaner-area"></div>')
      let cleanPanel = $('<div class="oxy-cleaner-panel" style="display:none"></div>');
      let cleanLabel = $('<h3 class="ct-css-section-label">Cleanup</h3>');
      let broomIcon = $('<span class="oxy-cleaner-broom-icon">C</span>');
      let unusedList = $('<select multiple class="oxy-cleaner-list"><option></option></select');
      let scanButton = $('<button class="oxy-cleaner-button">Scan for un-used classes</button>');
      let deleteButton = $('<button class="oxy-cleaner-button" style="display: none">Delete Selected</button>');
      let messageBox = $('<div class="oxy-cleaner-messages"></div>');
      let closeButton = $('<svg class="oxygen-close-icon""><use xlink:href="#oxy-icon-cross"></use></svg>');
      let renameLabel = $('<h3 class="ct-css-section-label">Rename</h3>');
      let renameFrom = $('<div class="oxygen-measure-box"><input placeholder="existing name" type="text" class="oxy-cleaner-rename oxy-cleaner-rename-source" /></div>');
      let renameTo = $('<div class="oxygen-measure-box"><input placeholder="new name" type="text" class="oxy-cleaner-rename oxy-cleaner-rename-source" /></div>');
      let renameButton = $('<button class="oxy-cleaner-button">Rename</button>');
      let renameMessageBox = $('<div class="oxy-cleaner-messages"></div>');
      let renameContainer = $('<div class="oxy-cleaner-rename-div">')
      renameContainer.append(renameLabel).append(renameFrom).append(renameTo).append(renameButton).append(renameMessageBox);
      cleanPanel.append(cleanLabel).append(closeButton).append(scanButton).append(messageBox).append(unusedList).append(deleteButton).append(renameContainer);
      
      let oldSwitchTab = iframeScope.parentScope.switchTab;
      iframeScope.parentScope.switchTab = function(primary, secondary) {

         if(primary === 'sidePanel' && secondary === 'selectors') {
            cleanPanel.hide();
            setTimeout(() => {
               //cleanArea.append(broomIcon);
               let container = $(".ct-selectors-tab > .oxygen-sidepanel-header-row", parent.document);
               broomIcon.insertBefore(container.children('.oxygen-close-icon'));
               cleanPanel.insertAfter(container);
            }, 500);
            
         }

         oldSwitchTab(primary, secondary);

      }
      iframeScope.parentScope.$apply();


      let usedClasses = {};
      let renamed = 0;

      function scanLocalClasses(children, rename) {
         let classes = null;
         
         if(rename) {
            classes = 0;
         } else {
            classes = [];
         }

          for(let child of children) {
              if(child['options'] && child['options']['classes']) {
                  if(rename) {
                     let index = child['options']['classes'].indexOf(rename.nameFrom);
                     if(index !== -1) {
                        child['options']['classes'].splice(index, 1, rename.nameTo);
                        if(child['options']['activeselector'] === rename.nameFrom) {
                           child['options']['activeselector'] = rename.nameTo;
                        }
                        classes++;
                     }
                  } else {
                     classes = classes.concat(child['options']['classes']);
                  }
              }
              if(child.children) {
                  if(rename) {
                     classes += scanLocalClasses(child.children, rename);
                  } else {
                     classes = classes.concat(scanLocalClasses(child.children));
                  }
              }
          }
          return classes;
      }

      function scanClasses(rename, step, pageindex) {

         if(step > 1000) {
            if(rename) {
               renameButton.removeClass('oxygen-small-progress');
               renameMessageBox.html(`<p>Renamed the class and updated ${renamed} references</p>`);
               
               if(iframeScope.isEditing('custom-selector')) {
                  iframeScope.setCustomSelectorToEdit(iframeScope.selectorToEdit);
                  if(iframeScope.component.active.id > 0) {
                     iframeScope.activateComponent(iframeScope.component.active.id);
                  }
               }

               setTimeout(() => {
                  iframeScope.rebuildDOM(0)
               }, 500)
               
            } else {
               let count = 0;
               unusedList.html('');
               scanButton.removeClass('oxygen-small-progress');
               for(let i in iframeScope.classes) {
                  if(!usedClasses[i]) {
                     count++;
                     unusedList.append($(`<option value='${i}'>${i}</option>`));   
                  }
               }

               if(count === 0) {
                  unusedList.append($(`<option></option>`));  
                  messageBox.html('<p>No un-used classes found.</p>') 
               }
               else {
                  messageBox.html('');
                  
               }

               deleteButton.show();
            }

            return;
         }

         var data = {
            'action': 'oxy_cleaner_backend',
            //'nonce': jQuery('#oxygen_vsb_sign_shortcodes_nonce').val(),
            'except' : window.CtBuilderAjax.postId,
         };

         if(typeof(step) !== 'undefined') {
            data['step'] = step;
         }

         if(typeof(pageindex) !== 'undefined') {
            data['index'] = pageindex;
         }

         if(rename) {
            data['rename'] = rename;
         }


         $.post(CtBuilderAjax.ajaxUrl, data, function(response) {
            
            if(rename) {
               renamed+=parseInt(response['classes']);
            } else {
               for(let usedClass of response['classes']) {
                  usedClasses[usedClass] = 1;
               }
            }

            if(typeof(response['step']) !== 'undefined') {
               
               if(typeof(response['index']) !== 'undefined') {
                  scanClasses(rename, parseInt(response['step']), parseInt(response['index']));
               }
               else {
                  scanClasses(rename, parseInt(response['step']));
               }
            }
         });

         
      }

      broomIcon.on('click', (e) => {
         cleanPanel.toggle();
         unusedList.html('');
         unusedList.append($(`<option></option>`));
         messageBox.html('');
         renameMessageBox.html('');
         deleteButton.hide();
         unusedList.hide();
      });

      scanButton.on('click', (e) => {
         deleteButton.hide();
         usedClasses = {};
         unusedList.show();
         unusedList.html('');
         unusedList.append($(`<option></option>`));
         let localClasses = [];
         if(iframeScope.componentsTree.children) {
            localClasses = scanLocalClasses(iframeScope.componentsTree.children);

            for(let usedClass of localClasses) {
               usedClasses[usedClass] = 1;
            }
         }
         
         scanButton.addClass('oxygen-small-progress');
         scanClasses();
      })


      deleteButton.on('click', (e) => {
         unusedList.val().forEach(item => { 
            unusedList.children(`[value="${item}"]`).remove();
            delete iframeScope.classes[item]; 
         })
         iframeScope.$apply();
      })

      closeButton.on('click', (e) => {
         cleanPanel.hide();
      })

      renameButton.on('click', (e) => {
         
         renamed = 0;

         let nameFrom = renameFrom.children('input').val().trim();
         let nameTo = renameTo.children('input').val().trim();
         
         
         // check if the destination name is a valid class name
         var valid = iframeScope.validateClassName(nameFrom) && iframeScope.validateClassName(nameTo);

         if (!valid) {
             alert("Wrong class(s) name. Names should not be blank and Names must begin with an underscore (_), a hyphen (-), or a letter(a–z), followed by any number of hyphens, underscores or letters.");
             return false;
         };

         // look for the source name in the existing list
         if(nameFrom === '' || !iframeScope.classes[nameFrom]) {
            alert('A class with the given \'existing name\' does not exist');
            return false;
         }
         
         if( iframeScope.classes[nameTo]) {
            alert(`A class with the provided new name '${nameTo}'' already exists. Provide a unqiue class name`);
            return false;
         }

         if(!confirm(`This will update the class references across the whole site, and those changes will be final. But, you will need to save these changes for the current post open in the builder. If you do not save after making these changes, the other pages will end up with broken references. Do you want to proceed?`)) {
            return false;
         }

         // now rename the class
         iframeScope.classes[nameTo] = iframeScope.classes[nameFrom];
         iframeScope.classes[nameTo]['key'] = nameTo;
         delete(iframeScope.classes[nameFrom]);

         // update local references
         
         if(iframeScope.componentsTree.children) {
            renamed = scanLocalClasses(iframeScope.componentsTree.children, {nameFrom, nameTo});
         }
         if(iframeScope.currentClass === nameFrom) {
            iframeScope.currentClass = nameTo;
         }
         iframeScope.$apply();
         // with the intent to rename
         renameButton.addClass('oxygen-small-progress');
         renameMessageBox.html('');
         scanClasses({nameFrom, nameTo})
         
      })


	});
})(jQuery);