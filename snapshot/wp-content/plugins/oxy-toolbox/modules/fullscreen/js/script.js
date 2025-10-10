(function($) {
   let mode = parseInt(window.oxy_toolbox_fullscreen_options['mode']);
   let hotkey = parseInt(window.oxy_toolbox_fullscreen_options['hotkey']);
   let codeMirrorLoaded = false;
   let sidePanel = false;
   let sidePanelBig = false;
   let sidebarprocessing = false
   let ignoreSidebar = false;
   let what = null;
   let keyLoaded = null;
   function retainSideBar() {
      
      if(sidebarprocessing || ignoreSidebar) {
         return
      }
      
      sidebarprocessing = true;
      
      setTimeout(() => {
         sidebarprocessing = false}
      , 200);

      let leftSidebarWidth = iframeScope.parentScope.verticalSidebar.width(),
         rightSidebarWidth = iframeScope.parentScope.sidePanelElement.width(),
         artificialViewportWidth = parent.window.innerWidth - rightSidebarWidth - leftSidebarWidth - 12;
         
      iframeScope.parentScope.adjustArtificialViewport(artificialViewportWidth);
   }

   function commonstuff(toggleFullscreen) {

      let switchTab = iframeScope.parentScope.switchTab;

      iframeScope.parentScope.switchTab = function(tabGroup, tabName) {
         if(tabGroup === 'sidePanel' && what && what.type === 'main') {
            toggleFullscreen();
            iframeScope.parentScope.tabs[tabGroup][tabName] = false;
         }
         switchTab(tabGroup, tabName);
      }

      let switchActionTab = iframeScope.parentScope.switchActionTab;

      iframeScope.parentScope.switchActionTab = function(prop1) {
         if(what && what.type === 'main') {
            toggleFullscreen();
         }
         switchActionTab(prop1);
      }


      let toggleSettingsPanel = iframeScope.parentScope.toggleSettingsPanel;

      iframeScope.parentScope.toggleSettingsPanel = function(forceOpen) {
         if(what && what.type === 'main') {
            toggleFullscreen();
            forceOpen = true;
         }
         toggleSettingsPanel(forceOpen);
      }


      let toggleSidebar = iframeScope.parentScope.toggleSidebar;
      
      iframeScope.parentScope.toggleSidebar = function(prop) {
         toggleSidebar(prop);

         if(what && what.type === 'codemirror') {
            codeMirrorToggle();
            toggleSidebar(true);
         }
         
         if(sidePanel || sidePanelBig) {
            retainSideBar();
         }   
         
         
      }

      let expandSidebar = iframeScope.parentScope.expandSidebar;
      
      iframeScope.parentScope.expandSidebar = function() {
         expandSidebar();
         
         if(sidePanel || sidePanelBig) {
            setTimeout(() => {
               retainSideBar();
            }, 200);
         }   
         
         
      }
      
      let activateComponent = iframeScope.activateComponent;
      
      iframeScope.activateComponent = function(id, componentName, $event) {
         
         if($event && jQuery($event.target).closest('#ct-dom-tree').length > 0) {
            ignoreSidebar = true;
         }
         
         activateComponent(id, componentName, $event);
         
         if(parseInt(id) === 0) {
            return;
         }

         if(sidePanel || sidePanelBig) {
            
               retainSideBar();
            
         }
         
         ignoreSidebar = false;
      }
      


      $('body', parent.document).on('mouseover', '#ct-sidepanel, #oxygen-global-settings', function(e) { sidePanel = true;});
      $('body', parent.document).on('mouseout', '#ct-sidepanel, #oxygen-global-settings', function(e) { sidePanel = false;});

      $('body', parent.document).on('mouseover', '.CodeMirror', function(e) { codeMirrorLoaded = true;});
      $('body', parent.document).on('mouseout', '.CodeMirror', function(e) { codeMirrorLoaded = false;});


      function captureKeys(e, whichframe) {

         if(e.which === hotkey || (hotkey === 91 && e.which === 224)) {
            keyLoaded = e.which;
            if(hotkey === 9) { // tab key, you dont want cursor to be running around
               e.preventDefault();
            }
         } else {
            keyLoaded = null;
         }

      }

      function releaseKeys(e, whichframe) {
         if(keyLoaded && e.which === keyLoaded) {
            toggleFullscreen();
            keyLoaded = null;
         }
      }
      
      $('body', parent.document).on('keydown', (e) => {captureKeys(e, 'outerFrame')});
      $('body').on('keydown', (e) => {captureKeys(e, 'innerFrame')});

      $('body', parent.document).on('keyup', (e) => {releaseKeys(e, 'outerFrame')});
      $('body').on('keyup', (e) => {releaseKeys(e, 'innerFrame')});
   }

   function codeMirrorToggle() {
      let codeMirrorPanel = $('.CodeMirror', parent.document).closest('.oxygen-sidebar-code-editor-wrap').parent();
      
         
      if(codeMirrorPanel.length < 1) {
         return;
      }

      let codeMirror = parent.document.querySelector('.CodeMirror').CodeMirror;

      if(codeMirrorPanel.hasClass('oxy-fullscreen-codemirror')) {
         what = null;
         codeMirrorPanel.removeClass('oxy-fullscreen-codemirror')
         codeMirror.refresh()
      } else {
         what = {
            type: 'codemirror',
            which: codeMirrorPanel
         }
         codeMirrorPanel.addClass('oxy-fullscreen-codemirror')
         codeMirror.refresh()
      }
   }

   function sidePanelToggle() {
      let sidePanelPanel = $('#ct-sidepanel, #oxygen-global-settings', parent.document);
      let container = $('#ct-viewport-container', parent.document);

      if(sidePanelPanel.length < 1) {
         return;
      }
      let sidebarWidth = iframeScope.parentScope.verticalSidebar.width()
      if(sidePanelPanel.hasClass('oxy-fullscreen-sidepanel')) {
         what = null;
         sidePanelPanel.removeClass('oxy-fullscreen-sidepanel')
         let artificialViewportWidth = parent.window.innerWidth - 300 - sidebarWidth - 12;
         iframeScope.parentScope.adjustArtificialViewport(artificialViewportWidth);
         sidePanelBig = false;
      } else {
         what = {
            type: 'sidepanel',
            which: sidePanelPanel
         }
         sidePanelPanel.addClass('oxy-fullscreen-sidepanel')
         let artificialViewportWidth = parent.window.innerWidth - 600 - sidebarWidth - 12;
         iframeScope.parentScope.adjustArtificialViewport(artificialViewportWidth);
         sidePanelBig = true;
      }

   }

   if(mode === 2) {

      window['frameElement'] = parent.oxy_fullscreen.document.querySelector('#ct-artificial-viewport');
      CTFrontendBuilder.factory('$parentScope', function($window) {
       return $window.parent.angular.element("#ct-artificial-viewport").scope();
      });

      
      $('document').ready(function() {
         let container = $('#ct-viewport-container', parent.document);
         container.addClass('oxy-fullscreen-detached');

         iframeScope.parentScope.isElementInViewport = function(el, threshold) {
      
            el = el[0];

            if (typeof el.getBoundingClientRect !== "function") {
               return false;
            }

            var rect = el.getBoundingClientRect();

            if ( rect.top >= (iframeScope.parentScope.artificialViewport[0].contentWindow.innerHeight || iframeScope.parentScope.artificialViewport[0].contentWindow.document.documentElement.clientHeight) ) {
               return "below";
            }

            var bottom = threshold ? (rect.top+threshold):rect.bottom;

            if (  bottom <= 0 ) {
               return "above";
            }

            return "visible";

         }

         iframeScope.parentScope.artificialViewport.contents = function() {
            return jQuery(parent.oxy_fullscreen.document);
         }

         function toggleFullscreen() {
            if(what && what.type==="sidepanel" || sidePanel || sidePanelBig) {
               sidePanelToggle();
            }
            else if(what && what.type==="codemirror" || codeMirrorLoaded) {
               codeMirrorToggle();
            } 
         }
         
         commonstuff(toggleFullscreen);

      })
      return;
   }
   else {
   
      keyLoaded = null;

      $('document').ready(function() {
         let container = $('#ct-viewport-container', parent.document);


         
         function toggleFullscreen() {
            if(what && what.type==="sidepanel" || sidePanel || sidePanelBig) {
               sidePanelToggle();
            }
            else if(what && what.type==="codemirror" || codeMirrorLoaded) {
               codeMirrorToggle();
            } 
            else {
               if(container.hasClass('oxy-fullscreen-toggle')) {
                  what = null;
                  container.removeClass('oxy-fullscreen-toggle');
                  container.removeClass('oxy-fullscreen-media');
               } else {
                  what = {
                     type: 'main',
                     which: container
                  };
                  if(iframeScope.currentMedia !== 'default') {
                     container.addClass('oxy-fullscreen-media');
                  }

                  container.addClass('oxy-fullscreen-toggle');
               }
            }
            
         }

         commonstuff(toggleFullscreen);


      })
   }

})(jQuery);