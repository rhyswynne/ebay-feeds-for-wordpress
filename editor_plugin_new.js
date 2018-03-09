// Search for Highlight Box in new expedia theme

(function() {
	tinymce.PluginManager.add('ebffwplugin', function( editor, url ) {
		editor.addButton( 'ebffwplugin', {
			title: 'Add eBay Feed',
			image: url + "/ebay.png",
			//icon: 'icon dashicons-lightbulb',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Add eBay Feed',
					body: [
					{
						type: 'textbox',
						name: 'feedurl',
						label: 'URL for Feed'
					},
					{
						type: 'textbox',
						name: 'items',
						label: 'Items to Display'
					},
					],
					onsubmit: function( e ) {
						
						ebaystring = '[ebayfeedsforwordpress feed="' + e.data.feedurl + '" items="' + e.data.items + '"]';

						editor.insertContent( ebaystring );
					}
				});
			}
		});
	});
})();