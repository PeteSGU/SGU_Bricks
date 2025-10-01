/**
 * File index-network-button.js
 *
 * Handle generating the network-wide Algolia index.
 */

/* global wpswaProNetworkIndexManager */

(function($) {

	$(
		function() {
			var $reindexButtons = $('.algolia-reindex-all-sites-button');
			$reindexButtons.on( 'click', handleReindexButtonClick );
		}
	);

	var ongoing = 0;

	$( window ).on(
		'beforeunload', function() {
			if (ongoing > 0) {
				return 'If you leave now, re-indexing tasks in progress will be aborted';
			}
		}
	);

	function handleReindexButtonClick(e) {
		let $clickedButton = $( e.currentTarget );

		// Add a confirmation before overwriting existing index.
		let  buttonType = $clickedButton.data( 'button-type' );
		if ( 'recreate-index' === buttonType ) {
			if ( false === confirm( wpswaProNetworkIndexManager.strings.dialog_confirm_overwrite_network_index ) ) {
				e.preventDefault();
				return;
			}
		}

		var index = $clickedButton.data( 'index' );
		if ( ! index) {
			throw new Error( 'Clicked button has no "data-index" set.' );
		}

		ongoing++;

		// Hide any other site indexing buttons (when resume is available, there are multiple buttons).
		$('.algolia-reindex-all-sites-button').not( this ).hide();

		$clickedButton.attr('disabled', 'disabled');
		$clickedButton.data('originalText', $clickedButton.text() );

		updateIndexingPercentage( $clickedButton, 0 );

		let currentPage = 0;
		let currentSite = 0;
		let resume = $clickedButton.data('resume');

		if ( true === resume ) {
			let siteToIndex = $clickedButton.data('site');

			if ( ! siteToIndex) {
				throw new Error( 'Clicked button has no "data-site" set. Cannot resume.' );
			}
			currentSite = siteToIndex;
		}

		// Reset indexing status table values.
		resetIndexingStatusDisplay( resume );

		reIndex( $clickedButton, index, currentPage, currentSite, resume );
	}

	function reIndex( $clickedButton, index, currentPage, site, resume = false ) {
		if ( ! currentPage) {
			currentPage = 1;
		}

		let data = {
			'index_id': index,
			'site_id': site,
			'p': currentPage,
			'resume': resume,
		};

		// no `action` param needed for REST API
		$.ajax({
			type : 'POST',
			beforeSend: ( xhr ) => {
				xhr.setRequestHeader( 'X-WP-Nonce', wpswaProNetworkIndexManager.rest_nonce );
			},
			url : getIndexNetworkRoute( site ),
			data : data,
			timeout: Number( wpswaProNetworkIndexManager.timeout ),
			success: function(response) {
				if (typeof response.totalPagesCount === 'undefined') {
					alert( 'Error: totalPagesCount undefined.' );
					resetButton( $clickedButton );
					return;
				}

				progress = Math.round( (currentPage / response.totalPagesCount) * 100 );
				updateIndexingPercentage( $clickedButton, progress );

				if (response.finished !== true || response.batchStatus !== 'complete' ) {

					if ( response.finished === true ) { // Pages are done being indexed...
						// Reset the indexing percentage.
						updateIndexingPercentage( $clickedButton, 0 );
						updateIndexingStatusDisplay(response, true);

						// Reset the page number to 1 for the next site and target the nextSiteId.
						reIndex( $clickedButton, index, 1, response.nextSiteId );
					} else { // Process the next page for the current site.
						updateIndexingStatusDisplay(response, true);
						reIndex( $clickedButton, index, ++currentPage, response.siteId );
					}

				} else {
					// Indexing all sites complete.
					updateIndexingStatusDisplay(response);
					$clickedButton.parents( '.error' ).fadeOut();
					resetButton( $clickedButton );

					// Hide the resume button.
					$('.algolia-reindex-all-sites-button[data-resume="true"]').hide();

					// Show the other Network Index button.
					$('.algolia-reindex-all-sites-button').not( "[data-resume='true']" ).show();

					const successMessage = $( '<div class="wpswap notice notice-success notice-alt updated-message"><p></p></div>' );
					successMessage.find('p').html( response.networkIndexCompleteMsg );
					$('.wrap h1').after( successMessage );
				}
			},
			error: ( response ) => {
				alert( 'An error occurred: ' + response.responseText );
				resetButton( $clickedButton );
			}
		});
	}

	/**
	 * Gets site information for the specified site ID.
	 *
	 * @global {object} wpswaProNetworkIndexManager
	 *
	 * @param {number} siteID The site ID to get the Index Network route for.
	 *
	 * @return {string} The Index Network route for the site ID requested.
	 */
	function getIndexNetworkRoute( siteId ) {
		// Get the main site if 0 is passed. This happens when the index is created for the first time or when it's being recreated.
		if ( 0 == siteId ) {
			siteId = wpswaProNetworkIndexManager.main_site_id;
		}

		var siteInfo = getSiteInfo( parseInt( siteId ) );

		if ( Object.keys( siteInfo ).length === 0 ) {
			throw new Error( 'Error: Site info not found for site: ' + siteId );
		}

		var mainSiteUrl = wpswaProNetworkIndexManager.main_site_url;
		var restEndpoint = wpswaProNetworkIndexManager.rest_endpoint;
		var routeUrl = mainSiteUrl + siteInfo.site + restEndpoint;

		return routeUrl;
	}

	/**
	 * Gets site information for the specified site ID.
	 *
	 * @global {object} wpswaProNetworkIndexManager
	 *
	 * @param {number} siteId The site ID to get info for.
	 *
	 * @return {object} Object containing site information.
	 */
	function getSiteInfo( siteId ) {
		siteInfo = {};
		wpswaProNetworkIndexManager.sites.forEach( ( site ) => {

			if ( parseInt( site.id ) === siteId ) {
				siteInfo = site;
				return;
			}
		} );

		return siteInfo;
	}

	function updateIndexingPercentage($clickedButton, amount) {
		$clickedButton.text( 'Processing, please be patient ... ' + amount + '%' );
	}

	function resetButton($clickedButton) {
		ongoing--;
		$clickedButton.text( $clickedButton.data( 'originalText' ) );
		$clickedButton.removeAttr( 'disabled' );
		$clickedButton.data( 'currentPage', 1 );
	}

	function resetIndexingStatusDisplay( resume = false ) {
		let $networkStatus = $('.wpswap-network-index-status');

		if ( true === resume ) {
			$( $networkStatus ).find('.batch-status').html(wpswaProNetworkIndexManager.strings.network_index_status_preparing);
			return;
		}

		let statusData = wpswaProNetworkIndexManager;

		$( 'body' ).find('.wpswap.notice').remove();
		$( $networkStatus ).find('.sites-indexed').html('0');
		$( $networkStatus ).find('.sites-not-yet-indexed').html( statusData.indexable_site_count );
		$( $networkStatus ).find('.next-site-to-index').html('');
		$( $networkStatus ).find('.batch-id').html('');
		$( $networkStatus ).find('.batch-status').html(wpswaProNetworkIndexManager.strings.network_index_status_preparing);
	}

	function updateIndexingStatusDisplay(response, indexing = false) {
		let $networkStatus = $('.wpswap-network-index-status');

		$( $networkStatus ).find('.sites-indexed').html( response.sitesIndexedCount );
		$( $networkStatus ).find('.sites-not-yet-indexed').html( response.sitesNotIndexedCount );
		$( $networkStatus ).find('.next-site-to-index').html(response.nextSiteId);
		$( $networkStatus ).find('.site-being-indexed').html(response.nextSiteId);
		$( $networkStatus ).find('.batch-id').html(response.batchId);

		if ( indexing ) {
			$( $networkStatus ).find('.batch-status').html(wpswaProNetworkIndexManager.strings.network_index_status_indexing);
		} else {
			let statusKey = 'network_index_status_' + response.batchStatus;
			$( $networkStatus ).find('.batch-status').html(wpswaProNetworkIndexManager.strings[statusKey]);
		}
	}

})( jQuery );
