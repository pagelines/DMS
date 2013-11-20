<?php


class PageLinesRegions {

	function __construct(){



		$this->url = PL_PARENT_URL . '/editor';
	}


	function region_start( $region ){

		if($region == 'header' || $region == 'footer'){

			$region_title = sprintf(__('Global Scope', 'pagelines'), $region);

		} else {
			$region_title = sprintf(__('Local Scope', 'pagelines'), $region);
		}

		printf(
			'<div class="pl-region-bar area-tag"><a class="btn-region tt-top" title="%s">%s</a></div>',
			$region_title,
			$region
		);

	}
	



}