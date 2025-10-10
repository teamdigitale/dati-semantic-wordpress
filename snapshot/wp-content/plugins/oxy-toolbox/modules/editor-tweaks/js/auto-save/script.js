(function($) {
   
   let autosave = window.oxy_toolbox_editor_tweaks_options['autosave'] && parseInt(window.oxy_toolbox_editor_tweaks_options['autosave']) ? parseInt(window.oxy_toolbox_editor_tweaks_options['autosave']) : false ;
   let smartAutoSave = window.oxy_toolbox_editor_tweaks_options['smartautosave'] && parseInt(window.oxy_toolbox_editor_tweaks_options['smartautosave']) ? parseInt(window.oxy_toolbox_editor_tweaks_options['smartautosave']) : false ;
   

   $('document').ready(function() {     


      let autosaveTimeout = false;
   

      let autoSaveDelay = false;

      let keyPressed = false;
      let keyPressedTimeout = false;

      let mouseMoved = false;
      let mouseMovedTimeout = false


      function fireAutoSave() {
         
         if(!smartAutoSave || (keyPressed === false && mouseMoved === false)) {
         	console.log('firing autosave via newe script');
            iframeScope.savePage();
            console.log('auto Save', (new Date()));
            setAutoSaveTimeout();
         } else {
            autoSaveDelay = setTimeout(() => {
               clearTimeout(autoSaveDelay);
               fireAutoSave();
            }, 3000);
         }

      }

      function setAutoSaveTimeout() {

         autosaveTimeout = setTimeout(() => {
            
            clearTimeout(autosaveTimeout);

            fireAutoSave();
         
         }, autosave*60000)
 
      }

      function captureMouse(e) {
         mouseMoved = true;
         
         if(mouseMovedTimeout) {
            clearTimeout(mouseMovedTimeout);
         }

         mouseMovedTimeout = setTimeout(() => {
            clearTimeout(mouseMovedTimeout);
            mouseMoved = false;
            
         }, 2000)
      }

      function captureKeys(event, whichFrame) {

		 keyPressed = true;

		 if(keyPressedTimeout) {
		    clearTimeout(keyPressedTimeout);
		 }

		 keyPressedTimeout = setTimeout(() => {
		    clearTimeout(keyPressedTimeout);
		    keyPressed = false;
		 }, 2000)
	  }

      function action() {
      	 $('body', parent.document).on('keydown', (e) => {captureKeys(e, 'outerFrame')});
         $('body').on('keydown', (e) => {captureKeys(e, 'innerFrame')});

         $(parent.document).on('mousemove', (e) => {captureMouse(e, 'outerFrame')});
         $(document).on('mousemove', (e) => {captureMouse(e, 'innerFrame')});

         if(autosave) {
            setAutoSaveTimeout();
         }
      }

      action();

   });

})(jQuery);