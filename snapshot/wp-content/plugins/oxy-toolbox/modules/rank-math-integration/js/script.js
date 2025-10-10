(function ($) {
	
	let postContent;

	RankMathIntegration = function () {
		wp.hooks.addFilter('rank_math_content', 'rank-math', this.replaceDataWithOxygenMarkup);
	}

	/**
	 * Replaces the full content with Oxygen generated markup, as it is supposed to contain the_content too
	 *
	 * @param data The data to modify
	 */
	RankMathIntegration.prototype.replaceDataWithOxygenMarkup = function ( data ) {
		return data + postContent;
	};

	$(document).ready(function () {
		$.get(oxy_toolbox_rank_math_integration_rm_data.permalink, (result) => {
			postContent = result;
			new RankMathIntegration();
		});
	});
})(jQuery);