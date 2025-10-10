(function($) {
	$('document').ready(function() {

		let func = iframeScope.getComponentTemplate;
		iframeScope.getComponentTemplate = function (componentName,id,type,domNodeFor,parent_id) {
		    let template = func(componentName,id,type,domNodeFor,parent_id);
		    if(componentName === 'oxy_dynamic_list') {
		    	if(iframeScope.component.options[id].id.tag && iframeScope.component.options[id].id.tag !== 'div') {
		        	template = template.replace('<div ', '<'+iframeScope.component.options[id].id.tag+' ').replace('</div>', '</'+iframeScope.component.options[id].id.tag+'>')
		        }
		    }
		    return template
		}

		iframeScope.updateRepeaterTag = function(tag) {
			iframeScope.setOptionModel('tag',tag);
			// find child element
			let child = iframeScope.findComponentItem(iframeScope.componentsTree.children, iframeScope.component.active.id, iframeScope.getComponentItem)['children'][0]
			iframeScope.setOptionModel('tag', tag === 'div' ? 'div' : 'li', child.id, child.name);
			iframeScope.changeTag('rebuild')
		}
	
		let container = $('#oxygen-sidebar-control-panel-basic-styles', parent.document);
		
		let field = `
		<div class="oxygen-control-row"  ng-show='iframeScope.component.active.name == "oxy_dynamic_list"'>
			<div class="oxygen-control-wrapper oxygen-ct_div_block-tag">
				<label class="oxygen-control-label">Tag</label>
				<div class="oxygen-control oxygen-special-property">
					<div class="oxygen-select oxygen-select-box-wrapper">
						<div class="oxygen-select-box" ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'tag')}">
							<div class="oxygen-select-box-current" ng-bind="iframeScope.component.options[iframeScope.component.active.id]['model']['tag']"></div>
							<div class="oxygen-select-box-dropdown"></div>
						</div>
						<div class="oxygen-select-box-options">
							<div class="oxygen-select-box-option" ng-click="iframeScope.updateRepeaterTag('div');">div</div>
								<div class="oxygen-select-box-option" ng-click="iframeScope.updateRepeaterTag('ol');">ol</div>
							<div class="oxygen-select-box-option" ng-click="iframeScope.updateRepeaterTag('ul');">ul</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="oxygen-control-row" ng-show='iframeScope.component.active.name == "oxy_dynamic_list"'>
			<div class="oxygen-control-wrapper">
				<label class="oxygen-checkbox">
					<input type="checkbox" ng-true-value="'true'" ng-false-value="'false'" ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['tb-ids']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,'tb-ids');"> 
					<div class="oxygen-checkbox-checkbox oxygen-option-default" ng-class="{'oxygen-checkbox-checkbox-active':iframeScope.getOption('tb-ids')=='true','oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'tb-ids')}">Remove Redundant IDs</div>
				</label>
			</div>
		</div>
		`;

		$(document, parent.document).injector().invoke(function($compile) {
	        container.prepend($compile(field)(iframeScope.parentScope));
	    });

	})
})(angular.element)

