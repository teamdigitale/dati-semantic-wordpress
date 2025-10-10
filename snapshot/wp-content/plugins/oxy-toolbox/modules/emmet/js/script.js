(($) => {
	$('document').ready(() => {
		let cmloaded = iframeScope.parentScope.codemirrorLoaded;
		iframeScope.parentScope.codemirrorLoaded = function(editor) {
			let mode = editor.getOption('mode');
			if( mode == 'php' || mode == 'css') {
			    editor.setOption('extraKeys', {
			        'Tab': 'emmetExpandAbbreviation',
			        'Esc': 'emmetResetAbbreviation',
			        'Enter': 'emmetInsertLineBreak'
			    });
			}
		    cmloaded(editor);
		}
	})
})(jQuery)