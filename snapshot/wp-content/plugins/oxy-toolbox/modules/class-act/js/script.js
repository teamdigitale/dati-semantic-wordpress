(function($) {

   let disableWarnings = (parseInt(oxy_toolbox_class_act_options.disable_warnings) === 1);

   let idOnlyProps = ['ct_id', 'ct_parent', 'classes', 'selector', 'ct_content', 'nicename'];
   let idOnlyOriginals = ['src', 'href', 'globalConditionsResult', 'globalconditions', 'use-custom', 'custom-code', 'tag', 'conditionsresult', 'code-php', 'code-css', 'code-js', 'full_shortcode', 'embed_src'];

   function doClear(e) {

      let sourceClass = iframeScope.isEditing('class') ? iframeScope.currentClass : false;

      if(disableWarnings === false) {
         let warningMessage = `This will wipe off all the CSS properties in the ${sourceClass?sourceClass+' class':'ID of this component'}. Are you sure you don't want to 'copy' or 'move' the properties instead? Press OK to proceed with erasing/resetting all the styles or Cancel to cancel this operation.`; 

         if(!confirm(warningMessage)) {
            return;
         }

         if(!confirm(`I wont ask thrice?`)) {
            return;
         }
      }

      let id = iframeScope.component.active.id;
      
      let component = null;
      if(!sourceClass) {
         
         // get component from the tree,
         component = iframeScope.findComponentItem(iframeScope.componentsTree.children, id, iframeScope.getComponentItem)
      }

      clearStyles(component, sourceClass);
      
      

      // if it is part of a dynamic list, update the component
      var oxyList = iframeScope.getComponentById(id).closest('.oxy-dynamic-list');

      if(oxyList.length > 0) {
         iframeScope.rebuildDOM(oxyList.attr('ng-attr-component-id'));
      } else if(iframeScope.component.active.name === 'ct_span') {
         iframeScope.rebuildDOM(iframeScope.component.active.parent.id);
      } else {
        iframeScope.rebuildDOM(id);
      }

      iframeScope.$apply();
   }

   function doCopy(e) {
      createClass();
   }

   function doMove(e) {
      createClass(true);
   }

   function doCopyToId(e) {
      createId();
   }

   function doMoveToId(e) {
      createId(true);
   }

   function createId(resetSource) {

      if(iframeScope.isEditing('id')) {
         return;
      }

      let sourceClass = iframeScope.isEditing('class') ? iframeScope.currentClass : false;

      if(disableWarnings === false) {
         let warningMessage = `This will overwrite all the CSS properties in the ID of this component. Press OK to proceed with overwriting the ID.`; 
         
         if(!confirm(warningMessage)) {
            return;
         }

         if(resetSource === true) {
            warningMessage = `This will wipe off all the CSS properties in the  '${sourceClass}' class and can affect any elements still having the '${sourceClass}' class. Are you sure you want to 'move' instead of 'copy'?`;
            if(!confirm(warningMessage)) {
               return;
            }
         }

      }


      let id = iframeScope.component.active.id;
      let name = iframeScope.component.active.name;

      // get component from the tree,
      let component = iframeScope.findComponentItem(iframeScope.componentsTree.children, id, iframeScope.getComponentItem)
      clearStyles(component);

      
      
      let classObject = {};

      if(sourceClass) {
               
         if( !iframeScope.classes[sourceClass] ) {
            alert('The source class does not exist');
            return false;
         }

         classObject = JSON.parse(JSON.stringify(iframeScope.classes[sourceClass]));

         for(let i in classObject) {
            if(component.options[i]) {
               Object.assign(component.options[i], classObject[i]);
            } else {
               component.options[i] = classObject[i];
            }
         }
         
         if(resetSource) {
            clearStyles(component, sourceClass);
         }

         iframeScope.applyComponentDefaultOptions(id, name);
         
         var oxyList = iframeScope.getComponentById(id).closest('.oxy-dynamic-list');

         if(oxyList.length > 0) {
             iframeScope.rebuildDOM(oxyList.attr('ng-attr-component-id'));
         } else if(iframeScope.component.active.name === 'ct_span') {
            iframeScope.rebuildDOM(iframeScope.component.active.parent.id);
         } else {
           iframeScope.rebuildDOM(id);
         }
      }
      iframeScope.$apply();
   }

   function clearStyles(component, sourceClass) {
      
      if(sourceClass) {
         iframeScope.classes[sourceClass] = {original: {}};
      } else {
         let id = iframeScope.component.active.id;
         let name = iframeScope.component.active.name;
         // reset ID properties
         for(let i in component.options) {
            if(idOnlyProps.concat(['original']).indexOf(i) === -1) {
               delete(component.options[i])
            }
         }

         for(let i in component.options['original']) {
            if(idOnlyOriginals.indexOf(i) === -1) {
               delete(component.options['original'][i]);
            }
         }

         delete(iframeScope.component.options[id]['original']);
         if(iframeScope.component.options[id]['hover'])
            delete(iframeScope.component.options[id]['hover']);
         if(iframeScope.component.options[id]['before'])
            delete(iframeScope.component.options[id]['before']);
         if(iframeScope.component.options[id]['after'])
            delete(iframeScope.component.options[id]['after']);
         delete(iframeScope.component.options[id]['id']);
         delete(iframeScope.component.options[id]['model']);

         iframeScope.applyComponentDefaultOptions(id, name);
      }
   }

   function createClass(resetSource) {
      
      let sourceClass = iframeScope.isEditing('class') ? iframeScope.currentClass : false;

      let promptMessage = "Class name:";

      if(sourceClass && resetSource === true) {
         promptMessage = `This will wipe off all the CSS properties in the  '${sourceClass}' class and can affect any elements still having the '${sourceClass}' class. Are you sure you want to 'move' instead of 'copy'?`+"\n\n"+promptMessage
      }

      let className = prompt(promptMessage);

      if (className != null) {

            var valid = iframeScope.validateClassName(className);

            if (!valid) {
                alert("Wrong class name. Name must begin with an underscore (_), a hyphen (-), or a letter(a–z), followed by any number of hyphens, underscores or letters.");
                return false;
            };
            
            if( iframeScope.classes[className]) {
               alert("A class with this name already exists. Provide another name for a new class");
               return false;
            }

            let id = iframeScope.component.active.id;
            
            // get component from the tree,
            let component = iframeScope.findComponentItem(iframeScope.componentsTree.children, id, iframeScope.getComponentItem)
            
            // stringify and copy to the class
            let classObject = {};
            
            if(sourceClass) {
               
               if( !iframeScope.classes[sourceClass] ) {
                  alert('The source class does not exist');
                  return false;
               }

               classObject = JSON.parse(JSON.stringify(iframeScope.classes[sourceClass]));

            } else {
               classObject = JSON.parse(JSON.stringify(component.options));
            
               // remove ID only properties
               for(let prop of idOnlyProps) {
                  if(classObject[prop]) {
                     delete(classObject[prop]);
                  }
               }

               classObject['original'] = classObject['original'] || {};

               for(let prop of idOnlyOriginals) {
                  if(classObject['original'][prop]) {
                     delete(classObject['original'][prop]);
                  }
               }
            }

            iframeScope.classes[className] = classObject;

            // add this class to component
            
            component.options['classes'] = component.options['classes'] || []
            component.options['classes'].push(className)

            iframeScope.componentsClasses[id] = iframeScope.componentsClasses[id] || [];
            iframeScope.componentsClasses[id].push(className);



            iframeScope.setCurrentClass(className);
            

            if(resetSource === true) {
               
               clearStyles(component, sourceClass);

            }
            
            
            // if it is part of a dynamic list, update the component
            var oxyList = iframeScope.getComponentById(id).closest('.oxy-dynamic-list');
            
            if(oxyList.length > 0) {
                iframeScope.rebuildDOM(oxyList.attr('ng-attr-component-id'));
            } else if(iframeScope.component.active.name === 'ct_span') {
               iframeScope.rebuildDOM(iframeScope.component.active.parent.id);
            } else {
              iframeScope.rebuildDOM(id);
            }

            iframeScope.$apply();
        }


   }

   $('document').ready(function() {

      let panelContainer = $(".oxygen-media-query-and-selector-wrapper", parent.document);
      let position = 0;
      
      if(panelContainer.length < 1) {
         panelContainer = $("#eeui-editor--active-selector-box-wrapper", parent.document);
         position= 1;
      }

      let clearButton = $(`<div class="oxy-id-to-class-button">Clear styles</div>`);
      let copyButton = $(`<div class="oxy-id-to-class-button">Copy to Class</div>`);
      let moveButton = $(`<div class="oxy-id-to-class-button">Move to Class</div>`);

      let copyToId = $(`<div class="oxy-id-to-class-button oxy-id-to-class-onlyid" style="display:none">Copy to ID</div>`);
      let moveToId = $(`<div class="oxy-id-to-class-button oxy-id-to-class-onlyid" style="display:none">Move to ID</div>`);

      let buttonContainer = $('<div class="oxy-id-to-class-wrapper"></div>');
      let label = $('<div class="oxy-id-to-class-label"><a href="https://oxyplugins.com" target="_blank">Oxy Class Act<br/><small>OxyPlugins.com</small></a></div>');
      
      buttonContainer
         .append(clearButton)
         .append(copyButton)
         .append(moveButton)
         .append($('<span class="oxy-id-to-class-onlyid" style="display:none">'))
         .append(copyToId)
         .append(moveToId);
      
      if(position === 0) {
         buttonContainer.insertAfter(panelContainer);
      }
      else {
         buttonContainer.css({marginBottom: '14px'}).insertBefore(panelContainer.prev());
      }

      if($('#eeui-styles-css', parent.document).length > 0 && $('#eeui-styles-css', parent.document).attr('href').indexOf('light.css') > 0) {
         $('.oxy-id-to-class-button', parent.document).css({'color': '#000', 'backgroundColor': '#bbb', 'border' : '1px solid #ddd'});
      }

      clearButton.on('click', doClear);
      copyButton.on('click', doCopy);
      moveButton.on('click', doMove);
      copyToId.on('click', doCopyToId);
      moveToId.on('click', doMoveToId);


      let switchEditToId = iframeScope.switchEditToId;
      let setCurrentClass = iframeScope.setCurrentClass;
      let setCustomSelectorToEdit = iframeScope.setCustomSelectorToEdit;
      let activateComponent = iframeScope.activateComponent;

      iframeScope.switchEditToId = (prop) => {

         setTimeout(() => {
            if(iframeScope.isEditing('id')) {
               buttonContainer.children('.oxy-id-to-class-onlyid').hide();   
            }
         }, 200);
         return switchEditToId(prop);
      }

      iframeScope.setCurrentClass = (prop) => {
         buttonContainer.children('.oxy-id-to-class-onlyid').show();
         return setCurrentClass(prop);
      }

      iframeScope.setCustomSelectorToEdit = (prop) => {

         if(prop !== false) {
            buttonContainer.hide();
         }
         setCustomSelectorToEdit(prop);
      }

      iframeScope.activateComponent = (id, componentName, $event) => {
         if(id > 0) {
            buttonContainer.show();
            if(iframeScope.isEditing('id')) {
               buttonContainer.children('.oxy-id-to-class-onlyid').hide(); 
            } else {
               buttonContainer.children('.oxy-id-to-class-onlyid').show();
            }
         }
         activateComponent(id, componentName, $event);
      }

   });
})(jQuery);