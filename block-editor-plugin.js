( function( blocks, i18n, element ) {
	/* Set up variables */
	var el = element.createElement;
	var children = blocks.source.children;
	var BlockControls = wp.blocks.BlockControls;
	var AlignmentToolbar = wp.blocks.AlignmentToolbar;
	var MediaUpload = wp.blocks.MediaUpload;
	var InspectorControls = wp.blocks.InspectorControls;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.blocks.InspectorControls.SelectControl;


	/* Register block type */
	blocks.registerBlockType('ebay-feeds-for-wordpress/ebay-feeds-for-wordpress-form', {
  		title: i18n.__( 'eBay Feed' ), // The title of our block.
  		icon: '', // Dashicon icon for our block
  		category: 'widgets', // The category of the block.
  		attributes: { // To save block content
  			feedurl: {
  				type: 'string'
  			},
  			items: {
  				type: 'string'
  			}
  		},

  		edit: function( props ) {

  			var focus = props.focus;
  			var focusedEditable = props.focus ? props.focus.editable || 'titleOne' : null;
  			var attributes = props.attributes;
  			var items = props.attributes.items;
  			var feedurl = props.attributes.feedurl;

  			function setFeedValue( nextValue ) {

  				props.setAttributes( {feedurl: nextValue } );

  			}

  			function setNumberOfItemsValue( nextValue ) {

  				props.setAttributes( { items: nextValue } );

  			}

  			return [

  			el(
  				'h3',
  				{ className: 'ebay-feeds-for-wordpress-admin-feed' },
  				'eBay Feed To Go Here'
  				),

  			!! focus && el(

  				blocks.InspectorControls,
  				{ key: 'inspector' },
  				el( 'div', { className: 'components-block-description'},
  					el( 'p', {}, i18n.__('Here are your options for the eBay Feed' ) ),
  					),
  				el(
  					TextControl,
  					{
  						label: i18n.__( 'eBay Feed' ),
  						value: feedurl,
  						instanceId: 'ebay-feeds-for-wordpress-feed-url',
  						onChange: setFeedValue,
  					} 
  					),
  				el(
  					TextControl,
  					{
  						label: i18n.__( 'Number of Items' ),
  						value: items,
  						onChange: setNumberOfItemsValue,
  						instanceId: 'ebay-feeds-for-wordpress-feed-items',
  					} 
  					),
  				)

  			]

  		},
  		save: function( props ) {
  			let { feedurl, items } = props.attributes;
  			let shortcode = '[ebayfeedsforwordpress feed="${feedurl}" items="${items}"]';

  			return wp.element.RawHTML( shortcode );
  		},
  	} )

  } )(
  window.wp.blocks,
  window.wp.i18n,
  window.wp.element,
  );