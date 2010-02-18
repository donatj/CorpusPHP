/*
	mediaboxAdvanced v1.1.1 - The ultimate extension of Slimbox and Mediabox; an all-media script
	updated 2009.08.08
	(c) 2007-2009 John Einselen <http://iaian7.com>
		based on
	Slimbox v1.64 - The ultimate lightweight Lightbox clone
	(c) 2007-2008 Christophe Beyls <http://www.digitalia.be>
	MIT-style license.
*/

var Mediabox;

(function() {

	// Global variables, accessible to Mediabox only
	var options, images, activeImage, prevImage, nextImage, top, left, fx, preload, preloadPrev = new Image(), preloadNext = new Image(), foxfix = false, iefix = false,
	// DOM elements
	overlay, center, image, bottom, captionSplit, title, caption, prevLink, number, nextLink,
	// Mediabox specific vars
	URL, WH, WHL, elrel, mediaWidth, mediaHeight, mediaType = "none", mediaSplit, mediaId = "mediaBox", mediaFmt;

	/*
		Initialization
	*/

	window.addEvent("domready", function() {
		// Create and append the Mediabox HTML code at the bottom of the document
		$(document.body).adopt(
			$$([
				overlay = new Element("div", {id: "mbOverlay"}).addEvent("click", close),
				center = new Element("div", {id: "mbCenter"})
			]).setStyle("display", "none")
		);

		image = new Element("div", {id: "mbImage"}).injectInside(center);
		bottom = new Element("div", {id: "mbBottom"}).injectInside(center).adopt(
			new Element("a", {id: "mbCloseLink", href: "#"}).addEvent("click", close),
			nextLink = new Element("a", {id: "mbNextLink", href: "#"}).addEvent("click", next),
			prevLink = new Element("a", {id: "mbPrevLink", href: "#"}).addEvent("click", previous),
			title = new Element("div", {id: "mbTitle"}),
			number = new Element("div", {id: "mbNumber"}),
			caption = new Element("div", {id: "mbCaption"})
		);

		fx = {
			overlay: new Fx.Tween(overlay, {property: "opacity", duration: 360}).set(0),
			image: new Fx.Tween(image, {property: "opacity", duration: 360, onComplete: captionAnimate}),
			bottom: new Fx.Tween(bottom, {property: "opacity", duration: 240}).set(0)
		};
	});

	/*
		API
	*/

	Mediabox = {
		close: function(){ 
			close();	// Thanks to Yosha on the google group for fixing the close function API!
		}, 

		open: function(_images, startImage, _options) {
			options = $extend({
				loop: false,					// Allows to navigate between first and last images
				stopKey: true,					// Prevents default keyboard action (such as up/down arrows), in lieu of the shortcuts
													// Does not apply to iFrame content
													// Does not affect mouse scrolling
				overlayOpacity: 0.7,			// 1 is opaque, 0 is completely transparent (change the color in the CSS file)
													// Remember that Firefox 2 and Camino 1.6 on the Mac require a background .png set in the CSS
				resizeOpening: true,			// Determines if box opens small and grows (true) or start full size (false)
				resizeDuration: 240,			// Duration of each of the box resize animations (in milliseconds)
				resizeTransition: false,		// Mootools transition effect (false leaves it at the default)
				initialWidth: 320,				// Initial width of the box (in pixels)
				initialHeight: 180,				// Initial height of the box (in pixels)
				defaultWidth: 640,				// Default width of the box (in pixels) for undefined media (MP4, FLV, etc.)
				defaultHeight: 360,				// Default height of the box (in pixels) for undefined media (MP4, FLV, etc.)
				showCaption: true,				// Display the title and caption, true / false
//				animateCaption: true,			// Animate the caption, true / false
				showCounter: true,				// If true, a counter will only be shown if there is more than 1 image to display
				counterText: '({x} of {y})',	// Translate or change as you wish
//			Global media options
				scriptaccess: 'true',		// Allow script access to flash files
				fullscreen: 'true',			// Use fullscreen
				fullscreenNum: '1',			// 1 = true
				autoplay: 'true',			// Plays the video as soon as it's opened
				autoplayNum: '1',			// 1 = true
				autoplayYes: 'yes',			// yes = true
//				volume: '50',				// 0-100 (not currently implemented)
				bgcolor: '#000000',			// Background color, used for both flash and QT media
				wmode: 'opaque',			// Background setting for Adobe Flash ('opaque' and 'transparent' are most common)
//			JW Media Player settings and options
				playerpath: '/js/player.swf',	// Path to the mediaplayer.swf or flvplayer.swf file
				backcolor:  '000000',		// Base color for the controller, color name / hex value (0x000000)
				frontcolor: '999999',		// Text and button color for the controller, color name / hex value (0x000000)
				lightcolor: '000000',		// Rollover color for the controller, color name / hex value (0x000000)
				screencolor: '000000',		// Rollover color for the controller, color name / hex value (0x000000)
				controlbar: 'over',			// bottom, over, none (this setting is ignored when playing audio files)
//			NonverBlaster
				useNB: true,				// use NonverBlaster in place of the JW Media Player for .flv and .mp4 files
				NBpath: '/js/NonverBlaster.swf',	// Path to NonverBlaster.swf
				NBloop: 'true',				// Loop video playback, true / false
				controllerColor: '0x777777',	// set the controlbar colour
				showTimecode: 'false',		// turn timecode display off or on
//			Quicktime options
				controller: 'true',			// Show controller, true / false
//			Flickr options
				flInfo: 'true',				// Show title and info at video start
//			Revver options
				revverID: '187866',			// Revver affiliate ID, required for ad revinue sharing
				revverFullscreen: 'true',	// Fullscreen option
				revverBack: '000000',		// Background colour
				revverFront: 'ffffff',		// Foreground colour
				revverGrad: '000000',		// Gradation colour
//			Ustream options
				usViewers: 'true',				// Show online viewer count	(true/false)
//			Youtube options
				ytBorder: '0',				// Outline 				(1=true, 0=false)
				ytColor1: '000000',			// Outline colour
				ytColor2: '333333',			// Base interface colour (highlight colours stay consistent)
				ytQuality: '&ap=%2526fmt%3D18',	// Leave empty for standard quality, use '&ap=%2526fmt%3D18' for high quality, and '&ap=%2526fmt%3D22' for HD (note that not all videos are availible in high quality, and very few in HD)
				ytRel: '0',					// Show related videos	(1=true, 0=false)
				ytInfo: '1',				// Show video info		(1=true, 0=false)
				ytSearch: '0',				// Show search field	(1=true, 0=false)
//			Viddyou options
				vuPlayer: 'basic',			// Use 'full' or 'basic' players
//			Vimeo options
				vmTitle: '1',				// Show video title
				vmByline: '1',				// Show byline
				vmPortrait: '1',			// Show author portrait
				vmColor: 'ffffff'			// Custom controller colours, hex value minus the # sign, defult is 5ca0b5
			}, _options || {});

			if ((Browser.Engine.gecko) && (Browser.Engine.version<19)) {	// Fixes Firefox 2 and Camino 1.6 incompatibility with opacity + flash
				foxfix = true;
				options.overlayOpacity = 1;
				overlay.className = 'mbOverlayFF';
			}
/*
			if ((Browser.Engine.gecko)) {	// Fixes Firefox 2 and Camino 1.6 incompatibility with opacity + flash
				foxfix = true;
				overlay.setStyle("position", "absolute");
				if ((Browser.Engine.version<19)) {
					options.overlayOpacity = 1;
					overlay.className = 'mbOverlayFF';
				}
			}
*/

			if (typeof _images == "string") {	// The function is called for a single image, with URL and Title as first two arguments
				_images = [[_images,startImage,_options]];
				startImage = 0;
			}

			images = _images;
			options.loop = options.loop && (images.length > 1);

			if ((Browser.Engine.trident) && (Browser.Engine.version<5)) {	// Fixes IE 6 and earlier incompatibilities with CSS position: fixed;
				iefix = true;
				overlay.className = 'mbOverlayIE';
				overlay.setStyle("position", "absolute");
				position();
			}
			size();
			setup(true);
			top = window.getScrollTop() + (window.getHeight()/2);
			left = window.getScrollLeft() + (window.getWidth()/2);
			fx.resize = new Fx.Morph(center, $extend({duration: options.resizeDuration, onComplete: imageAnimate}, options.resizeTransition ? {transition: options.resizeTransition} : {}));
			center.setStyles({top: top, left: left, width: options.initialWidth, height: options.initialHeight, marginTop: -(options.initialHeight/2), marginLeft: -(options.initialWidth/2), display: ""});
			fx.overlay.start(options.overlayOpacity);
			return changeImage(startImage);
		}
	};

	Element.implement({
		mediabox: function(_options, linkMapper) {
			$$(this).mediabox(_options, linkMapper);	// The processing of a single element is similar to the processing of a collection with a single element

			return this;
		}
	});

	Elements.implement({
		/*
			options:	Optional options object, see Mediabox.open()
			linkMapper:	Optional function taking a link DOM element and an index as arguments and returning an array containing 3 elements:
					the image URL and the image caption (may contain HTML)
			linksFilter:	Optional function taking a link DOM element and an index as arguments and returning true if the element is part of
					the image collection that will be shown on click, false if not. "this" refers to the element that was clicked.
					This function must always return true when the DOM element argument is "this".
		*/
		mediabox: function(_options, linkMapper, linksFilter) {
			linkMapper = linkMapper || function(el) {
				elrel = el.rel.split(/[\[\]]/);
				elrel = elrel[1];
				return [el.href, el.title, elrel];
			};

			linksFilter = linksFilter || function() {
				return true;
			};

			var links = this;

			links.removeEvents("click").addEvent("click", function() {
				// Build the list of images that will be displayed
				var filteredArray = links.filter(linksFilter, this);
				var filteredLinks = [];
				var filteredHrefs = [];

				filteredArray.each(function(item, index){
					if(filteredHrefs.indexOf(item.toString()) < 0) {
						filteredLinks.include(filteredArray[index]);
						filteredHrefs.include(filteredArray[index].toString());
					};
				});

				return Mediabox.open(filteredLinks.map(linkMapper), filteredHrefs.indexOf(this.toString()), _options);
			});

			return links;
		}
	});

	/*
		Internal functions
	*/

	function position() {
		overlay.setStyles({top: window.getScrollTop(), left: window.getScrollLeft()});
	}

	function size() {
		overlay.setStyles({width: window.getWidth(), height: window.getHeight()});
	}

	function setup(open) {
		// Hides on-page objects and embeds while the overlay is open, nessesary to counteract Firefox stupidity
		["object", window.ie ? "select" : "embed"].forEach(function(tag) {
			Array.forEach(document.getElementsByTagName(tag), function(el) {
				if (open) el._mediabox = el.style.visibility;
				el.style.visibility = open ? "hidden" : el._mediabox;
			});
		});

		overlay.style.display = open ? "" : "none";

		var fn = open ? "addEvent" : "removeEvent";
		if (iefix) window[fn]("scroll", position);
		window[fn]("resize", size);
		document[fn]("keydown", keyDown);
	}

	function keyDown(event) {
		switch(event.code) {
			case 27:	// Esc
			case 88:	// 'x'
			case 67:	// 'c'
				close();
				break;
			case 37:	// Left arrow
			case 80:	// 'p'
				previous();
				break;	
			case 39:	// Right arrow
			case 78:	// 'n'
				next();
		}
		if (options.stopKey) { return false; };
	}

	function previous() {
		return changeImage(prevImage);
	}

	function next() {
		return changeImage(nextImage);
	}

	function changeImage(imageIndex) {
		if (imageIndex >= 0) {
			image.set('html', '');
			activeImage = imageIndex;
			prevImage = ((activeImage || !options.loop) ? activeImage : images.length) - 1;
			nextImage = activeImage + 1;
			if (nextImage == images.length) nextImage = options.loop ? 0 : -1;
			stop();
			center.className = "mbLoading";

// MEDIABOX FORMATING
			WH = images[imageIndex][2].split(' ');
			WHL = WH.length;
			if (WHL>1) {
				mediaWidth = (WH[WHL-2].match("%")) ? (window.getWidth()*("0."+(WH[WHL-2].replace("%", ""))))+"px" : WH[WHL-2]+"px";
				mediaHeight = (WH[WHL-1].match("%")) ? (window.getHeight()*("0."+(WH[WHL-1].replace("%", ""))))+"px" : WH[WHL-1]+"px";
			} else {
				mediaWidth = "";
				mediaHeight = "";
			}
			URL = images[imageIndex][0];
			captionSplit = images[activeImage][1].split('::');

// Quietube and yFrog support
			if (URL.match(/quietube\.com/i)) {
				mediaSplit = URL.split('v.php/');
				URL = mediaSplit[1];
			} else if (URL.match(/\/\/yfrog/i)) {
				mediaType = (URL.substring(URL.length-1));
				if (mediaType.match(/b|g|j|p|t/i)) mediaType = 'image';
				if (mediaType == 's') mediaType = 'flash';
				if (mediaType.match(/f|z/i)) mediaType = 'video';
				URL = URL+":iphone";
			}

// MEDIA TYPES
// IMAGES
			if (URL.match(/\.gif|\.jpg|\.png|twitpic\.com/i) || mediaType == 'image') {
				mediaType = 'img';
				URL = URL.replace(/twitpic\.com/i, "twitpic.com/show/full");
				preload = new Image();
				preload.onload = startEffect;
				preload.src = URL;
// FLV, MP4
			} else if (URL.match(/\.flv|\.mp4/i) || mediaType == 'video') {
				mediaType = 'obj';
				mediaWidth = mediaWidth || options.defaultWidth;
				mediaHeight = mediaHeight || options.defaultHeight;
				if (options.useNB) {
				preload = new Swiff(''+options.NBpath+'?mediaURL='+URL+'&allowSmoothing=true&autoPlay='+options.autoplay+'&buffer=6&showTimecode='+options.showTimecode+'&loop='+options.NBloop+'&controlColour='+options.controllerColor+'&scaleIfFullScreen=true&showScalingButton=true', {
					id: 'MediaboxSWF',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				} else {
				preload = new Swiff(''+options.playerpath+'?file='+URL+'&backcolor='+options.backcolor+'&frontcolor='+options.frontcolor+'&lightcolor='+options.lightcolor+'&screencolor='+options.screencolor+'&autostart='+options.autoplay+'&controlbar='+options.controlbar, {
					id: 'MediaboxSWF',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				}
				startEffect();
// MP3, AAC
			} else if (URL.match(/\.mp3|\.aac|tweetmic\.com|tmic\.fm/i) || mediaType == 'audio') {
				mediaType = 'obj';
				mediaWidth = mediaWidth || options.defaultWidth;
				mediaHeight = mediaHeight || "20px";
				if (URL.match(/tweetmic\.com|tmic\.fm/i)) {
					URL = URL.split('/');
					URL[4] = URL[4] || URL[3];
					URL = "http://media4.fjarnet.net/tweet/tweetmicapp-"+URL[4]+'.mp3';
				}
//				if (options.useNB) {
//				preload = new Swiff(''+options.NBpath+'?mediaURL='+URL+'&allowSmoothing=true&autoPlay='+options.autoplay+'&buffer=6&showTimecode='+options.showTimecode+'&loop='+options.NBloop+'&controlColour='+options.controllerColor+'&scaleIfFullScreen=true&showScalingButton=true', {
//					id: 'MediaboxSWF',
//					width: mediaWidth,
//					height: mediaHeight,
//					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
//					});
//				} else {
				preload = new Swiff(''+options.playerpath+'?file='+URL+'&backcolor='+options.backcolor+'&frontcolor='+options.frontcolor+'&lightcolor='+options.lightcolor+'&screencolor='+options.screencolor+'&autostart='+options.autoplay, {
					id: 'MediaboxSWF',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
//				}
				startEffect();
// SWF
			} else if (URL.match(/\.swf/i) || mediaType == 'flash') {
				mediaType = 'obj';
				mediaWidth = mediaWidth || options.defaultWidth;
				mediaHeight = mediaHeight || options.defaultHeight;
				preload = new Swiff(URL, {
					id: 'MediaboxSWF',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// SOCIAL SITES
// Blip.tv
			} else if (URL.match(/blip\.tv/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "640px";
				mediaHeight = mediaHeight || "390px";
				preload = new Swiff(URL, {
					src: URL,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Break.com
			} else if (URL.match(/break\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "464px";
				mediaHeight = mediaHeight || "376px";
				mediaId = URL.match(/\d{6}/g)
				preload = new Swiff('http://embed.break.com/'+mediaId, {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// DailyMotion
			} else if (URL.match(/dailymotion\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "480px";
				mediaHeight = mediaHeight || "381px";
				preload = new Swiff(URL, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Facebook
			} else if (URL.match(/facebook\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "320px";
				mediaHeight = mediaHeight || "240px";
				mediaSplit = URL.split('v=');
				mediaSplit = mediaSplit[1].split('&');
				mediaId = mediaSplit[0];
				preload = new Swiff('http://www.facebook.com/v/'+mediaId, {
					movie: 'http://www.facebook.com/v/'+mediaId,
					classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Flickr
			} else if (URL.match(/flickr\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "500px";
				mediaHeight = mediaHeight || "375px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[5];
				preload = new Swiff('http://www.flickr.com/apps/video/stewart.swf', {
					id: mediaId,
					classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
					width: mediaWidth,
					height: mediaHeight,
					params: {flashvars: 'photo_id='+mediaId+'&amp;show_info_box='+options.flInfo, wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Fliggo
			} else if (URL.match(/fliggo\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "425px";
				mediaHeight = mediaHeight || "355px";
				URL = URL.replace('/video/', '/embed/');
				preload = new Swiff(URL, {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// GameTrailers Video
			} else if (URL.match(/gametrailers\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "480px";
				mediaHeight = mediaHeight || "392px";
				mediaId = URL.match(/\d{5}/g)
				preload = new Swiff('http://www.gametrailers.com/remote_wrap.php?mid='+mediaId, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Google Video
			} else if (URL.match(/google\.com\/videoplay/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "400px";
				mediaHeight = mediaHeight || "326px";
				mediaSplit = URL.split('=');
				mediaId = mediaSplit[1];
				preload = new Swiff('http://video.google.com/googleplayer.swf?docId='+mediaId+'&autoplay='+options.autoplayNum, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Justin.tv
			} else if (URL.match(/justin\.tv/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "353px";
				mediaHeight = mediaHeight || "295px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[3];
				preload = new Swiff('http://www.justin.tv/widgets/jtv_player.swf', {
					id: 'jtv_player_flash',
					width: mediaWidth,
					height: mediaHeight,
//					params: {flashvars: 'channel='+mediaId+'&auto_play=true&start_volume=25', wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					params: {flashvars: 'channel='+mediaId, wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Megavideo - Thanks to Robert Jandreu for suggesting this code!
			} else if (URL.match(/megavideo\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "640px";
				mediaHeight = mediaHeight || "360px";
				mediaSplit = URL.split('=');
				mediaId = mediaSplit[1];
				preload = new Swiff('http://wwwstatic.megavideo.com/mv_player.swf?v='+mediaId, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Metacafe
			} else if (URL.match(/metacafe\.com\/watch/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "400px";
				mediaHeight = mediaHeight || "345px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[4];
				preload = new Swiff('http://www.metacafe.com/fplayer/'+mediaId+'/.swf?playerVars=autoPlay='+options.autoplayYes, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// MyspaceTV
			} else if (URL.match(/myspacetv\.com|vids\.myspace\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "425px";
				mediaHeight = mediaHeight || "360px";
				mediaSplit = URL.split('=');
				mediaId = mediaSplit[2];
				preload = new Swiff('http://lads.myspace.com/videos/vplayer.swf?m='+mediaId+'&v=2&a='+options.autoplayNum+'&type=video', {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Revver
			} else if (URL.match(/revver\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "480px";
				mediaHeight = mediaHeight || "392px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[4];
				preload = new Swiff('http://flash.revver.com/player/1.0/player.swf?mediaId='+mediaId+'&affiliateId='+options.revverID+'&allowFullScreen='+options.revverFullscreen+'&autoStart='+options.autoplay+'&backColor=#'+options.revverBack+'&frontColor=#'+options.revverFront+'&gradColor=#'+options.revverGrad+'&shareUrl=revver', {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Rutube
			} else if (URL.match(/rutube\.ru/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "470px";
				mediaHeight = mediaHeight || "353px";
				mediaSplit = URL.split('=');
				mediaId = mediaSplit[1];
				preload = new Swiff('http://video.rutube.ru/'+mediaId, {
					movie: 'http://video.rutube.ru/'+mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Seesmic
			} else if (URL.match(/seesmic\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "435px";
				mediaHeight = mediaHeight || "355px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[5];
				preload = new Swiff('http://seesmic.com/Standalone.swf?video='+mediaId, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Tudou
			} else if (URL.match(/tudou\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "400px";
				mediaHeight = mediaHeight || "340px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[5];
				preload = new Swiff('http://www.tudou.com/v/'+mediaId, {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Twitvid
			} else if (URL.match(/twitvid\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "600px";
				mediaHeight = mediaHeight || "338px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[3];
				preload = new Swiff('http://www.twitvid.com/player/'+mediaId, {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Twitvid.io
			} else if (URL.match(/twitvid\.io/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "580px";
				mediaHeight = mediaHeight || "323px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[3];
				preload = new Swiff('http://twitvid.io/embed/'+mediaId, {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Ustream.tv
			} else if (URL.match(/ustream\.tv/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "400px";
				mediaHeight = mediaHeight || "326px";
				preload = new Swiff(URL+'&amp;viewcount='+options.usViewers+'&amp;autoplay='+options.autoplay, {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// YouKu
			} else if (URL.match(/youku\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "480px";
				mediaHeight = mediaHeight || "400px";
				mediaSplit = URL.split('id_');
				mediaId = mediaSplit[1];
				preload = new Swiff('http://player.youku.com/player.php/sid/'+mediaId+'=/v.swf', {
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// YouTube
			} else if (URL.match(/youtube\.com\/watch/i)) {
				mediaType = 'obj';
				mediaSplit = URL.split('v=');
				mediaId = mediaSplit[1];
				if (mediaId.match(/fmt=18/i)) {
					mediaFmt = '&ap=%2526fmt%3D18';
					mediaWidth = mediaWidth || "560px";
					mediaHeight = mediaHeight || "345px";
				} else if (mediaId.match(/fmt=22/i)) {
					mediaFmt = '&ap=%2526fmt%3D22';
					mediaWidth = mediaWidth || "640px";
					mediaHeight = mediaHeight || "385px";
				} else {
					mediaFmt = options.ytQuality;
					mediaWidth = mediaWidth || "480px";
					mediaHeight = mediaHeight || "295px";
				}
				preload = new Swiff('http://www.youtube.com/v/'+mediaId+'&autoplay='+options.autoplayNum+'&fs='+options.fullscreenNum+mediaFmt+'&border='+options.ytBorder+'&color1=0x'+options.ytColor1+'&color2=0x'+options.ytColor2+'&rel='+options.ytRel+'&showinfo='+options.ytInfo+'&showsearch='+options.ytSearch, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// YouTube
			} else if (URL.match(/youtube\.com\/view/i)) {
				mediaType = 'obj';
				mediaSplit = URL.split('p=');
				mediaId = mediaSplit[1];
				mediaWidth = mediaWidth || "480px";
				mediaHeight = mediaHeight || "385px";
				preload = new Swiff('http://www.youtube.com/p/'+mediaId+'&autoplay='+options.autoplayNum+'&fs='+options.fullscreenNum+mediaFmt+'&border='+options.ytBorder+'&color1=0x'+options.ytColor1+'&color2=0x'+options.ytColor2+'&rel='+options.ytRel+'&showinfo='+options.ytInfo+'&showsearch='+options.ytSearch, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Veoh
			} else if (URL.match(/veoh\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "410px";
				mediaHeight = mediaHeight || "341px";
				mediaSplit = URL.split('videos/');
				mediaId = mediaSplit[1];
				preload = new Swiff('http://www.veoh.com/videodetails2.swf?permalinkId='+mediaId+'&player=videodetailsembedded&videoAutoPlay='+options.AutoplayNum, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Viddler
			} else if (URL.match(/viddler\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "437px";
				mediaHeight = mediaHeight || "370px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[4];
				preload = new Swiff(URL, {
					id: 'viddler_'+mediaId,
					movie: URL,
					classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen, id: 'viddler_'+mediaId, movie: URL}
					});
				startEffect();
// Viddyou
			} else if (URL.match(/viddyou\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "416px";
				mediaHeight = mediaHeight || "312px";
				mediaSplit = URL.split('=');
				mediaId = mediaSplit[1];
				preload = new Swiff('http://www.viddyou.com/get/v2_'+options.vuPlayer+'/'+mediaId+'.swf', {
					id: mediaId,
					movie: 'http://www.viddyou.com/get/v2_'+options.vuPlayer+'/'+mediaId+'.swf',
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// Vimeo
			} else if (URL.match(/vimeo\.com/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "640px";		// site defualt: 400px
				mediaHeight = mediaHeight || "360px";	// site defualt: 225px
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[3];
				preload = new Swiff('http://www.vimeo.com/moogaloop.swf?clip_id='+mediaId+'&amp;server=www.vimeo.com&amp;fullscreen='+options.fullscreenNum+'&amp;autoplay='+options.autoplayNum+'&amp;show_title='+options.vmTitle+'&amp;show_byline='+options.vmByline+'&amp;show_portrait='+options.vmPortrait+'&amp;color='+options.vmColor, {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// 12seconds
			} else if (URL.match(/12seconds\.tv/i)) {
				mediaType = 'obj';
				mediaWidth = mediaWidth || "430px";
				mediaHeight = mediaHeight || "360px";
				mediaSplit = URL.split('/');
				mediaId = mediaSplit[5];
				preload = new Swiff('http://embed.12seconds.tv/players/remotePlayer.swf', {
					id: mediaId,
					width: mediaWidth,
					height: mediaHeight,
					params: {flashvars: 'vid='+mediaId+'', wmode: options.wmode, bgcolor: options.bgcolor, allowscriptaccess: options.scriptaccess, allowfullscreen: options.fullscreen}
					});
				startEffect();
// CONTENT TYPES
// INLINE
			} else if (URL.match(/\#mb_/i)) {
				mediaType = 'inline';
				mediaWidth = mediaWidth || options.defaultWidth;
				mediaHeight = mediaHeight || options.defaultHeight;
				URLsplit = URL.split('#');
				preload = $(URLsplit[1]).get('html');
				startEffect();
// HTML
			} else {
				mediaType = 'url';
				mediaWidth = mediaWidth || options.defaultWidth;
				mediaHeight = mediaHeight || options.defaultHeight;
				mediaId = "mediaId_"+new Date().getTime();	// Safari will not update iframe content with a static id.
				preload = new Element('iframe', {
					'src': URL,
					'id': mediaId,
					'width': mediaWidth,
					'height': mediaHeight,
//					'allowtransparency': 'true',
					'frameborder': 0
					});
				startEffect();
			}
		}
		return false;
	}

	function startEffect() {
		if (mediaType == "img"){
			mediaWidth = preload.width;
			mediaHeight = preload.height;
			image.setStyles({backgroundImage: "url("+URL+")", display: ""});
		} else if (mediaType == "obj") {
			if (Browser.Plugins.Flash.version<8) {
				image.setStyles({backgroundImage: "none", display: ""});
				image.set('html', '<div id="mbError"><b>Error</b><br/>Adobe Flash is either not installed or not up to date, please visit <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" title="Get Flash" target="_new">Adobe.com</a> to download the free player.</div>');
				mediaWidth = options.DefaultWidth;
				mediaHeight = options.DefaultHeight;
			} else {
				image.setStyles({backgroundImage: "none", display: ""});
				preload.inject(image);
			}
		} else if (mediaType == "inline") {
			image.setStyles({backgroundImage: "none", display: ""});
			image.set('html', preload);
		} else if (mediaType == "url") {
			image.setStyles({backgroundImage: "none", display: ""});
			preload.inject(image);
		} else {
			image.setStyles({backgroundImage: "none", display: ""});
			image.set('html', '<div id="mbError"><b>Error</b><br/>This file type is not supported, please visit <a href="iaian7.com/webcode/mediaboxAdvanced" title="mediaboxAdvanced" target="_new">iaian7.com</a> or contact the website author for more information.</div>');
			mediaWidth = options.defaultWidth;
			mediaHeight = options.defaultHeight;
//			alert('this file type is not supported\n'+URL+'\nplease visit iaian7.com/webcode/mediaboxAdvanced for more information');
		}
		image.setStyles({width: mediaWidth, height: mediaHeight});

		title.set('html', (options.showCaption) ? captionSplit[0] : "");
		caption.set('html', (options.showCaption && (captionSplit.length > 1)) ? captionSplit[1] : "");
		number.set('html', (options.showCounter && (images.length > 1)) ? options.counterText.replace(/{x}/, activeImage + 1).replace(/{y}/, images.length) : "");

		if ((prevImage >= 0) && (images[prevImage][0].match(/\.gif|\.jpg|\.png|twitpic\.com/i))) preloadPrev.src = images[prevImage][0].replace(/twitpic\.com/i, "twitpic.com/show/full");
		if ((nextImage >= 0) && (images[nextImage][0].match(/\.gif|\.jpg|\.png|twitpic\.com/i))) preloadNext.src = images[nextImage][0].replace(/twitpic\.com/i, "twitpic.com/show/full");

		mediaWidth = image.offsetWidth;
		mediaHeight = image.offsetHeight+bottom.offsetHeight;
		if (options.resizeOpening) { fx.resize.start({width: mediaWidth, height: mediaHeight, marginTop: -(mediaHeight/2), marginLeft: -mediaWidth/2});
		} else { center.setStyles({width: mediaWidth, height: mediaHeight, marginTop: -(mediaHeight/2), marginLeft: -mediaWidth/2}); imageAnimate(); }
	}

	function imageAnimate() {
		fx.image.start(1);
	}

	function captionAnimate() {
		center.className = "";
		if (prevImage >= 0) prevLink.style.display = "";
		if (nextImage >= 0) nextLink.style.display = "";
		fx.bottom.start(1);
	}

	function stop() {
		if (preload) preload.onload = $empty;
		fx.resize.cancel();
		fx.image.cancel().set(0);
		fx.bottom.cancel().set(0);
		$$(prevLink, nextLink).setStyle("display", "none");
	}

	function close() {
		if (activeImage >= 0) {
			preload.onload = $empty;
			image.set('html', '');
			for (var f in fx) fx[f].cancel();
			center.setStyle("display", "none");
			fx.overlay.chain(setup).start(0);
		}
		return false;
	}
})();

// AUTOLOAD CODE BLOCK
Mediabox.scanPage = function() {
//	$$('#mb_').each(function(hide) { hide.set('display', 'none'); });
	var links = $$("a").filter(function(el) {
		return el.rel && el.rel.test(/^lightbox/i);
	});
	$$(links).mediabox({/* Put custom options here */}, null, function(el) {
		var rel0 = this.rel.replace(/[[]|]/gi," ");
		var relsize = rel0.split(" ");
		return (this == el) || ((this.rel.length > 8) && el.rel.match(relsize[1]));
	});
};
window.addEvent("domready", Mediabox.scanPage);