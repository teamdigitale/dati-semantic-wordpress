(function($) {
	$('document').ready(function() {
		let container = $('#oxygen-sidebar-control-panel-basic-styles', parent.document);

		let field = `<div class="oxygen-control-row" id="oxy-toolbox-textedit"
			ng-show='["ct_headline", "ct_li", "ct_link_button", "ct_link_text", "oxy_rich_text", "ct_span", "ct_text_block"].indexOf(iframeScope.component.active.name) > -1'
			>
	
			<div class="oxygen-control-wrapper">
				<label class="oxygen-control-label">Text Content</label>
				<div class="oxygen-control">
					<div class="oxygen-input" style="height: auto; padding: 6px 0;">
						<textarea style="background: transparent;
									    border: none;
									    font-size: 14px;
									    color: #fff;
									    padding-left: 9px;
									    padding-right: 9px;
									    width: 100%;
										height: 100%;
										min-height: 15vh;
									    resize: vertical;
									    position: relative;"
     					spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['ct_content']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'ct_content', false, true);"></textarea>
					</div>
				</div>
			</div>` ;

		$(document, parent.document).injector().invoke(function($compile) {
            container.prepend($compile(field)(iframeScope.parentScope));
        });

        $('#oxy-toolbox-textedit', parent.document).on('keypress', function(e) {
        	if(e.which === 13) {
        		e.preventDefault();
        		parent.document.execCommand('insertText', false, '<br>');
        	}
        })

        $(parent.document).on('blur', '#oxy-toolbox-textedit textarea', function(e) { 
		    iframeScope.parentScope.actionTabs["contentEditing"] = true;
		    iframeScope.parentScope.disableContentEdit()

		})


	})
})(angular.element);
