<?php

	global $va_options;

	$listing = get_queried_object();

	if ( _va_needs_publish( $listing ) || _va_is_claimable( $listing->ID ) || !$plan = _va_get_last_plan_info( $listing->ID ) ) {

		$plans_data = va_get_listing_available_plans($listing);

		appthemes_load_template( 'purchase-listing-new.php', array(
			'listing' => $listing,
			'plans' => $plans_data
		));

	} else {

		appthemes_load_template( 'purchase-listing-existing.php', array(
			'listing' => $listing,
			'plan' => $plan
		));

	}

?>

<style type="text/css">
	.plan{
		border: 1px solid #CCC;
		margin: 5px;
		padding: 3px;
		font-size: 13px;
		position: relative;
	}
	
	.plan:after {
		content: "";
		position: absolute;
		top: 106px;
		right: -10px;		
		border-top: 10px solid #000;
	    border-right: 10px solid transparent;
	}

	.plan .content{
		background-color: #EEEEEF;
		padding: 8px;
		min-height: 95px;
	}

	.plan .title{
		font-size: 20px;
		font-weight: bold;
	}

	.plan .description{
		font-style: italic;
		margin-bottom: 10px;
		width: 40em;
	}

	.plan .option-header{
		font-weight: bold;
		margin-bottom: 2px;
	}

	.plan .price-box{
		position: absolute;
		top: 10px;
		right: -10px;
		background-color: white;
		padding: 10px;
		padding-right: 0px;
		border: 1px solid #CCC;
		border-bottom-left-radius: 5px;
		border-top-left-radius: 5px;
	}

	.plan .price-box .price{
		color: #0066CC;
		font-size: 40px;
		float: left;
		margin-right: 5px;
	}
	.plan .price-box .duration{
		margin-top: 4px;
		font-size: 15px;
		float: left;
	}
	.plan .price-box .radio-button{
		background-color: #CCC;
		clear: both;
		padding: 5px;
		padding-right: 20px;
		font-weight: bold;
		border-bottom-left-radius: 5px;
		border-top-left-radius: 5px;
	}

	.plan .price-box .radio-button label{
		font-style: normal;
	}

</style>