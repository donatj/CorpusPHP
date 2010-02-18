window.addEvent('domready', function() {
	
	$$('a.layout-1').addEvent('click', function(e){ e.stop(); });
	//alert( $$('button').get('text') ); 
	//$$('button').
	$$('button').each(function(e){
		e.setStyle('background','none');
		e.setStyle('border','none');
		e.setStyle('padding','0');
		e.setStyle('margin','0');
		e.set('html','<img src="images/button.php?text=' + escape( e.get('text') ) + '" />');
		//e.addEvent('click',function(){ this.form.submit(); });
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