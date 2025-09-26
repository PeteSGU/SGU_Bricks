/**
 * File index-network-site-button.js
 *
 * Handle generating the index for a single site within the network-wide Algolia index.
 */

/* global wpswaProNetworkIndexManager */

(function($) {

	$(
		function() {
			var $reindexButtons = $( '.algolia-reindex-network-site-button' );
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

		$clickedButton = $( e.currentTarget );

		// Add a confirmation before overwriting existing index.
		if ( false === confirm( wpswaProNetworkIndexManagerSite.strings.dialog_confirm_overwrite_network_site_index ) ) {
			e.preventDefault();
			return;
		}

		var index      = $clickedButton.data( 'index' );
		if ( ! index) {
			throw new Error( 'Clicked button has no "data-index" set.' );
		}

		ongoing++;

		$( 'body' ).find('.wpswap.notice').remove();

		$clickedButton.attr( 'disabled', 'disabled' );
		$clickedButton.data( 'originalText', $clickedButton.text() );
		updateIndexingPercentage( $clickedButton, 0 );

		let currentPage = 0;
		let site        = $clickedButton.data('site');

		reIndex( $clickedButton, index, currentPage, site );
	}

	function updateIndexingPercentage( $clickedButton, amount ) {
		$clickedButton.text( 'Processing site, please be patient ... ' + amount + '%' );
	}

	function reIndex($clickedButton, index, currentPage, site ) {
		if ( ! currentPage) {
			currentPage = 1;
		}

		var data = {
			'action': 'wpswa_pro_index_network_site',
			'index_id': index,
			'site_id': site,
			'p': currentPage
		};

		$.post(
			ajaxurl, data, function(response) {
				if (typeof response.totalPagesCount === 'undefined') {
					alert( 'An error occurred' );
					resetButton( $clickedButton );
					return;
				}

				if (response.totalPagesCount === 0) {
					$clickedButton.parents( '.error' ).fadeOut();
					resetButton( $clickedButton );
					return;
				}
				progress = Math.round( (currentPage / response.totalPagesCount) * 100 );
				updateIndexingPercentage( $clickedButton, progress );

				if (response.finished !== true) {
					reIndex( $clickedButton, index, ++currentPage, response.siteId );
				} else {
					$clickedButton.parents( '.error' ).fadeOut();

					const successMessage = $( '<div class="wpswap notice notice-success notice-alt updated-message"><p></p></div>' );
					successMessage.find('p').html( response.siteIndexCompleteMsg );
					$('.wpswap-settings').before( successMessage );

					resetButton( $clickedButton );
				}
			}
		).fail(
			function(response) {
				alert( 'An error occurred: ' + response.responseText );
				resetButton( $clickedButton );
			}
		);
	}

	function resetButton($clickedButton) {
		ongoing--;
		$clickedButton.text( $clickedButton.data( 'originalText' ) );
		$clickedButton.removeAttr( 'disabled' );
		$clickedButton.data( 'currentPage', 1 );
	}

})( jQuery );
