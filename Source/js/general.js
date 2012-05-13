window.addEvent('domready', function() {
	
	$$('a.layout-1').addEvent('click', function(e){ e.stop(); });
	
	var logo = $('logo');
	var logoSpan = $$('#logo span')[0];
	var logoSpanOrigStyle = logoSpan.getStyle('letterSpacing');
	logoSpan.set('morph', {duration: 'long', transition: Fx.Transitions.Elastic.easeOut} );
	
	logo.addEvent('mouseover', function(){
		logoSpan.morph({
			letterSpacing: 4,
			marginLeft: 4
		});

	});
	
	logo.addEvent('mouseout', function(){
		logoSpan.morph({
			letterSpacing: logoSpanOrigStyle,
			marginLeft: 0
		});
	});

});

function setScroll() { Cookie.write('scrolled_y', window.getScrollTop()); }

function restoreScroll() {
	//scroll restoration
	var scrolled_y = Cookie.read('scrolled_y');
	if ( scrolled_y > 0 ) { window.scrollTo(0,scrolled_y); Cookie.dispose('scrolled_y'); }	
}

window.addEvent('load', function() {
	if( Browser.Engine.webkit ) {
		//fix Safari/Chrome Float Bug
		$$('select, input').setStyle('float','left');
	}
});