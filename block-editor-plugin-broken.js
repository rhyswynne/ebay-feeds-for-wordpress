( function( blocks, i18n, element, _ ) {
	var el = element.createElement;
	var children = blocks.source.children;
	var attr = blocks.source.attr;

	blocks.registerBlockType( 'ebay-feeds-for-wordpress/ebay-feeds-for-wordpress-form', {
		title: i18n.__( 'eBay Feed' ),
		icon: '',
		category: 'widgets',
		attributes: {
			feedurl: {
				type: 'textbox'
			},
			items: {
				type: 'string'
			}
		},
		edit: function( props ) {
			var attributes = props.attributes;

			return [
			!!props.focus && el(
				wp.blocks.InspectorControls,
				{key: 'inspector'},
				el(
					wp.components.TextControl,
					{
						label: i18n.__('Feed URL', 'ebay-feeds-for-wordpress'),
						value: props.attributes.feedurl ?  props.attributes.feedurl : "",
						instanceId: 'ebay-feeds-for-wordpress-feed-url',
						onChange: function ( nextValue ) {

							props.setAttributes( {feedurl: nextValue } );

						}

					}
					),
				el(
					wp.components.TextControl,
					{
						label: i18n.__('Number of Items', 'ebay-feeds-for-wordpress'),
						value: props.attributes.items ?  props.attributes.items : "",
						instanceId: 'ebay-feeds-for-wordpress-number-of-items',
						onChange: function ( nextValue ) {

							props.setAttributes( {items: nextValue } );

						}

					}
					)
				),
			el( 'div', { className: props.className },
				el ('div', { className: 'ebay-feeds-for-wordpress' },
					el( 'h3', { className: 'ebay-feeds-for-wordpress-feed' }, i18n.__( 'Loading the eBay feed' ) ),
					)
				)
			];
		},
		save: function( props ) {
			var attributes 		= props.attributes;

			console.log( props );

			return (
				el( 'div', { className: 'ebay-feeds-for-wordpress' },
					el( 'h3', {}, 'Loading the eBay feed' )
					)
				); 
		},
	} );

} )(
window.wp.blocks,
window.wp.i18n,
window.wp.element,
window._,
);