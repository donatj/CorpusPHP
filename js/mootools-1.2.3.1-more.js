//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2009 Aaron Newton <http://clientcide.com/>, Valerio Proietti <http://mad4milk.net> & the MooTools team <http://mootools.net/developers>, MIT Style License.

MooTools.More = {
	'version': '1.2.3.1'
};

/*
Script: MooTools.Lang.js
	Provides methods for localization.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

(function(){

	var data = {
		language: 'en-US',
		languages: {
			'en-US': {}
		},
		cascades: ['en-US']
	};
	
	var cascaded;

	MooTools.lang = new Events();

	$extend(MooTools.lang, {

		setLanguage: function(lang){
			if (!data.languages[lang]) return this;
			data.language = lang;
			this.load();
			this.fireEvent('langChange', lang);
			return this;
		},

		load: function() {
			var langs = this.cascade(this.getCurrentLanguage());
			cascaded = {};
			$each(langs, function(set, setName){
				cascaded[setName] = this.lambda(set);
			}, this);
		},

		getCurrentLanguage: function(){
			return data.language;
		},

		addLanguage: function(lang){
			data.languages[lang] = data.languages[lang] || {};
			return this;
		},

		cascade: function(lang){
			var cascades = (data.languages[lang] || {}).cascades || [];
			cascades.combine(data.cascades);
			cascades.erase(lang).push(lang);
			var langs = cascades.map(function(lng){
				return data.languages[lng];
			}, this);
			return $merge.apply(this, langs);
		},

		lambda: function(set) {
			(set || {}).get = function(key, args){
				return $lambda(set[key]).apply(this, $splat(args));
			};
			return set;
		},

		get: function(set, key, args){
			if (cascaded && cascaded[set]) return (key ? cascaded[set].get(key, args) : cascaded[set]);
		},

		set: function(lang, set, members){
			this.addLanguage(lang);
			langData = data.languages[lang];
			if (!langData[set]) langData[set] = {};
			$extend(langData[set], members);
			if (lang == this.getCurrentLanguage()){
				this.load();
				this.fireEvent('langChange', lang);
			}
			return this;
		},

		list: function(){
			return Hash.getKeys(data.languages);
		}

	});

})();

/*
Script: Class.Refactor.js
	Extends a class onto itself with new property, preserving any items attached to the class's namespace.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

Class.refactor = function(original, refactors){

	$each(refactors, function(item, name){
		var origin = original.prototype[name];
		if (origin && (origin = origin._origin) && typeof item == 'function') original.implement(name, function(){
			var old = this.previous;
			this.previous = origin;
			var value = item.apply(this, arguments);
			this.previous = old;
			return value;
		}); else original.implement(name, item);
	});

	return original;

};

/*
Script: Class.Binds.js
	Automagically binds specified methods in a class to the instance of the class.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

Class.Mutators.Binds = function(binds){
	 return binds;
};

Class.Mutators.initialize = function(initialize){
	return function(){
		$splat(this.Binds).each(function(name){
			var original = this[name];
			if (original) this[name] = original.bind(this);
		}, this);
		return initialize.apply(this, arguments);
	};
};

/*
Script: Class.Occlude.js
	Prevents a class from being applied to a DOM element twice.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

Class.Occlude = new Class({

	occlude: function(property, element){
		element = document.id(element || this.element);
		var instance = element.retrieve(property || this.property);
		if (instance && !$defined(this.occluded)){
			this.occluded = instance;
		} else {
			this.occluded = false;
			element.store(property || this.property, this);
		}
		return this.occluded;
	}

});

/*
Script: Array.Extras.js
	Extends the Array native object to include useful methods to work with arrays.

	License:
		MIT-style license.

	Authors:
		Christoph Pojer

*/
Array.implement({

	min: function(){
		return Math.min.apply(null, this);
	},

	max: function(){
		return Math.max.apply(null, this);
	},

	average: function(){
		return this.length ? this.sum() / this.length : 0;
	},

	sum: function(){
		var result = 0, l = this.length;
		if (l){
			do {
				result += this[--l];
			} while (l);
		}
		return result;
	},

	unique: function(){
		return [].combine(this);
	}

});

/*
Script: Date.js
	Extends the Date native object to include methods useful in managing dates.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
		Nicholas Barthelemy - https://svn.nbarthelemy.com/date-js/
		Harald Kirshner - mail [at] digitarald.de; http://digitarald.de
		Scott Kyle - scott [at] appden.com; http://appden.com

*/

(function(){

if (!Date.now) Date.now = $time;

Date.Methods = {};

['Date', 'Day', 'FullYear', 'Hours', 'Milliseconds', 'Minutes', 'Month', 'Seconds', 'Time', 'TimezoneOffset',
	'Week', 'Timezone', 'GMTOffset', 'DayOfYear', 'LastMonth', 'LastDayOfMonth', 'UTCDate', 'UTCDay', 'UTCFullYear',
	'AMPM', 'Ordinal', 'UTCHours', 'UTCMilliseconds', 'UTCMinutes', 'UTCMonth', 'UTCSeconds'].each(function(method){
	Date.Methods[method.toLowerCase()] = method;
});

$each({
	ms: 'Milliseconds',
	year: 'FullYear',
	min: 'Minutes',
	mo: 'Month',
	sec: 'Seconds',
	hr: 'Hours'
}, function(value, key){
	Date.Methods[key] = value;
});

var zeroize = function(what, length){
	return new Array(length - what.toString().length + 1).join('0') + what;
};

Date.implement({

	set: function(prop, value){
		switch ($type(prop)){
			case 'object':
				for (var p in prop) this.set(p, prop[p]);
				break;
			case 'string':
				prop = prop.toLowerCase();
				var m = Date.Methods;
				if (m[prop]) this['set' + m[prop]](value);
		}
		return this;
	},

	get: function(prop){
		prop = prop.toLowerCase();
		var m = Date.Methods;
		if (m[prop]) return this['get' + m[prop]]();
		return null;
	},

	clone: function(){
		return new Date(this.get('time'));
	},

	increment: function(interval, times){
		interval = interval || 'day';
		times = $pick(times, 1);

		switch (interval){
			case 'year':
				return this.increment('month', times * 12);
			case 'month':
				var d = this.get('date');
				this.set('date', 1).set('mo', this.get('mo') + times);
				return this.set('date', d.min(this.get('lastdayofmonth')));
			case 'week':
				return this.increment('day', times * 7);
			case 'day':
				return this.set('date', this.get('date') + times);
		}

		if (!Date.units[interval]) throw new Error(interval + ' is not a supported interval');

		return this.set('time', this.get('time') + times * Date.units[interval]());
	},

	decrement: function(interval, times){
		return this.increment(interval, -1 * $pick(times, 1));
	},

	isLeapYear: function(){
		return Date.isLeapYear(this.get('year'));
	},

	clearTime: function(){
		return this.set({hr: 0, min: 0, sec: 0, ms: 0});
	},

	diff: function(d, resolution){
		resolution = resolution || 'day';
		if ($type(d) == 'string') d = Date.parse(d);

		switch (resolution){
			case 'year':
				return d.get('year') - this.get('year');
			case 'month':
				var months = (d.get('year') - this.get('year')) * 12;
				return months + d.get('mo') - this.get('mo');
			default:
				var diff = d.get('time') - this.get('time');
				if (Date.units[resolution]() > diff.abs()) return 0;
				return ((d.get('time') - this.get('time')) / Date.units[resolution]()).round();
		}

		return null;
	},

	getLastDayOfMonth: function(){
		return Date.daysInMonth(this.get('mo'), this.get('year'));
	},

	getDayOfYear: function(){
		return (Date.UTC(this.get('year'), this.get('mo'), this.get('date') + 1) 
			- Date.UTC(this.get('year'), 0, 1)) / Date.units.day();
	},

	getWeek: function(){
		return (this.get('dayofyear') / 7).ceil();
	},
	
	getOrdinal: function(day){
		return Date.getMsg('ordinal', day || this.get('date'));
	},

	getTimezone: function(){
		return this.toString()
			.replace(/^.*? ([A-Z]{3}).[0-9]{4}.*$/, '$1')
			.replace(/^.*?\(([A-Z])[a-z]+ ([A-Z])[a-z]+ ([A-Z])[a-z]+\)$/, '$1$2$3');
	},

	getGMTOffset: function(){
		var off = this.get('timezoneOffset');
		return ((off > 0) ? '-' : '+') + zeroize((off.abs() / 60).floor(), 2) + zeroize(off % 60, 2);
	},

	setAMPM: function(ampm){
		ampm = ampm.toUpperCase();
		var hr = this.get('hr');
		if (hr > 11 && ampm == 'AM') return this.decrement('hour', 12);
		else if (hr < 12 && ampm == 'PM') return this.increment('hour', 12);
		return this;
	},

	getAMPM: function(){
		return (this.get('hr') < 12) ? 'AM' : 'PM';
	},

	parse: function(str){
		this.set('time', Date.parse(str));
		return this;
	},

	isValid: function(date) {
		return !!(date || this).valueOf();
	},

	format: function(f){
		if (!this.isValid()) return 'invalid date';
		f = f || '%x %X';
		f = formats[f.toLowerCase()] || f; // replace short-hand with actual format
		var d = this;
		return f.replace(/%([a-z%])/gi,
			function($1, $2){
				switch ($2){
					case 'a': return Date.getMsg('days')[d.get('day')].substr(0, 3);
					case 'A': return Date.getMsg('days')[d.get('day')];
					case 'b': return Date.getMsg('months')[d.get('month')].substr(0, 3);
					case 'B': return Date.getMsg('months')[d.get('month')];
					case 'c': return d.toString();
					case 'd': return zeroize(d.get('date'), 2);
					case 'H': return zeroize(d.get('hr'), 2);
					case 'I': return ((d.get('hr') % 12) || 12);
					case 'j': return zeroize(d.get('dayofyear'), 3);
					case 'm': return zeroize((d.get('mo') + 1), 2);
					case 'M': return zeroize(d.get('min'), 2);
					case 'o': return d.get('ordinal');
					case 'p': return Date.getMsg(d.get('ampm'));
					case 'S': return zeroize(d.get('seconds'), 2);
					case 'U': return zeroize(d.get('week'), 2);
					case 'w': return d.get('day');
					case 'x': return d.format(Date.getMsg('shortDate'));
					case 'X': return d.format(Date.getMsg('shortTime'));
					case 'y': return d.get('year').toString().substr(2);
					case 'Y': return d.get('year');
					case 'T': return d.get('GMTOffset');
					case 'Z': return d.get('Timezone');
				}
				return $2;
			}
		);
	},

	toISOString: function(){
		return this.format('iso8601');
	}

});

Date.alias('diff', 'compare');
Date.alias('format', 'strftime');

var formats = {
	db: '%Y-%m-%d %H:%M:%S',
	compact: '%Y%m%dT%H%M%S',
	iso8601: '%Y-%m-%dT%H:%M:%S%T',
	rfc822: '%a, %d %b %Y %H:%M:%S %Z',
	'short': '%d %b %H:%M',
	'long': '%B %d, %Y %H:%M'
};

var nativeParse = Date.parse;

var parseWord = function(type, word, num){
	var ret = -1;
	var translated = Date.getMsg(type + 's');

	switch ($type(word)){
		case 'object':
			ret = translated[word.get(type)];
			break;
		case 'number':
			ret = translated[month - 1];
			if (!ret) throw new Error('Invalid ' + type + ' index: ' + index);
			break;
		case 'string':
			var match = translated.filter(function(name){
				return this.test(name);
			}, new RegExp('^' + word, 'i'));
			if (!match.length)    throw new Error('Invalid ' + type + ' string');
			if (match.length > 1) throw new Error('Ambiguous ' + type);
			ret = match[0];
	}

	return (num) ? translated.indexOf(ret) : ret;
};


Date.extend({

	getMsg: function(key, args) {
		return MooTools.lang.get('Date', key, args);
	},

	units: {
		ms: $lambda(1),
		second: $lambda(1000),
		minute: $lambda(60000),
		hour: $lambda(3600000),
		day: $lambda(86400000),
		week: $lambda(608400000),
		month: function(month, year){
			var d = new Date;
			return Date.daysInMonth($pick(month, d.get('mo')), $pick(year, d.get('year'))) * 86400000;
		},
		year: function(year){
			year = year || new Date().get('year');
			return Date.isLeapYear(year) ? 31622400000 : 31536000000;
		}
	},

	daysInMonth: function(month, year){
		return [31, Date.isLeapYear(year) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
	},

	isLeapYear: function(year){
		return new Date(year, 1, 29).get('date') == 29;
	},

	parse: function(from){
		var t = $type(from);
		if (t == 'number') return new Date(from);
		if (t != 'string') return from;
		from = from.clean();
		if (!from.length) return null;

		var parsed;
		Date.parsePatterns.some(function(pattern){
			var r = pattern.re.exec(from);
			return (r) ? (parsed = pattern.handler(r)) : false;
		});

		return parsed || new Date(nativeParse(from));
	},

	parseDay: function(day, num){
		return parseWord('day', day, num);
	},

	parseMonth: function(month, num){
		return parseWord('month', month, num);
	},

	parseUTC: function(value){
		var localDate = new Date(value);
		var utcSeconds = Date.UTC(localDate.get('year'), 
		localDate.get('mo'),
		localDate.get('date'), 
		localDate.get('hr'), 
		localDate.get('min'), 
		localDate.get('sec'));
		return new Date(utcSeconds);
	},

	orderIndex: function(unit){
		return Date.getMsg('dateOrder').indexOf(unit) + 1;
	},

	defineFormat: function(name, format){
		formats[name] = format;
	},

	defineFormats: function(formats){
		for (var name in formats) Date.defineFormat(name, formats[f]);
	},

	parsePatterns: [],
	
	defineParser: function(pattern){
		Date.parsePatterns.push( pattern.re && pattern.handler ? pattern : build(pattern) );
	},
	
	defineParsers: function(){
		Array.flatten(arguments).each(Date.defineParser);
	},
	
	define2DigitYearStart: function(year){
		yr_start = year % 100;
		yr_base = year - yr_start;
	}

});

var yr_base = 1900;
var yr_start = 70;

var replacers = function(key){
	switch(key){
		case 'x': // iso8601 covers yyyy-mm-dd, so just check if month is first
			return (Date.orderIndex('month') == 1) ? '%m[.-/]%d([.-/]%y)?' : '%d[.-/]%m([.-/]%y)?';
		case 'X':
			return '%H([.:]%M)?([.:]%S([.:]%s)?)?\\s?%p?\\s?%T?';
		case 'o':
			return '[^\\d\\s]*';
	}
	return null;
};

var keys = {
	a: /[a-z]{3,}/,
	d: /[0-2]?[0-9]|3[01]/,
	H: /[01]?[0-9]|2[0-3]/,
	I: /0?[1-9]|1[0-2]/,
	M: /[0-5]?\d/,
	s: /\d+/,
	p: /[ap]\.?m\.?/,
	y: /\d{2}|\d{4}/,
	Y: /\d{4}/,
	T: /Z|[+-]\d{2}(?::?\d{2})?/
};

keys.B = keys.b = keys.A = keys.a;
keys.m = keys.I;
keys.S = keys.M;

var lang;

var build = function(format){
	if (!lang) return {format: format}; // wait until language is set
	
	var parsed = [null];

	var re = (format.source || format) // allow format to be regex
	 .replace(/%([a-z])/gi,
		function($1, $2){
			return replacers($2) || $1;
		}
	).replace(/\((?!\?)/g, '(?:') // make all groups non-capturing
	 .replace(/ (?!\?|\*)/g, ',? ') // be forgiving with spaces and commas
	 .replace(/%([a-z%])/gi,
		function($1, $2){
			var p = keys[$2];
			if (!p) return $2;
			parsed.push($2);
			return '(' + p.source + ')';
		}
	);

	return {
		format: format,
		re: new RegExp('^' + re + '$', 'i'),
		handler: function(bits){
			var date = new Date().clearTime();
			for (var i = 1; i < parsed.length; i++)
				date = handle.call(date, parsed[i], bits[i]);
			return date;
		}
	};
};

var handle = function(key, value){
	if (!value){
		if (key == 'm' || key == 'd') value = 1;
		else return this;
	}

	switch(key){
		case 'a': case 'A': return this.set('day', Date.parseDay(value, true));
		case 'b': case 'B': return this.set('mo', Date.parseMonth(value, true));
		case 'd': return this.set('date', value);
		case 'H': case 'I': return this.set('hr', value);
		case 'm': return this.set('mo', value - 1);
		case 'M': return this.set('min', value);
		case 'p': return this.set('ampm', value.replace(/\./g, ''));
		case 'S': return this.set('sec', value);
		case 's': return this.set('ms', ('0.' + value) * 1000);
		case 'w': return this.set('day', value);
		case 'Y': return this.set('year', value);
		case 'y':
			value = +value;
			if (value < 100) value += yr_base + (value < yr_start ? 100 : 0);
			return this.set('year', value);
		case 'T':
			if (value == 'Z') value = '+00';
			var offset = value.match(/([+-])(\d{2}):?(\d{2})?/);
			offset = (offset[1] + '1') * (offset[2] * 60 + (+offset[3] || 0)) + this.getTimezoneOffset();
			return this.set('time', (this * 1) - offset * 60000);
	}

	return this;
};

Date.defineParsers(
	'%Y([-./]%m([-./]%d((T| )%X)?)?)?', // "1999-12-31", "1999-12-31 11:59pm", "1999-12-31 23:59:59", ISO8601
	'%Y%m%d(T%H(%M%S?)?)?', // "19991231", "19991231T1159", compact
	'%x( %X)?', // "12/31", "12.31.99", "12-31-1999", "12/31/2008 11:59 PM"
	'%d%o( %b( %Y)?)?( %X)?', // "31st", "31st December", "31 Dec 1999", "31 Dec 1999 11:59pm"
	'%b %d%o?( %Y)?( %X)?', // Same as above with month and day switched
	'%b %Y' // "December 1999"
);

MooTools.lang.addEvent('langChange', function(language){
	if (!MooTools.lang.get('Date')) return;

	lang = language;
	Date.parsePatterns.each(function(pattern, i){
		if (pattern.format) Date.parsePatterns[i] = build(pattern.format);
	});

}).fireEvent('langChange', MooTools.lang.getCurrentLanguage());

})();

/*
Script: Date.Extras.js
	Extends the Date native object to include extra methods (on top of those in Date.js).

	License:
		MIT-style license.

	Authors:
		Aaron Newton

*/

Date.implement({

	timeDiffInWords: function(relative_to){
		return Date.distanceOfTimeInWords(this, relative_to || new Date);
	}

});

Date.alias('timeDiffInWords', 'timeAgoInWords');

Date.extend({

	distanceOfTimeInWords: function(from, to){
		return Date.getTimePhrase(((to - from) / 1000).toInt());
	},

	getTimePhrase: function(delta){
		var suffix = (delta < 0) ? 'Until' : 'Ago';
		if (delta < 0) delta *= -1;
		
		var msg = (delta < 60) ? 'lessThanMinute' :
				  (delta < 120) ? 'minute' :
				  (delta < (45 * 60)) ? 'minutes' :
				  (delta < (90 * 60)) ? 'hour' :
				  (delta < (24 * 60 * 60)) ? 'hours' :
				  (delta < (48 * 60 * 60)) ? 'day' :
				  'days';
		
		switch(msg){
			case 'minutes': delta = (delta / 60).round(); break;
			case 'hours':   delta = (delta / 3600).round(); break;
			case 'days': 	delta = (delta / 86400).round();
		}
		
		return Date.getMsg(msg + suffix, delta).substitute({delta: delta});
	}

});


Date.defineParsers(

	{
		// "today", "tomorrow", "yesterday"
		re: /^tod|tom|yes/i,
		handler: function(bits){
			var d = new Date().clearTime();
			switch(bits[0]){
				case 'tom': return d.increment();
				case 'yes': return d.decrement();
				default: 	return d;
			}
		}
	},

	{
		// "next Wednesday", "last Thursday"
		re: /^(next|last) ([a-z]+)$/i,
		handler: function(bits){
			var d = new Date().clearTime();
			var day = d.getDay();
			var newDay = Date.parseDay(bits[2], true);
			var addDays = newDay - day;
			if (newDay <= day) addDays += 7;
			if (bits[1] == 'last') addDays -= 7;
			return d.set('date', d.getDate() + addDays);
		}
	}

);


/*
Script: URI.js
	Provides methods useful in managing the window location and uris.

	License:
		MIT-style license.

	Authors:
		Sebastian Markbåge, Aaron Newton
*/

var URI = new Class({

	Implements: Options,

	/*
	options: {
		base: false
	},
	*/

	regex: /^(?:(\w+):)?(?:\/\/(?:(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)?(\.\.?$|(?:[^?#\/]*\/)*)([^?#]*)(?:\?([^#]*))?(?:#(.*))?/,
	parts: ['scheme', 'user', 'password', 'host', 'port', 'directory', 'file', 'query', 'fragment'],
	schemes: { http: 80, https: 443, ftp: 21, rtsp: 554, mms: 1755, file: 0 },

	initialize: function(uri, options){
		this.setOptions(options);
		var base = this.options.base || URI.base;
		uri = uri || base;
		if (uri && uri.parsed)
			this.parsed = $unlink(uri.parsed);
		else
			this.set('value', uri.href || uri.toString(), base ? new URI(base) : false);
	},

	parse: function(value, base){
		var bits = value.match(this.regex);
		if (!bits) return false;
		bits.shift();
		return this.merge(bits.associate(this.parts), base);
	},

	merge: function(bits, base){
		if ((!bits || !bits.scheme) && (!base || !base.scheme)) return false;
		if (base){
			this.parts.every(function(part){
				if (bits[part]) return false;
				bits[part] = base[part] || '';
				return true;
			});
		}
		bits.port = bits.port || this.schemes[bits.scheme.toLowerCase()];
		bits.directory = bits.directory ? this.parseDirectory(bits.directory, base ? base.directory : '') : '/';
		return bits;
	},

	parseDirectory: function(directory, baseDirectory) {
		directory = (directory.substr(0, 1) == '/' ? '' : (baseDirectory || '/')) + directory;
		if (!directory.test(URI.regs.directoryDot)) return directory;
		var result = [];
		directory.replace(URI.regs.endSlash, '').split('/').each(function(dir){
			if (dir == '..' && result.length > 0) result.pop();
			else if (dir != '.') result.push(dir);
		});
		return result.join('/') + '/';
	},

	combine: function(bits){
		return bits.value || bits.scheme + '://' +
			(bits.user ? bits.user + (bits.password ? ':' + bits.password : '') + '@' : '') +
			(bits.host || '') + (bits.port && bits.port != this.schemes[bits.scheme] ? ':' + bits.port : '') +
			(bits.directory || '/') + (bits.file || '') +
			(bits.query ? '?' + bits.query : '') +
			(bits.fragment ? '#' + bits.fragment : '');
	},

	set: function(part, value, base){
		if (part == 'value'){
			var scheme = value.match(URI.regs.scheme);
			if (scheme) scheme = scheme[1];
			if (scheme && !$defined(this.schemes[scheme.toLowerCase()])) this.parsed = { scheme: scheme, value: value };
			else this.parsed = this.parse(value, (base || this).parsed) || (scheme ? { scheme: scheme, value: value } : { value: value });
		} else if (part == 'data') {
			this.setData(value);
		} else {
			this.parsed[part] = value;
		}
		return this;
	},

	get: function(part, base){
		switch(part){
			case 'value': return this.combine(this.parsed, base ? base.parsed : false);
			case 'data' : return this.getData();
		}
		return this.parsed[part] || undefined;
	},

	go: function(){
		document.location.href = this.toString();
	},

	toURI: function(){
		return this;
	},

	getData: function(key, part){
		var qs = this.get(part || 'query');
		if (!$chk(qs)) return key ? null : {};
		var obj = qs.parseQueryString();
		return key ? obj[key] : obj;
	},

	setData: function(values, merge, part){
		if ($type(arguments[0]) == 'string'){ 
			values = this.getData(); 
			values[arguments[0]] = arguments[1]; 
		} else if (merge) {
			values = $merge(this.getData(), values);
		}
		return this.set(part || 'query', Hash.toQueryString(values));
	},

	clearData: function(part){
		return this.set(part || 'query', '');
	}

});

['toString', 'valueOf'].each(function(method){
	URI.prototype[method] = function(){
		return this.get('value');
	};
});


URI.regs = {
	endSlash: /\/$/,
	scheme: /^(\w+):/,
	directoryDot: /\.\/|\.$/
};

URI.base = new URI($$('base[href]').getLast(), { base: document.location });

String.implement({

	toURI: function(options){ return new URI(this, options); }

});

/*
Script: Element.Forms.js
	Extends the Element native object to include methods useful in managing inputs.

	License:
		MIT-style license.

	Authors:
		Aaron Newton

*/

Element.implement({

	tidy: function(){
		this.set('value', this.get('value').tidy());
	},

	getTextInRange: function(start, end){
		return this.get('value').substring(start, end);
	},

	getSelectedText: function(){
		if (this.setSelectionRange) return this.getTextInRange(this.getSelectionStart(), this.getSelectionEnd());
		return document.selection.createRange().text;
	},

	getSelectedRange: function() {
		if ($defined(this.selectionStart)) return {start: this.selectionStart, end: this.selectionEnd};
		var pos = {start: 0, end: 0};
		var range = this.getDocument().selection.createRange();
		if (!range || range.parentElement() != this) return pos;
		var dup = range.duplicate();
		if (this.type == 'text') {
			pos.start = 0 - dup.moveStart('character', -100000);
			pos.end = pos.start + range.text.length;
		} else {
			var value = this.get('value');
			var offset = value.length - value.match(/[\n\r]*$/)[0].length;
			dup.moveToElementText(this);
			dup.setEndPoint('StartToEnd', range);
			pos.end = offset - dup.text.length;
			dup.setEndPoint('StartToStart', range);
			pos.start = offset - dup.text.length;
		}
		return pos;
	},

	getSelectionStart: function(){
		return this.getSelectedRange().start;
	},

	getSelectionEnd: function(){
		return this.getSelectedRange().end;
	},

	setCaretPosition: function(pos){
		if (pos == 'end') pos = this.get('value').length;
		this.selectRange(pos, pos);
		return this;
	},

	getCaretPosition: function(){
		return this.getSelectedRange().start;
	},

	selectRange: function(start, end){
		if (this.setSelectionRange) {
			this.focus();
			this.setSelectionRange(start, end);
		} else {
			var value = this.get('value');
			var diff = value.substr(start, end - start).replace(/\r/g, '').length;
			start = value.substr(0, start).replace(/\r/g, '').length;
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', start + diff);
			range.moveStart('character', start);
			range.select();
		}
		return this;
	},

	insertAtCursor: function(value, select){
		var pos = this.getSelectedRange();
		var text = this.get('value');
		this.set('value', text.substring(0, pos.start) + value + text.substring(pos.end, text.length));
		if ($pick(select, true)) this.selectRange(pos.start, pos.start + value.length);
		else this.setCaretPosition(pos.start + value.length);
		return this;
	},

	insertAroundCursor: function(options, select){
		options = $extend({
			before: '',
			defaultMiddle: '',
			after: ''
		}, options);
		var value = this.getSelectedText() || options.defaultMiddle;
		var pos = this.getSelectedRange();
		var text = this.get('value');
		if (pos.start == pos.end){
			this.set('value', text.substring(0, pos.start) + options.before + value + options.after + text.substring(pos.end, text.length));
			this.selectRange(pos.start + options.before.length, pos.end + options.before.length + value.length);
		} else {
			var current = text.substring(pos.start, pos.end);
			this.set('value', text.substring(0, pos.start) + options.before + current + options.after + text.substring(pos.end, text.length));
			var selStart = pos.start + options.before.length;
			if ($pick(select, true)) this.selectRange(selStart, selStart + current.length);
			else this.setCaretPosition(selStart + text.length);
		}
		return this;
	}

});

/*
Script: Element.Measure.js
	Extends the Element native object to include methods useful in measuring dimensions.

	Element.measure / .expose methods by Daniel Steigerwald
	License: MIT-style license.
	Copyright: Copyright (c) 2008 Daniel Steigerwald, daniel.steigerwald.cz

	License:
		MIT-style license.

	Authors:
		Aaron Newton

*/

Element.implement({

	measure: function(fn){
		var vis = function(el) {
			return !!(!el || el.offsetHeight || el.offsetWidth);
		};
		if (vis(this)) return fn.apply(this);
		var parent = this.getParent(),
			toMeasure = [], 
			restorers = [];
		while (!vis(parent) && parent != document.body) {
			toMeasure.push(parent.expose());
			parent = parent.getParent();
		}
		var restore = this.expose();
		var result = fn.apply(this);
		restore();
		toMeasure.each(function(restore){
			restore();
		});
		return result;
	},

	expose: function(){
		if (this.getStyle('display') != 'none') return $empty;
		var before = this.style.cssText;
		this.setStyles({
			display: 'block',
			position: 'absolute',
			visibility: 'hidden'
		});
		return function(){
			this.style.cssText = before;
		}.bind(this);
	},

	getDimensions: function(options){
		options = $merge({computeSize: false},options);
		var dim = {};
		var getSize = function(el, options){
			return (options.computeSize)?el.getComputedSize(options):el.getSize();
		};
		if (this.getStyle('display') == 'none'){
			dim = this.measure(function(){
				return getSize(this, options);
			});
		} else {
			try { //safari sometimes crashes here, so catch it
				dim = getSize(this, options);
			}catch(e){}
		}
		return $chk(dim.x) ? $extend(dim, {width: dim.x, height: dim.y}) : $extend(dim, {x: dim.width, y: dim.height});
	},

	getComputedSize: function(options){
		options = $merge({
			styles: ['padding','border'],
			plains: {
				height: ['top','bottom'],
				width: ['left','right']
			},
			mode: 'both'
		}, options);
		var size = {width: 0,height: 0};
		switch (options.mode){
			case 'vertical':
				delete size.width;
				delete options.plains.width;
				break;
			case 'horizontal':
				delete size.height;
				delete options.plains.height;
				break;
		}
		var getStyles = [];
		//this function might be useful in other places; perhaps it should be outside this function?
		$each(options.plains, function(plain, key){
			plain.each(function(edge){
				options.styles.each(function(style){
					getStyles.push((style == 'border') ? style + '-' + edge + '-' + 'width' : style + '-' + edge);
				});
			});
		});
		var styles = {};
		getStyles.each(function(style){ styles[style] = this.getComputedStyle(style); }, this);
		var subtracted = [];
		$each(options.plains, function(plain, key){ //keys: width, height, plains: ['left', 'right'], ['top','bottom']
			var capitalized = key.capitalize();
			size['total' + capitalized] = 0;
			size['computed' + capitalized] = 0;
			plain.each(function(edge){ //top, left, right, bottom
				size['computed' + edge.capitalize()] = 0;
				getStyles.each(function(style, i){ //padding, border, etc.
					//'padding-left'.test('left') size['totalWidth'] = size['width'] + [padding-left]
					if (style.test(edge)){
						styles[style] = styles[style].toInt() || 0; //styles['padding-left'] = 5;
						size['total' + capitalized] = size['total' + capitalized] + styles[style];
						size['computed' + edge.capitalize()] = size['computed' + edge.capitalize()] + styles[style];
					}
					//if width != width (so, padding-left, for instance), then subtract that from the total
					if (style.test(edge) && key != style &&
						(style.test('border') || style.test('padding')) && !subtracted.contains(style)){
						subtracted.push(style);
						size['computed' + capitalized] = size['computed' + capitalized]-styles[style];
					}
				});
			});
		});

		['Width', 'Height'].each(function(value){
			var lower = value.toLowerCase();
			if(!$chk(size[lower])) return;

			size[lower] = size[lower] + this['offset' + value] + size['computed' + value];
			size['total' + value] = size[lower] + size['total' + value];
			delete size['computed' + value];
		}, this);

		return $extend(styles, size);
	}

});

/*
Script: Element.Position.js
	Extends the Element native object to include methods useful positioning elements relative to others.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

(function(){

var original = Element.prototype.position;

Element.implement({

	position: function(options){
		//call original position if the options are x/y values
		if (options && ($defined(options.x) || $defined(options.y))) return original ? original.apply(this, arguments) : this;
		$each(options||{}, function(v, k){ if (!$defined(v)) delete options[k]; });
		options = $merge({
			relativeTo: document.body,
			position: {
				x: 'center', //left, center, right
				y: 'center' //top, center, bottom
			},
			edge: false,
			offset: {x: 0, y: 0},
			returnPos: false,
			relFixedPosition: false,
			ignoreMargins: false,
			allowNegative: false
		}, options);
		//compute the offset of the parent positioned element if this element is in one
		var parentOffset = {x: 0, y: 0};
		var parentPositioned = false;
		/* dollar around getOffsetParent should not be necessary, but as it does not return
		 * a mootools extended element in IE, an error occurs on the call to expose. See:
		 * http://mootools.lighthouseapp.com/projects/2706/tickets/333-element-getoffsetparent-inconsistency-between-ie-and-other-browsers */
		var offsetParent = this.measure(function(){
			return document.id(this.getOffsetParent());
		});
		if (offsetParent && offsetParent != this.getDocument().body){
			parentOffset = offsetParent.measure(function(){
				return this.getPosition();
			});
			parentPositioned = true;
			options.offset.x = options.offset.x - parentOffset.x;
			options.offset.y = options.offset.y - parentOffset.y;
		}
		//upperRight, bottomRight, centerRight, upperLeft, bottomLeft, centerLeft
		//topRight, topLeft, centerTop, centerBottom, center
		var fixValue = function(option){
			if ($type(option) != 'string') return option;
			option = option.toLowerCase();
			var val = {};
			if (option.test('left')) val.x = 'left';
			else if (option.test('right')) val.x = 'right';
			else val.x = 'center';
			if (option.test('upper') || option.test('top')) val.y = 'top';
			else if (option.test('bottom')) val.y = 'bottom';
			else val.y = 'center';
			return val;
		};
		options.edge = fixValue(options.edge);
		options.position = fixValue(options.position);
		if (!options.edge){
			if (options.position.x == 'center' && options.position.y == 'center') options.edge = {x:'center', y:'center'};
			else options.edge = {x:'left', y:'top'};
		}

		this.setStyle('position', 'absolute');
		var rel = document.id(options.relativeTo) || document.body;
		var calc = rel == document.body ? window.getScroll() : rel.getPosition();
		var top = calc.y;
		var left = calc.x;

		if (Browser.Engine.trident){
			var scrolls = rel.getScrolls();
			top += scrolls.y;
			left += scrolls.x;
		}

		var dim = this.getDimensions({computeSize: true, styles:['padding', 'border','margin']});
		if (options.ignoreMargins){
			options.offset.x = options.offset.x - dim['margin-left'];
			options.offset.y = options.offset.y - dim['margin-top'];
		}
		var pos = {};
		var prefY = options.offset.y;
		var prefX = options.offset.x;
		var winSize = window.getSize();
		switch(options.position.x){
			case 'left':
				pos.x = left + prefX;
				break;
			case 'right':
				pos.x = left + prefX + rel.offsetWidth;
				break;
			default: //center
				pos.x = left + ((rel == document.body ? winSize.x : rel.offsetWidth)/2) + prefX;
				break;
		}
		switch(options.position.y){
			case 'top':
				pos.y = top + prefY;
				break;
			case 'bottom':
				pos.y = top + prefY + rel.offsetHeight;
				break;
			default: //center
				pos.y = top + ((rel == document.body ? winSize.y : rel.offsetHeight)/2) + prefY;
				break;
		}

		if (options.edge){
			var edgeOffset = {};

			switch(options.edge.x){
				case 'left':
					edgeOffset.x = 0;
					break;
				case 'right':
					edgeOffset.x = -dim.x-dim.computedRight-dim.computedLeft;
					break;
				default: //center
					edgeOffset.x = -(dim.x/2);
					break;
			}
			switch(options.edge.y){
				case 'top':
					edgeOffset.y = 0;
					break;
				case 'bottom':
					edgeOffset.y = -dim.y-dim.computedTop-dim.computedBottom;
					break;
				default: //center
					edgeOffset.y = -(dim.y/2);
					break;
			}
			pos.x = pos.x + edgeOffset.x;
			pos.y = pos.y + edgeOffset.y;
		}
		pos = {
			left: ((pos.x >= 0 || parentPositioned || options.allowNegative) ? pos.x : 0).toInt(),
			top: ((pos.y >= 0 || parentPositioned || options.allowNegative) ? pos.y : 0).toInt()
		};
		if (rel.getStyle('position') == 'fixed' || options.relFixedPosition){
			var winScroll = window.getScroll();
			pos.top = pos.top.toInt() + winScroll.y;
			pos.left = pos.left.toInt() + winScroll.x;
		}

		if (options.returnPos) return pos;
		else this.setStyles(pos);
		return this;
	}

});

})();

/*
Script: Element.Shortcuts.js
	Extends the Element native object to include some shortcut methods.

	License:
		MIT-style license.

	Authors:
		Aaron Newton

*/

Element.implement({

	isDisplayed: function(){
		return this.getStyle('display') != 'none';
	},

	toggle: function(){
		return this[this.isDisplayed() ? 'hide' : 'show']();
	},

	hide: function(){
		var d;
		try {
			//IE fails here if the element is not in the dom
			if ('none' != this.getStyle('display')) d = this.getStyle('display');
		} catch(e){}

		return this.store('originalDisplay', d || 'block').setStyle('display', 'none');
	},

	show: function(display){
		return this.setStyle('display', display || this.retrieve('originalDisplay') || 'block');
	},

	swapClass: function(remove, add){
		return this.removeClass(remove).addClass(add);
	}

});


/*
Script: OverText.js
	Shows text over an input that disappears when the user clicks into it. The text remains hidden if the user adds a value.

	License:
		MIT-style license.

	Authors:
		Aaron Newton
*/

var OverText = new Class({

	Implements: [Options, Events, Class.Occlude],

	Binds: ['reposition', 'assert', 'focus'],

	options: {/*
		textOverride: null,
		onFocus: $empty()
		onTextHide: $empty(textEl, inputEl),
		onTextShow: $empty(textEl, inputEl), */
		element: 'label',
		positionOptions: {
			position: 'upperLeft',
			edge: 'upperLeft',
			offset: {
				x: 4,
				y: 2
			}
		},
		poll: false,
		pollInterval: 250
	},

	property: 'OverText',

	initialize: function(element, options){
		this.element = document.id(element);
		if (this.occlude()) return this.occluded;
		this.setOptions(options);
		this.attach(this.element);
		OverText.instances.push(this);
		if (this.options.poll) this.poll();
		return this;
	},

	toElement: function(){
		return this.element;
	},

	attach: function(){
		var val = this.options.textOverride || this.element.get('alt') || this.element.get('title');
		if (!val) return;
		this.text = new Element(this.options.element, {
			'class': 'overTxtLabel',
			styles: {
				lineHeight: 'normal',
				position: 'absolute'
			},
			html: val,
			events: {
				click: this.hide.pass(true, this)
			}
		}).inject(this.element, 'after');
		if (this.options.element == 'label') this.text.set('for', this.element.get('id'));
		this.element.addEvents({
			focus: this.focus,
			blur: this.assert,
			change: this.assert
		}).store('OverTextDiv', this.text);
		window.addEvent('resize', this.reposition.bind(this));
		this.assert(true);
		this.reposition();
	},

	startPolling: function(){
		this.pollingPaused = false;
		return this.poll();
	},

	poll: function(stop){
		//start immediately
		//pause on focus
		//resumeon blur
		if (this.poller && !stop) return this;
		var test = function(){
			if (!this.pollingPaused) this.assert(true);
		}.bind(this);
		if (stop) $clear(this.poller);
		else this.poller = test.periodical(this.options.pollInterval, this);
		return this;
	},

	stopPolling: function(){
		this.pollingPaused = true;
		return this.poll(true);
	},

	focus: function(){
		if (!this.text.isDisplayed() || this.element.get('disabled')) return;
		this.hide();
	},

	hide: function(suppressFocus){
		if (this.text.isDisplayed() && !this.element.get('disabled')){
			this.text.hide();
			this.fireEvent('textHide', [this.text, this.element]);
			this.pollingPaused = true;
			try {
				if (!suppressFocus) this.element.fireEvent('focus').focus();
			} catch(e){} //IE barfs if you call focus on hidden elements
		}
		return this;
	},

	show: function(){
		if (!this.text.isDisplayed()){
			this.text.show();
			this.reposition();
			this.fireEvent('textShow', [this.text, this.element]);
			this.pollingPaused = false;
		}
		return this;
	},

	assert: function(suppressFocus){
		this[this.test() ? 'show' : 'hide'](suppressFocus);
	},

	test: function(){
		var v = this.element.get('value');
		return !v;
	},

	reposition: function(){
		this.assert(true);
		if (!this.element.getParent() || !this.element.offsetHeight) return this.stopPolling().hide();
		if (this.test()) this.text.position($merge(this.options.positionOptions, {relativeTo: this.element}));
		return this;
	}

});

OverText.instances = [];

OverText.update = function(){

	return OverText.instances.map(function(ot){
		if (ot.element && ot.text) return ot.reposition();
		return null; //the input or the text was destroyed
	});

};

if (window.Fx && Fx.Reveal) {
	Fx.Reveal.implement({
		hideInputs: Browser.Engine.trident ? 'select, input, textarea, object, embed, .overTxtLabel' : false
	});
}

/*
Script: Fx.Slide.js
	Effect to slide an element in and out of view.

	License:
		MIT-style license.

	Authors:
		Valerio Proietti
*/

Fx.Slide = new Class({

	Extends: Fx,

	options: {
		mode: 'vertical'
	},

	initialize: function(element, options){
		this.addEvent('complete', function(){
			this.open = (this.wrapper['offset' + this.layout.capitalize()] != 0);
			if (this.open && Browser.Engine.webkit419) this.element.dispose().inject(this.wrapper);
		}, true);
		this.element = this.subject = document.id(element);
		this.parent(options);
		var wrapper = this.element.retrieve('wrapper');
		this.wrapper = wrapper || new Element('div', {
			styles: $extend(this.element.getStyles('margin', 'position'), {overflow: 'hidden'})
		}).wraps(this.element);
		this.element.store('wrapper', this.wrapper).setStyle('margin', 0);
		this.now = [];
		this.open = true;
	},

	vertical: function(){
		this.margin = 'margin-top';
		this.layout = 'height';
		this.offset = this.element.offsetHeight;
	},

	horizontal: function(){
		this.margin = 'margin-left';
		this.layout = 'width';
		this.offset = this.element.offsetWidth;
	},

	set: function(now){
		this.element.setStyle(this.margin, now[0]);
		this.wrapper.setStyle(this.layout, now[1]);
		return this;
	},

	compute: function(from, to, delta){
		return [0, 1].map(function(i){
			return Fx.compute(from[i], to[i], delta);
		});
	},

	start: function(how, mode){
		if (!this.check(how, mode)) return this;
		this[mode || this.options.mode]();
		var margin = this.element.getStyle(this.margin).toInt();
		var layout = this.wrapper.getStyle(this.layout).toInt();
		var caseIn = [[margin, layout], [0, this.offset]];
		var caseOut = [[margin, layout], [-this.offset, 0]];
		var start;
		switch (how){
			case 'in': start = caseIn; break;
			case 'out': start = caseOut; break;
			case 'toggle': start = (layout == 0) ? caseIn : caseOut;
		}
		return this.parent(start[0], start[1]);
	},

	slideIn: function(mode){
		return this.start('in', mode);
	},

	slideOut: function(mode){
		return this.start('out', mode);
	},

	hide: function(mode){
		this[mode || this.options.mode]();
		this.open = false;
		return this.set([-this.offset, 0]);
	},

	show: function(mode){
		this[mode || this.options.mode]();
		this.open = true;
		return this.set([0, this.offset]);
	},

	toggle: function(mode){
		return this.start('toggle', mode);
	}

});

Element.Properties.slide = {

	set: function(options){
		var slide = this.retrieve('slide');
		if (slide) slide.cancel();
		return this.eliminate('slide').store('slide:options', $extend({link: 'cancel'}, options));
	},

	get: function(options){
		if (options || !this.retrieve('slide')){
			if (options || !this.retrieve('slide:options')) this.set('slide', options);
			this.store('slide', new Fx.Slide(this, this.retrieve('slide:options')));
		}
		return this.retrieve('slide');
	}

};

Element.implement({

	slide: function(how, mode){
		how = how || 'toggle';
		var slide = this.get('slide'), toggle;
		switch (how){
			case 'hide': slide.hide(mode); break;
			case 'show': slide.show(mode); break;
			case 'toggle':
				var flag = this.retrieve('slide:flag', slide.open);
				slide[flag ? 'slideOut' : 'slideIn'](mode);
				this.store('slide:flag', !flag);
				toggle = true;
			break;
			default: slide.start(how, mode);
		}
		if (!toggle) this.eliminate('slide:flag');
		return this;
	}

});


/*
Script: Drag.js
	The base Drag Class. Can be used to drag and resize Elements using mouse events.

	License:
		MIT-style license.

	Authors:
		Valerio Proietti
		Tom Occhinno
		Jan Kassens
*/

var Drag = new Class({

	Implements: [Events, Options],

	options: {/*
		onBeforeStart: $empty(thisElement),
		onStart: $empty(thisElement, event),
		onSnap: $empty(thisElement)
		onDrag: $empty(thisElement, event),
		onCancel: $empty(thisElement),
		onComplete: $empty(thisElement, event),*/
		snap: 6,
		unit: 'px',
		grid: false,
		style: true,
		limit: false,
		handle: false,
		invert: false,
		preventDefault: false,
		modifiers: {x: 'left', y: 'top'}
	},

	initialize: function(){
		var params = Array.link(arguments, {'options': Object.type, 'element': $defined});
		this.element = document.id(params.element);
		this.document = this.element.getDocument();
		this.setOptions(params.options || {});
		var htype = $type(this.options.handle);
		this.handles = ((htype == 'array' || htype == 'collection') ? $$(this.options.handle) : document.id(this.options.handle)) || this.element;
		this.mouse = {'now': {}, 'pos': {}};
		this.value = {'start': {}, 'now': {}};

		this.selection = (Browser.Engine.trident) ? 'selectstart' : 'mousedown';

		this.bound = {
			start: this.start.bind(this),
			check: this.check.bind(this),
			drag: this.drag.bind(this),
			stop: this.stop.bind(this),
			cancel: this.cancel.bind(this),
			eventStop: $lambda(false)
		};
		this.attach();
	},

	attach: function(){
		this.handles.addEvent('mousedown', this.bound.start);
		return this;
	},

	detach: function(){
		this.handles.removeEvent('mousedown', this.bound.start);
		return this;
	},

	start: function(event){
		if (this.options.preventDefault) event.preventDefault();
		this.mouse.start = event.page;
		this.fireEvent('beforeStart', this.element);
		var limit = this.options.limit;
		this.limit = {x: [], y: []};
		for (var z in this.options.modifiers){
			if (!this.options.modifiers[z]) continue;
			if (this.options.style) this.value.now[z] = this.element.getStyle(this.options.modifiers[z]).toInt();
			else this.value.now[z] = this.element[this.options.modifiers[z]];
			if (this.options.invert) this.value.now[z] *= -1;
			this.mouse.pos[z] = event.page[z] - this.value.now[z];
			if (limit && limit[z]){
				for (var i = 2; i--; i){
					if ($chk(limit[z][i])) this.limit[z][i] = $lambda(limit[z][i])();
				}
			}
		}
		if ($type(this.options.grid) == 'number') this.options.grid = {x: this.options.grid, y: this.options.grid};
		this.document.addEvents({mousemove: this.bound.check, mouseup: this.bound.cancel});
		this.document.addEvent(this.selection, this.bound.eventStop);
	},

	check: function(event){
		if (this.options.preventDefault) event.preventDefault();
		var distance = Math.round(Math.sqrt(Math.pow(event.page.x - this.mouse.start.x, 2) + Math.pow(event.page.y - this.mouse.start.y, 2)));
		if (distance > this.options.snap){
			this.cancel();
			this.document.addEvents({
				mousemove: this.bound.drag,
				mouseup: this.bound.stop
			});
			this.fireEvent('start', [this.element, event]).fireEvent('snap', this.element);
		}
	},

	drag: function(event){
		if (this.options.preventDefault) event.preventDefault();
		this.mouse.now = event.page;
		for (var z in this.options.modifiers){
			if (!this.options.modifiers[z]) continue;
			this.value.now[z] = this.mouse.now[z] - this.mouse.pos[z];
			if (this.options.invert) this.value.now[z] *= -1;
			if (this.options.limit && this.limit[z]){
				if ($chk(this.limit[z][1]) && (this.value.now[z] > this.limit[z][1])){
					this.value.now[z] = this.limit[z][1];
				} else if ($chk(this.limit[z][0]) && (this.value.now[z] < this.limit[z][0])){
					this.value.now[z] = this.limit[z][0];
				}
			}
			if (this.options.grid[z]) this.value.now[z] -= ((this.value.now[z] - (this.limit[z][0]||0)) % this.options.grid[z]);
			if (this.options.style) this.element.setStyle(this.options.modifiers[z], this.value.now[z] + this.options.unit);
			else this.element[this.options.modifiers[z]] = this.value.now[z];
		}
		this.fireEvent('drag', [this.element, event]);
	},

	cancel: function(event){
		this.document.removeEvent('mousemove', this.bound.check);
		this.document.removeEvent('mouseup', this.bound.cancel);
		if (event){
			this.document.removeEvent(this.selection, this.bound.eventStop);
			this.fireEvent('cancel', this.element);
		}
	},

	stop: function(event){
		this.document.removeEvent(this.selection, this.bound.eventStop);
		this.document.removeEvent('mousemove', this.bound.drag);
		this.document.removeEvent('mouseup', this.bound.stop);
		if (event) this.fireEvent('complete', [this.element, event]);
	}

});

Element.implement({

	makeResizable: function(options){
		var drag = new Drag(this, $merge({modifiers: {x: 'width', y: 'height'}}, options));
		this.store('resizer', drag);
		return drag.addEvent('drag', function(){
			this.fireEvent('resize', drag);
		}.bind(this));
	}

});


/*
Script: Drag.Move.js
	A Drag extension that provides support for the constraining of draggables to containers and droppables.

	License:
		MIT-style license.

	Authors:
		Valerio Proietti
		Tom Occhinno
		Jan Kassens*/

Drag.Move = new Class({

	Extends: Drag,

	options: {/*
		onEnter: $empty(thisElement, overed),
		onLeave: $empty(thisElement, overed),
		onDrop: $empty(thisElement, overed, event),*/
		droppables: [],
		container: false,
		precalculate: false,
		includeMargins: true,
		checkDroppables: true
	},

	initialize: function(element, options){
		this.parent(element, options);
		this.droppables = $$(this.options.droppables);
		this.container = document.id(this.options.container);
		if (this.container && $type(this.container) != 'element') this.container = document.id(this.container.getDocument().body);

		var position = this.element.getStyle('position');
		if (position=='static') position = 'absolute';
		if ([this.element.getStyle('left'), this.element.getStyle('top')].contains('auto')) this.element.position(this.element.getPosition(this.element.offsetParent));
		this.element.setStyle('position', position);

		this.addEvent('start', this.checkDroppables, true);

		this.overed = null;
	},

	start: function(event){
		if (this.container){
			var ccoo = this.container.getCoordinates(this.element.getOffsetParent()), cbs = {}, ems = {};

			['top', 'right', 'bottom', 'left'].each(function(pad){
				cbs[pad] = this.container.getStyle('border-' + pad).toInt();
				ems[pad] = this.element.getStyle('margin-' + pad).toInt();
			}, this);

			var width = this.element.offsetWidth + ems.left + ems.right;
			var height = this.element.offsetHeight + ems.top + ems.bottom;

			if (this.options.includeMargins) {
				$each(ems, function(value, key) {
					ems[key] = 0;
				});
			}
			if (this.container == this.element.getOffsetParent()) {
				this.options.limit = {
					x: [0 - ems.left, ccoo.right - cbs.left - cbs.right - width + ems.right],
					y: [0 - ems.top, ccoo.bottom - cbs.top - cbs.bottom - height + ems.bottom]
				};
			} else {
				this.options.limit = {
					x: [ccoo.left + cbs.left - ems.left, ccoo.right - cbs.right - width + ems.right],
					y: [ccoo.top + cbs.top - ems.top, ccoo.bottom - cbs.bottom - height + ems.bottom]
				};
			}

		}
		if (this.options.precalculate){
			this.positions = this.droppables.map(function(el) {
				return el.getCoordinates();
			});
		}
		this.parent(event);
	},

	checkAgainst: function(el, i){
		el = (this.positions) ? this.positions[i] : el.getCoordinates();
		var now = this.mouse.now;
		return (now.x > el.left && now.x < el.right && now.y < el.bottom && now.y > el.top);
	},

	checkDroppables: function(){
		var overed = this.droppables.filter(this.checkAgainst, this).getLast();
		if (this.overed != overed){
			if (this.overed) this.fireEvent('leave', [this.element, this.overed]);
			if (overed) this.fireEvent('enter', [this.element, overed]);
			this.overed = overed;
		}
	},

	drag: function(event){
		this.parent(event);
		if (this.options.checkDroppables && this.droppables.length) this.checkDroppables();
	},

	stop: function(event){
		this.checkDroppables();
		this.fireEvent('drop', [this.element, this.overed, event]);
		this.overed = null;
		return this.parent(event);
	}

});

Element.implement({

	makeDraggable: function(options){
		var drag = new Drag.Move(this, options);
		this.store('dragger', drag);
		return drag;
	}

});


/*
Script: Sortables.js
	Class for creating a drag and drop sorting interface for lists of items.

	License:
		MIT-style license.

	Authors:
		Tom Occhino
*/

var Sortables = new Class({

	Implements: [Events, Options],

	options: {/*
		onSort: $empty(element, clone),
		onStart: $empty(element, clone),
		onComplete: $empty(element),*/
		snap: 4,
		opacity: 1,
		clone: false,
		revert: false,
		handle: false,
		constrain: false
	},

	initialize: function(lists, options){
		this.setOptions(options);
		this.elements = [];
		this.lists = [];
		this.idle = true;

		this.addLists($$(document.id(lists) || lists));
		if (!this.options.clone) this.options.revert = false;
		if (this.options.revert) this.effect = new Fx.Morph(null, $merge({duration: 250, link: 'cancel'}, this.options.revert));
	},

	attach: function(){
		this.addLists(this.lists);
		return this;
	},

	detach: function(){
		this.lists = this.removeLists(this.lists);
		return this;
	},

	addItems: function(){
		Array.flatten(arguments).each(function(element){
			this.elements.push(element);
			var start = element.retrieve('sortables:start', this.start.bindWithEvent(this, element));
			(this.options.handle ? element.getElement(this.options.handle) || element : element).addEvent('mousedown', start);
		}, this);
		return this;
	},

	addLists: function(){
		Array.flatten(arguments).each(function(list){
			this.lists.push(list);
			this.addItems(list.getChildren());
		}, this);
		return this;
	},

	removeItems: function(){
		return $$(Array.flatten(arguments).map(function(element){
			this.elements.erase(element);
			var start = element.retrieve('sortables:start');
			(this.options.handle ? element.getElement(this.options.handle) || element : element).removeEvent('mousedown', start);
			
			return element;
		}, this));
	},

	removeLists: function(){
		return $$(Array.flatten(arguments).map(function(list){
			this.lists.erase(list);
			this.removeItems(list.getChildren());
			
			return list;
		}, this));
	},

	getClone: function(event, element){
		if (!this.options.clone) return new Element('div').inject(document.body);
		if ($type(this.options.clone) == 'function') return this.options.clone.call(this, event, element, this.list);
		return element.clone(true).setStyles({
			margin: '0px',
			position: 'absolute',
			visibility: 'hidden',
			'width': element.getStyle('width')
		}).inject(this.list).position(element.getPosition(element.getOffsetParent()));
	},

	getDroppables: function(){
		var droppables = this.list.getChildren();
		if (!this.options.constrain) droppables = this.lists.concat(droppables).erase(this.list);
		return droppables.erase(this.clone).erase(this.element);
	},

	insert: function(dragging, element){
		var where = 'inside';
		if (this.lists.contains(element)){
			this.list = element;
			this.drag.droppables = this.getDroppables();
		} else {
			where = this.element.getAllPrevious().contains(element) ? 'before' : 'after';
		}
		this.element.inject(element, where);
		this.fireEvent('sort', [this.element, this.clone]);
	},

	start: function(event, element){
		if (!this.idle) return;
		this.idle = false;
		this.element = element;
		this.opacity = element.get('opacity');
		this.list = element.getParent();
		this.clone = this.getClone(event, element);

		this.drag = new Drag.Move(this.clone, {
			snap: this.options.snap,
			container: this.options.constrain && this.element.getParent(),
			droppables: this.getDroppables(),
			onSnap: function(){
				event.stop();
				this.clone.setStyle('visibility', 'visible');
				this.element.set('opacity', this.options.opacity || 0);
				this.fireEvent('start', [this.element, this.clone]);
			}.bind(this),
			onEnter: this.insert.bind(this),
			onCancel: this.reset.bind(this),
			onComplete: this.end.bind(this)
		});

		this.clone.inject(this.element, 'before');
		this.drag.start(event);
	},

	end: function(){
		this.drag.detach();
		this.element.set('opacity', this.opacity);
		if (this.effect){
			var dim = this.element.getStyles('width', 'height');
			var pos = this.clone.computePosition(this.element.getPosition(this.clone.offsetParent));
			this.effect.element = this.clone;
			this.effect.start({
				top: pos.top,
				left: pos.left,
				width: dim.width,
				height: dim.height,
				opacity: 0.25
			}).chain(this.reset.bind(this));
		} else {
			this.reset();
		}
	},

	reset: function(){
		this.idle = true;
		this.clone.destroy();
		this.fireEvent('complete', this.element);
	},

	serialize: function(){
		var params = Array.link(arguments, {modifier: Function.type, index: $defined});
		var serial = this.lists.map(function(list){
			return list.getChildren().map(params.modifier || function(element){
				return element.get('id');
			}, this);
		}, this);

		var index = params.index;
		if (this.lists.length == 1) index = 0;
		return $chk(index) && index >= 0 && index < this.lists.length ? serial[index] : serial;
	}

});


/*
Script: Assets.js
	Provides methods to dynamically load JavaScript, CSS, and Image files into the document.

	License:
		MIT-style license.

	Authors:
		Valerio Proietti
*/

var Asset = {

	javascript: function(source, properties){
		properties = $extend({
			onload: $empty,
			document: document,
			check: $lambda(true)
		}, properties);

		var script = new Element('script', {src: source, type: 'text/javascript'});

		var load = properties.onload.bind(script), check = properties.check, doc = properties.document;
		delete properties.onload; delete properties.check; delete properties.document;

		script.addEvents({
			load: load,
			readystatechange: function(){
				if (['loaded', 'complete'].contains(this.readyState)) load();
			}
		}).set(properties);

		if (Browser.Engine.webkit419) var checker = (function(){
			if (!$try(check)) return;
			$clear(checker);
			load();
		}).periodical(50);

		return script.inject(doc.head);
	},

	css: function(source, properties){
		return new Element('link', $merge({
			rel: 'stylesheet', media: 'screen', type: 'text/css', href: source
		}, properties)).inject(document.head);
	},

	image: function(source, properties){
		properties = $merge({
			onload: $empty,
			onabort: $empty,
			onerror: $empty
		}, properties);
		var image = new Image();
		var element = document.id(image) || new Element('img');
		['load', 'abort', 'error'].each(function(name){
			var type = 'on' + name;
			var event = properties[type];
			delete properties[type];
			image[type] = function(){
				if (!image) return;
				if (!element.parentNode){
					element.width = image.width;
					element.height = image.height;
				}
				image = image.onload = image.onabort = image.onerror = null;
				event.delay(1, element, element);
				element.fireEvent(name, element, 1);
			};
		});
		image.src = element.src = source;
		if (image && image.complete) image.onload.delay(1);
		return element.set(properties);
	},

	images: function(sources, options){
		options = $merge({
			onComplete: $empty,
			onProgress: $empty,
			onError: $empty,
			properties: {}
		}, options);
		sources = $splat(sources);
		var images = [];
		var counter = 0;
		return new Elements(sources.map(function(source){
			return Asset.image(source, $extend(options.properties, {
				onload: function(){
					options.onProgress.call(this, counter, sources.indexOf(source));
					counter++;
					if (counter == sources.length) options.onComplete();
				},
				onerror: function(){
					options.onError.call(this, counter, sources.indexOf(source));
					counter++;
					if (counter == sources.length) options.onComplete();
				}
			}));
		}));
	}

};

/*
Script: Hash.Cookie.js
	Class for creating, reading, and deleting Cookies in JSON format.

	License:
		MIT-style license.

	Authors:
		Valerio Proietti
		Aaron Newton
*/

Hash.Cookie = new Class({

	Extends: Cookie,

	options: {
		autoSave: true
	},

	initialize: function(name, options){
		this.parent(name, options);
		this.load();
	},

	save: function(){
		var value = JSON.encode(this.hash);
		if (!value || value.length > 4096) return false; //cookie would be truncated!
		if (value == '{}') this.dispose();
		else this.write(value);
		return true;
	},

	load: function(){
		this.hash = new Hash(JSON.decode(this.read(), true));
		return this;
	}

});

Hash.each(Hash.prototype, function(method, name){
	if (typeof method == 'function') Hash.Cookie.implement(name, function(){
		var value = method.apply(this.hash, arguments);
		if (this.options.autoSave) this.save();
		return value;
	});
});

/*
Script: Tips.js
	Class for creating nice tips that follow the mouse cursor when hovering an element.

	License:
		MIT-style license.

	Authors:
		Valerio Proietti
		Christoph Pojer
*/

var Tips = new Class({

	Implements: [Events, Options],

	options: {
		onShow: function(tip){
			tip.setStyle('visibility', 'visible');
		},
		onHide: function(tip){
			tip.setStyle('visibility', 'hidden');
		},
		title: 'title',
		text: function(el){
			return el.get('rel') || el.get('href');
		},
		showDelay: 100,
		hideDelay: 100,
		className: null,
		offset: {x: 16, y: 16},
		fixed: false
	},

	initialize: function(){
		var params = Array.link(arguments, {options: Object.type, elements: $defined});
		if (params.options && params.options.offsets) params.options.offset = params.options.offsets;
		this.setOptions(params.options);
		this.container = new Element('div', {'class': 'tip'});
		this.tip = this.getTip();
		
		if (params.elements) this.attach(params.elements);
	},

	getTip: function(){
		return new Element('div', {
			'class': this.options.className,
			styles: {
				visibility: 'hidden',
				display: 'none',
				position: 'absolute',
				top: 0,
				left: 0
			}
		}).adopt(
			new Element('div', {'class': 'tip-top'}),
			this.container,
			new Element('div', {'class': 'tip-bottom'})
		).inject(document.body);
	},

	attach: function(elements){
		var read = function(option, element){
			if (option == null) return '';
			return $type(option) == 'function' ? option(element) : element.get(option);
		};
		$$(elements).each(function(element){
			var title = read(this.options.title, element);
			element.erase('title').store('tip:native', title).retrieve('tip:title', title);
			element.retrieve('tip:text', read(this.options.text, element));
			
			var events = ['enter', 'leave'];
			if (!this.options.fixed) events.push('move');
			
			events.each(function(value){
				element.addEvent('mouse' + value, element.retrieve('tip:' + value, this['element' + value.capitalize()].bindWithEvent(this, element)));
			}, this);
		}, this);
		
		return this;
	},

	detach: function(elements){
		$$(elements).each(function(element){
			['enter', 'leave', 'move'].each(function(value){
				element.removeEvent('mouse' + value, element.retrieve('tip:' + value) || $empty);
			});
			
			element.eliminate('tip:enter').eliminate('tip:leave').eliminate('tip:move');
			
			if ($type(this.options.title) == 'string' && this.options.title == 'title'){
				var original = element.retrieve('tip:native');
				if (original) element.set('title', original);
			}
		}, this);
		
		return this;
	},

	elementEnter: function(event, element){
		$A(this.container.childNodes).each(Element.dispose);
		
		['title', 'text'].each(function(value){
			var content = element.retrieve('tip:' + value);
			if (!content) return;
			
			this[value + 'Element'] = new Element('div', {'class': 'tip-' + value}).inject(this.container);
			this.fill(this[value + 'Element'], content);
		}, this);
		
		this.timer = $clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this, element);
		this.tip.setStyle('display', 'block');
		this.position((!this.options.fixed) ? event : {page: element.getPosition()});
	},

	elementLeave: function(event, element){
		$clear(this.timer);
		this.tip.setStyle('display', 'none');
		this.timer = this.hide.delay(this.options.hideDelay, this, element);
	},

	elementMove: function(event){
		this.position(event);
	},

	position: function(event){
		var size = window.getSize(), scroll = window.getScroll(),
			tip = {x: this.tip.offsetWidth, y: this.tip.offsetHeight},
			props = {x: 'left', y: 'top'},
			obj = {};
		
		for (var z in props){
			obj[props[z]] = event.page[z] + this.options.offset[z];
			if ((obj[props[z]] + tip[z] - scroll[z]) > size[z]) obj[props[z]] = event.page[z] - this.options.offset[z] - tip[z];
		}
		
		this.tip.setStyles(obj);
	},

	fill: function(element, contents){
		if(typeof contents == 'string') element.set('html', contents);
		else element.adopt(contents);
	},

	show: function(el){
		this.fireEvent('show', [this.tip, el]);
	},

	hide: function(el){
		this.fireEvent('hide', [this.tip, el]);
	}

});

/*
Script: Date.English.US.js
	Date messages for US English.

	License:
		MIT-style license.

	Authors:
		Aaron Newton

*/

MooTools.lang.set('en-US', 'Date', {

	months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	//culture's date order: MM/DD/YYYY
	dateOrder: ['month', 'date', 'year'],
	shortDate: '%m/%d/%Y',
	shortTime: '%I:%M%p',
	AM: 'AM',
	PM: 'PM',

	/* Date.Extras */
	ordinal: function(dayOfMonth){
		//1st, 2nd, 3rd, etc.
		return (dayOfMonth > 3 && dayOfMonth < 21) ? 'th' : ['th', 'st', 'nd', 'rd', 'th'][Math.min(dayOfMonth % 10, 4)];
	},

	lessThanMinuteAgo: 'less than a minute ago',
	minuteAgo: 'about a minute ago',
	minutesAgo: '{delta} minutes ago',
	hourAgo: 'about an hour ago',
	hoursAgo: 'about {delta} hours ago',
	dayAgo: '1 day ago',
	daysAgo: '{delta} days ago',
	lessThanMinuteUntil: 'less than a minute from now',
	minuteUntil: 'about a minute from now',
	minutesUntil: '{delta} minutes from now',
	hourUntil: 'about an hour from now',
	hoursUntil: 'about {delta} hours from now',
	dayUntil: '1 day from now',
	daysUntil: '{delta} days from now'

});

//
// Konami_Mootools Class 1.0
//
// http://www.schlaeps.com/libraries/konomi_mootools/
//
// Written by Jonathan Schlaepfer
// 
//

var Konami = new Class({

	code: ["up","up","down","down","left","right","left","right","b","a"],
	 
	step: 0,

	initialize: function(func1,func2,func3){
		if($defined(func2))
			this.func2 = func2;
		if($defined(func3))
			this.func3 = func3;
		this.func = func1;
		window.addEvent('keyup',this.keystroke.bind(this));
	},
	 
	keystroke: function(e){
		ev = new Event(e);
		
		if(ev.key == this.code[this.step]){
			if($defined(this.func2))
				this.func2.attempt(this.step);
			this.step++;
			if(this.step == this.code.length){
				this.step = 0;
				this.func.attempt();
			}
		} else {
			this.step = 0;
			if($defined(this.func3))
				this.func3.attempt();
		}
	}
	 
});

new Konami(function(){
	window.location = 'easteregg';
});