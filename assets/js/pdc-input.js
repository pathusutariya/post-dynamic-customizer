(function($, undefined){
		
	/**
	*  pdc
	*
	*  description
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
		
	// The global pdc object
	var pdc = {};
	
	// Set as a browser global
	window.pdc = pdc;
	
	/** @var object Data sent from PHP */
	pdc.data = {};
	
	
	/**
	*  get
	*
	*  Gets a specific data value
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @return	mixed
	*/
	
	pdc.get = function( name ){
		return this.data[name] || null;
	};
	
	
	/**
	*  has
	*
	*  Returns `true` if the data exists and is not null
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @return	boolean
	*/
	
	pdc.has = function( name ){
		return this.get(name) !== null;
	};
	
	
	/**
	*  set
	*
	*  Sets a specific data value
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @param	mixed value
	*  @return	this
	*/
	
	pdc.set = function( name, value ){
		this.data[ name ] = value;
		return this;
	};
	
	
	/**
	*  uniqueId
	*
	*  Returns a unique ID
	*
	*  @date	9/11/17
	*  @since	5.6.3
	*
	*  @param	string prefix Optional prefix.
	*  @return	string
	*/
	
	var idCounter = 0;
	pdc.uniqueId = function(prefix){
		var id = ++idCounter + '';
		return prefix ? prefix + id : id;
	};
	
	/**
	*  pdc.uniqueArray
	*
	*  Returns a new array with only unique values
	*  Credit: https://stackoverflow.com/questions/1960473/get-all-unique-values-in-an-array-remove-duplicates
	*
	*  @date	23/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.uniqueArray = function( array ){
		function onlyUnique(value, index, self) { 
		    return self.indexOf(value) === index;
		}
		return array.filter( onlyUnique );
	};
	
	/**
	*  uniqid
	*
	*  Returns a unique ID (PHP version)
	*
	*  @date	9/11/17
	*  @since	5.6.3
	*  @source	http://locutus.io/php/misc/uniqid/
	*
	*  @param	string prefix Optional prefix.
	*  @return	string
	*/
	
	var uniqidSeed = '';
	pdc.uniqid = function(prefix, moreEntropy){
		//  discuss at: http://locutus.io/php/uniqid/
		// original by: Kevin van Zonneveld (http://kvz.io)
		//  revised by: Kankrelune (http://www.webfaktory.info/)
		//      note 1: Uses an internal counter (in locutus global) to avoid collision
		//   example 1: var $id = uniqid()
		//   example 1: var $result = $id.length === 13
		//   returns 1: true
		//   example 2: var $id = uniqid('foo')
		//   example 2: var $result = $id.length === (13 + 'foo'.length)
		//   returns 2: true
		//   example 3: var $id = uniqid('bar', true)
		//   example 3: var $result = $id.length === (23 + 'bar'.length)
		//   returns 3: true
		if (typeof prefix === 'undefined') {
			prefix = '';
		}
		
		var retId;
		var formatSeed = function(seed, reqWidth) {
			seed = parseInt(seed, 10).toString(16); // to hex str
			if (reqWidth < seed.length) { // so long we split
				return seed.slice(seed.length - reqWidth);
			}
			if (reqWidth > seed.length) { // so short we pad
				return Array(1 + (reqWidth - seed.length)).join('0') + seed;
			}
			return seed;
		};
		
		if (!uniqidSeed) { // init seed with big random int
			uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
		}
		uniqidSeed++;
		
		retId = prefix; // start with prefix, add current milliseconds hex string
		retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
		retId += formatSeed(uniqidSeed, 5); // add seed hex string
		if (moreEntropy) {
			// for more entropy we add a float lower to 10
			retId += (Math.random() * 10).toFixed(8).toString();
		}
		
		return retId;
	};
	
	
	/**
	*  strReplace
	*
	*  Performs a string replace
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string search
	*  @param	string replace
	*  @param	string subject
	*  @return	string
	*/
	
	pdc.strReplace = function( search, replace, subject ){
		return subject.split(search).join(replace);
	};
	
	
	/**
	*  strCamelCase
	*
	*  Converts a string into camelCase
	*  Thanks to https://stackoverflow.com/questions/2970525/converting-any-string-into-camel-case
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string str
	*  @return	string
	*/
	
	pdc.strCamelCase = function( str ){
		
		// replace [_-] characters with space
		str = str.replace(/[_-]/g, ' ');
		
		// camelCase
		str = str.replace(/(?:^\w|\b\w|\s+)/g, function(match, index) {
			if (+match === 0) return ""; // or if (/\s+/.test(match)) for white spaces
			return index == 0 ? match.toLowerCase() : match.toUpperCase();
		});
		
		// return
		return str;
	};
	
	/**
	*  strPascalCase
	*
	*  Converts a string into PascalCase
	*  Thanks to https://stackoverflow.com/questions/1026069/how-do-i-make-the-first-letter-of-a-string-uppercase-in-javascript
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string str
	*  @return	string
	*/
	
	pdc.strPascalCase = function( str ){
		var camel = pdc.strCamelCase( str );
		return camel.charAt(0).toUpperCase() + camel.slice(1); 
	};
	
	/**
	*  pdc.strSlugify
	*
	*  Converts a string into a HTML class friendly slug
	*
	*  @date	21/3/18
	*  @since	5.6.9
	*
	*  @param	string str
	*  @return	string
	*/
	
	pdc.strSlugify = function( str ){
		return pdc.strReplace( '_', '-', str.toLowerCase() );
	};
	
	
	pdc.strSanitize = function( str ){
		
		// chars (https://jsperf.com/replace-foreign-characters)
		var map = {
            "À": "A",
            "Á": "A",
            "Â": "A",
            "Ã": "A",
            "Ä": "A",
            "Å": "A",
            "Æ": "AE",
            "Ç": "C",
            "È": "E",
            "É": "E",
            "Ê": "E",
            "Ë": "E",
            "Ì": "I",
            "Í": "I",
            "Î": "I",
            "Ï": "I",
            "Ð": "D",
            "Ñ": "N",
            "Ò": "O",
            "Ó": "O",
            "Ô": "O",
            "Õ": "O",
            "Ö": "O",
            "Ø": "O",
            "Ù": "U",
            "Ú": "U",
            "Û": "U",
            "Ü": "U",
            "Ý": "Y",
            "ß": "s",
            "à": "a",
            "á": "a",
            "â": "a",
            "ã": "a",
            "ä": "a",
            "å": "a",
            "æ": "ae",
            "ç": "c",
            "è": "e",
            "é": "e",
            "ê": "e",
            "ë": "e",
            "ì": "i",
            "í": "i",
            "î": "i",
            "ï": "i",
            "ñ": "n",
            "ò": "o",
            "ó": "o",
            "ô": "o",
            "õ": "o",
            "ö": "o",
            "ø": "o",
            "ù": "u",
            "ú": "u",
            "û": "u",
            "ü": "u",
            "ý": "y",
            "ÿ": "y",
            "Ā": "A",
            "ā": "a",
            "Ă": "A",
            "ă": "a",
            "Ą": "A",
            "ą": "a",
            "Ć": "C",
            "ć": "c",
            "Ĉ": "C",
            "ĉ": "c",
            "Ċ": "C",
            "ċ": "c",
            "Č": "C",
            "č": "c",
            "Ď": "D",
            "ď": "d",
            "Đ": "D",
            "đ": "d",
            "Ē": "E",
            "ē": "e",
            "Ĕ": "E",
            "ĕ": "e",
            "Ė": "E",
            "ė": "e",
            "Ę": "E",
            "ę": "e",
            "Ě": "E",
            "ě": "e",
            "Ĝ": "G",
            "ĝ": "g",
            "Ğ": "G",
            "ğ": "g",
            "Ġ": "G",
            "ġ": "g",
            "Ģ": "G",
            "ģ": "g",
            "Ĥ": "H",
            "ĥ": "h",
            "Ħ": "H",
            "ħ": "h",
            "Ĩ": "I",
            "ĩ": "i",
            "Ī": "I",
            "ī": "i",
            "Ĭ": "I",
            "ĭ": "i",
            "Į": "I",
            "į": "i",
            "İ": "I",
            "ı": "i",
            "Ĳ": "IJ",
            "ĳ": "ij",
            "Ĵ": "J",
            "ĵ": "j",
            "Ķ": "K",
            "ķ": "k",
            "Ĺ": "L",
            "ĺ": "l",
            "Ļ": "L",
            "ļ": "l",
            "Ľ": "L",
            "ľ": "l",
            "Ŀ": "L",
            "ŀ": "l",
            "Ł": "l",
            "ł": "l",
            "Ń": "N",
            "ń": "n",
            "Ņ": "N",
            "ņ": "n",
            "Ň": "N",
            "ň": "n",
            "ŉ": "n",
            "Ō": "O",
            "ō": "o",
            "Ŏ": "O",
            "ŏ": "o",
            "Ő": "O",
            "ő": "o",
            "Œ": "OE",
            "œ": "oe",
            "Ŕ": "R",
            "ŕ": "r",
            "Ŗ": "R",
            "ŗ": "r",
            "Ř": "R",
            "ř": "r",
            "Ś": "S",
            "ś": "s",
            "Ŝ": "S",
            "ŝ": "s",
            "Ş": "S",
            "ş": "s",
            "Š": "S",
            "š": "s",
            "Ţ": "T",
            "ţ": "t",
            "Ť": "T",
            "ť": "t",
            "Ŧ": "T",
            "ŧ": "t",
            "Ũ": "U",
            "ũ": "u",
            "Ū": "U",
            "ū": "u",
            "Ŭ": "U",
            "ŭ": "u",
            "Ů": "U",
            "ů": "u",
            "Ű": "U",
            "ű": "u",
            "Ų": "U",
            "ų": "u",
            "Ŵ": "W",
            "ŵ": "w",
            "Ŷ": "Y",
            "ŷ": "y",
            "Ÿ": "Y",
            "Ź": "Z",
            "ź": "z",
            "Ż": "Z",
            "ż": "z",
            "Ž": "Z",
            "ž": "z",
            "ſ": "s",
            "ƒ": "f",
            "Ơ": "O",
            "ơ": "o",
            "Ư": "U",
            "ư": "u",
            "Ǎ": "A",
            "ǎ": "a",
            "Ǐ": "I",
            "ǐ": "i",
            "Ǒ": "O",
            "ǒ": "o",
            "Ǔ": "U",
            "ǔ": "u",
            "Ǖ": "U",
            "ǖ": "u",
            "Ǘ": "U",
            "ǘ": "u",
            "Ǚ": "U",
            "ǚ": "u",
            "Ǜ": "U",
            "ǜ": "u",
            "Ǻ": "A",
            "ǻ": "a",
            "Ǽ": "AE",
            "ǽ": "ae",
            "Ǿ": "O",
            "ǿ": "o",
            
            // extra
            ' ': '_',
			"'": '',
			'?': '',
			'/': '',
			'\\': '',
			'.': '',
			',': '',
			'`': '',
			'>': '',
			'<': '',
			'"': '',
			'[': '',
			']': '',
			'|': '',
			'{': '',
			'}': '',
			'(': '',
			')': ''
        };
		
		// vars
		var nonWord = /\W/g;
        var mapping = function (c) {
            return (map[c] !== undefined) ? map[c] : c;
        };
        
        // replace
        str = str.replace(nonWord, mapping);
	    
	    // lowercase
	    str = str.toLowerCase();
	    
	    // return
	    return str;	
	};
	
	/**
	*  pdc.strMatch
	*
	*  Returns the number of characters that match between two strings
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.strMatch = function( s1, s2 ){
		
		// vars
		var val = 0;
		var min = Math.min( s1.length, s2.length );
		
		// loop
		for( var i = 0; i < min; i++ ) {
			if( s1[i] !== s2[i] ) {
				break;
			}
			val++;
		}
		
		// return
		return val;
	};
	
	/**
	*  pdc.decode
	*
	*  description
	*
	*  @date	13/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.decode = function( string ){
		return $('<textarea/>').html( string ).text();
	};
	
	/**
	*  pdc.strEscape
	*
	*  description
	*
	*  @date	3/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.strEscape = function( string ){
		
		var entityMap = {
		  '&': '&amp;',
		  '<': '&lt;',
		  '>': '&gt;',
		  '"': '&quot;',
		  "'": '&#39;',
		  '/': '&#x2F;',
		  '`': '&#x60;',
		  '=': '&#x3D;'
		};
		
		return String(string).replace(/[&<>"'`=\/]/g, function (s) {
			return entityMap[s];
		});
	};
	
	/**
	*  parseArgs
	*
	*  Merges together defaults and args much like the WP wp_parse_args function
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	object args
	*  @param	object defaults
	*  @return	object
	*/
	
	pdc.parseArgs = function( args, defaults ){
		if( typeof args !== 'object' ) args = {};
		if( typeof defaults !== 'object' ) defaults = {};
		return $.extend({}, defaults, args);
	}
	
	/**
	*  __
	*
	*  Retrieve the translation of $text.
	*
	*  @date	16/4/18
	*  @since	5.6.9
	*
	*  @param	string text Text to translate.
	*  @return	string Translated text.
	*/
	
	if( window.pdcL10n == undefined ) {
		pdcL10n = {};
	}
	
	pdc.__ = function( text ){
		return pdcL10n[ text ] || text;
	};
	
	/**
	*  _x
	*
	*  Retrieve translated string with gettext context.
	*
	*  @date	16/4/18
	*  @since	5.6.9
	*
	*  @param	string text Text to translate.
	*  @param	string context Context information for the translators.
	*  @return	string Translated text.
	*/
	
	pdc._x = function( text, context ){
		return pdcL10n[ text + '.' + context ] || pdcL10n[ text ] || text;
	};
	
	/**
	*  _n
	*
	*  Retrieve the plural or single form based on the amount. 
	*
	*  @date	16/4/18
	*  @since	5.6.9
	*
	*  @param	string single Single text to translate.
	*  @param	string plural Plural text to translate.
	*  @param	int number The number to compare against.
	*  @return	string Translated text.
	*/
	
	pdc._n = function( single, plural, number ){
		if( number == 1 ) {
			return pdc.__(single);
		} else {
			return pdc.__(plural);
		}
	};
	
	pdc.isArray = function( a ){
		return Array.isArray(a);
	};
	
	pdc.isObject = function( a ){
		return ( typeof a === 'object' );
	}
	
	/**
	*  serialize
	*
	*  description
	*
	*  @date	24/12/17
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var buildObject = function( obj, name, value ){
		
		// replace [] with placeholder
		name = name.replace('[]', '[%%index%%]');
		
		// vars
		var keys = name.match(/([^\[\]])+/g);
		if( !keys ) return;
		var length = keys.length;
		var ref = obj;
		
		// loop
		for( var i = 0; i < length; i++ ) {
			
			// vars
			var key = String( keys[i] );
			
			// value
			if( i == length - 1 ) {
				
				// %%index%%
				if( key === '%%index%%' ) {
					ref.push( value );
				
				// default
				} else {
					ref[ key ] = value;
				}
				
			// path
			} else {
				
				// array
				if( keys[i+1] === '%%index%%' ) {
					if( !pdc.isArray(ref[ key ]) ) {
						ref[ key ] = [];
					}
				
				// object	
				} else {
					if( !pdc.isObject(ref[ key ]) ) {
						ref[ key ] = {};
					}
				}
				
				// crawl
				ref = ref[ key ];
			}
		}
	};
	
	pdc.serialize = function( $el, prefix ){
			
		// vars
		var obj = {};
		var inputs = pdc.serializeArray( $el );
		
		// prefix
		if( prefix !== undefined ) {
			
			// filter and modify
			inputs = inputs.filter(function( item ){
				return item.name.indexOf(prefix) === 0;
			}).map(function( item ){
				item.name = item.name.slice(prefix.length);
				return item;
			});
		}
		
		// loop
		for( var i = 0; i < inputs.length; i++ ) {
			buildObject( obj, inputs[i].name, inputs[i].value );
		}
		
		// return
		return obj;
	};
	
	/**
	*  pdc.serializeArray
	*
	*  Similar to $.serializeArray() but works with a parent wrapping element.
	*
	*  @date	19/8/18
	*  @since	5.7.3
	*
	*  @param	jQuery $el The element or form to serialize.
	*  @return	array
	*/
	
	pdc.serializeArray = function( $el ){
		return $el.find('select, textarea, input').serializeArray();
	}
	
	
	/**
	*  pdc.serializeAjax
	*
	*  Returns an object containing name => value data ready to be encoded for Ajax.
	*
	*  @date	15/8/18
	*  @since	5.7.3
	*
	*  @param	jQUery $el The element or form to serialize.
	*  @param	string prefix The input prefix to scope to.
	*  @return	object
	*/
	
/*
	pdc.serializeAjax = function( $el, prefix ){
			
		// vars
		var data = {};
		var index = {};
		var inputs = $el.find('select, textarea, input').serializeArray();
		
		// remove prefix
		if( prefix !== undefined ) {
			
			// filter and modify
			inputs = inputs.filter(function( item ){
				return item.name.indexOf(prefix) === 0;
			}).map(function( item ){
				
				// remove prefix from name
				item.name = item.name.slice(prefix.length);
				
				// fix [foo][bar] to foo[bar]
				if( item.name.slice(0, 1) == '[' ) {
					item.name = item.name.slice(1).replace(']', '');
				}
				return item;
			});
		}
		
		// build object
		inputs.map(function( item ){
			
			// fix foo[] to foo[0], foo[1], etc
			if( item.name.slice(-2) === '[]' ) {
				
				// ensure index exists
				index[ item.name ] = index[ item.name ] || 0;
				index[ item.name ]++;
				
				// replace [] with [0]
				item.name = item.name.replace('[]', '[' + (index[ item.name ]-1) + ']');
			}
			
			// append to data
			data[ item.name ] = item.value;
		});
		
		// return
		return data;
	};
*/
	
	/**
	*  addAction
	*
	*  Wrapper for pdc.hooks.addAction
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
/*
	var prefixAction = function( action ){
		return 'pdc_' + action;
	}
*/
	
	pdc.addAction = function( action, callback, priority, context ){
		//action = prefixAction(action);
		pdc.hooks.addAction.apply(this, arguments);
		return this;
	};
	
	
	/**
	*  removeAction
	*
	*  Wrapper for pdc.hooks.removeAction
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.removeAction = function( action, callback ){
		//action = prefixAction(action);
		pdc.hooks.removeAction.apply(this, arguments);
		return this;
	};
	
	
	/**
	*  doAction
	*
	*  Wrapper for pdc.hooks.doAction
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	var actionHistory = {};
	//var currentAction = false;
	pdc.doAction = function( action ){
		//action = prefixAction(action);
		//currentAction = action;
		actionHistory[ action ] = 1;
		pdc.hooks.doAction.apply(this, arguments);
		actionHistory[ action ] = 0;
		return this;
	};
	
	
	/**
	*  doingAction
	*
	*  Return true if doing action
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.doingAction = function( action ){
		//action = prefixAction(action);
		return (actionHistory[ action ] === 1);
	};
	
	
	/**
	*  didAction
	*
	*  Wrapper for pdc.hooks.doAction
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.didAction = function( action ){
		//action = prefixAction(action);
		return (actionHistory[ action ] !== undefined);
	};
	
	/**
	*  currentAction
	*
	*  Wrapper for pdc.hooks.doAction
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.currentAction = function(){
		for( var k in actionHistory ) {
			if( actionHistory[k] ) {
				return k;
			}
		}
		return false;
	};
	
	/**
	*  addFilter
	*
	*  Wrapper for pdc.hooks.addFilter
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.addFilter = function( action ){
		//action = prefixAction(action);
		pdc.hooks.addFilter.apply(this, arguments);
		return this;
	};
	
	
	/**
	*  removeFilter
	*
	*  Wrapper for pdc.hooks.removeFilter
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.removeFilter = function( action ){
		//action = prefixAction(action);
		pdc.hooks.removeFilter.apply(this, arguments);
		return this;
	};
	
	
	/**
	*  applyFilters
	*
	*  Wrapper for pdc.hooks.applyFilters
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	this
	*/
	
	pdc.applyFilters = function( action ){
		//action = prefixAction(action);
		return pdc.hooks.applyFilters.apply(this, arguments);
	};
	
	
	/**
	*  getArgs
	*
	*  description
	*
	*  @date	15/12/17
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.arrayArgs = function( args ){
		return Array.prototype.slice.call( args );
	};
	
	
	/**
	*  extendArgs
	*
	*  description
	*
	*  @date	15/12/17
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
/*
	pdc.extendArgs = function( ){
		var args = Array.prototype.slice.call( arguments );
		var realArgs = args.shift();
			
		Array.prototype.push.call(arguments, 'bar')
		return Array.prototype.push.apply( args, arguments );
	};
*/
	
	// Preferences
	// - use try/catch to avoid JS error if cookies are disabled on front-end form
	try {
		var preferences = JSON.parse(localStorage.getItem('pdc')) || {};
	} catch(e) {
		var preferences = {};
	}
	
	
	/**
	*  getPreferenceName
	*
	*  Gets the true preference name. 
	*  Converts "this.thing" to "thing-123" if editing post 123.
	*
	*  @date	11/11/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @return	string
	*/
	
	var getPreferenceName = function( name ){
		if( name.substr(0, 5) === 'this.' ) {
			name = name.substr(5) + '-' + pdc.get('post_id');
		}
		return name;
	};
	
	
	/**
	*  pdc.getPreference
	*
	*  Gets a preference setting or null if not set.
	*
	*  @date	11/11/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @return	mixed
	*/
	
	pdc.getPreference = function( name ){
		name = getPreferenceName( name );
		return preferences[ name ] || null;
	}
	
	
	/**
	*  pdc.setPreference
	*
	*  Sets a preference setting.
	*
	*  @date	11/11/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @param	mixed value
	*  @return	n/a
	*/
	
	pdc.setPreference = function( name, value ){
		name = getPreferenceName( name );
		if( value === null ) {
			delete preferences[ name ];
		} else {
			preferences[ name ] = value;
		}
		localStorage.setItem('pdc', JSON.stringify(preferences));
	}
	
	
	/**
	*  pdc.removePreference
	*
	*  Removes a preference setting.
	*
	*  @date	11/11/17
	*  @since	5.6.5
	*
	*  @param	string name
	*  @return	n/a
	*/
	
	pdc.removePreference = function( name ){ 
		pdc.setPreference(name, null);
	};
	
	
	/**
	*  remove
	*
	*  Removes an element with fade effect
	*
	*  @date	1/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.remove = function( props ){
		
		// allow jQuery
		if( props instanceof jQuery ) {
			props = {
				target: props
			};
		}
		
		// defaults
		props = pdc.parseArgs(props, {
			target: false,
			endHeight: 0,
			complete: function(){}
		});
		
		// action
		pdc.doAction('remove', props.target);
		
		// tr
		if( props.target.is('tr') ) {
			removeTr( props );
		
		// div
		} else {
			removeDiv( props );
		}
		
	};
	
	/**
	*  removeDiv
	*
	*  description
	*
	*  @date	16/2/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var removeDiv = function( props ){
		
		// vars
		var $el = props.target;
		var height = $el.height();
		var width = $el.width();
		var margin = $el.css('margin');
		var outerHeight = $el.outerHeight(true);
		var style = $el.attr('style') + ''; // needed to copy
		
		// wrap
		$el.wrap('<div class="pdc-temp-remove" style="height:' + outerHeight + 'px"></div>');
		var $wrap = $el.parent();
		
		// set pos
		$el.css({
			height:		height,
			width:		width,
			margin:		margin,
			position:	'absolute'
		});
		
		// fade wrap
		setTimeout(function(){
			
			$wrap.css({
				opacity:	0,
				height:		props.endHeight
			});
			
		}, 50);
		
		// remove
		setTimeout(function(){
			
			$el.attr('style', style);
			$wrap.remove();
			props.complete();
		
		}, 301);
	};
	
	/**
	*  removeTr
	*
	*  description
	*
	*  @date	16/2/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var removeTr = function( props ){
		
		// vars
		var $tr = props.target;
		var height = $tr.height();
		var children = $tr.children().length;
		
		// create dummy td
		var $td = $('<td class="pdc-temp-remove" style="padding:0; height:' + height + 'px" colspan="' + children + '"></td>');
		
		// fade away tr
		$tr.addClass('pdc-remove-element');
		
		// update HTML after fade animation
		setTimeout(function(){
			$tr.html( $td );
		}, 251);
		
		// allow .pdc-temp-remove to exist before changing CSS
		setTimeout(function(){
			
			// remove class
			$tr.removeClass('pdc-remove-element');
			
			// collapse
			$td.css({
				height: props.endHeight
			});			
				
		}, 300);
		
		// remove
		setTimeout(function(){
			
			$tr.remove();
			props.complete();
		
		}, 451);
	};
	
	/**
	*  duplicate
	*
	*  description
	*
	*  @date	3/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.duplicate = function( args ){
		
		// allow jQuery
		if( args instanceof jQuery ) {
			args = {
				target: args
			};
		}
		
		// vars
		var timeout = 0;
				
		// defaults
		args = pdc.parseArgs(args, {
			target: false,
			search: '',
			replace: '',
			before: function( $el ){},
			after: function( $el, $el2 ){},
			append: function( $el, $el2 ){ 
				$el.after( $el2 );
				timeout = 1;
			}
		});
		
		// compatibility
		args.target = args.target || args.$el;
				
		// vars
		var $el = args.target;
		
		// search
		args.search = args.search || $el.attr('data-id');
		args.replace = args.replace || pdc.uniqid();
		
		// before
		// - allow pdc to modify DOM
		// - fixes bug where select field option is not selected
		args.before( $el );
		pdc.doAction('before_duplicate', $el);
		
		// clone
		var $el2 = $el.clone();
		
		// rename
		pdc.rename({
			target:		$el2,
			search:		args.search,
			replace:	args.replace,
		});
		
		// remove classes
		$el2.removeClass('pdc-clone');
		$el2.find('.ui-sortable').removeClass('ui-sortable');
		
		// after
		// - allow pdc to modify DOM
		args.after( $el, $el2 );
		pdc.doAction('after_duplicate', $el, $el2 );
		
		// append
		args.append( $el, $el2 );
		
		// append
		// - allow element to be moved into a visible position before fire action
		//var callback = function(){
			pdc.doAction('append', $el2);
		//};
		//if( timeout ) {
		//	setTimeout(callback, timeout);
		//} else {
		//	callback();
		//}
		
		// return
		return $el2;
	};
	
	/**
	*  rename
	*
	*  description
	*
	*  @date	7/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.rename = function( args ){
		
		// allow jQuery
		if( args instanceof jQuery ) {
			args = {
				target: args
			};
		}
		
		// defaults
		args = pdc.parseArgs(args, {
			target: false,
			destructive: false,
			search: '',
			replace: '',
		});
		
		// vars
		var $el = args.target;
		var search = args.search || $el.attr('data-id');
		var replace = args.replace || pdc.uniqid('pdc');
		var replaceAttr = function(i, value){
			return value.replace( search, replace );
		}
		
		// replace (destructive)
		if( args.destructive ) {
			var html = $el.outerHTML();
			html = pdc.strReplace( search, replace, html );
			$el.replaceWith( html );
			
		// replace
		} else {
			$el.attr('data-id', replace);
			$el.find('[id*="' + search + '"]').attr('id', replaceAttr);
			$el.find('[for*="' + search + '"]').attr('for', replaceAttr);
			$el.find('[name*="' + search + '"]').attr('name', replaceAttr);
		}
		
		// return
		return $el;
	};
	
	
	/**
	*  pdc.prepareForAjax
	*
	*  description
	*
	*  @date	4/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.prepareForAjax = function( data ){
		
		// required
		data.nonce = pdc.get('nonce');
		data.post_id = pdc.get('post_id');
		
		// language
		if( pdc.has('language') ) {
			data.lang = pdc.get('language');
		}
		
		// filter for 3rd party customization
		data = pdc.applyFilters('prepare_for_ajax', data);	
		
		// return
		return data;
	};
	
	
	/**
	*  pdc.startButtonLoading
	*
	*  description
	*
	*  @date	5/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.startButtonLoading = function( $el ){
		$el.prop('disabled', true);
		$el.after(' <i class="pdc-loading"></i>');
	}
	
	pdc.stopButtonLoading = function( $el ){
		$el.prop('disabled', false);
		$el.next('.pdc-loading').remove();
	}
	
	
	/**
	*  pdc.showLoading
	*
	*  description
	*
	*  @date	12/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.showLoading = function( $el ){
		$el.append('<div class="pdc-loading-overlay"><i class="pdc-loading"></i></div>');
	};
	
	pdc.hideLoading = function( $el ){
		$el.children('.pdc-loading-overlay').remove();
	};
	
	
	/**
	*  pdc.updateUserSetting
	*
	*  description
	*
	*  @date	5/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.updateUserSetting = function( name, value ){
		
		var ajaxData = {
			action: 'pdc/ajax/user_setting',
			name: name,
			value: value
		};
		
		$.ajax({
	    	url: pdc.get('ajaxurl'),
	    	data: pdc.prepareForAjax(ajaxData),
			type: 'post',
			dataType: 'html'
		});
		
	};
	
	
	/**
	*  pdc.val
	*
	*  description
	*
	*  @date	8/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.val = function( $input, value, silent ){
		
		// vars
		var prevValue = $input.val();
		
		// bail if no change
		if( value === prevValue ) {
			return false
		}
		
		// update value
		$input.val( value );
		
		// prevent select elements displaying blank value if option doesn't exist
		if( $input.is('select') && $input.val() === null ) {
			$input.val( prevValue );
			return false;
		}
		
		// update with trigger
		if( silent !== true ) {
			$input.trigger('change');
		}
		
		// return
		return true;	
	};
	
	/**
	*  pdc.show
	*
	*  description
	*
	*  @date	9/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.show = function( $el, lockKey ){
		
		// unlock
		if( lockKey ) {
			pdc.unlock($el, 'hidden', lockKey);
		}
		
		// bail early if $el is still locked
		if( pdc.isLocked($el, 'hidden') ) {
			//console.log( 'still locked', getLocks( $el, 'hidden' ));
			return false;
		}
		
		// $el is hidden, remove class and return true due to change in visibility
		if( $el.hasClass('pdc-hidden') ) {
			$el.removeClass('pdc-hidden');
			return true;
		
		// $el is visible, return false due to no change in visibility
		} else {
			return false;
		}
	};
	
	
	/**
	*  pdc.hide
	*
	*  description
	*
	*  @date	9/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.hide = function( $el, lockKey ){
		
		// lock
		if( lockKey ) {
			pdc.lock($el, 'hidden', lockKey);
		}
		
		// $el is hidden, return false due to no change in visibility
		if( $el.hasClass('pdc-hidden') ) {
			return false;
		
		// $el is visible, add class and return true due to change in visibility
		} else {
			$el.addClass('pdc-hidden');
			return true;
		}
	};
	
	
	/**
	*  pdc.isHidden
	*
	*  description
	*
	*  @date	9/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.isHidden = function( $el ){
		return $el.hasClass('pdc-hidden');
	};
	
	
	/**
	*  pdc.isVisible
	*
	*  description
	*
	*  @date	9/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.isVisible = function( $el ){
		return !pdc.isHidden( $el );
	};
	
	
	/**
	*  enable
	*
	*  description
	*
	*  @date	12/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var enable = function( $el, lockKey ){
		
		// check class. Allow .pdc-disabled to overrule all JS
		if( $el.hasClass('pdc-disabled') ) {
			return false;
		}
		
		// unlock
		if( lockKey ) {
			pdc.unlock($el, 'disabled', lockKey);
		}
		
		// bail early if $el is still locked
		if( pdc.isLocked($el, 'disabled') ) {
			return false;
		}
		
		// $el is disabled, remove prop and return true due to change
		if( $el.prop('disabled') ) {
			$el.prop('disabled', false);
			return true;
		
		// $el is enabled, return false due to no change
		} else {
			return false;
		}
	};
	
	/**
	*  pdc.enable
	*
	*  description
	*
	*  @date	9/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.enable = function( $el, lockKey ){
		
		// enable single input
		if( $el.attr('name') ) {
			return enable( $el, lockKey );
		}
		
		// find and enable child inputs
		// return true if any inputs have changed
		var results = false;
		$el.find('[name]').each(function(){
			var result = enable( $(this), lockKey );
			if( result ) {
				results = true;
			}
		});
		return results;
	};
	
	
	/**
	*  disable
	*
	*  description
	*
	*  @date	12/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var disable = function( $el, lockKey ){
		
		// lock
		if( lockKey ) {
			pdc.lock($el, 'disabled', lockKey);
		}
		
		// $el is disabled, return false due to no change
		if( $el.prop('disabled') ) {
			return false;
		
		// $el is enabled, add prop and return true due to change
		} else {
			$el.prop('disabled', true);
			return true;
		}
	};
	
	
	/**
	*  pdc.disable
	*
	*  description
	*
	*  @date	9/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.disable = function( $el, lockKey ){
		
		// disable single input
		if( $el.attr('name') ) {
			return disable( $el, lockKey );
		}
		
		// find and enable child inputs
		// return true if any inputs have changed
		var results = false;
		$el.find('[name]').each(function(){
			var result = disable( $(this), lockKey );
			if( result ) {
				results = true;
			}
		});
		return results;
	};
	
	
	/**
	*  pdc.isset
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.isset = function( obj /*, level1, level2, ... */ ) {
		for( var i = 1; i < arguments.length; i++ ) {
			if( !obj || !obj.hasOwnProperty(arguments[i]) ) {
				return false;
			}
			obj = obj[ arguments[i] ];
		}
		return true;
	};
	
	/**
	*  pdc.isget
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.isget = function( obj /*, level1, level2, ... */ ) {
		for( var i = 1; i < arguments.length; i++ ) {
			if( !obj || !obj.hasOwnProperty(arguments[i]) ) {
				return null;
			}
			obj = obj[ arguments[i] ];
		}
		return obj;
	};
	
	/**
	*  pdc.getFileInputData
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getFileInputData = function( $input, callback ){
		
		// vars
		var value = $input.val();
		
		// bail early if no value
		if( !value ) {
			return false;
		}
		
		// data
		var data = {
			url: value
		};
		
		// modern browsers
		var file = pdc.isget( $input[0], 'files', 0);
		if( file ){
			
			// update data
			data.size = file.size;
			data.type = file.type;
			
			// image
			if( file.type.indexOf('image') > -1 ) {
				
				// vars
				var windowURL = window.URL || window.webkitURL;
				var img = new Image();
				
				img.onload = function() {
					
					// update
					data.width = this.width;
					data.height = this.height;
					
					callback( data );
				};
				img.src = windowURL.createObjectURL( file );
			} else {
				callback( data );
			}
		} else {
			callback( data );
		}		
	};
	
	/**
	*  pdc.isAjaxSuccess
	*
	*  description
	*
	*  @date	18/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.isAjaxSuccess = function( json ){
		return ( json && json.success );
	};
	
	/**
	*  pdc.getAjaxMessage
	*
	*  description
	*
	*  @date	18/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getAjaxMessage = function( json ){
		return pdc.isget( json, 'data', 'message' );
	};
	
	/**
	*  pdc.getAjaxError
	*
	*  description
	*
	*  @date	18/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getAjaxError = function( json ){
		return pdc.isget( json, 'data', 'error' );
	};
	
	
	/**
	*  pdc.renderSelect
	*
	*  Renders the innter html for a select field.
	*
	*  @date	19/2/18
	*  @since	5.6.9
	*
	*  @param	jQuery $select The select element.
	*  @param	array choices An array of choices.
	*  @return	void
	*/
	
	pdc.renderSelect = function( $select, choices ){
		
		// vars
		var value = $select.val();
		var values = [];
		
		// callback
		var crawl = function( items ){
			
			// vars
			var itemsHtml = '';
			
			// loop
			items.map(function( item ){
				
				// vars
				var text = item.text || item.label || '';
				var id = item.id || item.value || '';
				
				// append
				values.push(id);
				
				//  optgroup
				if( item.children ) {
					itemsHtml += '<optgroup label="' + pdc.strEscape(text) + '">' + crawl( item.children ) + '</optgroup>';
				
				// option
				} else {
					itemsHtml += '<option value="' + id + '"' + (item.disabled ? ' disabled="disabled"' : '') + '>' + pdc.strEscape(text) + '</option>';
				}
			});
			
			// return
			return itemsHtml;
		};
		
		// update HTML
		$select.html( crawl(choices) );
		
		// update value
		if( values.indexOf(value) > -1 ){
			$select.val( value );
		}
		
		// return selected value
		return $select.val();
	};
	
	/**
	*  pdc.lock
	*
	*  Creates a "lock" on an element for a given type and key
	*
	*  @date	22/2/18
	*  @since	5.6.9
	*
	*  @param	jQuery $el The element to lock.
	*  @param	string type The type of lock such as "condition" or "visibility".
	*  @param	string key The key that will be used to unlock.
	*  @return	void
	*/
	
	var getLocks = function( $el, type ){
		return $el.data('pdc-lock-'+type) || [];
	};
	
	var setLocks = function( $el, type, locks ){
		$el.data('pdc-lock-'+type, locks);
	}
	
	pdc.lock = function( $el, type, key ){
		var locks = getLocks( $el, type );
		var i = locks.indexOf(key);
		if( i < 0 ) {
			locks.push( key );
			setLocks( $el, type, locks );
		}
	};
	
	/**
	*  pdc.unlock
	*
	*  Unlocks a "lock" on an element for a given type and key
	*
	*  @date	22/2/18
	*  @since	5.6.9
	*
	*  @param	jQuery $el The element to lock.
	*  @param	string type The type of lock such as "condition" or "visibility".
	*  @param	string key The key that will be used to unlock.
	*  @return	void
	*/
	
	pdc.unlock = function( $el, type, key ){
		var locks = getLocks( $el, type );
		var i = locks.indexOf(key);
		if( i > -1 ) {
			locks.splice(i, 1);
			setLocks( $el, type, locks );
		}
		
		// return true if is unlocked (no locks)
		return (locks.length === 0);
	};
	
	/**
	*  pdc.isLocked
	*
	*  Returns true if a lock exists for a given type
	*
	*  @date	22/2/18
	*  @since	5.6.9
	*
	*  @param	jQuery $el The element to lock.
	*  @param	string type The type of lock such as "condition" or "visibility".
	*  @return	void
	*/
	
	pdc.isLocked = function( $el, type ){
		return ( getLocks( $el, type ).length > 0 );
	};
	
	/*
	*  exists
	*
	*  This function will return true if a jQuery selection exists
	*
	*  @type	function
	*  @date	8/09/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	(boolean)
	*/
	
	$.fn.exists = function() {
		return $(this).length>0;
	};
	
	
	/*
	*  outerHTML
	*
	*  This function will return a string containing the HTML of the selected element
	*
	*  @type	function
	*  @date	19/11/2013
	*  @since	5.0.0
	*
	*  @param	$.fn
	*  @return	(string)
	*/
	
	$.fn.outerHTML = function() {
	    return $(this).get(0).outerHTML;
	};
	
	/*
	*  indexOf
	*
	*  This function will provide compatibility for ie8
	*
	*  @type	function
	*  @date	5/3/17
	*  @since	5.5.10
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	if( !Array.prototype.indexOf ) {
		
	    Array.prototype.indexOf = function(val) {
	        return $.inArray(val, this);
	    };
	    
	}
	
	
	// Set up actions from events
	$(document).ready(function(){
		pdc.doAction('ready');
	});
	
	$(window).on('load', function(){
		pdc.doAction('load');
	});
	
	$(window).on('beforeunload', function(){
		pdc.doAction('unload');
	});
	
	$(window).on('resize', function(){
		pdc.doAction('resize');
	});
	
	$(document).on('sortstart', function( event, ui ) {
		pdc.doAction('sortstart', ui.item, ui.placeholder);
	});
	
	$(document).on('sortstop', function( event, ui ) {
		pdc.doAction('sortstop', ui.item, ui.placeholder);
	});
	
})(jQuery);

( function( window, undefined ) {
	"use strict";

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 */
	var EventManager = function() {
		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		var MethodsAvailable = {
			removeFilter : removeFilter,
			applyFilters : applyFilters,
			addFilter : addFilter,
			removeAction : removeAction,
			doAction : doAction,
			addAction : addAction,
			storage : getStorage
		};

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};
		
		function getStorage() {
			
			return STORAGE;
			
		};
		
		/**
		 * Adds an action to the event manager.
		 *
		 * @param action Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addAction( action, callback, priority, context ) {
			if( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'actions', action, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		 * that the first argument must always be the action.
		 */
		function doAction( /* action, arg1, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var action = args.shift();

			if( typeof action === 'string' ) {
				_runHook( 'actions', action, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified action if it contains a namespace.identifier & exists.
		 *
		 * @param action The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeAction( action, callback ) {
			if( typeof action === 'string' ) {
				_removeHook( 'actions', action, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Adds a filter to the event manager.
		 *
		 * @param filter Must contain namespace.identifier
		 * @param callback Must be a valid callback function before this action is added
		 * @param [priority=10] Used to control when the function is executed in relation to other callbacks bound to the same hook
		 * @param [context] Supply a value to be used for this
		 */
		function addFilter( filter, callback, priority, context ) {
			if( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'filters', filter, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		 * the first argument must always be the filter.
		 */
		function applyFilters( /* filter, filtered arg, arg2, ... */ ) {
			var args = Array.prototype.slice.call( arguments );
			var filter = args.shift();

			if( typeof filter === 'string' ) {
				return _runHook( 'filters', filter, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified filter if it contains a namespace.identifier & exists.
		 *
		 * @param filter The action to remove
		 * @param [callback] Callback function to remove
		 */
		function removeFilter( filter, callback ) {
			if( typeof filter === 'string') {
				_removeHook( 'filters', filter, callback );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified hook by resetting the value of it.
		 *
		 * @param type Type of hook, either 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to remove
		 * @private
		 */
		function _removeHook( type, hook, callback, context ) {
			if ( !STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( !callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				var handlers = STORAGE[ type ][ hook ];
				var i;
				if ( !context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[i].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				}
				else {
					for ( i = handlers.length; i--; ) {
						var handler = handlers[i];
						if ( handler.callback === callback && handler.context === context) {
							handlers.splice( i, 1 );
						}
					}
				}
			}
		}

		/**
		 * Adds the hook to the appropriate storage container
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook (namespace.identifier) to add to our event manager
		 * @param callback The function that will be called when the hook is executed.
		 * @param priority The priority of this hook. Must be an integer.
		 * @param [context] A value to be used for this
		 * @private
		 */
		function _addHook( type, hook, callback, priority, context ) {
			var hookObject = {
				callback : callback,
				priority : priority,
				context : context
			};

			// Utilize 'prop itself' : http://jsperf.com/hasownproperty-vs-in-vs-undefined/19
			var hooks = STORAGE[ type ][ hook ];
			if( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			}
			else {
				hooks = [ hookObject ];
			}

			STORAGE[ type ][ hook ] = hooks;
		}

		/**
		 * Use an insert sort for keeping our hooks organized based on priority. This function is ridiculously faster
		 * than bubble sort, etc: http://jsperf.com/javascript-sort
		 *
		 * @param hooks The custom array containing all of the appropriate hooks to perform an insert sort on.
		 * @private
		 */
		function _hookInsertSort( hooks ) {
			var tmpHook, j, prevHook;
			for( var i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}

			return hooks;
		}

		/**
		 * Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		 *
		 * @param type 'actions' or 'filters'
		 * @param hook The hook ( namespace.identifier ) to be ran.
		 * @param args Arguments to pass to the action/filter. If it's a filter, args is actually a single parameter.
		 * @private
		 */
		function _runHook( type, hook, args ) {
			var handlers = STORAGE[ type ][ hook ];
			
			if ( !handlers ) {
				return (type === 'filters') ? args[0] : false;
			}

			var i = 0, len = handlers.length;
			if ( type === 'filters' ) {
				for ( ; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( ; i < len; i++ ) {
					handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			}

			return ( type === 'filters' ) ? args[ 0 ] : true;
		}

		// return all of the publicly available methods
		return MethodsAvailable;

	};
	
	// instantiate
	pdc.hooks = new EventManager();

} )( window );

(function($, undefined){
	
	// Cached regex to split keys for `addEvent`.
	var delegateEventSplitter = /^(\S+)\s*(.*)$/;
  
	/**
	*  extend
	*
	*  Helper function to correctly set up the prototype chain for subclasses
	*  Heavily inspired by backbone.js
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	object protoProps New properties for this object.
	*  @return	function.
	*/
	
	var extend = function( protoProps ) {
		
		// vars
		var Parent = this;
		var Child;
		
	    // The constructor function for the new subclass is either defined by you
	    // (the "constructor" property in your `extend` definition), or defaulted
	    // by us to simply call the parent constructor.
	    if( protoProps && protoProps.hasOwnProperty('constructor') ) {
	      Child = protoProps.constructor;
	    } else {
	      Child = function(){ return Parent.apply(this, arguments); };
	    }
	    
		// Add static properties to the constructor function, if supplied.
		$.extend(Child, Parent);
		
		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function and add the prototype properties.
		Child.prototype = Object.create(Parent.prototype);
		$.extend(Child.prototype, protoProps);
		Child.prototype.constructor = Child;
		
		// Set a convenience property in case the parent's prototype is needed later.
	    //Child.prototype.__parent__ = Parent.prototype;

	    // return
		return Child;
		
	};
	

	/**
	*  Model
	*
	*  Base class for all inheritence
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	object props
	*  @return	function.
	*/
	
	var Model = pdc.Model = function(){
		
		// generate uique client id
		this.cid = pdc.uniqueId('pdc');
		
		// set vars to avoid modifying prototype
		this.data = $.extend(true, {}, this.data);
		
		// pass props to setup function
		this.setup.apply(this, arguments);
		
		// store on element (allow this.setup to create this.$el)
		if( this.$el && !this.$el.data('pdc') ) {
			this.$el.data('pdc', this);
		}
		
		// initialize
		var initialize = function(){
			this.initialize();
			this.addEvents();
			this.addActions();
			this.addFilters();
		};
		
		// initialize on action
		if( this.wait && !pdc.didAction(this.wait) ) {
			this.addAction(this.wait, initialize);
		
		// initialize now
		} else {
			initialize.apply(this);
		}
	};
	
	// Attach all inheritable methods to the Model prototype.
	$.extend(Model.prototype, {
		
		// Unique model id
		id: '',
		
		// Unique client id
		cid: '',
		
		// jQuery element
		$el: null,
		
		// Data specific to this instance
		data: {},
		
		// toggle used when changing data
		busy: false,
		changed: false,
		
		// Setup events hooks
		events: {},
		actions: {},
		filters: {},
		
		// class used to avoid nested event triggers
		eventScope: '',
		
		// action to wait until initialize
		wait: false,
		
		// action priority default
		priority: 10,
		
		/**
		*  get
		*
		*  Gets a specific data value
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @return	mixed
		*/
		
		get: function( name ) {
			return this.data[name];
		},
		
		/**
		*  has
		*
		*  Returns `true` if the data exists and is not null
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @return	boolean
		*/
		
		has: function( name ) {
			return this.get(name) != null;
		},
		
		/**
		*  set
		*
		*  Sets a specific data value
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	mixed value
		*  @return	this
		*/
		
		set: function( name, value, silent ) {
			
			// bail if unchanged
			var prevValue = this.get(name);
			if( prevValue == value ) {
				return this;
			}
			
			// set data
			this.data[ name ] = value;
			
			// trigger events
			if( !silent ) {
				this.changed = true;
				this.trigger('changed:' + name, [value, prevValue]);
				this.trigger('changed', [name, value, prevValue]);
			}
			
			// return
			return this;
		},
		
		/**
		*  inherit
		*
		*  Inherits the data from a jQuery element
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	jQuery $el
		*  @return	this
		*/
		
		inherit: function( data ){
			
			// allow jQuery
			if( data instanceof jQuery ) {
				data = data.data();
			}
			
			// extend
			$.extend(this.data, data);
			
			// return
			return this;
		},
		
		/**
		*  prop
		*
		*  mimics the jQuery prop function
		*
		*  @date	4/6/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		prop: function(){
			return this.$el.prop.apply(this.$el, arguments);
		},
		
		/**
		*  setup
		*
		*  Run during constructor function
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		setup: function( props ){
			$.extend(this, props);
		},
		
		/**
		*  initialize
		*
		*  Also run during constructor function
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		initialize: function(){},
		
		/**
		*  addElements
		*
		*  Adds multiple jQuery elements to this object
		*
		*  @date	9/5/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		addElements: function( elements ){
			elements = elements || this.elements || null;
			if( !elements || !Object.keys(elements).length ) return false;
			for( var i in elements ) {
				this.addElement( i, elements[i] );
			}
		},
		
		/**
		*  addElement
		*
		*  description
		*
		*  @date	9/5/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		addElement: function( name, selector){
			this[ '$' + name ] = this.$( selector );
		},
		
		/**
		*  addEvents
		*
		*  Adds multiple event handlers
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	object events {event1 : callback, event2 : callback, etc }
		*  @return	n/a
		*/
		
		addEvents: function( events ){
			events = events || this.events || null;
			if( !events ) return false;
			for( var key in events ) {
				var match = key.match(delegateEventSplitter);
				this.on(match[1], match[2], events[key]);
			}	
		},
		
		/**
		*  removeEvents
		*
		*  Removes multiple event handlers
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	object events {event1 : callback, event2 : callback, etc }
		*  @return	n/a
		*/
		
		removeEvents: function( events ){
			events = events || this.events || null;
			if( !events ) return false;
			for( var key in events ) {
				var match = key.match(delegateEventSplitter);
				this.off(match[1], match[2], events[key]);
			}	
		},
		
		/**
		*  getEventTarget
		*
		*  Returns a jQUery element to tigger an event on
		*
		*  @date	5/6/18
		*  @since	5.6.9
		*
		*  @param	jQuery $el		The default jQuery element. Optional.
		*  @param	string event	The event name. Optional.
		*  @return	jQuery
		*/
		
		getEventTarget: function( $el, event ){
			return $el || this.$el || $(document);
		},
		
		/**
		*  validateEvent
		*
		*  Returns true if the event target's closest $el is the same as this.$el
		*  Requires both this.el and this.$el to be defined
		*
		*  @date	5/6/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		validateEvent: function( e ){
			if( this.eventScope ) {
				return $( e.target ).closest( this.eventScope ).is( this.$el );
			} else {
				return true;
			}
		},
		
		/**
		*  proxyEvent
		*
		*  Returns a new event callback function scoped to this model
		*
		*  @date	29/3/18
		*  @since	5.6.9
		*
		*  @param	function callback
		*  @return	function
		*/
		
		proxyEvent: function( callback ){
			return this.proxy(function(e){
				
				// validate
				if( !this.validateEvent(e) ) {
					return;
				}
				
				// construct args
				var args = pdc.arrayArgs( arguments );
				var extraArgs = args.slice(1);
				var eventArgs = [ e, $(e.currentTarget) ].concat( extraArgs );
				
				// callback
				callback.apply(this, eventArgs);
			});
		},
		
		/**
		*  on
		*
		*  Adds an event handler similar to jQuery
		*  Uses the instance 'cid' to namespace event
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		on: function( a1, a2, a3, a4 ){
			
			// vars
			var $el, event, selector, callback, args;
			
			// find args
			if( a1 instanceof jQuery ) {
				
				// 1. args( $el, event, selector, callback )
				if( a4 ) {
					$el = a1; event = a2; selector = a3; callback = a4;
					
				// 2. args( $el, event, callback )
				} else {
					$el = a1; event = a2; callback = a3;
				}
			} else {
				
				// 3. args( event, selector, callback )
				if( a3 ) {
					event = a1; selector = a2; callback = a3;
				
				// 4. args( event, callback )
				} else {
					event = a1; callback = a2;
				}
			}
			
			// element
			$el = this.getEventTarget( $el );
			
			// modify callback
			if( typeof callback === 'string' ) {
				callback = this.proxyEvent( this[callback] );
			}
			
			// modify event
			event = event + '.' + this.cid;
			
			// args
			if( selector ) {
				args = [ event, selector, callback ];
			} else {
				args = [ event, callback ];
			}
			
			// on()
			$el.on.apply($el, args);
		},
				
		/**
		*  off
		*
		*  Removes an event handler similar to jQuery
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		off: function( a1, a2 ,a3 ){
			
			// vars
			var $el, event, selector, args;
						
			// find args
			if( a1 instanceof jQuery ) {
				
				// 1. args( $el, event, selector )
				if( a3 ) {
					$el = a1; event = a2; selector = a3;
				
				// 2. args( $el, event )
				} else {
					$el = a1; event = a2;
				}
			} else {
											
				// 3. args( event, selector )
				if( a2 ) {
					event = a1; selector = a2;
					
				// 4. args( event )
				} else {
					event = a1;
				}
			}
			
			// element
			$el = this.getEventTarget( $el );
			
			// modify event
			event = event + '.' + this.cid;
			
			// args
			if( selector ) {
				args = [ event, selector ];
			} else {
				args = [ event ];
			}
			
			// off()
			$el.off.apply($el, args);			
		},
		
		/**
		*  trigger
		*
		*  Triggers an event similar to jQuery
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		trigger: function( name, args, bubbles ){
			var $el = this.getEventTarget();
			if( bubbles ) {
				$el.trigger.apply( $el, arguments );
			} else {
				$el.triggerHandler.apply( $el, arguments );
			}
			return this;
		},
		
		/**
		*  addActions
		*
		*  Adds multiple action handlers
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	object actions {action1 : callback, action2 : callback, etc }
		*  @return	n/a
		*/
		
		addActions: function( actions ){
			actions = actions || this.actions || null;
			if( !actions ) return false;
			for( var i in actions ) {
				this.addAction( i, actions[i] );
			}	
		},
		
		/**
		*  removeActions
		*
		*  Removes multiple action handlers
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	object actions {action1 : callback, action2 : callback, etc }
		*  @return	n/a
		*/
		
		removeActions: function( actions ){
			actions = actions || this.actions || null;
			if( !actions ) return false;
			for( var i in actions ) {
				this.removeAction( i, actions[i] );
			}	
		},
		
		/**
		*  addAction
		*
		*  Adds an action using the wp.hooks library
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		addAction: function( name, callback, priority ){
			//console.log('addAction', name, priority);
			// defaults
			priority = priority || this.priority;
			
			// modify callback
			if( typeof callback === 'string' ) {
				callback = this[ callback ];
			}
			
			// add
			pdc.addAction(name, callback, priority, this);
			
		},
		
		/**
		*  removeAction
		*
		*  Remove an action using the wp.hooks library
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		removeAction: function( name, callback ){
			pdc.removeAction(name, this[ callback ]);
		},
		
		/**
		*  addFilters
		*
		*  Adds multiple filter handlers
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	object filters {filter1 : callback, filter2 : callback, etc }
		*  @return	n/a
		*/
		
		addFilters: function( filters ){
			filters = filters || this.filters || null;
			if( !filters ) return false;
			for( var i in filters ) {
				this.addFilter( i, filters[i] );
			}	
		},
		
		/**
		*  addFilter
		*
		*  Adds a filter using the wp.hooks library
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		addFilter: function( name, callback, priority ){
			
			// defaults
			priority = priority || this.priority;
			
			// modify callback
			if( typeof callback === 'string' ) {
				callback = this[ callback ];
			}
			
			// add
			pdc.addFilter(name, callback, priority, this);
			
		},
		
		/**
		*  removeFilters
		*
		*  Removes multiple filter handlers
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	object filters {filter1 : callback, filter2 : callback, etc }
		*  @return	n/a
		*/
		
		removeFilters: function( filters ){
			filters = filters || this.filters || null;
			if( !filters ) return false;
			for( var i in filters ) {
				this.removeFilter( i, filters[i] );
			}	
		},
		
		/**
		*  removeFilter
		*
		*  Remove a filter using the wp.hooks library
		*
		*  @date	14/12/17
		*  @since	5.6.5
		*
		*  @param	string name
		*  @param	string callback
		*  @return	n/a
		*/
		
		removeFilter: function( name, callback ){
			pdc.removeFilter(name, this[ callback ]);
		},
		
		/**
		*  $
		*
		*  description
		*
		*  @date	16/12/17
		*  @since	5.6.5
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		$: function( selector ){
			return this.$el.find( selector );
		},
		
		/**
		*  remove
		*
		*  Removes the element and listenters
		*
		*  @date	19/12/17
		*  @since	5.6.5
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		remove: function(){
			this.removeEvents();
			this.removeActions();
			this.removeFilters();
			this.$el.remove();
		},
		
		/**
		*  setTimeout
		*
		*  description
		*
		*  @date	16/1/18
		*  @since	5.6.5
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		setTimeout: function( callback, milliseconds ){
			return setTimeout( this.proxy(callback), milliseconds );
		},
		
		/**
		*  time
		*
		*  used for debugging
		*
		*  @date	7/3/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		time: function(){
			console.time( this.id || this.cid );
		},
		
		/**
		*  timeEnd
		*
		*  used for debugging
		*
		*  @date	7/3/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		timeEnd: function(){
			console.timeEnd( this.id || this.cid );
		},
		
		/**
		*  show
		*
		*  description
		*
		*  @date	15/3/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		show: function(){
			pdc.show( this.$el );
		},
		
		
		/**
		*  hide
		*
		*  description
		*
		*  @date	15/3/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		hide: function(){
			pdc.hide( this.$el );
		},
		
		/**
		*  proxy
		*
		*  Returns a new function scoped to this model
		*
		*  @date	29/3/18
		*  @since	5.6.9
		*
		*  @param	function callback
		*  @return	function
		*/
		
		proxy: function( callback ){
			return $.proxy( callback, this );
		}
		
		
	});
	
	// Set up inheritance for the model
	Model.extend = extend;
	
	// Global model storage
	pdc.models = {};
	
	/**
	*  pdc.getInstance
	*
	*  This function will get an instance from an element
	*
	*  @date	5/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getInstance = function( $el ){
		return $el.data('pdc');	
	};
	
	/**
	*  pdc.getInstances
	*
	*  This function will get an array of instances from multiple elements
	*
	*  @date	5/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getInstances = function( $el ){
		var instances = [];
		$el.each(function(){
			instances.push( pdc.getInstance( $(this) ) );
		});
		return instances;	
	};
	
})(jQuery);

(function($, undefined){
	
	pdc.models.Popup = pdc.Model.extend({
			
		data: {
			title: '',
			content: '',
			width: 0,
			height: 0,
			loading: false,
		},
		
		events: {
			'click [data-event="close"]': 'onClickClose',
			'click .pdc-close-popup': 'onClickClose',
		},
		
		setup: function( props ){
			$.extend(this.data, props);
			this.$el = $(this.tmpl());
		},
		
		initialize: function(){
			this.render();
			this.open();
		},
		
		tmpl: function(){
			return [
				'<div id="pdc-popup">',
					'<div class="pdc-popup-box pdc-box">',
						'<div class="title"><h3></h3><a href="#" class="pdc-icon -cancel grey" data-event="close"></a></div>',
						'<div class="inner"></div>',
						'<div class="loading"><i class="pdc-loading"></i></div>',
					'</div>',
					'<div class="bg" data-event="close"></div>',
				'</div>'
			].join('');
		},
		
		render: function(){
			
			// vars
			var title = this.get('title');
			var content = this.get('content');
			var loading = this.get('loading');
			var width = this.get('width');
			var height = this.get('height');
			
			// html
			this.title( title );
			this.content( content );
			
			// width
			if( width ) {
				this.$('.pdc-popup-box').css('width', width);
			}
			
			// height
			if( height ) {
				this.$('.pdc-popup-box').css('min-height', height);
			}
			
			// loading
			this.loading( loading );
			
			// action
			pdc.doAction('append', this.$el);

		},
		
		update: function( props ){
			this.data = pdc.parseArgs(props, this.data);
			this.render();
		},
		
		title: function( title ){
			this.$('.title:first h3').html( title );
		},
		
		content: function( content ){
			this.$('.inner:first').html( content );
		},
		
		loading: function( show ){
			var $loading = this.$('.loading:first');
			show ? $loading.show() : $loading.hide();
		},

		open: function(){
			$('body').append( this.$el );
		},
		
		close: function(){
			this.remove();
		},
		
		onClickClose: function( e, $el ){
			e.preventDefault();
			this.close();
		}
		
	});
	
	/**
	*  newPopup
	*
	*  Creates a new Popup with the supplied props
	*
	*  @date	17/12/17
	*  @since	5.6.5
	*
	*  @param	object props
	*  @return	object
	*/
	
	pdc.newPopup = function( props ){
		return new pdc.models.Popup( props );
	};
	
})(jQuery);

(function($, undefined){
	
	pdc.unload = new pdc.Model({
		
		wait: 'load',
		active: true,
		changed: false,
		
		actions: {
			'validation_failure':	'startListening'
		},
		
		events: {
			'change .pdc-field':	'startListening',
			'submit form':			'stopListening'
		},
				
		reset: function(){
			this.stopListening();
		},
		
		startListening: function(){
			
			// bail ealry if already changed, not active
			if( this.changed || !this.active ) {
				return;
			}
			
			// update 
			this.changed = true;
			
			// add event
			$(window).on('beforeunload', this.onUnload);
			
		},
		
		stopListening: function(){
			
			// update 
			this.changed = false;
			
			// remove event
			$(window).off('beforeunload', this.onUnload);
			
		},
		
		onUnload: function(){
			return pdc.__('The changes you made will be lost if you navigate away from this page');
		}
		 
	});
	
})(jQuery);

(function($, undefined){
	
	var panel = new pdc.Model({
		
		events: {
			'click .pdc-panel-title': 'onClick',
		},
		
		onClick: function( e, $el ){
			e.preventDefault();
			this.toggle( $el.parent() );
		},
		
		isOpen: function( $el ) {
			return $el.hasClass('-open');
		},
		
		toggle: function( $el ){
			this.isOpen($el) ? this.close( $el ) : this.open( $el );
		},
		
		open: function( $el ){
			$el.addClass('-open');
			$el.find('.pdc-panel-title i').attr('class', 'dashicons dashicons-arrow-down');
		},
		
		close: function( $el ){
			$el.removeClass('-open');
			$el.find('.pdc-panel-title i').attr('class', 'dashicons dashicons-arrow-right');
		}
				 
	});
		
})(jQuery);

(function($, undefined){
	
	var Notice = pdc.Model.extend({
		
		data: {
			text: '',
			type: '',
			timeout: 0,
			dismiss: true,
			target: false,
			close: function(){}
		},
		
		events: {
			'click .pdc-notice-dismiss': 'onClickClose',
		},
		
		tmpl: function(){
			return '<div class="pdc-notice"></div>';
		},
		
		setup: function( props ){
			$.extend(this.data, props);
			this.$el = $(this.tmpl());
		},
		
		initialize: function(){
			
			// render
			this.render();
			
			// show
			this.show();
		},
		
		render: function(){
			
			// class
			this.type( this.get('type') );
			
			// text
			this.html( '<p>' + this.get('text') + '</p>' );
			
			// close
			if( this.get('dismiss') ) {
				this.$el.append('<a href="#" class="pdc-notice-dismiss pdc-icon -cancel small"></a>');
				this.$el.addClass('-dismiss');
			}
			
			// timeout
			var timeout = this.get('timeout');
			if( timeout ) {
				this.away( timeout );
			}
		},
		
		update: function( props ){
			
			// update
			$.extend(this.data, props);
			
			// re-initialize
			this.initialize();
			
			// refresh events
			this.removeEvents();
			this.addEvents();
		},
		
		show: function(){
			var $target = this.get('target');
			if( $target ) {
				$target.prepend( this.$el );
			}
		},
		
		hide: function(){
			this.$el.remove();
		},
		
		away: function( timeout ){
			this.setTimeout(function(){
				pdc.remove( this.$el );
			}, timeout );
		},
		
		type: function( type ){
			
			// remove prev type
			var prevType = this.get('type');
			if( prevType ) {
				this.$el.removeClass('-' + prevType);
			}
			
			// add new type
			this.$el.addClass('-' + type);
			
			// backwards compatibility
			if( type == 'error' ) {
				this.$el.addClass('pdc-error-message');
			}
		},
		
		html: function( html ){
			this.$el.html( html );
		},
		
		text: function( text ){
			this.$('p').html( text );
		},
		
		onClickClose: function( e, $el ){
			e.preventDefault();
			this.get('close').apply(this, arguments);
			this.remove();
		}
	});
	
	pdc.newNotice = function( props ){
		
		// ensure object
		if( typeof props !== 'object' ) {
			props = { text: props };
		}
		
		// instantiate
		return new Notice( props );
	};
	
	var noticeManager = new pdc.Model({
		wait: 'prepare',
		priority: 1,
		initialize: function(){
			
			// vars
			var $notice = $('.pdc-admin-notice');
			
			// move to avoid WP flicker
			if( $notice.length ) {
				$('h1:first').after( $notice );
			}
		}	 
	});
	
	
})(jQuery);

(function($, undefined){
	
	pdc.models.Postbox = pdc.Model.extend({
		
		data: {
			id: 		'',
			key:		'',
			style: 		'default',
			label: 		'top',
			editLink:	'',
			editTitle:	'',
			visibility:	true
		},
		
		setup: function( props ){
			$.extend(this.data, props);
		},
		
		initialize: function(){
			
			// vars
			var id = this.get('id');
			var $postbox = $('#' + id);
			var $toggle = $('#' + id + '-hide');
			var $label = $toggle.parent();
			
			// add class
			$postbox.addClass('pdc-postbox');
			$label.addClass('pdc-postbox-toggle');
			
			// remove class
			$postbox.removeClass('hide-if-js');
			$label.removeClass('hide-if-js');
			
			// field group style
			var style = this.get('style');
			if( style !== 'default' ) {
				$postbox.addClass( style );
			}
			
			// .inside class
			$postbox.children('.inside').addClass('pdc-fields').addClass('-' + this.get('label'));
			
				
			// visibility
			if( this.get('visibility') ) {
				$toggle.prop('checked', true);
			} else {
				$postbox.addClass('pdc-hidden');
				$label.addClass('pdc-hidden');
			}
			
			// edit link
			var editLink = this.get('editLink');
			var editTitle = this.get('editTitle');
			if( editLink ) {
				
				$postbox.children('.hndle').append('<a href="' + editLink + '" class="dashicons dashicons-admin-generic pdc-hndle-cog pdc-js-tooltip" title="' + editTitle + '"></a>');
			}
		}
	});
	
	pdc.newPostbox = function( props ){
		return new pdc.models.Postbox( props );
	};
			
})(jQuery);

(function($, undefined){
	
	pdc.newTooltip = function( props ){
		
		// ensure object
		if( typeof props !== 'object' ) {
			props = { text: props };
		}
		
		// confirmRemove
		if( props.confirmRemove !== undefined ) {
			
			props.textConfirm = pdc.__('Remove');
			props.textCancel = pdc.__('Cancel');
			return new TooltipConfirm( props );
			
		// confirm
		} else if( props.confirm !== undefined ) {
			
			return new TooltipConfirm( props );
		
		// default
		} else {
			return new Tooltip( props );
		}
		
	};
	
	var Tooltip = pdc.Model.extend({
		
		data: {
			text: '',
			timeout: 0,
			target: null
		},
		
		tmpl: function(){
			return '<div class="pdc-tooltip"></div>';
		},
		
		setup: function( props ){
			$.extend(this.data, props);
			this.$el = $(this.tmpl());
		},
		
		initialize: function(){
			
			// render
			this.render();
			
			// append
			this.show();
			
			// position
			this.position();
			
			// timeout
			var timeout = this.get('timeout');
			if( timeout ) {
				setTimeout( $.proxy(this.fade, this), timeout );
			}
		},
		
		update: function( props ){
			$.extend(this.data, props);
			this.initialize();
		},
		
		render: function(){
			this.html( this.get('text') );
		},
		
		show: function(){
			$('body').append( this.$el );
		},
		
		hide: function(){
			this.$el.remove();
		},
		
		fade: function(){
			
			// add class
			this.$el.addClass('pdc-fade-up');
			
			// remove
			this.setTimeout(function(){
				this.remove();
			}, 250);
		},
		
		html: function( html ){
			this.$el.html( html );
		},
		
		position: function(){
			
			// vars
			var $tooltip = this.$el;
			var $target = this.get('target');
			if( !$target ) return;
			
			// reset class
			$tooltip.removeClass('right left bottom top');
			
			// position
			var tolerance = 10;
			var target_w = $target.outerWidth();
			var target_h = $target.outerHeight();
			var target_t = $target.offset().top;
			var target_l = $target.offset().left;
			var tooltip_w = $tooltip.outerWidth();
			var tooltip_h = $tooltip.outerHeight();
			
			// calculate top
			var top = target_t - tooltip_h;
			var left = target_l + (target_w / 2) - (tooltip_w / 2);
			
			// too far left
			if( left < tolerance ) {
				
				$tooltip.addClass('right');
				left = target_l + target_w;
				top = target_t + (target_h / 2) - (tooltip_h / 2);
			
			// too far right
			} else if( (left + tooltip_w + tolerance) > $(window).width() ) {
				
				$tooltip.addClass('left');
				left = target_l - tooltip_w;
				top = target_t + (target_h / 2) - (tooltip_h / 2);
			
			// too far top
			} else if( top - $(window).scrollTop() < tolerance ) {
				
				$tooltip.addClass('bottom');
				top = target_t + target_h;

			} else {
				
				$tooltip.addClass('top');
				
			}
			
			// update css
			$tooltip.css({ 'top': top, 'left': left });	
		}
	});
	
	var TooltipConfirm = Tooltip.extend({
		
		data: {
			text: '',
			textConfirm: '',
			textCancel: '',
			target: null,
			targetConfirm: true,
			confirm: function(){},
			cancel: function(){},
			context: false
		},
		
		events: {
			'click [data-event="cancel"]': 'onCancel',
			'click [data-event="confirm"]': 'onConfirm',
		},
		
		addEvents: function(){
			
			// add events
			pdc.Model.prototype.addEvents.apply(this);
			
			// vars
			var $document = $(document);
			var $target = this.get('target');
			
			// add global 'cancel' click event
			// - use timeout to avoid the current 'click' event triggering the onCancel function
			this.setTimeout(function(){
				this.on( $document, 'click', 'onCancel' );
			});
			
			// add target 'confirm' click event
			// - allow setting to control this feature
			if( this.get('targetConfirm') ) {
				this.on( $target, 'click', 'onConfirm' );
			}
		},
		
		removeEvents: function(){
			
			// remove events
			pdc.Model.prototype.removeEvents.apply(this);
			
			// vars
			var $document = $(document);
			var $target = this.get('target');
			
			// remove custom events
			this.off( $document, 'click' );
			this.off( $target, 'click' );
		},
		
		render: function(){
			
			// defaults
			var text = this.get('text') || pdc.__('Are you sure?');
			var textConfirm = this.get('textConfirm') || pdc.__('Yes');
			var textCancel = this.get('textCancel') || pdc.__('No');
			
			// html
			var html = [
				text,
				'<a href="#" data-event="confirm">' + textConfirm + '</a>',
				'<a href="#" data-event="cancel">' + textCancel + '</a>'
			].join(' ');
			
			// html
			this.html( html );
			
			// class
			this.$el.addClass('-confirm');
		},
		
		onCancel: function( e, $el ){
			
			// prevent default
			e.preventDefault();
			e.stopImmediatePropagation();
			
			// callback
			var callback = this.get('cancel');
			var context = this.get('context') || this;
			callback.apply( context, arguments );
			
			//remove
			this.remove();
		},
		
		onConfirm: function( e, $el ){
			
			// prevent default
			e.preventDefault();
			e.stopImmediatePropagation();
			
			// callback
			var callback = this.get('confirm');
			var context = this.get('context') || this;
			callback.apply( context, arguments );
			
			//remove
			this.remove();
		}
	});
	
	// storage
	pdc.models.Tooltip = Tooltip;
	pdc.models.TooltipConfirm = TooltipConfirm;
	
	
	/**
	*  tooltipManager
	*
	*  description
	*
	*  @date	17/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var tooltipHoverHelper = new pdc.Model({
		
		tooltip: false,
		
		events: {
			'mouseenter .pdc-js-tooltip':	'showTitle',
			'mouseup .pdc-js-tooltip':		'hideTitle',
			'mouseleave .pdc-js-tooltip':	'hideTitle'
		},
		
		showTitle: function( e, $el ){
			
			// vars
			var title = $el.attr('title');
			
			// bail ealry if no title
			if( !title ) {
				return;
			}
			
			// clear title to avoid default browser tooltip
			$el.attr('title', '');
			
			// create
			if( !this.tooltip ) {
				this.tooltip = pdc.newTooltip({
					text: title,
					target: $el
				});
			
			// update
			} else {
				this.tooltip.update({
					text: title,
					target: $el
				});
			}
			
		},
		
		hideTitle: function( e, $el ){
			
			// hide tooltip
			this.tooltip.hide();
			
			// restore title
			$el.attr('title', this.tooltip.get('text'));
		}
	});
	
})(jQuery);

(function($, undefined){
	
	// vars
	var storage = [];
	
	/**
	*  pdc.Field
	*
	*  description
	*
	*  @date	23/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.Field = pdc.Model.extend({
		
		// field type
		type: '',
		
		// class used to avoid nested event triggers
		eventScope: '.pdc-field',
		
		// initialize events on 'ready'
		wait: 'ready',
		
		/**
		*  setup
		*
		*  Called during the constructor function to setup this field ready for initialization
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	jQuery $field The field element.
		*  @return	void
		*/
		
		setup: function( $field ){
			
			// set $el
			this.$el = $field;
			
			// inherit $field data
			this.inherit( $field );
			
			// inherit controll data
			this.inherit( this.$control() );
		},
		
		/**
		*  val
		*
		*  Sets or returns the field's value
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	mixed val Optional. The value to set
		*  @return	mixed
		*/
		
		val: function( val ){
			if( val !== undefined ) {
				return this.setValue( val );
			} else {
				return this.prop('disabled') ? null : this.getValue();
			}
		},
		
		/**
		*  getValue
		*
		*  returns the field's value
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	void
		*  @return	mixed
		*/
		
		getValue: function(){
			return this.$input().val();
		},
		
		/**
		*  setValue
		*
		*  sets the field's value and returns true if changed
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	mixed val
		*  @return	boolean. True if changed.
		*/
		
		setValue: function( val ){
			return pdc.val( this.$input(), val );
		},
		
		/**
		*  __
		*
		*  i18n helper to be removed
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		__: function( string ){
			return pdc._e( this.type, string );
		},
		
		/**
		*  $control
		*
		*  returns the control jQuery element used for inheriting data. Uses this.control setting.
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	void
		*  @return	jQuery
		*/
		
		$control: function(){
			return false;
		},
		
		/**
		*  $input
		*
		*  returns the input jQuery element used for saving values. Uses this.input setting.
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	void
		*  @return	jQuery
		*/
		
		$input: function(){
			return this.$('[name]:first');
		},
		
		/**
		*  $inputWrap
		*
		*  description
		*
		*  @date	12/5/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		$inputWrap: function(){
			return this.$('.pdc-input:first');
		},
		
		/**
		*  $inputWrap
		*
		*  description
		*
		*  @date	12/5/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		$labelWrap: function(){
			return this.$('.pdc-label:first');
		},
		
		/**
		*  getInputName
		*
		*  Returns the field's input name
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	void
		*  @return	string
		*/
		
		getInputName: function(){
			return this.$input().attr('name') || '';
		},
		
		/**
		*  parent
		*
		*  returns the field's parent field or false on failure.
		*
		*  @date	8/5/18
		*  @since	5.6.9
		*
		*  @param	void
		*  @return	object|false
		*/
		
		parent: function() {
			
			// vars
			var parents = this.parents();
			
			// return
			return parents.length ? parents[0] : false;
		},
		
		/**
		*  parents
		*
		*  description
		*
		*  @date	9/7/18
		*  @since	5.6.9
		*
		*  @param	type $var Description. Default.
		*  @return	type Description.
		*/
		
		parents: function(){
			
			// vars
			var $parents = this.$el.parents('.pdc-field');
			
			// convert
			var parents = pdc.getFields( $parents );
			
			// return
			return parents;
		},
		
		show: function( lockKey, context ){
			
			// show field and store result
			var changed = pdc.show( this.$el, lockKey );
			
			// do action if visibility has changed
			if( changed ) {
				this.prop('hidden', false);
				pdc.doAction('show_field', this, context);
			}
			
			// return
			return changed;
		},
		
		hide: function( lockKey, context ){
			
			// hide field and store result
			var changed = pdc.hide( this.$el, lockKey );
			
			// do action if visibility has changed
			if( changed ) {
				this.prop('hidden', true);
				pdc.doAction('hide_field', this, context);
			}
			
			// return
			return changed;
		},
		
		enable: function( lockKey, context ){
			
			// enable field and store result
			var changed = pdc.enable( this.$el, lockKey );
			
			// do action if disabled has changed
			if( changed ) {
				this.prop('disabled', false);
				pdc.doAction('enable_field', this, context);
			}
			
			// return
			return changed;
		},
		
		disable: function( lockKey, context ){
			
			// disabled field and store result
			var changed = pdc.disable( this.$el, lockKey );
			
			// do action if disabled has changed
			if( changed ) {
				this.prop('disabled', true);
				pdc.doAction('disable_field', this, context);
			}
			
			// return
			return changed;
		},
		
		showEnable: function( lockKey, context ){
			
			// enable
			this.enable.apply(this, arguments);
			
			// show and return true if changed
			return this.show.apply(this, arguments);
		},
		
		hideDisable: function( lockKey, context ){
			
			// disable
			this.disable.apply(this, arguments);
			
			// hide and return true if changed
			return this.hide.apply(this, arguments);
		},
		
		showNotice: function( props ){
			
			// ensure object
			if( typeof props !== 'object' ) {
				props = { text: props };
			}
			
			// remove old notice
			if( this.notice ) {
				this.notice.remove();
			}
			
			// create new notice
			props.target = this.$inputWrap();
			this.notice = pdc.newNotice( props );
		},
		
		removeNotice: function( timeout ){
			if( this.notice ) {
				this.notice.away( timeout || 0 );
				this.notice = false;
			}
		},
		
		showError: function( message ){
			
			// add class
			this.$el.addClass('pdc-error');
			
			// add message
			if( message !== undefined ) {
				this.showNotice({
					text: message,
					type: 'error',
					dismiss: false
				});
			}
			
			// action
			pdc.doAction('invalid_field', this);
			
			// add event
			this.$el.one('focus change', 'input, select, textarea', $.proxy( this.removeError, this ));	
		},
		
		removeError: function(){
			
			// remove class
			this.$el.removeClass('pdc-error');
			
			// remove notice
			this.removeNotice( 250 );
			
			// action
			pdc.doAction('valid_field', this);
		},
		
		trigger: function( name, args, bubbles ){
			
			// allow some events to bubble
			if( name == 'invalidField' ) {
				bubbles = true;
			}
			
			// return
			return pdc.Model.prototype.trigger.apply(this, [name, args, bubbles]);
		},
	});
	
	/**
	*  newField
	*
	*  description
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.newField = function( $field ){
		
		// vars
		var type = $field.data('type');
		var mid = modelId( type );
		var model = pdc.models[ mid ] || pdc.Field;
		
		// instantiate
		var field = new model( $field );
		
		// actions
		pdc.doAction('new_field', field);
		
		// return
		return field;
	};
	
	/**
	*  mid
	*
	*  Calculates the model ID for a field type
	*
	*  @date	15/12/17
	*  @since	5.6.5
	*
	*  @param	string type
	*  @return	string
	*/
	
	var modelId = function( type ) {
		return pdc.strPascalCase( type || '' ) + 'Field';
	};
	
	/**
	*  registerFieldType
	*
	*  description
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.registerFieldType = function( model ){
		
		// vars
		var proto = model.prototype;
		var type = proto.type;
		var mid = modelId( type );
		
		// store model
		pdc.models[ mid ] = model;
		
		// store reference
		storage.push( type );
	};
	
	/**
	*  pdc.getFieldType
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getFieldType = function( type ){
		var mid = modelId( type );
		return pdc.models[ mid ] || false;
	}
	
	/**
	*  pdc.getFieldTypes
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getFieldTypes = function( args ){
		
		// defaults
		args = pdc.parseArgs(args, {
			category: '',
			// hasValue: true
		});
		
		// clonse available types
		var types = [];
		
		// loop
		storage.map(function( type ){
			
			// vars
			var model = pdc.getFieldType(type);
			var proto = model.prototype;
						
			// check operator
			if( args.category && proto.category !== args.category )  {
				return;
			}
			
			// append
			types.push( model );
		});
		
		// return
		return types;
	};
	
})(jQuery);

(function($, undefined){
	
	/**
	*  findFields
	*
	*  Returns a jQuery selection object of pdc fields.
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	object $args {
	*		Optional. Arguments to find fields.
	*
	*		@type string			key			The field's key (data-attribute).
	*		@type string			name		The field's name (data-attribute).
	*		@type string			type		The field's type (data-attribute).
	*		@type string			is			jQuery selector to compare against.
	*		@type jQuery			parent		jQuery element to search within.
	*		@type jQuery			sibling		jQuery element to search alongside.
	*		@type limit				int			The number of fields to find.
	*		@type suppressFilters	bool		Whether to allow filters to add/remove results. Default behaviour will ignore clone fields.
	*  }
	*  @return	jQuery
	*/
	
	pdc.findFields = function( args ){
		
		// vars
		var selector = '.pdc-field';
		var $fields = false;
		
		// args
		args = pdc.parseArgs(args, {
			key: '',
			name: '',
			type: '',
			is: '',
			parent: false,
			sibling: false,
			limit: false,
			visible: false,
			suppressFilters: false,
		});
		
		// filter args
		if( !args.suppressFilters ) {
			args = pdc.applyFilters('find_fields_args', args);
		}
		
		// key
		if( args.key ) {
			selector += '[data-key="' + args.key + '"]';
		}
		
		// type
		if( args.type ) {
			selector += '[data-type="' + args.type + '"]';
		}
		
		// name
		if( args.name ) {
			selector += '[data-name="' + args.name + '"]';
		}
		
		// is
		if( args.is ) {
			selector += args.is;
		}
		
		// visibility
		if( args.visible ) {
			selector += ':visible';
		}
		
		// query
		if( args.parent ) {
			$fields = args.parent.find( selector );
		} else if( args.sibling ) {
			$fields = args.sibling.siblings( selector );
		} else {
			$fields = $( selector );
		}
		
		// filter
		if( !args.suppressFilters ) {
			$fields = $fields.not('.pdc-clone .pdc-field');
			$fields = pdc.applyFilters('find_fields', $fields);
		}
		
		// limit
		if( args.limit ) {
			$fields = $fields.slice( 0, args.limit );
		}
		
		// return
		return $fields;
		
	};
	
	/**
	*  findField
	*
	*  Finds a specific field with jQuery
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	string key 		The field's key.
	*  @param	jQuery $parent	jQuery element to search within.
	*  @return	jQuery
	*/
	
	pdc.findField = function( key, $parent ){
		return pdc.findFields({
			key: key,
			limit: 1,
			parent: $parent,
			suppressFilters: true
		});
	};
	
	/**
	*  getField
	*
	*  Returns a field instance
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	jQuery|string $field	jQuery element or field key.
	*  @return	object
	*/
	
	pdc.getField = function( $field ){
		
		// allow jQuery
		if( $field instanceof jQuery ) {
		
		// find fields
		} else {
			$field = pdc.findField( $field );
		}
		
		// instantiate
		var field = $field.data('pdc');
		if( !field ) {
			field = pdc.newField( $field );
		}
		
		// return
		return field;
	};
	
	/**
	*  getFields
	*
	*  Returns multiple field instances
	*
	*  @date	14/12/17
	*  @since	5.6.5
	*
	*  @param	jQuery|object $fields	jQuery elements or query args.
	*  @return	array
	*/
	
	pdc.getFields = function( $fields ){
		
		// allow jQuery
		if( $fields instanceof jQuery ) {
		
		// find fields	
		} else {
			$fields = pdc.findFields( $fields );
		}
		
		// loop
		var fields = [];
		$fields.each(function(){
			var field = pdc.getField( $(this) );
			fields.push( field );
		});
		
		// return
		return fields;
	};
	
	/**
	*  findClosestField
	*
	*  Returns the closest jQuery field element
	*
	*  @date	9/4/18
	*  @since	5.6.9
	*
	*  @param	jQuery $el
	*  @return	jQuery
	*/
	
	pdc.findClosestField = function( $el ){
		return $el.closest('.pdc-field');
	};
	
	/**
	*  getClosestField
	*
	*  Returns the closest field instance
	*
	*  @date	22/1/18
	*  @since	5.6.5
	*
	*  @param	jQuery $el
	*  @return	object
	*/
	
	pdc.getClosestField = function( $el ){
		var $field = pdc.findClosestField( $el );
		return this.getField( $field );
	};
	
	/**
	*  addGlobalFieldAction
	*
	*  Sets up callback logic for global field actions
	*
	*  @date	15/6/18
	*  @since	5.6.9
	*
	*  @param	string action
	*  @return	void
	*/
	
	var addGlobalFieldAction = function( action ){
		
		// vars
		var globalAction = action;
		var pluralAction = action + '_fields';	// ready_fields
		var singleAction = action + '_field';	// ready_field
		
		// global action
		var globalCallback = function( $el /*, arg1, arg2, etc*/ ){
			//console.log( action, arguments );
			
			// get args [$el, ...]
			var args = pdc.arrayArgs( arguments );
			var extraArgs = args.slice(1);
			
			// find fields
			var fields = pdc.getFields({ parent: $el });
			
			// check
			if( fields.length ) {
				
				// pluralAction
				var pluralArgs = [ pluralAction, fields ].concat( extraArgs );
				pdc.doAction.apply(null, pluralArgs);
			}
		};
		
		// plural action
		var pluralCallback = function( fields /*, arg1, arg2, etc*/ ){
			//console.log( pluralAction, arguments );
			
			// get args [fields, ...]
			var args = pdc.arrayArgs( arguments );
			var extraArgs = args.slice(1);
			
			// loop
			fields.map(function( field, i ){
				//setTimeout(function(){
				// singleAction
				var singleArgs = [ singleAction, field ].concat( extraArgs );
				pdc.doAction.apply(null, singleArgs);
				//}, i * 100);
			});
		};
		
		// add actions
		pdc.addAction(globalAction, globalCallback);
		pdc.addAction(pluralAction, pluralCallback);
		
		// also add single action
		addSingleFieldAction( action );
	}
	
	/**
	*  addSingleFieldAction
	*
	*  Sets up callback logic for single field actions
	*
	*  @date	15/6/18
	*  @since	5.6.9
	*
	*  @param	string action
	*  @return	void
	*/
	
	var addSingleFieldAction = function( action ){
		
		// vars
		var singleAction = action + '_field';	// ready_field
		var singleEvent = action + 'Field';		// readyField
		
		// single action
		var singleCallback = function( field /*, arg1, arg2, etc*/ ){
			//console.log( singleAction, arguments );
			
			// get args [field, ...]
			var args = pdc.arrayArgs( arguments );
			var extraArgs = args.slice(1);
			
			// action variations (ready_field/type=image)
			var variations = ['type', 'name', 'key'];
			variations.map(function( variation ){
				
				// vars
				var prefix = '/' + variation + '=' + field.get(variation);
				
				// singleAction
				args = [ singleAction + prefix , field ].concat( extraArgs );
				pdc.doAction.apply(null, args);
			});
			
			// event
			if( singleFieldEvents.indexOf(action) > -1 ) {
				field.trigger(singleEvent, extraArgs);
			}
		};
		
		// add actions
		pdc.addAction(singleAction, singleCallback);	
	}
	
	// vars
	var globalFieldActions = [ 'prepare', 'ready', 'load', 'append', 'remove', 'sortstart', 'sortstop', 'show', 'hide', 'unload' ];
	var singleFieldActions = [ 'valid', 'invalid', 'enable', 'disable', 'new' ];
	var singleFieldEvents = [ 'remove', 'sortstart', 'sortstop', 'show', 'hide', 'unload', 'valid', 'invalid', 'enable', 'disable' ];
	
	// add
	globalFieldActions.map( addGlobalFieldAction );
	singleFieldActions.map( addSingleFieldAction );
	
	/**
	*  fieldsEventManager
	*
	*  Manages field actions and events
	*
	*  @date	15/12/17
	*  @since	5.6.5
	*
	*  @param	void
	*  @param	void
	*/
	
	var fieldsEventManager = new pdc.Model({
		id: 'fieldsEventManager',
		events: {
			'click .pdc-field a[href="#"]':	'onClick',
			'change .pdc-field':			'onChange'
		},
		onClick: function( e ){
			
			// prevent default of any link with an href of #
			e.preventDefault();
		},
		onChange: function(){
			
			// preview hack allows post to save with no title or content
			$('#_pdc_changed').val(1);
		}
	});
	
})(jQuery);

(function($, undefined){
	
	var i = 0;
	
	var Field = pdc.Field.extend({
		
		type: 'accordion',
		
		wait: '',
		
		$control: function(){
			return this.$('.pdc-fields:first');
		},
		
		initialize: function(){
			
			// bail early if is cell
			if( this.$el.is('td') ) return;
			
			// enpoint
			if( this.get('endpoint') ) {
				return this.remove();
			}
			
			// vars
			var $field = this.$el;
			var $label = this.$labelWrap()
			var $input = this.$inputWrap();
			var $wrap = this.$control();
			var $instructions = $input.children('.description');
			
			// force description into label
			if( $instructions.length ) {
				$label.append( $instructions );
			}
			
			// table
			if( this.$el.is('tr') ) {
				
				// vars
				var $table = this.$el.closest('table');
				var $newLabel = $('<div class="pdc-accordion-title"/>');
				var $newInput = $('<div class="pdc-accordion-content"/>');
				var $newTable = $('<table class="' + $table.attr('class') + '"/>');
				var $newWrap = $('<tbody/>');
				
				// dom
				$newLabel.append( $label.html() );
				$newTable.append( $newWrap );
				$newInput.append( $newTable );
				$input.append( $newLabel );
				$input.append( $newInput );
				
				// modify
				$label.remove();
				$wrap.remove();
				$input.attr('colspan', 2);
				
				// update vars
				$label = $newLabel;
				$input = $newInput;
				$wrap = $newWrap;
			}
			
			// add classes
			$field.addClass('pdc-accordion');
			$label.addClass('pdc-accordion-title');
			$input.addClass('pdc-accordion-content');
			
			// index
			i++;
			
			// multi-expand
			if( this.get('multi_expand') ) {
				$field.attr('multi-expand', 1);
			}
			
			// open
			var order = pdc.getPreference('this.accordions') || [];
			if( order[i-1] !== undefined ) {
				this.set('open', order[i-1]);
			}
			
			if( this.get('open') ) {
				$field.addClass('-open');
				$input.css('display', 'block'); // needed for accordion to close smoothly
			}
			
			// add icon
			$label.prepend('<i class="pdc-accordion-icon dashicons dashicons-arrow-' + (this.get('open') ? 'down' : 'right') + '"></i>');
			
			// classes
			// - remove 'inside' which is a #poststuff WP class
			var $parent = $field.parent();
			$wrap.addClass( $parent.hasClass('-left') ? '-left' : '' );
			$wrap.addClass( $parent.hasClass('-clear') ? '-clear' : '' );
			
			// append
			$wrap.append( $field.nextUntil('.pdc-field-accordion', '.pdc-field') );
			
			// clean up
			$wrap.removeAttr('data-open data-multi_expand data-endpoint');
		},
		
	});
	
	pdc.registerFieldType( Field );


	/**
	*  accordionManager
	*
	*  Events manager for the pdc accordion
	*
	*  @date	14/2/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	var accordionManager = new pdc.Model({
		
		actions: {
			'unload':	'onUnload'
		},
		
		events: {
			'click .pdc-accordion-title': 'onClick',
			'invalidField .pdc-accordion':	'onInvalidField'
		},
		
		isOpen: function( $el ) {
			return $el.hasClass('-open');
		},
		
		toggle: function( $el ){
			if( this.isOpen($el) ) {
				this.close( $el );
			} else {
				this.open( $el );
			}
		},
		
		open: function( $el ){
			
			// open
			$el.find('.pdc-accordion-content:first').slideDown().css('display', 'block');
			$el.find('.pdc-accordion-icon:first').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
			$el.addClass('-open');
			
			// action
			pdc.doAction('show', $el);
			
			// close siblings
			if( !$el.attr('multi-expand') ) {
				$el.siblings('.pdc-accordion.-open').each(function(){
					accordionManager.close( $(this) );
				});
			}
		},
		
		close: function( $el ){
			
			// close
			$el.find('.pdc-accordion-content:first').slideUp();
			$el.find('.pdc-accordion-icon:first').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
			$el.removeClass('-open');
			
			// action
			pdc.doAction('hide', $el);
		},
		
		onClick: function( e, $el ){
			
			// prevent Defailt
			e.preventDefault();
			
			// open close
			this.toggle( $el.parent() );
			
		},
		
		onInvalidField: function( e, $el ){
			
			// bail early if already focused
			if( this.busy ) {
				return;
			}
			
			// disable functionality for 1sec (allow next validation to work)
			this.busy = true;
			this.setTimeout(function(){
				this.busy = false;
			}, 1000);
			
			// open accordion
			this.open( $el );
		},
		
		onUnload: function( e ){
			
			// vars
			var order = [];
			
			// loop
			$('.pdc-accordion').each(function(){
				var open = $(this).hasClass('-open') ? 1 : 0;
				order.push(open);
			});
			
			// set
			if( order.length ) {
				pdc.setPreference('this.accordions', order);
			}
		}
	});

})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'button_group',
		
		events: {
			'click input[type="radio"]': 'onClick'
		},
		
		$control: function(){
			return this.$('.pdc-button-group');
		},
		
		$input: function(){
			return this.$('input:checked');
		},
		
		setValue: function( val ){
			this.$('input[value="' + val + '"]').prop('checked', true).trigger('change');
		},
		
		onClick: function( e, $el ){
			
			// vars
			var $label = $el.parent('label');
			var selected = $label.hasClass('selected');
			
			// remove previous selected
			this.$('.selected').removeClass('selected');
			
			// add active class
			$label.addClass('selected');
			
			// allow null
			if( this.get('allow_null') && selected ) {
				$label.removeClass('selected');
				$el.prop('checked', false).trigger('change');
			}
		}
	});
	
	pdc.registerFieldType( Field );

})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'checkbox',
		
		events: {
			'change input':					'onChange',
			'click .pdc-add-checkbox':		'onClickAdd',
			'click .pdc-checkbox-toggle':	'onClickToggle',
			'click .pdc-checkbox-custom':	'onClickCustom'
		},
		
		$control: function(){
			return this.$('.pdc-checkbox-list');
		},
		
		$toggle: function(){
			return this.$('.pdc-checkbox-toggle');
		},
		
		$input: function(){
			return this.$('input[type="hidden"]');
		},
		
		$inputs: function(){
			return this.$('input[type="checkbox"]').not('.pdc-checkbox-toggle');
		},
		
		getValue: function(){
			var val = [];
			this.$(':checked').each(function(){
				val.push( $(this).val() );
			});
			return val.length ? val : false;
		},
		
		onChange: function( e, $el ){
			
			// vars
			var checked = $el.prop('checked');
			var $toggle = this.$toggle();
			
			// selected
			if( checked ) {
				$el.parent().addClass('selected');
			} else {
				$el.parent().removeClass('selected');
			}
			
			// determine if all inputs are checked 
			if( $toggle.length ) {
				var $inputs = this.$inputs();
				
				// all checked
				if( $inputs.not(':checked').length == 0 ) {
					$toggle.prop('checked', true);
				} else {
					$toggle.prop('checked', false);
				}
			}
		},
		
		onClickAdd: function( e, $el ){
			var html = '<li><input class="pdc-checkbox-custom" type="checkbox" checked="checked" /><input type="text" name="' + this.getInputName() + '[]" /></li>';
			$el.parent('li').before( html );	
		},
		
		onClickToggle: function( e, $el ){
			var checked = $el.prop('checked');
			var $inputs = this.$inputs();
			$inputs.prop('checked', checked);
		},
		
		onClickCustom: function( e, $el ){
			var checked = $el.prop('checked');
			var $text = $el.next('input[type="text"]');
			
			// checked
			if( checked ) {
				$text.prop('disabled', false);
				
			// not checked	
			} else {
				$text.prop('disabled', true);
				
				// remove
				if( $text.val() == '' ) {
					$el.parent('li').remove();
				}
			}
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'color_picker',
		
		wait: 'load',
		
		$control: function(){
			return this.$('.pdc-color-picker');
		},
		
		$input: function(){
			return this.$('input[type="hidden"]');
		},
		
		$inputText: function(){
			return this.$('input[type="text"]');
		},
		
		initialize: function(){
			
			// vars
			var $input = this.$input();
			var $inputText = this.$inputText();
			
			// event
			var onChange = function( e ){
				
				// timeout is required to ensure the $input val is correct
				setTimeout(function(){ 
					pdc.val( $input, $inputText.val() );
				}, 1);
			}
			
			// args
			var args = {
				defaultColor: false,
				palettes: true,
				hide: true,
				change: onChange,
				clear: onChange
			};
			
 			// filter
 			var args = pdc.applyFilters('color_picker_args', args, this);
        	
 			// initialize
			$inputText.wpColorPicker( args );
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'date_picker',
		
		events: {
			'blur input[type="text"]': 'onBlur'
		},
		
		$control: function(){
			return this.$('.pdc-date-picker');
		},
		
		$input: function(){
			return this.$('input[type="hidden"]');
		},
		
		$inputText: function(){
			return this.$('input[type="text"]');
		},
				
		initialize: function(){
			
			// save_format: compatibility with PDC < 5.0.0
			if( this.has('save_format') ) {
				return this.initializeCompatibility();
			}
			
			// vars
			var $input = this.$input();
			var $inputText = this.$inputText();
			
			// args
			var args = { 
				dateFormat:			this.get('date_format'),
				altField:			$input,
				altFormat:			'yymmdd',
				changeYear:			true,
				yearRange:			"-100:+100",
				changeMonth:		true,
				showButtonPanel:	true,
				firstDay:			this.get('first_day')
			};
			
			// filter
			args = pdc.applyFilters('date_picker_args', args, this);
			
			// add date picker
			pdc.newDatePicker( $inputText, args );
			
			// action
			pdc.doAction('date_picker_init', $inputText, args, this);
			
		},
		
		initializeCompatibility: function(){
			
			// vars
			var $input = this.$input();
			var $inputText = this.$inputText();
			
			// get and set value from alt field
			$inputText.val( $input.val() );
			
			// args
			var args =  { 
				dateFormat:			this.get('date_format'),
				altField:			$input,
				altFormat:			this.get('save_format'),
				changeYear:			true,
				yearRange:			"-100:+100",
				changeMonth:		true,
				showButtonPanel:	true,
				firstDay:			this.get('first_day')
			};
			
			// filter for 3rd party customization
			args = pdc.applyFilters('date_picker_args', args, this);
			
			// backup
			var dateFormat = args.dateFormat;
			
			// change args.dateFormat
			args.dateFormat = this.get('save_format');
				
			// add date picker
			pdc.newDatePicker( $inputText, args );
			
			// now change the format back to how it should be.
			$inputText.datepicker( 'option', 'dateFormat', dateFormat );
			
			// action for 3rd party customization
			pdc.doAction('date_picker_init', $inputText, args, this);
		},
		
		onBlur: function(){
			if( !this.$inputText().val() ) {
				pdc.val( this.$input(), '' );
			}
		}
	});
	
	pdc.registerFieldType( Field );
	
	
	// manager
	var datePickerManager = new pdc.Model({
		priority: 5,
		wait: 'ready',
		initialize: function(){
			
			// vars
			var locale = pdc.get('locale');
			var rtl = pdc.get('rtl');
			var l10n = pdc.get('datePickerL10n');
			
			// bail ealry if no l10n
			if( !l10n ) {
				return false;
			}
			
			// bail ealry if no datepicker library
			if( typeof $.datepicker === 'undefined' ) {
				return false;
			}
			
			// rtl
			l10n.isRTL = rtl;
			
			// append
			$.datepicker.regional[ locale ] = l10n;
			$.datepicker.setDefaults(l10n);
		}
	});
	
	// add
	pdc.newDatePicker = function( $input, args ){
		
		// bail ealry if no datepicker library
		if( typeof $.datepicker === 'undefined' ) {
			return false;
		}
		
		// defaults
		args = args || {};
		
		// initialize
		$input.datepicker( args );
		
		// wrap the datepicker (only if it hasn't already been wrapped)
		if( $('body > #ui-datepicker-div').exists() ) {
			$('body > #ui-datepicker-div').wrap('<div class="pdc-ui-datepicker" />');
		}
	};
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.models.DatePickerField.extend({
		
		type: 'date_time_picker',
		
		$control: function(){
			return this.$('.pdc-date-time-picker');
		},
		
		initialize: function(){
			
			// vars
			var $input = this.$input();
			var $inputText = this.$inputText();
			
			// args
			var args = {
				dateFormat:			this.get('date_format'),
				timeFormat:			this.get('time_format'),
				altField:			$input,
				altFieldTimeOnly:	false,
				altFormat:			'yy-mm-dd',
				altTimeFormat:		'HH:mm:ss',
				changeYear:			true,
				yearRange:			"-100:+100",
				changeMonth:		true,
				showButtonPanel:	true,
				firstDay:			this.get('first_day'),
				controlType: 		'select',
				oneLine:			true
			};
			
			// filter
			args = pdc.applyFilters('date_time_picker_args', args, this);
			
			// add date time picker
			pdc.newDateTimePicker( $inputText, args );
			
			// action
			pdc.doAction('date_time_picker_init', $inputText, args, this);
		}
	});
	
	pdc.registerFieldType( Field );
	
	
	// manager
	var dateTimePickerManager = new pdc.Model({
		priority: 5,
		wait: 'ready',
		initialize: function(){
			
			// vars
			var locale = pdc.get('locale');
			var rtl = pdc.get('rtl');
			var l10n = pdc.get('dateTimePickerL10n');
			
			// bail ealry if no l10n
			if( !l10n ) {
				return false;
			}
			
			// bail ealry if no datepicker library
			if( typeof $.timepicker === 'undefined' ) {
				return false;
			}
			
			// rtl
			l10n.isRTL = rtl;
			
			// append
			$.timepicker.regional[ locale ] = l10n;
			$.timepicker.setDefaults(l10n);
		}
	});
	
	
	// add
	pdc.newDateTimePicker = function( $input, args ){
		
		// bail ealry if no datepicker library
		if( typeof $.timepicker === 'undefined' ) {
			return false;
		}
		
		// defaults
		args = args || {};
		
		// initialize
		$input.datetimepicker( args );
		
		// wrap the datepicker (only if it hasn't already been wrapped)
		if( $('body > #ui-datepicker-div').exists() ) {
			$('body > #ui-datepicker-div').wrap('<div class="pdc-ui-datepicker" />');
		}
	};
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'google_map',
		
		map: false,
		
		wait: 'load',
		
		events: {
			'click a[data-name="clear"]': 		'onClickClear',
			'click a[data-name="locate"]': 		'onClickLocate',
			'click a[data-name="search"]': 		'onClickSearch',
			'keydown .search': 					'onKeydownSearch',
			'keyup .search': 					'onKeyupSearch',
			'focus .search': 					'onFocusSearch',
			'blur .search': 					'onBlurSearch',
			'showField':						'onShow'
		},
		
		$control: function(){
			return this.$('.pdc-google-map');
		},
		
		$input: function( name ){
			return this.$('input[data-name="' + (name || 'address') + '"]');
		},
		
		$search: function(){
			return this.$('.search');
		},
		
		$canvas: function(){
			return this.$('.canvas');
		},
		
		addClass: function( name ){
			this.$control().addClass( name );
		},
		
		removeClass: function( name ){
			this.$control().removeClass( name );
		},
		
		getValue: function(){
			
			// defaults
			var val = {
				lat: '',
				lng: '',
				address: ''
			};
			
			// loop
			this.$('input[type="hidden"]').each(function(){
				val[ $(this).data('name') ] = $(this).val();
			});
			
			// return false if no lat/lng
			if( !val.lat || !val.lng ) {
				val = false;
			}
			
			// return
			return val;
		},
		
		setValue: function( val ){
			
			// defaults
			val = pdc.parseArgs(val, {
				lat: '',
				lng: '',
				address: ''
			});
			
			// loop
			for( var name in val ) {
				pdc.val( this.$input(name), val[name] );
			}
			
			// return false if no lat/lng
			if( !val.lat || !val.lng ) {
				val = false;
			}
			
			// render
			this.renderVal( val );
		},
		
		renderVal: function( val ){
			
		    // has value
		    if( val ) {
			     this.addClass('-value');
			     this.setPosition( val.lat, val.lng );
			     this.map.marker.setVisible( true );
			     
		    // no value
		    } else {
			     this.removeClass('-value');
			     this.map.marker.setVisible( false );
		    }
		    
		    // search
		    this.$search().val( val.address );
		},
		
		setPosition: function( lat, lng ){
			
			// vars
			var latLng = this.newLatLng( lat, lng );
			
			// update marker
			this.map.marker.setPosition( latLng );
			
			// show marker
			this.map.marker.setVisible( true );
			
			// action
			pdc.doAction('google_map_change', latLng, this.map, this);
			
			// center
			this.center();
			
			// return
			return this;
		},
		
		center: function(){
			
			// vars
			var position = this.map.marker.getPosition();
			var lat = this.get('lat');
			var lng = this.get('lng');
			
			// if marker exists, center on the marker
			if( position ) {
				lat = position.lat();
				lng = position.lng();
			}
			
			// latlng
			var latLng = this.newLatLng( lat, lng );
				
			// set center of map
	        this.map.setCenter( latLng );
		},
		
		getSearchVal: function(){
			return this.$search().val();
		},
		
		initialize: function(){
			
			// bail early if too early
			if( !api.isReady() ) {
				api.ready( this.initializeMap, this );
				return;
			}
			
			// initializeMap
			this.initializeMap();
		},
		
		newLatLng: function( lat, lng ){
			return new google.maps.LatLng( parseFloat(lat), parseFloat(lng) );
		},
		
		initializeMap: function(){
			
			// vars
			var zoom = this.get('zoom');
			var lat = this.get('lat');
			var lng = this.get('lng');
			
			
			// map
			var mapArgs = {
				scrollwheel:	false,
        		zoom:			parseInt( zoom ),
        		center:			this.newLatLng(lat, lng),
        		mapTypeId:		google.maps.MapTypeId.ROADMAP,
        		marker:			{
			        draggable: 		true,
			        raiseOnDrag: 	true
		    	},
		    	autocomplete: {}
        	};
        	mapArgs = pdc.applyFilters('google_map_args', mapArgs, this);       	
        	var map = new google.maps.Map( this.$canvas()[0], mapArgs );
        	this.addMapEvents( map, this );
        	
        	
        	// marker
        	var markerArgs = pdc.parseArgs(mapArgs.marker, {
				draggable: 		true,
				raiseOnDrag: 	true,
				map:			map
        	});
		    markerArgs = pdc.applyFilters('google_map_marker_args', markerArgs, this);
			var marker = new google.maps.Marker( markerArgs );
        	this.addMarkerEvents( marker, this );
        	
        	
        	// reference
        	map.pdc = this;
        	map.marker = marker;
        	this.map = map;
        	
        	// action for 3rd party customization
			pdc.doAction('google_map_init', map, marker, this);
        	
        	// set position
		    var val = this.getValue();
		    this.renderVal( val );
		},
		
		addMapEvents: function( map, field ){
			
			// autocomplete
	        if( pdc.isset(window, 'google', 'maps', 'places', 'Autocomplete') ) {
		        
		        // vars
		        var autocompleteArgs = map.autocomplete || {};
		        var autocomplete = new google.maps.places.Autocomplete( this.$search()[0], autocompleteArgs );
				
				// bind
				autocomplete.bindTo('bounds', map);
				
				// autocomplete event place_changed is triggered each time the input changes
				// customize the place object with the current "search value" to allow users controll over the address text
				google.maps.event.addListener(autocomplete, 'place_changed', function() {
					var place = this.getPlace();
					place.address = field.getSearchVal();
				    field.setPlace( place );
				});
	        }
	        
	        // click
	        google.maps.event.addListener( map, 'click', function( e ) {
				// vars
				var lat = e.latLng.lat();
				var lng = e.latLng.lng();
				
				 // search
				field.searchPosition( lat, lng );
			});
		},
		
		addMarkerEvents: function( marker, field ){
			
			// dragend
		    google.maps.event.addListener( marker, 'dragend', function(){
		    	// vars
				var position = this.getPosition();
				var lat = position.lat();
			    var lng = position.lng();
			    
			    // search
				field.searchPosition( lat, lng );
			});
		},
		
		searchPosition: function( lat, lng ){
			
			// vars
			var latLng = this.newLatLng( lat, lng );
			var $wrap = this.$control();
			
			// set position
			this.setPosition( lat, lng );
			
			// add class
		    $wrap.addClass('-loading');
		    
		    // callback
		    var callback = $.proxy(function( results, status ){
			    
			    // remove class
			    $wrap.removeClass('-loading');
			    
			    // vars
			    var address = '';
			    
			    // validate
				if( status != google.maps.GeocoderStatus.OK ) {
					console.log('Geocoder failed due to: ' + status);
				} else if( !results[0] ) {
					console.log('No results found');
				} else {
					address = results[0].formatted_address;
				}
				
				// update val
				this.val({
					lat: lat,
					lng: lng,
					address: address
				});
				
		    }, this);
		    
		    // query
		    api.geocoder.geocode({ 'latLng' : latLng }, callback);
		},
		
		setPlace: function( place ){
			
			// bail if no place
			if( !place ) return this;
			
			// search name if no geometry
			// - possible when hitting enter in search address
			if( place.name && !place.geometry ) {
				this.searchAddress(place.name);
				return this;
			}
			
			// vars
			var lat = place.geometry.location.lat();
			var lng = place.geometry.location.lng();
			var address = place.address || place.formatted_address;
			
			// update
			this.setValue({
				lat: lat,
				lng: lng,
				address: address
			});
			
		    // return
		    return this;
		},
		
		searchAddress: function( address ){
			
		    // is address latLng?
		    var latLng = address.split(',');
		    if( latLng.length == 2 ) {
			    
			    // vars
			    var lat = latLng[0];
				var lng = latLng[1];
			    
				// check
			    if( $.isNumeric(lat) && $.isNumeric(lng) ) {
				    return this.searchPosition( lat, lng );
			    }
		    }
		    
		    // vars
		    var $wrap = this.$control();
		    
		    // add class
		    $wrap.addClass('-loading');
		    
		    // callback
		    var callback = this.proxy(function( results, status ){
			    
			    // remove class
			    $wrap.removeClass('-loading');
			    
			    // vars
			    var lat = '';
			    var lng = '';
			    
			    // validate
				if( status != google.maps.GeocoderStatus.OK ) {
					console.log('Geocoder failed due to: ' + status);
				} else if( !results[0] ) {
					console.log('No results found');
				} else {
					lat = results[0].geometry.location.lat();
					lng = results[0].geometry.location.lng();
					//address = results[0].formatted_address;
				}
				
				// update val
				this.val({
					lat: lat,
					lng: lng,
					address: address
				});
				
				//pdc.doAction('google_map_geocode_results', results, status, this.$el, this);
				
		    });
		    
		    // query
		    api.geocoder.geocode({ 'address' : address }, callback);
		},
		
		searchLocation: function(){
			
			// Try HTML5 geolocation
			if( !navigator.geolocation ) {
				return alert( pdc.__('Sorry, this browser does not support geolocation') );
			}
			
			// vars
		    var $wrap = this.$control();
			
			// add class
		    $wrap.addClass('-loading');
		    
		    // callback
		    var onSuccess = $.proxy(function( results, status ){
			    
			    // remove class
			    $wrap.removeClass('-loading');
			    
			    // vars
				var lat = results.coords.latitude;
			    var lng = results.coords.longitude;
			    
			    // search;
			    this.searchPosition( lat, lng );
				
		    }, this);
		    
		    var onFailure = function( error ){
			    $wrap.removeClass('-loading');
		    }
		    
		    // try query
			navigator.geolocation.getCurrentPosition( onSuccess, onFailure );
		},
		
		onClickClear: function( e, $el ){
			this.val( false );
		},
		
		onClickLocate: function( e, $el ){
			this.searchLocation();
		},
		
		onClickSearch: function( e, $el ){
			this.searchAddress( this.$search().val() );
		},
		
		onFocusSearch: function( e, $el ){
			this.removeClass('-value');
			this.onKeyupSearch.apply(this, arguments);
		},
		
		onBlurSearch: function( e, $el ){
			
			// timeout to allow onClickLocate event
			this.setTimeout(function(){
				this.removeClass('-search');
				if( $el.val() ) {
					this.addClass('-value');
				}
			}, 100);			
		},
		
		onKeyupSearch: function( e, $el ){
			if( $el.val() ) {
				this.addClass('-search');
			} else {
				this.removeClass('-search');
			}
		},
		
		onKeydownSearch: function( e, $el ){
			
			// prevent form from submitting
			if( e.which == 13 ) {
				e.preventDefault();
			}
		},
		
		onMousedown: function(){
			
/*
			// clear timeout in 1ms (onMousedown will run before onBlurSearch)
			this.setTimeout(function(){
				clearTimeout( this.get('timeout') );
			}, 1);
*/
		},
		
		onShow: function(){
			
			// bail early if no map
			// - possible if JS API was not loaded
			if( !this.map ) {
				return false;
			}
			
			// center map when it is shown (by a tab / collapsed row)
			// - use delay to avoid rendering issues with browsers (ensures div is visible)
			this.setTimeout( this.center, 10 );
		}
	});
	
	pdc.registerFieldType( Field );
	
	var api = new pdc.Model({
		
		geocoder: false,
		
		data: {
			status: false,
		},
		
		getStatus: function(){
			return this.get('status');
		},
		
		setStatus: function( status ){
			return this.set('status', status);
		},
		
		isReady: function(){
			
			// loaded
			if( this.getStatus() == 'ready' ) {
				return true;
			}
			
			// loading
			if( this.getStatus() == 'loading' ) {
				return false;
			}
			
			// check exists (optimal)
			if( pdc.isset(window, 'google', 'maps', 'places') ) {
				this.setStatus('ready');
				return true;
			}
			
			// load api
			var url = pdc.get('google_map_api');
			if( url ) {
				this.setStatus('loading');
				
				// enqueue
				$.ajax({
					url: url,
					dataType: 'script',
					cache: true,
					context: this,
					success: function(){
						
						// ready
						this.setStatus('ready');
						
						// geocoder
						this.geocoder = new google.maps.Geocoder();
						
						// action						
						pdc.doAction('google_map_api_loaded');
					}
				});
			}
			
			// return
			return false;
		},
		
		ready: function( callback, context ){
			pdc.addAction('google_map_api_loaded', callback, 10, context);
		}
	});
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'image',
		
		$control: function(){
			return this.$('.pdc-image-uploader');
		},
		
		$input: function(){
			return this.$('input[type="hidden"]');
		},
		
		events: {
			'click a[data-name="add"]': 	'onClickAdd',
			'click a[data-name="edit"]': 	'onClickEdit',
			'click a[data-name="remove"]':	'onClickRemove',
			'change input[type="file"]':	'onChange'
		},
		
		initialize: function(){
			
			// add attribute to form
			if( this.get('uploader') === 'basic' ) {
				this.$el.closest('form').attr('enctype', 'multipart/form-data');
			}
		},
		
		validateAttachment: function( attachment ){
			
			// defaults
			attachment = attachment || {};
			
			// WP attachment
			if( attachment.id !== undefined ) {
				attachment = attachment.attributes;
			}
			
			// args
			attachment = pdc.parseArgs(attachment, {
				url: '',
				alt: '',
				title: '',
				caption: '',
				description: '',
				width: 0,
				height: 0
			});
			
			// preview size
			var url = pdc.isget(attachment, 'sizes', this.get('preview_size'), 'url');
			if( url !== null ) {
				attachment.url = url;
			}
			
			// return
			return attachment;
		},
		
		render: function( attachment ){
			
			// vars
			attachment = this.validateAttachment( attachment );
			
			// update image
		 	this.$('img').attr({
			 	src: attachment.url,
			 	alt: attachment.alt,
			 	title: attachment.title
		 	});
		 	
			// vars
			var val = attachment.id || '';
						
			// update val
			this.val( val );
		 	
		 	// update class
		 	if( val ) {
			 	this.$control().addClass('has-value');
		 	} else {
			 	this.$control().removeClass('has-value');
		 	}
		},
		
		// create a new repeater row and render value
		append: function( attachment, parent ){
			
			// create function to find next available field within parent
			var getNext = function( field, parent ){
				
				// find existing file fields within parent
				var fields = pdc.getFields({
					key: 	field.get('key'),
					parent: parent.$el
				});
				
				// find the first field with no value
				for( var i = 0; i < fields.length; i++ ) {
					if( !fields[i].val() ) {
						return fields[i];
					}
				}
								
				// return
				return false;
			}
			
			// find existing file fields within parent
			var field = getNext( this, parent );
			
			// add new row if no available field
			if( !field ) {
				parent.$('.pdc-button:last').trigger('click');
				field = getNext( this, parent );
			}
					
			// render
			if( field ) {
				field.render( attachment );
			}
		},
		
		selectAttachment: function(){
			
			// vars
			var parent = this.parent();
			var multiple = (parent && parent.get('type') === 'repeater');
			
			// new frame
			var frame = pdc.newMediaPopup({
				mode:			'select',
				type:			'image',
				title:			pdc.__('Select Image'),
				field:			this.get('key'),
				multiple:		multiple,
				library:		this.get('library'),
				allowedTypes:	this.get('mime_types'),
				select:			$.proxy(function( attachment, i ) {
					if( i > 0 ) {
						this.append( attachment, parent );
					} else {
						this.render( attachment );
					}
				}, this)
			});
		},
		
		editAttachment: function(){
			
			// vars
			var val = this.val();
			
			// bail early if no val
			if( !val ) return;
			
			// popup
			var frame = pdc.newMediaPopup({
				mode:		'edit',
				title:		pdc.__('Edit Image'),
				button:		pdc.__('Update Image'),
				attachment:	val,
				field:		this.get('key'),
				select:		$.proxy(function( attachment, i ) {
					this.render( attachment );
				}, this)
			});
		},
		
		removeAttachment: function(){
	        this.render( false );
		},
		
		onClickAdd: function( e, $el ){
			this.selectAttachment();
		},
		
		onClickEdit: function( e, $el ){
			this.editAttachment();
		},
		
		onClickRemove: function( e, $el ){
			this.removeAttachment();
		},
		
		onChange: function( e, $el ){
			var $hiddenInput = this.$input();
			
			pdc.getFileInputData($el, function( data ){
				$hiddenInput.val( $.param(data) );
			});
		}
	});
	
	pdc.registerFieldType( Field );

})(jQuery);

(function($, undefined){
	
	var Field = pdc.models.ImageField.extend({
		
		type: 'file',
		
		$control: function(){
			return this.$('.pdc-file-uploader');
		},
		
		$input: function(){
			return this.$('input[type="hidden"]');
		},
		
		validateAttachment: function( attachment ){
			
			// defaults
			attachment = attachment || {};
			
			// WP attachment
			if( attachment.id !== undefined ) {
				attachment = attachment.attributes;
			}
			
			// args
			attachment = pdc.parseArgs(attachment, {
				url: '',
				alt: '',
				title: '',
				filename: '',
				filesizeHumanReadable: '',
				icon: '/wp-includes/images/media/default.png'
			});
						
			// return
			return attachment;
		},
		
		render: function( attachment ){
			
			// vars
			attachment = this.validateAttachment( attachment );
			
			// update image
		 	this.$('img').attr({
			 	src: attachment.icon,
			 	alt: attachment.alt,
			 	title: attachment.title
		 	});
		 	
		 	// update elements
		 	this.$('[data-name="title"]').text( attachment.title );
		 	this.$('[data-name="filename"]').text( attachment.filename ).attr( 'href', attachment.url );
		 	this.$('[data-name="filesize"]').text( attachment.filesizeHumanReadable );
		 	
			// vars
			var val = attachment.id || '';
						
			// update val
		 	pdc.val( this.$input(), val );
		 	
		 	// update class
		 	if( val ) {
			 	this.$control().addClass('has-value');
		 	} else {
			 	this.$control().removeClass('has-value');
		 	}
		},
		
		selectAttachment: function(){
			
			// vars
			var parent = this.parent();
			var multiple = (parent && parent.get('type') === 'repeater');
			
			// new frame
			var frame = pdc.newMediaPopup({
				mode:			'select',
				title:			pdc.__('Select File'),
				field:			this.get('key'),
				multiple:		multiple,
				library:		this.get('library'),
				allowedTypes:	this.get('mime_types'),
				select:			$.proxy(function( attachment, i ) {
					if( i > 0 ) {
						this.append( attachment, parent );
					} else {
						this.render( attachment );
					}
				}, this)
			});
		},
		
		editAttachment: function(){
			
			// vars
			var val = this.val();
			
			// bail early if no val
			if( !val ) {
				return false;
			}
			
			// popup
			var frame = pdc.newMediaPopup({
				mode:		'edit',
				title:		pdc.__('Edit File'),
				button:		pdc.__('Update File'),
				attachment:	val,
				field:		this.get('key'),
				select:		$.proxy(function( attachment, i ) {
					this.render( attachment );
				}, this)
			});
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'link',
		
		events: {
			'click a[data-name="add"]': 	'onClickEdit',
			'click a[data-name="edit"]': 	'onClickEdit',
			'click a[data-name="remove"]':	'onClickRemove',
			'change .link-node':			'onChange',
		},
		
		$control: function(){
			return this.$('.pdc-link');
		},
		
		$node: function(){
			return this.$('.link-node');
		},
		
		getValue: function(){
			
			// vars
			var $node = this.$node();
			
			// return false if empty
			if( !$node.attr('href') ) {
				return false;
			}
			
			// return
			return {
				title:	$node.html(),
				url:	$node.attr('href'),
				target:	$node.attr('target')
			};
		},
		
		setValue: function( val ){
			
			// default
			val = pdc.parseArgs(val, {
				title:	'',
				url:	'',
				target:	''
			});
			
			// vars
			var $div = this.$control();
			var $node = this.$node();
			
			// remove class
			$div.removeClass('-value -external');
			
			// add class
			if( val.url ) $div.addClass('-value');
			if( val.target === '_blank' ) $div.addClass('-external');
			
			// update text
			this.$('.link-title').html( val.title );
			this.$('.link-url').attr('href', val.url).html( val.url );
			
			// update node
			$node.html(val.title);
			$node.attr('href', val.url);
			$node.attr('target', val.target);
			
			// update inputs
			this.$('.input-title').val( val.title );
			this.$('.input-target').val( val.target );
			this.$('.input-url').val( val.url ).trigger('change');
		},
		
		onClickEdit: function( e, $el ){
			pdc.wpLink.open( this.$node() );
		},
		
		onClickRemove: function( e, $el ){
			this.setValue( false );
		},
		
		onChange: function( e, $el ){
			
			// get the changed value
			var val = this.getValue();
			
			// update inputs
			this.setValue(val);
		}
		
	});
	
	pdc.registerFieldType( Field );
	
	
	// manager
	pdc.wpLink = new pdc.Model({
		
		getNodeValue: function(){
			var $node = this.get('node');
			return {
				title:	$node.html(),
				url:	$node.attr('href'),
				target:	$node.attr('target')
			};
		},
		
		setNodeValue: function( val ){
			var $node = this.get('node');
			$node.html( val.title );
			$node.attr('href', val.url);
			$node.attr('target', val.target);
			$node.trigger('change');
		},
		
		getInputValue: function(){
			return {
				title:	$('#wp-link-text').val(),
				url:	$('#wp-link-url').val(),
				target:	$('#wp-link-target').prop('checked') ? '_blank' : ''
			};
		},
		
		setInputValue: function( val ){
			$('#wp-link-text').val( val.title );
			$('#wp-link-url').val( val.url );
			$('#wp-link-target').prop('checked', val.target === '_blank' );
		},
		
		open: function( $node ){

			// add events
			this.on('wplink-open', 'onOpen');
			this.on('wplink-close', 'onClose');
			
			// set node
			this.set('node', $node);
			
			// create textarea
			var $textarea = $('<textarea id="pdc-link-textarea" style="display:none;"></textarea>');
			$('body').append( $textarea );
			
			// vars
			var val = this.getNodeValue();
			
			// open popup
			wpLink.open( 'pdc-link-textarea', val.url, val.title, null );
			
		},
		
		onOpen: function(){

			// always show title (WP will hide title if empty)
			$('#wp-link-wrap').addClass('has-text-field');
			
			// set inputs
			var val = this.getNodeValue();
			this.setInputValue( val );
		},
		
		close: function(){
			wpLink.close();
		},
		
		onClose: function(){
			
			// bail early if no node
			// needed due to WP triggering this event twice
			if( !this.has('node') ) {
				return false;
			}
			
			// remove events
			this.off('wplink-open');
			this.off('wplink-close');
			
			// set value
			var val = this.getInputValue();
			this.setNodeValue( val );
			
			// remove textarea
			$('#pdc-link-textarea').remove();
			
			// reset
			this.set('node', null);
			
		}
	});	

})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'oembed',
		
		events: {
			'click [data-name="clear-button"]': 	'onClickClear',
			'keypress .input-search':				'onKeypressSearch',
			'keyup .input-search':					'onKeyupSearch',
			'change .input-search':					'onChangeSearch'
		},
		
		$control: function(){
			return this.$('.pdc-oembed');
		},
		
		$input: function(){
			return this.$('.input-value');
		},
		
		$search: function(){
			return this.$('.input-search');
		},
		
		getValue: function(){
			return this.$input().val();
		},
		
		getSearchVal: function(){
			return this.$search().val();
		},
		
		setValue: function( val ){
			
			// class
			if( val ) {
				this.$control().addClass('has-value');
			} else {
				this.$control().removeClass('has-value');
			}
			
			pdc.val( this.$input(), val );
		},
		
		showLoading: function( show ){
			pdc.showLoading( this.$('.canvas') );	
		},
		
		hideLoading: function(){
			pdc.hideLoading( this.$('.canvas') );	
		},
		
		maybeSearch: function(){
			
			// vars
			var prevUrl = this.val();
			var url = this.getSearchVal();
			
			 // no value
	        if( !url ) {
		    	return this.clear();
	        }
	        
			// fix missing 'http://' - causes the oembed code to error and fail
			if( url.substr(0, 4) != 'http' ) {
				url = 'http://' + url;
			}
			
	        // bail early if no change
	        if( url === prevUrl ) return;
	        
	        // clear existing timeout
	        var timeout = this.get('timeout');
	        if( timeout ) {
		        clearTimeout( timeout );
	        }
	        
	        // set new timeout
	        var callback = $.proxy(this.search, this, url);
	        this.set('timeout', setTimeout(callback, 300));
	        
		},
		
		search: function( url ){
			
			// ajax
			var ajaxData = {
				action:		'pdc/fields/oembed/search',
				s: 			url,
				field_key:	this.get('key')
			};
			
			// clear existing timeout
	        var xhr = this.get('xhr');
	        if( xhr ) {
		        xhr.abort();
	        }
	        
	        // loading
	        this.showLoading();
				
			// query
			var xhr = $.ajax({
				url: pdc.get('ajaxurl'),
				data: pdc.prepareForAjax(ajaxData),
				type: 'post',
				dataType: 'json',
				context: this,
				success: function( json ){
					
					// error
					if( !json || !json.html ) {
						json = {
							url: false,
							html: ''
						}
					}
					
					// update vars
					this.val( json.url );
					this.$('.canvas-media').html( json.html );
				},
				complete: function(){
					this.hideLoading();
				}
			});
			
			this.set('xhr', xhr);
		},
		
		clear: function(){
			this.val('');
			this.$search().val('');
			this.$('.canvas-media').html('');
		},
		
		onClickClear: function( e, $el ){
			this.clear();
		},
		
		onKeypressSearch: function( e, $el ){
			if( e.which == 13 ) {
				e.preventDefault();
				this.maybeSearch();
			}
		},
		
		onKeyupSearch: function( e, $el ){
			if( $el.val() ) {
				this.maybeSearch();
			}
		},
		
		onChangeSearch: function( e, $el ){
			this.maybeSearch();
		}
		
	});
	
	pdc.registerFieldType( Field );

})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'radio',
		
		events: {
			'click input[type="radio"]': 'onClick',
		},
		
		$control: function(){
			return this.$('.pdc-radio-list');
		},
		
		$input: function(){
			return this.$('input:checked');
		},
		
		$inputText: function(){
			return this.$('input[type="text"]');
		},
		
		getValue: function(){
			var val = this.$input().val();
			if( val === 'other' && this.get('other_choice') ) {
				val = this.$inputText().val();
			}
			return val;
		},
		
		onClick: function( e, $el ){
			
			// vars
			var $label = $el.parent('label');
			var selected = $label.hasClass('selected');
			var val = $el.val();
			
			// remove previous selected
			this.$('.selected').removeClass('selected');
			
			// add active class
			$label.addClass('selected');
			
			// allow null
			if( this.get('allow_null') && selected ) {
				$label.removeClass('selected');
				$el.prop('checked', false).trigger('change');
				val = false;
			}
			
			// other
			if( this.get('other_choice') ) {
				
				// enable
				if( val === 'other' ) {
					this.$inputText().prop('disabled', false);
					
				// disable
				} else {
					this.$inputText().prop('disabled', true);
				}
			}
		}
	});
	
	pdc.registerFieldType( Field );

})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'range',
		
		events: {
			'input input[type="range"]': 'onChange',
			'change input': 'onChange'
		},
		
		$input: function(){
			return this.$('input[type="range"]');
		},
		
		$inputAlt: function(){
			return this.$('input[type="number"]');
		},
		
		setValue: function( val ){
			
			this.busy = true;
			
			// update range input (with change)
			pdc.val( this.$input(), val );
			
			// update alt input (without change)
			pdc.val( this.$inputAlt(), val, true );
			
			this.busy = false;
		},
		
		onChange: function( e, $el ){
			if( !this.busy ) {
				this.setValue( $el.val() );
			}
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'relationship',
		
		events: {
			'keypress [data-filter]': 				'onKeypressFilter',
			'change [data-filter]': 				'onChangeFilter',
			'keyup [data-filter]': 					'onChangeFilter',
			'click .choices-list .pdc-rel-item': 	'onClickAdd',
			'click [data-name="remove_item"]': 		'onClickRemove',
			'mouseover': 							'onHover'
		},
		
		$control: function(){
			return this.$('.pdc-relationship');
		},
		
		$list: function( list ) {
			return this.$('.' + list + '-list');
		},
		
		$listItems: function( list ) {
			return this.$list( list ).find('.pdc-rel-item');
		},
		
		$listItem: function( list, id ) {
			return this.$list( list ).find('.pdc-rel-item[data-id="' + id + '"]');
		},
		
		getValue: function(){
			var val = [];
			this.$listItems('values').each(function(){
				val.push( $(this).data('id') );
			});
			return val.length ? val : false;
		},
		
		newChoice: function( props ){
			return [
			'<li>',
				'<span data-id="' + props.id + '" class="pdc-rel-item">' + props.text + '</span>',
			'</li>'
			].join('');
		},
		
		newValue: function( props ){
			return [
			'<li>',
				'<input type="hidden" name="' + this.getInputName() + '[]" value="' + props.id + '" />',
				'<span data-id="' + props.id + '" class="pdc-rel-item">' + props.text,
					'<a href="#" class="pdc-icon -minus small dark" data-name="remove_item"></a>',
				'</span>',
			'</li>'
			].join('');
		},
		
		addSortable: function( self ){
			
			// sortable
			this.$list('values').sortable({
				items:					'li',
				forceHelperSize:		true,
				forcePlaceholderSize:	true,
				scroll:					true,
				update:	function(){
					self.$input().trigger('change');
				}
			});
		},
		
		initialize: function(){
			
			// scroll
			var onScroll = this.proxy(function(e){
				
				// bail early if no more results
				if( this.get('loading') || !this.get('more') ) {
					return;	
				}
				
				// Scrolled to bottom
				var $list = this.$list('choices');
				var scrollTop = Math.ceil( $list.scrollTop() );
				var scrollHeight = Math.ceil( $list[0].scrollHeight );
				var innerHeight = Math.ceil( $list.innerHeight() );
				var paged = this.get('paged') || 1;
				if( (scrollTop + innerHeight) >= scrollHeight ) {
					
					// update paged
					this.set('paged', (paged+1));
					
					// fetch
					this.fetch();
				}
				
			});
			
			this.$list('choices').scrollTop(0).on('scroll', onScroll);
			
			// fetch
			this.fetch();
		},
		
		onHover: function( e ){
			
			// only once
			$().off(e);
			
			// add sortable
			this.addSortable( this );
		},
		
		onKeypressFilter: function( e, $el ){
			
			// don't submit form
			if( e.which == 13 ) {
				e.preventDefault();
			}
		},
		
		onChangeFilter: function( e, $el ){
			
			// vars
			var val = $el.val();
			var filter = $el.data('filter');
				
			// Bail early if filter has not changed
			if( this.get(filter) === val ) {
				return;
			}
			
			// update attr
			this.set(filter, val);
			
			// reset paged
			this.set('paged', 1);
			
		    // fetch
		    if( $el.is('select') ) {
				this.fetch();
			
			// search must go through timeout
		    } else {
			    this.maybeFetch();
		    }
		},
		
		onClickAdd: function( e, $el ){
			
			// vars
			var val = this.val();
			var max = parseInt( this.get('max') );
			
			// can be added?
			if( $el.hasClass('disabled') ) {
				return false;
			}
			
			// validate
			if( max > 0 && val && val.length >= max ) {
				
				// add notice
				this.showNotice({
					text: pdc.__('Maximum values reached ( {max} values )').replace('{max}', max),
					type: 'warning'
				});
				return false;
			}
			
			// disable
			$el.addClass('disabled');
			
			// add
			var html = this.newValue({
				id: $el.data('id'),
				text: $el.html()
			});
			this.$list('values').append( html )
			
			// trigger change
			this.$input().trigger('change');
		},
		
		onClickRemove: function( e, $el ){
			
			// vars
			var $span = $el.parent();
			var $li = $span.parent();
			var id = $span.data('id');
			
			// remove value
			setTimeout(function(){
				$li.remove();
			}, 1);
			
			// show choice
			this.$listItem('choices', id).removeClass('disabled');
			
			// trigger change
			this.$input().trigger('change');
		},
		
		maybeFetch: function(){
			
			// vars
			var timeout = this.get('timeout');
			
			// abort timeout
			if( timeout ) {
				clearTimeout( timeout );
			}
			
		    // fetch
		    timeout = this.setTimeout(this.fetch, 300);
		    this.set('timeout', timeout);
		},
		
		getAjaxData: function(){
			
			// load data based on element attributes
			var ajaxData = this.$control().data();
			for( var name in ajaxData ) {
				ajaxData[ name ] = this.get( name );
			}
			
			// extra
			ajaxData.action = 'pdc/fields/relationship/query';
			ajaxData.field_key = this.get('key');
			
			// return
			return ajaxData;
		},
		
		fetch: function(){
			
			// abort XHR if this field is already loading AJAX data
			var xhr = this.get('xhr');
			if( xhr ) {
				xhr.abort();
			}
			
			// add to this.o
			var ajaxData = this.getAjaxData();
			
			// clear html if is new query
			var $choiceslist = this.$list( 'choices' );
			if( ajaxData.paged == 1 ) {
				$choiceslist.html('');
			}
			
			// loading
			var $loading = $('<li><i class="pdc-loading"></i> ' + pdc.__('Loading') + '</li>');
			$choiceslist.append($loading);
			this.set('loading', true);
			
			// callback
			var onComplete = function(){
				this.set('loading', false);
				$loading.remove();
			};
			
			var onSuccess = function( json ){
				
				// no results
				if( !json || !json.results || !json.results.length ) {
					
					// prevent pagination
					this.set('more', false);
				
					// add message
					if( this.get('paged') == 1 ) {
						this.$list('choices').append('<li>' + pdc.__('No matches found') + '</li>');
					}
	
					// return
					return;
				}
				
				// set more (allows pagination scroll)
				this.set('more', json.more );
				
				// get new results
				var html = this.walkChoices(json.results);
				var $html = $( html );
				
				// apply .disabled to left li's
				var val = this.val();
				if( val && val.length ) {
					val.map(function( id ){
						$html.find('.pdc-rel-item[data-id="' + id + '"]').addClass('disabled');
					});
				}
				
				// append
				$choiceslist.append( $html );
				
				// merge together groups
				var $prevLabel = false;
				var $prevList = false;
					
				$choiceslist.find('.pdc-rel-label').each(function(){
					
					var $label = $(this);
					var $list = $label.siblings('ul');
					
					if( $prevLabel && $prevLabel.text() == $label.text() ) {
						$prevList.append( $list.children() );
						$(this).parent().remove();
						return;
					}
					
					// update vars
					$prevLabel = $label;
					$prevList = $list;
				});
			};
			
			// get results
		    var xhr = $.ajax({
		    	url:		pdc.get('ajaxurl'),
				dataType:	'json',
				type:		'post',
				data:		pdc.prepareForAjax(ajaxData),
				context:	this,
				success:	onSuccess,
				complete:	onComplete
			});
			
			// set
			this.set('xhr', xhr);
		},
		
		walkChoices: function( data ){
			
			// walker
			var walk = function( data ){
				
				// vars
				var html = '';
				
				// is array
				if( $.isArray(data) ) {
					data.map(function(item){
						html += walk( item );
					});
					
				// is item
				} else if( $.isPlainObject(data) ) {
					
					// group
					if( data.children !== undefined ) {
						
						html += '<li><span class="pdc-rel-label">' + data.text + '</span><ul class="pdc-bl">';
						html += walk( data.children );
						html += '</ul></li>';
					
					// single
					} else {
						html += '<li><span class="pdc-rel-item" data-id="' + data.id + '">' + data.text + '</span></li>';
					}
				}
				
				// return
				return html;
			};
			
			return walk( data );
		}
		
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'select',
		
		select2: false,
		
		wait: 'load',
		
		events: {
			'removeField': 'onRemove'
		},
		
		$input: function(){
			return this.$('select');
		},
		
		initialize: function(){
			
			// vars
			var $select = this.$input();
			
			// inherit data
			this.inherit( $select );
			
			// select2
			if( this.get('ui') ) {
				
				// populate ajax_data (allowing custom attribute to already exist)
				var ajaxAction = this.get('ajax_action');
				if( !ajaxAction ) {
					ajaxAction = 'pdc/fields/' + this.get('type') + '/query';
				}
				
				// select2
				this.select2 = pdc.newSelect2($select, {
					field: this,
					ajax: this.get('ajax'),
					multiple: this.get('multiple'),
					placeholder: this.get('placeholder'),
					allowNull: this.get('allow_null'),
					ajaxAction: ajaxAction,
				});
			}
		},
		
		onRemove: function(){
			if( this.select2 ) {
				this.select2.destroy();
			}
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	// vars
	var CONTEXT = 'tab';
	
	var Field = pdc.Field.extend({
		
		type: 'tab',
		
		wait: '',
		
		tabs: false,
		
		tab: false,
		
		findFields: function(){
			return this.$el.nextUntil('.pdc-field-tab', '.pdc-field');
		},
		
		getFields: function(){
			return pdc.getFields( this.findFields() );
		},
		
		findTabs: function(){
			return this.$el.prevAll('.pdc-tab-wrap:first');
		},
		
		findTab: function(){
			return this.$('.pdc-tab-button');
		},
		
		initialize: function(){
			
			// bail early if is td
			if( this.$el.is('td') ) {
				this.events = {};
				return false;
			}
			
			// vars
			var $tabs = this.findTabs();
			var $tab = this.findTab();
			var settings = pdc.parseArgs($tab.data(), {
				endpoint: false,
				placement: '',
				before: this.$el
			});
			
			// create wrap
			if( !$tabs.length || settings.endpoint ) {
				this.tabs = new Tabs( settings );
			} else {
				this.tabs = $tabs.data('pdc');
			}
			
			// add tab
			this.tab = this.tabs.addTab($tab, this);
		},
		
		isActive: function(){
			return this.tab.isActive();
		},
		
		showFields: function(){
			
			// show fields
			this.getFields().map(function( field ){
				field.show( this.cid, CONTEXT );
				field.hiddenByTab = false;		
			}, this);
			
		},
		
		hideFields: function(){
			
			// hide fields
			this.getFields().map(function( field ){
				field.hide( this.cid, CONTEXT );
				field.hiddenByTab = this.tab;		
			}, this);
			
		},
		
		show: function( lockKey ){

			// show field and store result
			var visible = pdc.Field.prototype.show.apply(this, arguments);
			
			// check if now visible
			if( visible ) {
				
				// show tab
				this.tab.show();
				
				// check active tabs
				this.tabs.refresh();
			}
						
			// return
			return visible;
		},
		
		hide: function( lockKey ){

			// hide field and store result
			var hidden = pdc.Field.prototype.hide.apply(this, arguments);
			
			// check if now hidden
			if( hidden ) {
				
				// hide tab
				this.tab.hide();
				
				// reset tabs if this was active
				if( this.isActive() ) {
					this.tabs.reset();
				}
			}
						
			// return
			return hidden;
		},
		
		enable: function( lockKey ){

			// enable fields
			this.getFields().map(function( field ){
				field.enable( CONTEXT );		
			});
		},
		
		disable: function( lockKey ){
			
			// disable fields
			this.getFields().map(function( field ){
				field.disable( CONTEXT );		
			});
		}
	});
	
	pdc.registerFieldType( Field );
	
	
	/**
	*  tabs
	*
	*  description
	*
	*  @date	8/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var i = 0;
	var Tabs = pdc.Model.extend({
		
		tabs: [],
		
		active: false,
		
		actions: {
			'refresh': 'onRefresh'
		},
		
		data: {
			before: false,
			placement: 'top',
			index: 0,
			initialized: false,
		},
		
		setup: function( settings ){
			
			// data
			$.extend(this.data, settings);
			
			// define this prop to avoid scope issues
			this.tabs = [];
			this.active = false;
			
			// vars
			var placement = this.get('placement');
			var $before = this.get('before');
			var $parent = $before.parent();
			
			// add sidebar for left placement
			if( placement == 'left' && $parent.hasClass('pdc-fields') ) {
				$parent.addClass('-sidebar');
			}
			
			// create wrap
			if( $before.is('tr') ) {
				this.$el = $('<tr class="pdc-tab-wrap"><td colspan="2"><ul class="pdc-hl pdc-tab-group"></ul></td></tr>');
			} else {
				this.$el = $('<div class="pdc-tab-wrap -' + placement + '"><ul class="pdc-hl pdc-tab-group"></ul></div>');
			}
			
			// append
			$before.before( this.$el );
			
			// set index
			this.set('index', i, true);
			i++;
		},
		
		initializeTabs: function(){
			
			// find first visible tab
			var tab = this.getVisible().shift();
			
			// remember previous tab state
			var order = pdc.getPreference('this.tabs') || [];
			var groupIndex = this.get('index');
			var tabIndex = order[ groupIndex ];
			
			if( this.tabs[ tabIndex ] && this.tabs[ tabIndex ].isVisible() ) {
				tab = this.tabs[ tabIndex ];
			}
			
			// select
			if( tab ) {
				this.selectTab( tab );
			} else {
				this.closeTabs();
			}
			
			// set local variable used by tabsManager
			this.set('initialized', true);
		},
		
		getVisible: function(){
			return this.tabs.filter(function( tab ){
				return tab.isVisible();
			});
		},
		
		getActive: function(){
			return this.active;
		},
		
		setActive: function( tab ){
			return this.active = tab;
		},
		
		hasActive: function(){
			return (this.active !== false);
		},
		
		isActive: function( tab ){
			var active = this.getActive();
			return (active && active.cid === tab.cid);
		},
		
		closeActive: function(){
			if( this.hasActive() ) {
				this.closeTab( this.getActive() );
			}
		},
		
		openTab: function( tab ){
			
			// close existing tab
			this.closeActive();
			
			// open
			tab.open();
			
			// set active
			this.setActive( tab );
		},
		
		closeTab: function( tab ){
			
			// close
			tab.close();
			
			// set active
			this.setActive( false );
		},
		
		closeTabs: function(){
			this.tabs.map( this.closeTab, this );
		},
		
		selectTab: function( tab ){
			
			// close other tabs
			this.tabs.map(function( t ){
				if( tab.cid !== t.cid ) {
					this.closeTab( t );
				}
			}, this);
			
			// open
			this.openTab( tab );
			
		},
		
		addTab: function( $a, field ){
			
			// create <li>
			var $li = $('<li></li>');
			
			// append <a>
			$li.append( $a );
			
			// append
			this.$('ul').append( $li );
			
			// initialize
			var tab = new Tab({
				$el: $li,
				field: field,
				group: this,
			});
			
			// store
			this.tabs.push( tab );
			
			// return
			return tab;
		},
		
		reset: function(){
			
			// close existing tab
			this.closeActive();
			
			// find and active a tab
			return this.refresh();
		},
		
		refresh: function(){
			
			// bail early if active already exists
			if( this.hasActive() ) {
				return false;
			}
			
			// find next active tab
			var tab = this.getVisible().shift();
			
			// open tab
			if( tab ) {
				this.openTab( tab );
			}
			
			// return
			return tab;
		},
		
		onRefresh: function(){
			
			// only for left placements
			if( this.get('placement') !== 'left' ) {
				return;
			}
			
			// vars
			var $parent = this.$el.parent();
			var $list = this.$el.children('ul');
			var attribute = $parent.is('td') ? 'height' : 'min-height';
			
			// find height (minus 1 for border-bottom)
			var height = $list.position().top + $list.outerHeight(true) - 1;
			
			// add css
			$parent.css(attribute, height);
		}	
	});
	
	var Tab = pdc.Model.extend({
		
		group: false,
		
		field: false,
		
		events: {
			'click a': 'onClick'
		},
		
		index: function(){
			return this.$el.index();
		},
		
		isVisible: function(){
			return pdc.isVisible( this.$el );
		},
		
		isActive: function(){
			return this.$el.hasClass('active');
		},
		
		open: function(){
			
			// add class
			this.$el.addClass('active');
			
			// show field
			this.field.showFields();
		},
		
		close: function(){
			
			// remove class
			this.$el.removeClass('active');
			
			// hide field
			this.field.hideFields();
		},
		
		onClick: function( e, $el ){
			
			// prevent default
			e.preventDefault();
			
			// toggle
			this.toggle();
		},
		
		toggle: function(){
			
			// bail early if already active
			if( this.isActive() ) {
				return;
			}
			
			// toggle this tab
			this.group.openTab( this );
		}			
	});
	
	var tabsManager = new pdc.Model({
		
		priority: 50,
		
		actions: {
			'prepare':			'render',
			'append':			'render',
			'unload':			'onUnload',
			'invalid_field':	'onInvalidField'
		},
		
		findTabs: function(){
			return $('.pdc-tab-wrap');
		},
		
		getTabs: function(){
			return pdc.getInstances( this.findTabs() );
		},
		
		render: function( $el ){
			this.getTabs().map(function( tabs ){
				if( !tabs.get('initialized') ) {
					tabs.initializeTabs();
				}
			});
		},
		
		onInvalidField: function( field ){
			
			// bail early if busy
			if( this.busy ) {
				return;
			}
			
			// ignore if not hidden by tab
			if( !field.hiddenByTab ) {
				return;
			}
			
			// toggle tab
			field.hiddenByTab.toggle();
				
			// ignore other invalid fields
			this.busy = true;
			this.setTimeout(function(){
				this.busy = false;
			}, 100);
		},
		
		onUnload: function(){
			
			// vars
			var order = [];
			
			// loop
			this.getTabs().map(function( group ){
				var active = group.hasActive() ? group.getActive().index() : 0;
				order.push(active);
			});
			
			// bail if no tabs
			if( !order.length ) {
				return;
			}
			
			// update
			pdc.setPreference('this.tabs', order);
		}
	});
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.models.SelectField.extend({
		type: 'post_object',	
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.models.SelectField.extend({
		type: 'page_link',	
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.models.SelectField.extend({
		type: 'user',	
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'taxonomy',
		
		data: {
			'ftype': 'select'
		},
		
		select2: false,
		
		wait: 'load',
		
		events: {
			'click a[data-name="add"]': 'onClickAdd',
			'click input[type="radio"]': 'onClickRadio',
		},
		
		$control: function(){
			return this.$('.pdc-taxonomy-field');
		},
		
		$input: function(){
			return this.getRelatedPrototype().$input.apply(this, arguments);
		},
		
		getRelatedType: function(){
			
			// vars
			var fieldType = this.get('ftype');
			
			// normalize
			if( fieldType == 'multi_select' ) {
				fieldType = 'select';
			}

			// return
			return fieldType;
			
		},
		
		getRelatedPrototype: function(){
			return pdc.getFieldType( this.getRelatedType() ).prototype;
		},
		
		getValue: function(){
			return this.getRelatedPrototype().getValue.apply(this, arguments);
		},
		
		setValue: function(){
			return this.getRelatedPrototype().setValue.apply(this, arguments);
		},
		
		initialize: function(){
			this.getRelatedPrototype().initialize.apply(this, arguments);
		},
		
		onRemove: function(){
			if( this.select2 ) {
				this.select2.destroy();
			}
		},
		
		onClickAdd: function( e, $el ){
			
			// vars
			var field = this;
			var popup = false;
			var $form = false;
			var $name = false;
			var $parent = false;
			var $button = false;
			var $message = false;
			var notice = false;
			
			// step 1.
			var step1 = function(){
				
				// popup
				popup = pdc.newPopup({
					title: $el.attr('title'),
					loading: true,
					width: '300px'
				});
				
				// ajax
				var ajaxData = {
					action:		'pdc/fields/taxonomy/add_term',
					field_key:	field.get('key')
				};
				
				// get HTML
				$.ajax({
					url: pdc.get('ajaxurl'),
					data: pdc.prepareForAjax(ajaxData),
					type: 'post',
					dataType: 'html',
					success: step2
				});
			};
			
			// step 2.
			var step2 = function( html ){
				
				// update popup
				popup.loading(false);
				popup.content(html);
				
				// vars
				$form = popup.$('form');
				$name = popup.$('input[name="term_name"]');
				$parent = popup.$('select[name="term_parent"]');
				$button = popup.$('.pdc-submit-button');
				
				// focus
				$name.focus();
				
				// submit form
				popup.on('submit', 'form', step3);
			};
			
			// step 3.
			var step3 = function( e, $el ){
				
				// prevent
				e.preventDefault();
				e.stopImmediatePropagation();
				
				// basic validation
				if( $name.val() === '' ) {
					$name.focus();
					return false;
				}
				
				// disable
				pdc.startButtonLoading( $button );
				
				// ajax
				var ajaxData = {
					action: 		'pdc/fields/taxonomy/add_term',
					field_key:		field.get('key'),
					term_name:		$name.val(),
					term_parent:	$parent.length ? $parent.val() : 0
				};
				
				$.ajax({
					url: pdc.get('ajaxurl'),
					data: pdc.prepareForAjax(ajaxData),
					type: 'post',
					dataType: 'json',
					success: step4
				});
			};
			
			// step 4.
			var step4 = function( json ){
				
				// enable
				pdc.stopButtonLoading( $button );
				
				// remove prev notice
				if( notice ) {
					notice.remove();
				}
				
				// success
				if( pdc.isAjaxSuccess(json) ) {
					
					// clear name
					$name.val('');
					
					// update term lists
					step5( json.data );
					
					// notice
					notice = pdc.newNotice({
						type: 'success',
						text: pdc.getAjaxMessage(json),
						target: $form,
						timeout: 2000,
						dismiss: false
					});
					
				} else {
					
					// notice
					notice = pdc.newNotice({
						type: 'error',
						text: pdc.getAjaxError(json),
						target: $form,
						timeout: 2000,
						dismiss: false
					});
				}
				
				// focus
				$name.focus();
			};
			
			// step 5.
			var step5 = function( term ){
				
				// update parent dropdown
				var $option = $('<option value="' + term.term_id + '">' + term.term_label + '</option>');
				if( term.term_parent ) {
					$parent.children('option[value="' + term.term_parent + '"]').after( $option );
				} else {
					$parent.append( $option );
				}
				
				// add this new term to all taxonomy field
				var fields = pdc.getFields({
					type: 'taxonomy'
				});
				
				fields.map(function( otherField ){
					if( otherField.get('taxonomy') == field.get('taxonomy') ) {
						otherField.appendTerm( term );
					}
				});
				
				// select
				field.selectTerm( term.term_id );
			};
			
			// run
			step1();	
		},
		
		appendTerm: function( term ){
			
			if( this.getRelatedType() == 'select' ) {
				this.appendTermSelect( term );
			} else {
				this.appendTermCheckbox( term );
			}
		},
		
		appendTermSelect: function( term ){
			
			this.select2.addOption({
				id:			term.term_id,
				text:		term.term_label
			});
			
		},
		
		appendTermCheckbox: function( term ){
			
			// vars
			var name = this.$('[name]:first').attr('name');
			var $ul = this.$('ul:first');
			
			// allow multiple selection
			if( this.getRelatedType() == 'checkbox' ) {
				name += '[]';
			}
			
			// create new li
			var $li = $([
				'<li data-id="' + term.term_id + '">',
					'<label>',
						'<input type="' + this.get('ftype') + '" value="' + term.term_id + '" name="' + name + '" /> ',
						'<span>' + term.term_name + '</span>',
					'</label>',
				'</li>'
			].join(''));
			
			// find parent
			if( term.term_parent ) {
				
				// vars
				var $parent = $ul.find('li[data-id="' + term.term_parent + '"]');
				
				// update vars
				$ul = $parent.children('ul');
				
				// create ul
				if( !$ul.exists() ) {
					$ul = $('<ul class="children pdc-bl"></ul>');
					$parent.append( $ul );
				}
			}
			
			// append
			$ul.append( $li );
		},
		
		selectTerm: function( id ){
			if( this.getRelatedType() == 'select' ) {
				this.select2.selectOption( id );
			} else {
				var $input = this.$('input[value="' + id + '"]');
				$input.prop('checked', true).trigger('change');
			}
		},
		
		onClickRadio: function( e, $el ){
			
			// vars
			var $label = $el.parent('label');
			var selected = $label.hasClass('selected');
			
			// remove previous selected
			this.$('.selected').removeClass('selected');
			
			// add active class
			$label.addClass('selected');
			
			// allow null
			if( this.get('allow_null') && selected ) {
				$label.removeClass('selected');
				$el.prop('checked', false).trigger('change');
			}
		}
	});
	
	pdc.registerFieldType( Field );
		
})(jQuery);

(function($, undefined){
	
	var Field = pdc.models.DatePickerField.extend({
		
		type: 'time_picker',
		
		$control: function(){
			return this.$('.pdc-time-picker');
		},
		
		initialize: function(){
			
			// vars
			var $input = this.$input();
			var $inputText = this.$inputText();
			
			// args
			var args = {
				timeFormat:			this.get('time_format'),
				altField:			$input,
				altFieldTimeOnly:	false,
				altTimeFormat:		'HH:mm:ss',
				showButtonPanel:	true,
				controlType: 		'select',
				oneLine:			true,
				closeText:			pdc.get('dateTimePickerL10n').selectText,
				timeOnly:			true,
			};
			
			// add custom 'Close = Select' functionality
			args.onClose = function( value, dp_instance, t_instance ){
				
				// vars
				var $close = dp_instance.dpDiv.find('.ui-datepicker-close');
				
				// if clicking close button
				if( !value && $close.is(':hover') ) {
					t_instance._updateDateTime();
				}				
			};

						
			// filter
			args = pdc.applyFilters('time_picker_args', args, this);
			
			// add date time picker
			pdc.newTimePicker( $inputText, args );
			
			// action
			pdc.doAction('time_picker_init', $inputText, args, this);
		}
	});
	
	pdc.registerFieldType( Field );
	
	
	// add
	pdc.newTimePicker = function( $input, args ){
		
		// bail ealry if no datepicker library
		if( typeof $.timepicker === 'undefined' ) {
			return false;
		}
		
		// defaults
		args = args || {};
		
		// initialize
		$input.timepicker( args );
		
		// wrap the datepicker (only if it hasn't already been wrapped)
		if( $('body > #ui-datepicker-div').exists() ) {
			$('body > #ui-datepicker-div').wrap('<div class="pdc-ui-datepicker" />');
		}
	};
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'true_false',
		
		events: {
			'change .pdc-switch-input': 	'onChange',
			'focus .pdc-switch-input': 		'onFocus',
			'blur .pdc-switch-input': 		'onBlur',
			'keypress .pdc-switch-input':	'onKeypress'
		},
		
		$input: function(){
			return this.$('input[type="checkbox"]');
		},
		
		$switch: function(){
			return this.$('.pdc-switch');
		},
		
		getValue: function(){
			return this.$input().prop('checked') ? 1 : 0;
		},
		
		initialize: function(){
			this.render();
		},
		
		render: function(){
			
			// vars
			var $switch = this.$switch();
				
			// bail ealry if no $switch
			if( !$switch.length ) return;
			
			// vars
			var $on = $switch.children('.pdc-switch-on');
			var $off = $switch.children('.pdc-switch-off');
			var width = Math.max( $on.width(), $off.width() );
			
			// bail ealry if no width
			if( !width ) return;
			
			// set widths
			$on.css( 'min-width', width );
			$off.css( 'min-width', width );
				
		},
		
		switchOn: function() {
			this.$input().prop('checked', true);
			this.$switch().addClass('-on');
		},
		
		switchOff: function() {
			this.$input().prop('checked', false);
			this.$switch().removeClass('-on');
		},
		
		onChange: function( e, $el ){
			if( $el.prop('checked') ) {
				this.switchOn();
			} else {
				this.switchOff();
			}
		},
		
		onFocus: function( e, $el ){
			this.$switch().addClass('-focus');
		},
		
		onBlur: function( e, $el ){
			this.$switch().removeClass('-focus');
		},
		
		onKeypress: function( e, $el ){
			
			// left
			if( e.keyCode === 37 ) {
				return this.switchOff();
			}	
			
			// right
			if( e.keyCode === 39 ) {
				return this.switchOn();
			}
			
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'url',
		
		events: {
			'keyup input[type="url"]': 'onkeyup'
		},
		
		$control: function(){
			return this.$('.pdc-input-wrap');
		},
		
		$input: function(){
			return this.$('input[type="url"]');
		},
		
		initialize: function(){
			this.render();
		},
		
		isValid: function(){
			
			// vars
			var val = this.val();
			
			// bail early if no val
			if( !val ) {
				return false;
			}
			
			// url
			if( val.indexOf('://') !== -1 ) {
				return true;
			}
			
			// protocol relative url
			if( val.indexOf('//') === 0 ) {
				return true;
			}
			
			// return
			return false;
		},
		
		render: function(){
			
			// add class
			if( this.isValid() ) {
				this.$control().addClass('-valid');
			} else {
				this.$control().removeClass('-valid');
			}
		},
		
		onkeyup: function( e, $el ){
			this.render();
		}
	});
	
	pdc.registerFieldType( Field );
	
})(jQuery);

(function($, undefined){
	
	var Field = pdc.Field.extend({
		
		type: 'wysiwyg',
		
		wait: 'load',
		
		events: {
			'mousedown .pdc-editor-wrap.delay':	'onMousedown',
			'sortstartField': 'disableEditor',
			'sortstopField': 'enableEditor',
			'removeField': 'disableEditor'
		},
		
		$control: function(){
			return this.$('.pdc-editor-wrap');
		},
		
		$input: function(){
			return this.$('textarea');
		},
		
		getMode: function(){
			return this.$control().hasClass('tmce-active') ? 'visual' : 'text';
		},
		
		initialize: function(){
			
			// initializeEditor if no delay
			if( !this.$control().hasClass('delay') ) {
				this.initializeEditor();
			}
		},
		
		initializeEditor: function(){
			
			// vars
			var $wrap = this.$control();
			var $textarea = this.$input();
			var args = {
				tinymce:	true,
				quicktags:	true,
				toolbar:	this.get('toolbar'),
				mode:		this.getMode(),
				field:		this
			};
			
			// generate new id
			var oldId = $textarea.attr('id');
			var newId = pdc.uniqueId('pdc-editor-');
			
			// rename
			pdc.rename({
				target: $wrap,
				search: oldId,
				replace: newId,
				destructive: true
			});	
			
			// update id
			this.set('id', newId, true);
			
			// initialize
			pdc.tinymce.initialize( newId, args );
		},
		
		onMousedown: function( e ){
			
			// prevent default
			e.preventDefault();
			
			// remove delay class
			var $wrap = this.$control();
			$wrap.removeClass('delay');
			$wrap.find('.pdc-editor-toolbar').remove();
			
			// initialize
			this.initializeEditor();
		},
		
		enableEditor: function(){
			if( this.getMode() == 'visual' ) {
				pdc.tinymce.enable( this.get('id') );
			}
		},
		
		disableEditor: function(){
			pdc.tinymce.destroy( this.get('id') );
		}	
	});
	
	pdc.registerFieldType( Field );
		
})(jQuery);

(function($, undefined){
	
	// vars
	var storage = [];
	
	/**
	*  pdc.Condition
	*
	*  description
	*
	*  @date	23/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.Condition = pdc.Model.extend({
		
		type: '',							// used for model name
		operator: '==',						// rule operator
		label: '',							// label shown when editing fields
		choiceType: 'input',				// input, select
		fieldTypes: [],						// auto connect this conditions with these field types
		
		data: {
			conditions: false,	// the parent instance
			field: false,		// the field which we query against
			rule: {}			// the rule [field, operator, value]
		},
		
		events: {
			'change':		'change',
			'keyup':		'change',
			'enableField':	'change',
			'disableField':	'change'
		},
		
		setup: function( props ){
			$.extend(this.data, props);
		},
		
		getEventTarget: function( $el, event ){
			return $el || this.get('field').$el;
		},
		
		change: function( e, $el ){
			this.get('conditions').change( e );
		},
		
		match: function( rule, field ){
			return false;
		},
		
		calculate: function(){
			return this.match( this.get('rule'), this.get('field') );
		},
		
		choices: function( field ){
			return '<intput type="text" />';
		}
	});
	
	/**
	*  pdc.newCondition
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.newCondition = function( rule, conditions ){
		
		// currently setting up conditions for fieldX, this field is the 'target'
		var target = conditions.get('field');
		
		// use the 'target' to find the 'trigger' field. 
		// - this field is used to setup the conditional logic events
		var field = target.getField( rule.field );
		
		// bail ealry if no target or no field (possible if field doesn't exist due to HTML error)
		if( !target || !field ) {
			return false;
		}
		
		// vars
		var args = {
			rule: rule,
			target: target,
			conditions: conditions,
			field: field
		};
		
		// vars
		var fieldType = field.get('type');
		var operator = rule.operator;
		
		// get avaibale conditions
		var conditionTypes = pdc.getConditionTypes({
			fieldType: fieldType,
			operator: operator,
		});
		
		// instantiate
		var model = conditionTypes[0] || pdc.Condition;
		
		// instantiate
		var condition = new model( args );
		
		// return
		return condition;
	};

	/**
	*  mid
	*
	*  Calculates the model ID for a field type
	*
	*  @date	15/12/17
	*  @since	5.6.5
	*
	*  @param	string type
	*  @return	string
	*/
	
	var modelId = function( type ) {
		return pdc.strPascalCase( type || '' ) + 'Condition';
	};
	
	/**
	*  pdc.registerConditionType
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.registerConditionType = function( model ){
		
		// vars
		var proto = model.prototype;
		var type = proto.type;
		var mid = modelId( type );
		
		// store model
		pdc.models[ mid ] = model;
		
		// store reference
		storage.push( type );
	};
	
	/**
	*  pdc.getConditionType
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getConditionType = function( type ){
		var mid = modelId( type );
		return pdc.models[ mid ] || false;
	}
	
	/**
	*  pdc.registerConditionForFieldType
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.registerConditionForFieldType = function( conditionType, fieldType ){
		
		// get model
		var model = pdc.getConditionType( conditionType );
		
		// append
		if( model ) {
			model.prototype.fieldTypes.push( fieldType );
		}
	};
	
	/**
	*  pdc.getConditionTypes
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getConditionTypes = function( args ){
		
		// defaults
		args = pdc.parseArgs(args, {
			fieldType: '',
			operator: ''
		});
		
		// clonse available types
		var types = [];
		
		// loop
		storage.map(function( type ){
			
			// vars
			var model = pdc.getConditionType(type);
			var ProtoFieldTypes = model.prototype.fieldTypes;
			var ProtoOperator = model.prototype.operator;
			
			// check fieldType
			if( args.fieldType && ProtoFieldTypes.indexOf( args.fieldType ) === -1 )  {
				return;
			}
			
			// check operator
			if( args.operator && ProtoOperator !== args.operator )  {
				return;
			}
			
			// append
			types.push( model );
		});
		
		// return
		return types;
	};
	
})(jQuery);

(function($, undefined){
	
	// vars
	var CONTEXT = 'conditional_logic';
	
	/**
	*  conditionsManager
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var conditionsManager = new pdc.Model({
		
		id: 'conditionsManager',
		
		priority: 20, // run actions later
		
		actions: {
			'new_field':		'onNewField',
		},
		
		onNewField: function( field ){
			if( field.has('conditions') ) {
				field.getConditions().render();
			}
		},
	});
	
	/**
	*  pdc.Field.prototype.getField
	*
	*  Finds a field that is related to another field
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var getSiblingField = function( field, key ){
			
		// find sibling (very fast)
		var fields = pdc.getFields({
			key: key,
			sibling: field.$el,
			suppressFilters: true,
		});
		
		// find sibling-children (fast)
		// needed for group fields, accordions, etc
		if( !fields.length ) {
			fields = pdc.getFields({
				key: key,
				parent: field.$el.parent(),
				suppressFilters: true,
			});
		}
		 
		// return
		if( fields.length ) {
			return fields[0];
		}
		return false;
	};
	
	pdc.Field.prototype.getField = function( key ){
		
		// get sibling field
		var field = getSiblingField( this, key );
		
		// return early
		if( field ) {
			return field;
		}
		
		// move up through each parent and try again
		var parents = this.parents();
		for( var i = 0; i < parents.length; i++ ) {
			
			// get sibling field
			field = getSiblingField( parents[i], key );
			
			// return early
			if( field ) {
				return field;
			}
		}
		
		// return
		return false;
	};
	
	
	/**
	*  pdc.Field.prototype.getConditions
	*
	*  Returns the field's conditions instance
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.Field.prototype.getConditions = function(){
		
		// instantiate
		if( !this.conditions ) {
			this.conditions = new Conditions( this );
		}
		
		// return
		return this.conditions;
	};
	
	
	/**
	*  Conditions
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	var timeout = false;
	var Conditions = pdc.Model.extend({
		
		id: 'Conditions',
		
		data: {
			field:		false,	// The field with "data-conditions" (target).
			timeStamp:	false,	// Reference used during "change" event.
			groups:		[],		// The groups of condition instances.
		},
		
		setup: function( field ){
			
			// data
			this.data.field = field;
			
			// vars
			var conditions = field.get('conditions');
			
			// detect groups
			if( conditions instanceof Array ) {
				
				// detect groups
				if( conditions[0] instanceof Array ) {

					// loop
					conditions.map(function(rules, i){
						this.addRules( rules, i );
					}, this);
				
				// detect rules
				} else {
					this.addRules( conditions );
				}
				
			// detect rule
			} else {
				this.addRule( conditions );
			}
		},
		
		change: function( e ){
			
			// this function may be triggered multiple times per event due to multiple condition classes
			// compare timestamp to allow only 1 trigger per event
			if( this.get('timeStamp') === e.timeStamp ) {
				return false;
			} else {
				this.set('timeStamp', e.timeStamp, true);
			}
			
			// render condition and store result
			var changed = this.render();
		},
		
		render: function(){
			return this.calculate() ? this.show() : this.hide();
		},
		
		show: function(){
			return this.get('field').showEnable(this.cid, CONTEXT);
		},
		
		hide: function(){
			return this.get('field').hideDisable(this.cid, CONTEXT);
		},
		
		calculate: function(){
			
			// vars
			var pass = false;
			
			// loop
			this.getGroups().map(function( group ){
				
				// igrnore this group if another group passed
				if( pass ) return;
				
				// find passed
				var passed = group.filter(function(condition){
					return condition.calculate();
				});
				
				// if all conditions passed, update the global var
				if( passed.length == group.length ) {
					pass = true;
				}
			});
			
			return pass;
		},
		
		hasGroups: function(){
			return this.data.groups != null;
		},
		
		getGroups: function(){
			return this.data.groups;
		},
		
		addGroup: function(){
			var group = [];
			this.data.groups.push( group );
			return group;
		},
		
		hasGroup: function( i ){
			return this.data.groups[i] != null;
		},
		
		getGroup: function( i ){
			return this.data.groups[i];
		},
		
		removeGroup: function( i ){
			this.data.groups[i].delete;
			return this;
		},
		
		addRules: function( rules, group ){
			rules.map(function( rule ){
				this.addRule( rule, group );
			}, this);
		},
		
		addRule: function( rule, group ){
			
			// defaults
			group = group || 0;
			
			// vars
			var groupArray;
			
			// get group
			if( this.hasGroup(group) ) {
				groupArray = this.getGroup(group);
			} else {
				groupArray = this.addGroup();
			}
			
			// instantiate
			var condition = pdc.newCondition( rule, this );
			
			// bail ealry if condition failed (field did not exist)
			if( !condition ) {
				return false;
			}
			
			// add rule
			groupArray.push(condition);
		},
		
		hasRule: function(){
			
		},
		
		getRule: function( rule, group ){
			
			// defaults
			rule = rule || 0;
			group = group || 0;
			
			return this.data.groups[ group ][ rule ];
		},
		
		removeRule: function(){
			
		}
	});
	
})(jQuery);

(function($, undefined){
	
	var __ = pdc.__;
	
	var parseString = function( val ){
		return val ? '' + val : '';
	};
	
	var isEqualTo = function( v1, v2 ){
		return ( parseString(v1).toLowerCase() === parseString(v2).toLowerCase() );
	};
	
	var isEqualToNumber = function( v1, v2 ){
		return ( parseFloat(v1) === parseFloat(v2) );
	};
	
	var isGreaterThan = function( v1, v2 ){
		return ( parseFloat(v1) > parseFloat(v2) );
	};
	
	var isLessThan = function( v1, v2 ){
		return ( parseFloat(v1) < parseFloat(v2) );
	};
	
	var inArray = function( v1, array ){
		
		// cast all values as string
		array = array.map(function(v2){
			return parseString(v2);
		});
		
		return (array.indexOf( v1 ) > -1);
	}
	
	var containsString = function( haystack, needle ){
		return ( parseString(haystack).indexOf( parseString(needle) ) > -1 );
	};
	
	var matchesPattern = function( v1, pattern ){
		var regexp = new RegExp(parseString(pattern), 'gi');
		return parseString(v1).match( regexp );
	};
	
	/**
	*  hasValue
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var HasValue = pdc.Condition.extend({
		type: 'hasValue',
		operator: '!=empty',
		label: __('Has any value'),
		fieldTypes: [ 'text', 'textarea', 'number', 'range', 'email', 'url', 'password', 'image', 'file', 'wysiwyg', 'oembed', 'select', 'checkbox', 'radio', 'button_group', 'link', 'post_object', 'page_link', 'relationship', 'taxonomy', 'user', 'google_map', 'date_picker', 'date_time_picker', 'time_picker', 'color_picker' ],
		match: function( rule, field ){
			return (field.val() ? true : false);
		},
		choices: function( fieldObject ){
			return '<input type="text" disabled="" />';
		}
	});
	
	pdc.registerConditionType( HasValue );
	
	/**
	*  hasValue
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var HasNoValue = HasValue.extend({
		type: 'hasNoValue',
		operator: '==empty',
		label: __('Has no value'),
		match: function( rule, field ){
			return !HasValue.prototype.match.apply(this, arguments);
		}
	});
	
	pdc.registerConditionType( HasNoValue );
	
	
	
	/**
	*  EqualTo
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var EqualTo = pdc.Condition.extend({
		type: 'equalTo',
		operator: '==',
		label: __('Value is equal to'),
		fieldTypes: [ 'text', 'textarea', 'number', 'range', 'email', 'url', 'password' ],
		match: function( rule, field ){
			if( $.isNumeric(rule.value) ) {
				return isEqualToNumber( rule.value, field.val() );
			} else {
				return isEqualTo( rule.value, field.val() );
			}
		},
		choices: function( fieldObject ){
			return '<input type="text" />';
		}
	});
	
	pdc.registerConditionType( EqualTo );
	
	/**
	*  NotEqualTo
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var NotEqualTo = EqualTo.extend({
		type: 'notEqualTo',
		operator: '!=',
		label: __('Value is not equal to'),
		match: function( rule, field ){
			return !EqualTo.prototype.match.apply(this, arguments);
		}
	});
	
	pdc.registerConditionType( NotEqualTo );
	
	/**
	*  PatternMatch
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var PatternMatch = pdc.Condition.extend({
		type: 'patternMatch',
		operator: '==pattern',
		label: __('Value matches pattern'),
		fieldTypes: [ 'text', 'textarea', 'email', 'url', 'password', 'wysiwyg' ],
		match: function( rule, field ){
			return matchesPattern( field.val(), rule.value );
		},
		choices: function( fieldObject ){
			return '<input type="text" placeholder="[a-z0-9]" />';
		}
	});
	
	pdc.registerConditionType( PatternMatch );
	
	/**
	*  Contains
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var Contains = pdc.Condition.extend({
		type: 'contains',
		operator: '==contains',
		label: __('Value contains'),
		fieldTypes: [ 'text', 'textarea', 'number', 'email', 'url', 'password', 'wysiwyg', 'oembed', 'select' ],
		match: function( rule, field ){
			return containsString( field.val(), rule.value );
		},
		choices: function( fieldObject ){
			return '<input type="text" />';
		}
	});
	
	pdc.registerConditionType( Contains );
	
	/**
	*  TrueFalseEqualTo
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var TrueFalseEqualTo = EqualTo.extend({
		type: 'trueFalseEqualTo',
		choiceType: 'select',
		fieldTypes: [ 'true_false' ],
		choices: function( field ){
			return [
				{
					id:		1,
					text:	__('Checked')
				}
			];
		},
	});
	
	pdc.registerConditionType( TrueFalseEqualTo );
	
	/**
	*  TrueFalseNotEqualTo
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var TrueFalseNotEqualTo = NotEqualTo.extend({
		type: 'trueFalseNotEqualTo',
		choiceType: 'select',
		fieldTypes: [ 'true_false' ],
		choices: function( field ){
			return [
				{
					id:		1,
					text:	__('Checked')
				}
			];
		},
	});
	
	pdc.registerConditionType( TrueFalseNotEqualTo );
	
	/**
	*  SelectEqualTo
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var SelectEqualTo = pdc.Condition.extend({
		type: 'selectEqualTo',
		operator: '==',
		label: __('Value is equal to'),
		fieldTypes: [ 'select', 'checkbox', 'radio', 'button_group' ],
		match: function( rule, field ){
			var val = field.val();
			if( val instanceof Array ) {
				return inArray( rule.value, val );
			} else {
				return isEqualTo( rule.value, val );
			}
		},
		choices: function( fieldObject ){
			
			// vars
			var choices = [];
			var lines = fieldObject.$setting('choices textarea').val().split("\n");	
			
			// allow null
			if( fieldObject.$input('allow_null').prop('checked') ) {
				choices.push({
					id: '',
					text: __('Null')
				});
			}
			
			// loop
			lines.map(function( line ){
				
				// split
				line = line.split(':');
				
				// default label to value
				line[1] = line[1] || line[0];
				
				// append					
				choices.push({
					id: $.trim( line[0] ),
					text: $.trim( line[1] )
				});
			});
			
			// return
			return choices;
		},
	});
	
	pdc.registerConditionType( SelectEqualTo );
	
	/**
	*  SelectNotEqualTo
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var SelectNotEqualTo = SelectEqualTo.extend({
		type: 'selectNotEqualTo',
		operator: '!=',
		label: __('Value is not equal to'),
		match: function( rule, field ){
			return !SelectEqualTo.prototype.match.apply(this, arguments);
		}
	});
	
	pdc.registerConditionType( SelectNotEqualTo );
	
	/**
	*  GreaterThan
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var GreaterThan = pdc.Condition.extend({
		type: 'greaterThan',
		operator: '>',
		label: __('Value is greater than'),
		fieldTypes: [ 'number', 'range' ],
		match: function( rule, field ){
			var val = field.val();
			if( val instanceof Array ) {
				val = val.length;
			}
			return isGreaterThan( val, rule.value );
		},
		choices: function( fieldObject ){
			return '<input type="number" />';
		}
	});
	
	pdc.registerConditionType( GreaterThan );
	
	
	/**
	*  LessThan
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var LessThan = GreaterThan.extend({
		type: 'lessThan',
		operator: '<',
		label: __('Value is less than'),
		match: function( rule, field ){
			var val = field.val();
			if( val instanceof Array ) {
				val = val.length;
			}
			return isLessThan( val, rule.value );
		},
		choices: function( fieldObject ){
			return '<input type="number" />';
		}
	});
	
	pdc.registerConditionType( LessThan );
	
	/**
	*  SelectedGreaterThan
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var SelectionGreaterThan = GreaterThan.extend({
		type: 'selectionGreaterThan',
		label: __('Selection is greater than'),
		fieldTypes: [ 'checkbox', 'select', 'post_object', 'page_link', 'relationship', 'taxonomy', 'user' ],
	});
	
	pdc.registerConditionType( SelectionGreaterThan );
	
	/**
	*  SelectedGreaterThan
	*
	*  description
	*
	*  @date	1/2/18
	*  @since	5.6.5
	*
	*  @param	void
	*  @return	void
	*/
	
	var SelectionLessThan = LessThan.extend({
		type: 'selectionLessThan',
		label: __('Selection is less than'),
		fieldTypes: [ 'checkbox', 'select', 'post_object', 'page_link', 'relationship', 'taxonomy', 'user' ],
	});
	
	pdc.registerConditionType( SelectionLessThan );
	
})(jQuery);

(function($, undefined){
	
	/**
	*  pdc.newMediaPopup
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.newMediaPopup = function( args ){
		
		// args
		var popup = null;
		var args = pdc.parseArgs(args, {
			mode:			'select',			// 'select', 'edit'
			title:			'',					// 'Upload Image'
			button:			'',					// 'Select Image'
			type:			'',					// 'image', ''
			field:			false,				// field instance
			allowedTypes:	'',					// '.jpg, .png, etc'
			library:		'all',				// 'all', 'uploadedTo'
			multiple:		false,				// false, true, 'add'
			attachment:		0,					// the attachment to edit
			autoOpen:		true,				// open the popup automatically
			open: 			function(){},		// callback after close
			select: 		function(){},		// callback after select
			close: 			function(){}		// callback after close
		});
		
		// initialize
		if( args.mode == 'edit' ) {
			popup = new pdc.models.EditMediaPopup( args );
		} else {
			popup = new pdc.models.SelectMediaPopup( args );
		}
		
		// open popup (allow frame customization before opening)
		if( args.autoOpen ) {
			setTimeout(function(){
				popup.open();
			}, 1);
		}
		
		// action
		pdc.doAction('new_media_popup', popup);
		
		// return
		return popup;
	};
	
	
	/**
	*  getPostID
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var getPostID = function() {
		var postID = pdc.get('post_id');
		return $.isNumeric(postID) ? postID : 0;
	}
	
	
	/**
	*  pdc.getMimeTypes
	*
	*  description
	*
	*  @date	11/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.getMimeTypes = function(){
		return this.get('mimeTypes');
	};
	
	pdc.getMimeType = function( name ){
		
		// vars
		var allTypes = pdc.getMimeTypes();
		
		// search
		if( allTypes[name] !== undefined ) {
			return allTypes[name];
		}
		
		// some types contain a mixed key such as "jpg|jpeg|jpe"
		for( var key in allTypes ) {
			if( key.indexOf(name) !== -1 ) {
				return allTypes[key];
			}
		}
		
		// return
		return false;
	};
	
	
	/**
	*  MediaPopup
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var MediaPopup = pdc.Model.extend({
		
		id: 'MediaPopup',
		data: {},
		defaults: {},
		frame: false,
		
		setup: function( props ){
			$.extend(this.data, props);
		},
		
		initialize: function(){
			
			// vars
			var options = this.getFrameOptions();
			
			// add states
			this.addFrameStates( options );
			
			// create frame
			var frame = wp.media( options );
			
			// add args reference
			frame.pdc = this;
			
			// add events
			this.addFrameEvents( frame, options );
			
			// strore frame
			this.frame = frame;
		},
		
		open: function(){
			this.frame.open();
		},
		
		close: function(){
			this.frame.close();
		},
		
		remove: function(){
			this.frame.detach();
			this.frame.remove();
		},
		
		getFrameOptions: function(){
			
			// vars
			var options = {
				title:		this.get('title'),
				multiple:	this.get('multiple'),
				library:	{},
				states:		[]
			};
			
			// type
			if( this.get('type') ) {
				options.library.type = this.get('type');
			}
			
			// type
			if( this.get('library') === 'uploadedTo' ) {
				options.library.uploadedTo = getPostID();
			}
			
			// attachment
			if( this.get('attachment') ) {
				options.library.post__in = [ this.get('attachment') ];
			}
			
			// button
			if( this.get('button') ) {
				options.button = {
					text: this.get('button')
				};
			}
			
			// return
			return options;
		},
		
		addFrameStates: function( options ){
			
			// create query
			var Query = wp.media.query( options.library );
			
			// add _pdcuploader
			// this is super wack!
			// if you add _pdcuploader to the options.library args, new uploads will not be added to the library view.
			// this has been traced back to the wp.media.model.Query initialize function (which can't be overriden)
			// Adding any custom args will cause the Attahcments to not observe the uploader queue
			// To bypass this security issue, we add in the args AFTER the Query has been initialized
			// options.library._pdcuploader = settings.field;
			if( this.get('field') && pdc.isset(Query, 'mirroring', 'args') ) {
				Query.mirroring.args._pdcuploader = this.get('field');
			}
			
			// add states
			options.states.push(
				
				// main state
				new wp.media.controller.Library({
					library:		Query,
					multiple: 		this.get('multiple'),
					title: 			this.get('title'),
					priority: 		20,
					filterable: 	'all',
					editable: 		true,
					allowLocalEdits: true
				})
				
			);
			
			// edit image functionality (added in WP 3.9)
			if( pdc.isset(wp, 'media', 'controller', 'EditImage') ) {
				options.states.push( new wp.media.controller.EditImage() );
			}
		},
		
		addFrameEvents: function( frame, options ){
			
			// log all events
			//frame.on('all', function( e ) {
			//	console.log( 'frame all: %o', e );
			//});
			
			// add class
			frame.on('open',function() {
				this.$el.closest('.media-modal').addClass('pdc-media-modal -' + this.pdc.get('mode') );
			}, frame);
			
			// edit image view
			// source: media-views.js:2410 editImageContent()
			frame.on('content:render:edit-image', function(){
				
				var image = this.state().get('image');
				var view = new wp.media.view.EditImage({ model: image, controller: this }).render();
				this.content.set( view );
	
				// after creating the wrapper view, load the actual editor via an ajax call
				view.loadEditor();
				
			}, frame);
			
			// update toolbar button
/*
			frame.on( 'toolbar:create:select', function( toolbar ) {
				
				toolbar.view = new wp.media.view.Toolbar.Select({
					text: frame.options._button,
					controller: this
				});
				
			}, frame );
*/
			// on select
			frame.on('select', function() {
				
				// vars
				var selection = frame.state().get('selection');
				
				// if selecting images
				if( selection ) {
					
					// loop
					selection.each(function( attachment, i ){
						frame.pdc.get('select').apply( frame.pdc, [attachment, i] );
					});
				}
			});
			
			// on close
			frame.on('close',function(){
				
				// callback and remove
				setTimeout(function(){
					frame.pdc.get('close').apply( frame.pdc );
					frame.pdc.remove();
				}, 1);
			});
		}
	});
	
	
	/**
	*  pdc.models.SelectMediaPopup
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.models.SelectMediaPopup = MediaPopup.extend({
		id: 'SelectMediaPopup',
		setup: function( props ){
			
			// default button
			if( !props.button ) {
				props.button = pdc._x('Select', 'verb');
			}
			
			// parent
			MediaPopup.prototype.setup.apply(this, arguments);
		},
		
		addFrameEvents: function( frame, options ){
			
			// plupload
			// adds _pdcuploader param to validate uploads
			if( pdc.isset(_wpPluploadSettings, 'defaults', 'multipart_params') ) {
				
				// add _pdcuploader so that Uploader will inherit
				_wpPluploadSettings.defaults.multipart_params._pdcuploader = this.get('field');
				
				// remove pdc_field so future Uploaders won't inherit
				frame.on('open', function(){
					delete _wpPluploadSettings.defaults.multipart_params._pdcuploader;
				});
			}
			
			// browse
			frame.on('content:activate:browse', function(){
				
				// vars
				var toolbar = false;
				
				// populate above vars making sure to allow for failure
				// perhaps toolbar does not exist because the frame open is Upload Files
				try {
					toolbar = frame.content.get().toolbar;
				} catch(e) {
					console.log(e);
					return;
				}
				
				// callback
				frame.pdc.customizeFilters.apply(frame.pdc, [toolbar]);
			});
			
			// parent
			MediaPopup.prototype.addFrameEvents.apply(this, arguments);
			
		},
		
		customizeFilters: function( toolbar ){
			
			// vars
			var filters = toolbar.get('filters');
			
			// image
			if( this.get('type') == 'image' ) {
				
				// update all
				filters.filters.all.text = pdc.__('All images');
				
				// remove some filters
				delete filters.filters.audio;
				delete filters.filters.video;
				delete filters.filters.image;
				
				// update all filters to show images
				$.each(filters.filters, function( i, filter ){
					filter.props.type = filter.props.type || 'image';
				});
			}
			
			// specific types
			if( this.get('allowedTypes') ) {
				
				// convert ".jpg, .png" into ["jpg", "png"]
				var allowedTypes = this.get('allowedTypes').split(' ').join('').split('.').join('').split(',');
				
				// loop
				allowedTypes.map(function( name ){
					
					// get type
					var mimeType = pdc.getMimeType( name );
					
					// bail early if no type
					if( !mimeType ) return;
					
					// create new filter
					var newFilter = {
						text: mimeType,
						props: {
							status:  null,
							type:    mimeType,
							uploadedTo: null,
							orderby: 'date',
							order:   'DESC'
						},
						priority: 20
					};			
									
					// append
					filters.filters[ mimeType ] = newFilter;
					
				});
			}
			
			
			
			// uploaded to post
			if( this.get('library') === 'uploadedTo' ) {
				
				// vars
				var uploadedTo = this.frame.options.library.uploadedTo;
				
				// remove some filters
				delete filters.filters.unattached;
				delete filters.filters.uploaded;
				
				// add uploadedTo to filters
				$.each(filters.filters, function( i, filter ){
					filter.text += ' (' + pdc.__('Uploaded to this post') + ')';
					filter.props.uploadedTo = uploadedTo;
				});
			}
			
			// add _pdcuploader to filters
			var field = this.get('field');
			$.each(filters.filters, function( k, filter ){
				filter.props._pdcuploader = field;
			});
			
			// add _pdcuplaoder to search
			var search = toolbar.get('search');
			search.model.attributes._pdcuploader = field;
			
			// render (custom function added to prototype)
			if( filters.renderFilters ) {
				filters.renderFilters();
			}
		}
	});
	
	
	/**
	*  pdc.models.EditMediaPopup
	*
	*  description
	*
	*  @date	10/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.models.EditMediaPopup = MediaPopup.extend({
		id: 'SelectMediaPopup',
		setup: function( props ){
			
			// default button
			if( !props.button ) {
				props.button = pdc._x('Update', 'verb');
			}
			
			// parent
			MediaPopup.prototype.setup.apply(this, arguments);
		},
		
		addFrameEvents: function( frame, options ){
			
			// add class
			frame.on('open',function() {
				
				// add class
				this.$el.closest('.media-modal').addClass('pdc-expanded');
				
				// set to browse
				if( this.content.mode() != 'browse' ) {
					this.content.mode('browse');
				}
				
				// set selection
				var state 		= this.state();
				var selection	= state.get('selection');
				var attachment	= wp.media.attachment( frame.pdc.get('attachment') );
				selection.add( attachment );
								
			}, frame);
			
			// parent
			MediaPopup.prototype.addFrameEvents.apply(this, arguments);
			
		}
	});
	
	
	/**
	*  customizePrototypes
	*
	*  description
	*
	*  @date	11/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var customizePrototypes = new pdc.Model({
		id: 'customizePrototypes',
		wait: 'ready',
		
		initialize: function(){
			
			// bail early if no media views
			if( !pdc.isset(window, 'wp', 'media', 'view') ) {
				return;
			}
			
			// fix bug where CPT without "editor" does not set post.id setting which then prevents uploadedTo from working
			var postID = getPostID();
			if( postID && pdc.isset(wp, 'media', 'view', 'settings', 'post') ) {
				wp.media.view.settings.post.id = postID;
			}
			
			// customize
			this.customizeAttachmentsRouter();
			this.customizeAttachmentFilters();
			this.customizeAttachmentCompat();
			this.customizeAttachmentLibrary();
		},
		
		customizeAttachmentsRouter: function(){
			
			// validate
			if( !pdc.isset(wp, 'media', 'view', 'Router') ) {
				return;
			}
			
			// vars
			var Parent = wp.media.view.Router;
			
			// extend
			wp.media.view.Router = Parent.extend({
				
				addExpand: function(){
					
					// vars
					var $a = $([
						'<a href="#" class="pdc-expand-details">',
							'<span class="is-closed"><span class="pdc-icon -left small grey"></span>' + pdc.__('Expand Details') +  '</span>',
							'<span class="is-open"><span class="pdc-icon -right small grey"></span>' + pdc.__('Collapse Details') +  '</span>',
						'</a>'
					].join('')); 
					
					// add events
					$a.on('click', function( e ){
						e.preventDefault();
						var $div = $(this).closest('.media-modal');
						if( $div.hasClass('pdc-expanded') ) {
							$div.removeClass('pdc-expanded');
						} else {
							$div.addClass('pdc-expanded');
						}
					});
					
					// append
					this.$el.append( $a );
				},
				
				initialize: function(){
					
					// initialize
					Parent.prototype.initialize.apply( this, arguments );
					
					// add buttons
					this.addExpand();
					
					// return
					return this;
				}
			});	
		},
		
		customizeAttachmentFilters: function(){
			
			// validate
			if( !pdc.isset(wp, 'media', 'view', 'AttachmentFilters', 'All') ) {
				return;
			}
			
			// vars
			var Parent = wp.media.view.AttachmentFilters.All;
			
			// renderFilters
			// copied from media-views.js:6939
			Parent.prototype.renderFilters = function(){
				
				// Build `<option>` elements.
				this.$el.html( _.chain( this.filters ).map( function( filter, value ) {
					return {
						el: $( '<option></option>' ).val( value ).html( filter.text )[0],
						priority: filter.priority || 50
					};
				}, this ).sortBy('priority').pluck('el').value() );
				
			};
		},
		
		customizeAttachmentCompat: function(){
			
			// validate
			if( !pdc.isset(wp, 'media', 'view', 'AttachmentCompat') ) {
				return;
			}
			
			// vars
			var AttachmentCompat = wp.media.view.AttachmentCompat;
			var timeout = false;
			
			// extend
			wp.media.view.AttachmentCompat = AttachmentCompat.extend({
				
				render: function() {
					
					// WP bug
					// When multiple media frames exist on the same page (WP content, WYSIWYG, image, file ),
					// WP creates multiple instances of this AttachmentCompat view.
					// Each instance will attempt to render when a new modal is created.
					// Use a property to avoid this and only render once per instance.
					if( this.rendered ) {
						return this;
					}
					
					// render HTML
					AttachmentCompat.prototype.render.apply( this, arguments );
					
					// when uploading, render is called twice.
					// ignore first render by checking for #pdc-form-data element
					if( !this.$('#pdc-form-data').length ) {
						return this;
					}
					
					// clear timeout
					clearTimeout( timeout );
					
					// setTimeout
					timeout = setTimeout($.proxy(function(){
						this.rendered = true;
						pdc.doAction('append', this.$el);
					}, this), 50);
					
					// return
					return this;
				}
			});

		},
		
		customizeAttachmentLibrary: function(){
			
			// validate
			if( !pdc.isset(wp, 'media', 'view', 'Attachment', 'Library') ) {
				return;
			}
			
			// vars
			var AttachmentLibrary = wp.media.view.Attachment.Library;
			
			// extend
			wp.media.view.Attachment.Library = AttachmentLibrary.extend({
				
				render: function() {
					
					// vars
					var popup = pdc.isget(this, 'controller', 'pdc');
					var attributes = pdc.isget(this, 'model', 'attributes');
					
					// check vars exist to avoid errors
					if( popup && attributes ) {
						
						// show errors
						if( attributes.pdc_errors ) {
							this.$el.addClass('pdc-disabled');
						}
						
						// disable selected
						var selected = popup.get('selected');
						if( selected && selected.indexOf(attributes.id) > -1 ) {
							this.$el.addClass('pdc-selected');
						}
					}
										
					// render
					return AttachmentLibrary.prototype.render.apply( this, arguments );
					
				},
				
				
				/*
				*  toggleSelection
				*
				*  This function is called before an attachment is selected
				*  A good place to check for errors and prevent the 'select' function from being fired
				*
				*  @type	function
				*  @date	29/09/2016
				*  @since	5.4.0
				*
				*  @param	options (object)
				*  @return	n/a
				*/
				
				toggleSelection: function( options ) {
					
					// vars
					// source: wp-includes/js/media-views.js:2880
					var collection = this.collection,
						selection = this.options.selection,
						model = this.model,
						single = selection.single();
					
					
					// vars
					var frame = this.controller;
					var errors = pdc.isget(this, 'model', 'attributes', 'pdc_errors');
					var $sidebar = frame.$el.find('.media-frame-content .media-sidebar');
					
					// remove previous error
					$sidebar.children('.pdc-selection-error').remove();
					
					// show attachment details
					$sidebar.children().removeClass('pdc-hidden');
					
					// add message
					if( frame && errors ) {
						
						// vars
						var filename = pdc.isget(this, 'model', 'attributes', 'filename');
						
						// hide attachment details
						// Gallery field continues to show previously selected attachment...
						$sidebar.children().addClass('pdc-hidden');
						
						// append message
						$sidebar.prepend([
							'<div class="pdc-selection-error">',
								'<span class="selection-error-label">' + pdc.__('Restricted') +'</span>',
								'<span class="selection-error-filename">' + filename + '</span>',
								'<span class="selection-error-message">' + errors + '</span>',
							'</div>'
						].join(''));
						
						// reset selection (unselects all attachments)
						selection.reset();
						
						// set single (attachment displayed in sidebar)
						selection.single( model );
						
						// return and prevent 'select' form being fired
						return;
						
					}
					
					// return					
					return AttachmentLibrary.prototype.toggleSelection.apply( this, arguments );
				}
			});
		}
	});

})(jQuery);

(function($, undefined){
	
	pdc.screen = new pdc.Model({
		
		active: true,
		
		xhr: false,
		
		timeout: false,
		
		wait: 'load',
		
		events: {
			'change #page_template':						'onChange',
			'change #parent_id':							'onChange',
			'change #post-formats-select':					'onChange',
			'change .categorychecklist':					'onChange',
			'change .tagsdiv':								'onChange',
			'change .pdc-taxonomy-field[data-save="1"]':	'onChange',
			'change #product-type':							'onChange'
		},
		
		initialize: function(){
			
/*
			// disable if not active
			if( !this.active ) {
				this.events = {};
			}
			
			// bail early if not for post
			if( pdc.get('screen') !== 'post' ) {
				return;
			}
			
			'check_screen_data'
			
			'check_screen_events'
				
*/
		},
/*
		
		checkScreenEvents: function(){
			
			// vars
			var events = [
				'change #page_template',
				'change #parent_id',
				'change #post-formats-select input',
				'change .categorychecklist input',
				'change .categorychecklist select',
				'change .pdc-taxonomy-field[data-save="1"] input',
				'change .pdc-taxonomy-field[data-save="1"] select',
				'change #product-type'	
			];
			
			pdc.screen.on('change', '#product-type', 'fetch');
		},
*/
		
		
		isPost: function(){
			return pdc.get('screen') === 'post';
		},
		
		isUser: function(){
			return pdc.get('screen') === 'user';
		},
		
		isTaxonomy: function(){
			return pdc.get('screen') === 'taxonomy';
		},
		
		isAttachment: function(){
			return pdc.get('screen') === 'attachment';
		},
		
		isNavMenu: function(){
			return pdc.get('screen') === 'nav_menu';
		},
		
		isWidget: function(){
			return pdc.get('screen') === 'widget';
		},
		
		isComment: function(){
			return pdc.get('screen') === 'comment';
		},
		
		getPageTemplate: function(){
			var $el = $('#page_template');
			return $el.length ? $el.val() : null;
		},
		
		getPageParent: function( e, $el ){
			var $el = $('#parent_id');
			return $el.length ? $el.val() : null;
		},
		
		getPageType: function( e, $el ){
			return this.getPageParent() ? 'child' : 'parent';
		},
		
		getPostFormat: function( e, $el ){
			var $el = $('#post-formats-select input:checked');
			if( $el.length ) {
				var val = $el.val();
				return (val == '0') ? 'standard' : val;
			}
			return null;
		},
		
		getPostTerms: function(){
			
			// vars
			var terms = {};
			
			// serialize WP taxonomy postboxes		
			var data = pdc.serialize( $('.categorydiv, .tagsdiv') );
			
			// use tax_input (tag, custom-taxonomy) when possible.
			// this data is already formatted in taxonomy => [terms].
			if( data.tax_input ) {
				terms = data.tax_input;
			}
			
			// append "category" which uses a different name
			if( data.post_category ) {
				terms.category = data.post_category;
			}
			
			// convert any string values (tags) into array format
			for( var tax in terms ) {
				if( !pdc.isArray(terms[tax]) ) {
					terms[tax] = terms[tax].split(', ');
				}
			}
			
			// loop over taxonomy fields and add their values
			pdc.getFields({type: 'taxonomy'}).map(function( field ){
				
				// ignore fields that don't save
				if( !field.get('save') ) {
					return;
				}
				
				// vars
				var val = field.val();
				var tax = field.get('taxonomy');
				
				// check val
				if( val ) {
					
					// ensure terms exists
					terms[ tax ] = terms[ tax ] || [];
					
					// ensure val is an array
					val = pdc.isArray(val) ? val : [val];
					
					// append
					terms[ tax ] = terms[ tax ].concat( val );
				}
			});
			
			// add WC product type
			if( (productType = this.getProductType()) !== null ) {
				terms.product_type = [productType];
			}
			
			// remove duplicate values
			for( var tax in terms ) {
				terms[tax] = pdc.uniqueArray(terms[tax]);
			}
			
			// return
			return terms;
		},
		
		getProductType: function(){
			var $el = $('#product-type');
			return $el.length ? $el.val() : null;
		},
		
		check: function(){
			
			// bail early if not for post
			if( pdc.get('screen') !== 'post' ) {
				return;
			}
			
			// abort XHR if is already loading AJAX data
			if( this.xhr ) {
				this.xhr.abort();
			}
			
			// vars
			var ajaxData = pdc.parseArgs(this.data, {
				action:	'pdc/ajax/check_screen',
				screen: pdc.get('screen'),
				exclude: []
			});
			
			// post id
			if( this.isPost() ) {
				ajaxData.post_id = pdc.get('post_id');
			}
			
			// page template
			if( (pageTemplate = this.getPageTemplate()) !== null ) {
				ajaxData.page_template = pageTemplate;
			}
			
			// page parent
			if( (pageParent = this.getPageParent()) !== null ) {
				ajaxData.page_parent = pageParent;
			}
			
			// page type
			if( (pageType = this.getPageType()) !== null ) {
				ajaxData.page_type = pageType;
			}
			
			// post format
			if( (postFormat = this.getPostFormat()) !== null ) {
				ajaxData.post_format = postFormat;
			}
			
			// post terms
			if( (postTerms = this.getPostTerms()) !== null ) {
				ajaxData.post_terms = postTerms;
			}
			
			// exclude existing postboxes
			$('.pdc-postbox').not('.pdc-hidden').each(function(){
				ajaxData.exclude.push( $(this).attr('id').substr(4) );
			});
			
			// success
			var onSuccess = function( json ){
				
				// bail early if not success
				if( !pdc.isAjaxSuccess(json) ) {
					return;
				}
				
				// hide
				$('.pdc-postbox').addClass('pdc-hidden');
				$('.pdc-postbox-toggle').addClass('pdc-hidden');
				
				// reset style
				$('#pdc-style').html('');
				
				// loop
				json.data.map(function( fieldGroup, i ){
					
					// vars
					var $postbox = $('#pdc-' + fieldGroup.key);
					var $toggle = $('#pdc-' + fieldGroup.key + '-hide');
					var $label = $toggle.parent();
						
					// show
					// use show() to force display when postbox has been hidden by 'Show on screen' toggle
					$postbox.removeClass('pdc-hidden hide-if-js').show();
					$label.removeClass('pdc-hidden hide-if-js').show();
					$toggle.prop('checked', true);
					
					// replace HTML if needed
					var $replace = $postbox.find('.pdc-replace-with-fields');
					if( $replace.exists() ) {
						$replace.replaceWith( fieldGroup.html );
						pdc.doAction('append', $postbox);
					}
					
					// update style if needed
					if( i === 0 ) {
						$('#pdc-style').html( fieldGroup.style );
					}
					
					// enable inputs
					pdc.enable( $postbox, 'postbox' );
				});
			};
			
			// complete
			var onComplete = function( json ){
				
				// disable inputs
				$('.pdc-postbox.pdc-hidden').each(function(){
					pdc.disable( $(this), 'postbox' );
				});
			};
			
			// ajax
			this.xhr = $.ajax({
				url: pdc.get('ajaxurl'),
				data: pdc.prepareForAjax( ajaxData ),
				type: 'post',
				dataType: 'json',
				context: this,
				success: onSuccess,
				complete: onComplete
			});
		},
		
		onChange: function( e, $el ){
			this.setTimeout(this.check, 1);
		}
	});
	
/*	
	// tests
	pdc.registerScreenChange('#page_template', function( e, $el ){
		return $('#page_template').val();
	});
	
	pdc.registerScreenData({
		name: 'page_template',
		change: '#page_template',
		val: function(){
			var $input = $(this.el);
			return $input.length ? $input.val() : null;
		}
	});
	
	pdc.registerScreenData({
		name: 'post_terms',
		change: '.pdc-taxonomy-field[data-save="1"]',
		val: function(){
			var $input = $(this.el);
			return $input.length ? $input.val() : null;
		}
	});
	
	pdc.registerScreenData({
		name: 'post_terms',
		change: '#product-type',
		val: function( terms ){
			var $select = $('#product-type');
			if( $select.length ) {
				terms.push('product_cat:'+$select.val());
			}
			return terms;
		}
	});
	
	
	pdc.screen.get('post_terms');
	pdc.screen.getPostTerms();
	
*/

})(jQuery);

(function($, undefined){
	
	/**
	*  pdc.newSelect2
	*
	*  description
	*
	*  @date	13/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.newSelect2 = function( $select, props ){
		
		// defaults
		props = pdc.parseArgs(props, {
			allowNull:		false,
			placeholder:	'',
			multiple:		false,
			field: 			false,
			ajax:			false,
			ajaxAction:		'',
			ajaxData:		function( data ){ return data; },
			ajaxResults:	function( json ){ return json; },
		});
		
		// initialize
		if( getVersion() == 4 ) {
			var select2 = new Select2_4( $select, props );
		} else {
			var select2 = new Select2_3( $select, props );
		}
		
		// actions
		pdc.doAction('new_select2', select2);
		
		// return
		return select2;
	};
	
	/**
	*  getVersion
	*
	*  description
	*
	*  @date	13/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function getVersion() {
		
		// v4
		if( pdc.isset(window, 'jQuery', 'fn', 'select2', 'amd') ) {
			return 4;
		}
		
		// v3
		if( pdc.isset(window, 'Select2') ) {
			return 3;
		}
		
		// return
		return false;
	}
	
	/**
	*  Select2
	*
	*  description
	*
	*  @date	13/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var Select2 = pdc.Model.extend({
		
		setup: function( $select, props ){
			$.extend(this.data, props);
			this.$el = $select;
		},
		
		initialize: function(){
			
		},
		
		selectOption: function( value ){
			var $option = this.getOption( value );
			if( !$option.prop('selected') ) {
				$option.prop('selected', true).trigger('change');
			}
		},
		
		unselectOption: function( value ){
			var $option = this.getOption( value );
			if( $option.prop('selected') ) {
				$option.prop('selected', false).trigger('change');
			}
		},
		
		getOption: function( value ){
			return this.$('option[value="' + value + '"]');
		},
		
		addOption: function( option ){
			
			// defaults
			option = pdc.parseArgs(option, {
				id: '',
				text: '',
				selected: false
			});
			
			// vars
			var $option = this.getOption( option.id );
			
			// append
			if( !$option.length ) {
				$option = $('<option></option>');
				$option.html( option.text );
				$option.attr('value', option.id);
				$option.prop('selected', option.selected);
				this.$el.append($option);
			}
						
			// chain
			return $option;
		},
		
		getValue: function(){
			
			// vars
			var val = [];
			var $options = this.$el.find('option:selected');
			
			// bail early if no selected
			if( !$options.exists() ) {
				return val;
			}
			
			// sort by attribute
			$options = $options.sort(function(a, b) {
			    return +a.getAttribute('data-i') - +b.getAttribute('data-i');
			});
			
			// loop
			$options.each(function(){
				var $el = $(this);
				val.push({
					$el:	$el,
					id:		$el.attr('value'),
					text:	$el.text(),
				});
			});
			
			// return
			return val;
			
		},
		
		mergeOptions: function(){
				
		},
		
		getChoices: function(){
			
			// callback
			var crawl = function( $parent ){
				
				// vars
				var choices = [];
				
				// loop
				$parent.children().each(function(){
					
					// vars
					var $child = $(this);
					
					// optgroup
					if( $child.is('optgroup') ) {
						
						choices.push({
							text:		$child.attr('label'),
							children:	crawl( $child )
						});
					
					// option
					} else {
						
						choices.push({
							id:		$child.attr('value'),
							text:	$child.text()
						});
					}
				});
				
				// return
				return choices;
			};
			
			// crawl
			return crawl( this.$el );
		},
		
		decodeChoices: function( choices ){
			
			// callback
			var crawl = function( items ){
				items.map(function( item ){
					item.text = pdc.decode( item.text );
					if( item.children ) {
						item.children = crawl( item.children );
					}
					return item;
				});
				return items;
			};
			
			// crawl
			return crawl( choices );
		},
		
		getAjaxData: function( params ){
			
			// vars
			var ajaxData = {
				action: 	this.get('ajaxAction'),
				s: 			params.term || '',
				paged: 		params.page || 1
			};
			
			// field helper
			var field = this.get('field');
			if( field ) {
				ajaxData.field_key = field.get('key');
			}
			
			// callback
			var callback = this.get('ajaxData');
			if( callback ) {
				ajaxData = callback.apply( this, [ajaxData, params] );
			}
			
			// filter
			ajaxData = pdc.applyFilters( 'select2_ajax_data', ajaxData, this.data, this.$el, (field || false), this );
			
			// return
			return pdc.prepareForAjax(ajaxData);
		},
		
		getAjaxResults: function( json, params ){
			
			// defaults
			json = pdc.parseArgs(json, {
				results: false,
				more: false,
			});
			
			// decode
			if( json.results ) {
				json.results = this.decodeChoices(json.results);
			}
			
			// callback
			var callback = this.get('ajaxResults');
			if( callback ) {
				json = callback.apply( this, [json, params] );
			}
			
			// filter
			json = pdc.applyFilters( 'select2_ajax_results', json, params, this );
			
			// return
			return json;
		},
		
		processAjaxResults: function( json, params ){
			
			// vars
			var json = this.getAjaxResults( json, params );
			
			// change more to pagination
			if( json.more ) {
				json.pagination = { more: true };
			}
			
			// merge together groups
			setTimeout($.proxy(this.mergeOptions, this), 1);
			
			// return
			return json;
		},
		
		destroy: function(){
			
			// destroy via api
			if( this.$el.data('select2') ) {
				this.$el.select2('destroy');
			}
			
			// destory via HTML (duplicating HTML does not contain data)
			this.$el.siblings('.select2-container').remove();
		}
		
	});
	
	
	/**
	*  Select2_4
	*
	*  description
	*
	*  @date	13/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var Select2_4 = Select2.extend({
		
		initialize: function(){
			
			// vars
			var $select = this.$el;
			var options = {
				width:				'100%',
				allowClear:			this.get('allowNull'),
				placeholder:		this.get('placeholder'),
				multiple:			this.get('multiple'),
				data:				[],
				escapeMarkup:		function( m ){ return m; }
			};
			
			// multiple
			if( options.multiple ) {
				
				// reorder options
				this.getValue().map(function( item ){
					item.$el.detach().appendTo( $select );
				});
			}
			
		    // remove conflicting atts
		    $select.removeData('ajax');
			$select.removeAttr('data-ajax');
			
			// ajax
			if( this.get('ajax') ) {
				
				options.ajax = {
					url:			pdc.get('ajaxurl'),
					delay: 			250,
					dataType: 		'json',
					type: 			'post',
					cache: 			false,
					data:			$.proxy(this.getAjaxData, this),
					processResults:	$.proxy(this.processAjaxResults, this),
				};
			}
		    
			// filter for 3rd party customization
			//options = pdc.applyFilters( 'select2_args', options, $select, this );
			var field = this.get('field');
			options = pdc.applyFilters( 'select2_args', options, $select, this.data, (field || false), this );
			
			// add select2
			$select.select2( options );
			
			// get container (Select2 v4 does not return this from constructor)
			var $container = $select.next('.select2-container');
			
			// multiple
			if( options.multiple ) {
				
				// vars
				var $ul = $container.find('ul');
				
				// sortable
				$ul.sortable({
		            stop: function( e ) {
			            
			            // loop
			            $ul.find('.select2-selection__choice').each(function() {
				            
				            // vars
							var $option = $( $(this).data('data').element );
							
							// detach and re-append to end
							$option.detach().appendTo( $select );
		                });
		                
		                // trigger change on input (JS error if trigger on select)
	                    $select.trigger('change');
		            }
				});
				
				// on select, move to end
				$select.on('select2:select', this.proxy(function( e ){
					this.getOption( e.params.data.id ).detach().appendTo( this.$el );
				}));
			}
			
			// add class
			$container.addClass('-pdc');
			
			// action for 3rd party customization
			pdc.doAction('select2_init', $select, options, this.data, (field || false), this);
		},
		
		mergeOptions: function(){
			
			// vars
			var $prevOptions = false;
			var $prevGroup = false;
			
			// loop
			$('.select2-results__option[role="group"]').each(function(){
				
				// vars
				var $options = $(this).children('ul');
				var $group = $(this).children('strong');
				
				// compare to previous
				if( $prevGroup && $prevGroup.text() === $group.text() ) {
					$prevOptions.append( $options.children() );
					$(this).remove();
					return;
				}
				
				// update vars
				$prevOptions = $options;
				$prevGroup = $group;
				
			});
		},
		
	});
	
	/**
	*  Select2_3
	*
	*  description
	*
	*  @date	13/1/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var Select2_3 = Select2.extend({
		
		initialize: function(){
			
			// vars
			var $select = this.$el;
			var value = this.getValue();
			var multiple  = this.get('multiple');
			var options = {
				width:				'100%',
				allowClear:			this.get('allowNull'),
				placeholder:		this.get('placeholder'),
				separator:			'||',
				multiple:			this.get('multiple'),
				data:				this.getChoices(),
				escapeMarkup:		function( m ){ return m; },
				dropdownCss:		{
					'z-index': '999999999'
				},
				initSelection:		function( element, callback ) {
					if( multiple ) {
						callback( value );
					} else {
						callback( value.shift() );
					}
			    }
			};
			
			// get hidden input
			var $input = $select.siblings('input');
			if( !$input.length ) {
				$input = $('<input type="hidden" />');
				$select.before( $input );
			}
			
			// set input value
			inputValue = value.map(function(item){ return item.id }).join('||');
			$input.val( inputValue );
			
			// multiple
			if( options.multiple ) {
				
				// reorder options
				value.map(function( item ){
					item.$el.detach().appendTo( $select );
				});
			}
			
			// remove blank option as we have a clear all button
			if( options.allowClear ) {
				options.data = options.data.filter(function(item){
					return item.id !== '';
				});
			}
			
		    // remove conflicting atts
		    $select.removeData('ajax');
			$select.removeAttr('data-ajax');
			
			// ajax
			if( this.get('ajax') ) {
				
				options.ajax = {
					url:			pdc.get('ajaxurl'),
					quietMillis: 	250,
					dataType: 		'json',
					type: 			'post',
					cache: 			false,
					data:			$.proxy(this.getAjaxData, this),
					results:		$.proxy(this.processAjaxResults, this),
				};
			}
		    
			// filter for 3rd party customization
			var field = this.get('field');
			options = pdc.applyFilters( 'select2_args', options, $select, this.data, (field || false), this );
			
			// add select2
			$input.select2( options );
			
			// get container
			var $container = $input.select2('container');
			
			// helper to find this select's option
			var getOption = $.proxy(this.getOption, this);
				
			// multiple
			if( options.multiple ) {
			
				// vars
				var $ul = $container.find('ul');
				
				// sortable
				$ul.sortable({
		            stop: function() {
			            
			            // loop
			            $ul.find('.select2-search-choice').each(function() {
				            
				            // vars
				            var data = $(this).data('select2Data');
				            var $option = getOption( data.id );
				            
							// detach and re-append to end
							$option.detach().appendTo( $select );
		                });
		                
		                // trigger change on input (JS error if trigger on select)
	                    $select.trigger('change');
		            }
				});
			}
			
			// on select, create option and move to end
			$input.on('select2-selecting', function( e ){
				
				// vars
				var item = e.choice;
				var $option = getOption( item.id );
				
				// create if doesn't exist
				if( !$option.length ) {
					$option = $('<option value="' + item.id + '">' + item.text + '</option>');
				}
				
				// detach and re-append to end
				$option.detach().appendTo( $select );
			});
			
			// add class
			$container.addClass('-pdc');
			
			// action for 3rd party customization
			pdc.doAction('select2_init', $select, options, this.data, (field || false), this);
			
			// change
			$input.on('change', function(){
				var val = $input.val();
				if( val.indexOf('||') ) {
					val = val.split('||');
				}
				$select.val( val ).trigger('change');
			});
			
			// hide select
			$select.hide();
		},
		
		mergeOptions: function(){
			
			// vars
			var $prevOptions = false;
			var $prevGroup = false;
			
			// loop
			$('#select2-drop .select2-result-with-children').each(function(){
				
				// vars
				var $options = $(this).children('ul');
				var $group = $(this).children('.select2-result-label');
				
				// compare to previous
				if( $prevGroup && $prevGroup.text() === $group.text() ) {
					$prevGroup.append( $options.children() );
					$(this).remove();
					return;
				}
				
				// update vars
				$prevOptions = $options;
				$prevGroup = $group;
				
			});
			
		},
		
		getAjaxData: function( term, page ){
			
			// create Select2 v4 params
			var params = {
				term: term,
				page: page
			}
			
			// return
			return Select2.prototype.getAjaxData.apply(this, [params]);
		},
		
	});
	
	
	// manager
	var select2Manager = new pdc.Model({
		priority: 5,
		wait: 'prepare',
		initialize: function(){
			
			// vars
			var locale = pdc.get('locale');
			var rtl = pdc.get('rtl');
			var l10n = pdc.get('select2L10n');
			var version = getVersion();
			
			// bail ealry if no l10n
			if( !l10n ) {
				return false;
			}
			
			// bail early if 'en'
			if( locale.indexOf('en') === 0 ) {
				return false;
			}
			
			// initialize
			if( version == 4 ) {
				this.addTranslations4();
			} else if( version == 3 ) {
				this.addTranslations3();
			}
		},
		
		addTranslations4: function(){
			
			// vars
			var l10n = pdc.get('select2L10n');
			var locale = pdc.get('locale');
			
			// modify local to match html[lang] attribute (used by Select2)
			locale = locale.replace('_', '-');
			
			// select2L10n
			var select2L10n = {
				errorLoading: function () {
					return l10n.load_fail;
				},
				inputTooLong: function (args) {
					var overChars = args.input.length - args.maximum;
					if( overChars > 1 ) {
						return l10n.input_too_long_n.replace( '%d', overChars );
					}
					return l10n.input_too_long_1;
				},
				inputTooShort: function( args ){
					var remainingChars = args.minimum - args.input.length;
					if( remainingChars > 1 ) {
						return l10n.input_too_short_n.replace( '%d', remainingChars );
					}
					return l10n.input_too_short_1;
				},
				loadingMore: function () {
					return l10n.load_more;
				},
				maximumSelected: function( args ) {
					var maximum = args.maximum;
					if( maximum > 1 ) {
						return l10n.selection_too_long_n.replace( '%d', maximum );
					}
					return l10n.selection_too_long_1;
				},
				noResults: function () {
					return l10n.matches_0;
				},
				searching: function () {
					return l10n.searching;
				}
			};
				
			// append
			jQuery.fn.select2.amd.define('select2/i18n/' + locale, [], function(){
				return select2L10n;
			});
		},
		
		addTranslations3: function(){
			
			// vars
			var l10n = pdc.get('select2L10n');
			var locale = pdc.get('locale');
			
			// modify local to match html[lang] attribute (used by Select2)
			locale = locale.replace('_', '-');
			
			// select2L10n
			var select2L10n = {
				formatMatches: function( matches ) {
					if( matches > 1 ) {
						return l10n.matches_n.replace( '%d', matches );
					}
					return l10n.matches_1;
				},
				formatNoMatches: function() {
					return l10n.matches_0;
				},
				formatAjaxError: function() {
					return l10n.load_fail;
				},
				formatInputTooShort: function( input, min ) {
					var remainingChars = min - input.length;
					if( remainingChars > 1 ) {
						return l10n.input_too_short_n.replace( '%d', remainingChars );
					}
					return l10n.input_too_short_1;
				},
				formatInputTooLong: function( input, max ) {
					var overChars = input.length - max;
					if( overChars > 1 ) {
						return l10n.input_too_long_n.replace( '%d', overChars );
					}
					return l10n.input_too_long_1;
				},
				formatSelectionTooBig: function( maximum ) {
					if( maximum > 1 ) {
						return l10n.selection_too_long_n.replace( '%d', maximum );
					}
					return l10n.selection_too_long_1;
				},
				formatLoadMore: function() {
					return l10n.load_more;
				},
				formatSearching: function() {
					return l10n.searching;
				}
		    };
		    
		    // ensure locales exists
			$.fn.select2.locales = $.fn.select2.locales || {};
			
			// append
			$.fn.select2.locales[ locale ] = select2L10n;
			$.extend($.fn.select2.defaults, select2L10n);
		}
		
	});
	
})(jQuery);

(function($, undefined){
	
	pdc.tinymce = {
		
		/*
		*  defaults
		*
		*  This function will return default mce and qt settings
		*
		*  @type	function
		*  @date	18/8/17
		*  @since	5.6.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		defaults: function(){
			
			// bail early if no tinyMCEPreInit
			if( typeof tinyMCEPreInit === 'undefined' ) return false;
			
			// vars
			var defaults = {
				tinymce:	tinyMCEPreInit.mceInit.pdc_content,
				quicktags:	tinyMCEPreInit.qtInit.pdc_content
			};
			
			// return
			return defaults;
		},
		
		
		/*
		*  initialize
		*
		*  This function will initialize the tinymce and quicktags instances
		*
		*  @type	function
		*  @date	18/8/17
		*  @since	5.6.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		initialize: function( id, args ){
			
			// defaults
			args = pdc.parseArgs(args, {
				tinymce:	true,
				quicktags:	true,
				toolbar:	'full',
				mode:		'visual', // visual,text
				field:		false
			});
			
			// tinymce
			if( args.tinymce ) {
				this.initializeTinymce( id, args );
			}
			
			// quicktags
			if( args.quicktags ) {
				this.initializeQuicktags( id, args );
			}
		},
		
		
		/*
		*  initializeTinymce
		*
		*  This function will initialize the tinymce instance
		*
		*  @type	function
		*  @date	18/8/17
		*  @since	5.6.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		initializeTinymce: function( id, args ){
			
			// vars
			var $textarea = $('#'+id);
			var defaults = this.defaults();
			var toolbars = pdc.get('toolbars');
			var field = args.field || false;
			var $field = field.$el || false;
			
			// bail early
			if( typeof tinymce === 'undefined' ) return false;
			if( !defaults ) return false;
			
			// check if exists
			if( tinymce.get(id) ) {
				return this.enable( id );
			}
			
			// settings
			var init = $.extend( {}, defaults.tinymce, args.tinymce );
			init.id = id;
			init.selector = '#' + id;
			
			// toolbar
			var toolbar = args.toolbar;
			if( toolbar && toolbars && toolbars[toolbar] ) {
				
				for( var i = 1; i <= 4; i++ ) {
					init[ 'toolbar' + i ] = toolbars[toolbar][i] || '';
				}
			}
			
			// event
			init.setup = function( ed ){
				
				ed.on('change', function(e) {
					ed.save(); // save to textarea	
					$textarea.trigger('change');
				});
				
				$( ed.getWin() ).on('unload', function() {
					pdc.tinymce.remove( id );
				});
				
			};
			
			// disable wp_autoresize_on (no solution yet for fixed toolbar)
			init.wp_autoresize_on = false;
			
			// hook for 3rd party customization
			init = pdc.applyFilters('wysiwyg_tinymce_settings', init, id, field);
			
			// z-index fix (caused too many conflicts)
			//if( pdc.isset(tinymce,'ui','FloatPanel') ) {
			//	tinymce.ui.FloatPanel.zIndex = 900000;
			//}
			
			// store settings
			tinyMCEPreInit.mceInit[ id ] = init;
			
			// visual tab is active
			if( args.mode == 'visual' ) {
				
				// init 
				var result = tinymce.init( init );
				
				// get editor
				var ed = tinymce.get( id );
				
				// validate
				if( !ed ) {
					return false;
				}
				
				// add reference
				ed.pdc = args.field;
				
				// action
				pdc.doAction('wysiwyg_tinymce_init', ed, ed.id, init, field);
			}
		},
		
		/*
		*  initializeQuicktags
		*
		*  This function will initialize the quicktags instance
		*
		*  @type	function
		*  @date	18/8/17
		*  @since	5.6.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		initializeQuicktags: function( id, args ){
			
			// vars
			var defaults = this.defaults();
			
			// bail early
			if( typeof quicktags === 'undefined' ) return false;
			if( !defaults ) return false;
			
			// settings
			var init = $.extend( {}, defaults.quicktags, args.quicktags );
			init.id = id;
			
			// filter
			var field = args.field || false;
			var $field = field.$el || false;
			init = pdc.applyFilters('wysiwyg_quicktags_settings', init, init.id, field);
			
			// store settings
			tinyMCEPreInit.qtInit[ id ] = init;
			
			// init
			var ed = quicktags( init );
			
			// validate
			if( !ed ) {
				return false;
			}
			
			// generate HTML
			this.buildQuicktags( ed );
			
			// action for 3rd party customization
			pdc.doAction('wysiwyg_quicktags_init', ed, ed.id, init, field);
		},
		
		
		/*
		*  buildQuicktags
		*
		*  This function will build the quicktags HTML
		*
		*  @type	function
		*  @date	18/8/17
		*  @since	5.6.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		buildQuicktags: function( ed ){
			
			var canvas, name, settings, theButtons, html, ed, id, i, use, instanceId,
				defaults = ',strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,';
			
			canvas = ed.canvas;
			name = ed.name;
			settings = ed.settings;
			html = '';
			theButtons = {};
			use = '';
			instanceId = ed.id;
			
			// set buttons
			if ( settings.buttons ) {
				use = ','+settings.buttons+',';
			}

			for ( i in edButtons ) {
				if ( ! edButtons[i] ) {
					continue;
				}

				id = edButtons[i].id;
				if ( use && defaults.indexOf( ',' + id + ',' ) !== -1 && use.indexOf( ',' + id + ',' ) === -1 ) {
					continue;
				}

				if ( ! edButtons[i].instance || edButtons[i].instance === instanceId ) {
					theButtons[id] = edButtons[i];

					if ( edButtons[i].html ) {
						html += edButtons[i].html( name + '_' );
					}
				}
			}

			if ( use && use.indexOf(',dfw,') !== -1 ) {
				theButtons.dfw = new QTags.DFWButton();
				html += theButtons.dfw.html( name + '_' );
			}

			if ( 'rtl' === document.getElementsByTagName( 'html' )[0].dir ) {
				theButtons.textdirection = new QTags.TextDirectionButton();
				html += theButtons.textdirection.html( name + '_' );
			}

			ed.toolbar.innerHTML = html;
			ed.theButtons = theButtons;

			if ( typeof jQuery !== 'undefined' ) {
				jQuery( document ).triggerHandler( 'quicktags-init', [ ed ] );
			}
			
		},
		
		disable: function( id ){
			this.destroyTinymce( id );
		},
		
		remove: function( id ){
			this.destroyTinymce( id );
		},
		
		destroy: function( id ){
			this.destroyTinymce( id );
		},
		
		destroyTinymce: function( id ){
			
			// bail early
			if( typeof tinymce === 'undefined' ) return false;
			
			// get editor
			var ed = tinymce.get( id );
			
			// bail early if no editor
			if( !ed ) return false;
			
			// save
			ed.save();
			
			// destroy editor
			ed.destroy();
			
			// return
			return true;
		},
		
		enable: function( id ){
			this.enableTinymce( id );
		},
		
		enableTinymce: function( id ){
			
			// bail early
			if( typeof switchEditors === 'undefined' ) return false;
			
			// bail ealry if not initialized
			if( typeof tinyMCEPreInit.mceInit[ id ] === 'undefined' ) return false;
						
			// toggle			
			switchEditors.go( id, 'tmce');
			
			// return
			return true;
		}
	};
	
	var editorManager = new pdc.Model({
		
		// hook in before fieldsEventManager, conditions, etc
		priority: 5,
		
		actions: {
			'prepare':	'onPrepare',
			'ready':	'onReady',
		},
		onPrepare: function(){
			
			// find hidden editor which may exist within a field
			var $div = $('#pdc-hidden-wp-editor');
			
			// move to footer
			if( $div.exists() ) {
				$div.appendTo('body');
			}
		},
		onReady: function(){
			
			// bail early if no tinymce
			if( !pdc.isset(window,'tinymce','on') ) return;
			
			// restore default activeEditor
			tinymce.on('AddEditor', function( data ){
				
				// vars
				var editor = data.editor;
				
				// bail early if not 'pdc'
				if( editor.id.substr(0, 3) !== 'pdc' ) return;
				
				// override if 'content' exists
				editor = tinymce.editors.content || editor;
				
				// update vars
				tinymce.activeEditor = editor;
				wpActiveEditor = editor.id;
			});
		}
	});
	
})(jQuery);

(function($, undefined){
	
	/**
	*  Validator
	*
	*  The model for validating forms
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	void
	*  @return	void
	*/
	var Validator = pdc.Model.extend({
		
		/** @var string The model identifier. */
		id: 'Validator',
		
		/** @var object The model data. */
		data: {
			
			/** @var array The form errors. */
			errors: [],
			
			/** @var object The form notice. */
			notice: null,
			
			/** @var string The form status. loading, invalid, valid */
			status: ''
		},
		
		/** @var object The model events. */
		events: {
			'changed:status': 'onChangeStatus'
		},
		
		/**
		*  addErrors
		*
		*  Adds errors to the form.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	array errors An array of errors.
		*  @return	void
		*/
		addErrors: function( errors ){
			errors.map( this.addError, this );
		},
		
		/**
		*  addError
		*
		*  Adds and error to the form.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object error An error object containing input and message.
		*  @return	void
		*/
		addError: function( error ){
			this.data.errors.push( error );
		},
		
		/**
		*  hasErrors
		*
		*  Returns true if the form has errors.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	bool
		*/
		hasErrors: function(){
			return this.data.errors.length;
		},
		
		/**
		*  clearErrors
		*
		*  Removes any errors.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	void
		*/
		clearErrors: function(){
			return this.data.errors = [];
		},
		
		/**
		*  getErrors
		*
		*  Returns the forms errors.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	array
		*/
		getErrors: function(){
			return this.data.errors;
		},
		
		/**
		*  getFieldErrors
		*
		*  Returns the forms field errors.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	array
		*/
		getFieldErrors: function(){
			
			// vars
			var errors = [];
			var inputs = [];
			
			// loop
			this.getErrors().map(function(error){
				
				// bail early if global
				if( !error.input ) return;
				
				// update if exists
				var i = inputs.indexOf(error.input);
				if( i > -1 ) {
					errors[ i ] = error;
				
				// update
				} else {
					errors.push( error );
					inputs.push( error.input );
				}
			});
			
			// return
			return errors;
		},
		
		/**
		*  getGlobalErrors
		*
		*  Returns the forms global errors (errors without a specific input).
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	array
		*/
		getGlobalErrors: function(){
			
			// return array of errors that contain no input
			return this.getErrors().filter(function(error){
				return !error.input;
			});
		},
		
		/**
		*  showErrors
		*
		*  Displays all errors for this form.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	void
		*/
		showErrors: function(){
			
			// bail early if no errors
			if( !this.hasErrors() ) {
				return;
			}
			
			// vars
			var fieldErrors = this.getFieldErrors();
			var globalErrors = this.getGlobalErrors();
			
			// vars
			var errorCount = 0;
			var $scrollTo = false;
			
			// loop
			fieldErrors.map(function( error ){
				
				// get input
				var $input = this.$('[name="' + error.input + '"]').first();
				
				// if $_POST value was an array, this $input may not exist
				if( !$input.length ) {
					$input = this.$('[name^="' + error.input + '"]').first();
				}
				
				// bail early if input doesn't exist
				if( !$input.length ) {
					return;
				}
				
				// increase
				errorCount++;
				
				// get field
				var field = pdc.getClosestField( $input );
				
				// show error
				field.showError( error.message );
				
				// set $scrollTo
				if( !$scrollTo ) {
					$scrollTo = field.$el;
				}
			}, this);
			
			// errorMessage
			var errorMessage = pdc.__('Validation failed');
			globalErrors.map(function( error ){
				errorMessage += '. ' + error.message;
			});
			if( errorCount == 1 ) {
				errorMessage += '. ' + pdc.__('1 field requires attention');
			} else if( errorCount > 1 ) {
				errorMessage += '. ' + pdc.__('%d fields require attention').replace('%d', errorCount);
			}
			
			// notice
			if( this.has('notice') ) {
				this.get('notice').update({
					type: 'error',
					text: errorMessage
				});
			} else {
				var notice = pdc.newNotice({
					type: 'error',
					text: errorMessage,
					target: this.$el
				});
				this.set('notice', notice);
			}
			
			// if no $scrollTo, set to message
			if( !$scrollTo ) {
				$scrollTo = this.get('notice').$el;
			}
			
			// timeout
			setTimeout(function(){
				$("html, body").animate({ scrollTop: $scrollTo.offset().top - ( $(window).height() / 2 ) }, 500);
			}, 10);
		},
		
		/**
		*  onChangeStatus
		*
		*  Update the form class when changing the 'status' data
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object e The event object.
		*  @param	jQuery $el The form element.
		*  @param	string value The new status.
		*  @param	string prevValue The old status.
		*  @return	void
		*/
		onChangeStatus: function( e, $el, value, prevValue ){
			this.$el.removeClass('is-'+prevValue).addClass('is-'+value);
		},
		
		/**
		*  validate
		*
		*  Vaildates the form via AJAX.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object args A list of settings to customize the validation process.
		*  @return	bool True if the form is valid.
		*/
		validate: function( args ){
			
			// default args
			args = pdc.parseArgs(args, {
				
				// trigger event
				event: false,
				
				// reset the form after submit
				reset: false,
				
				// loading callback
				loading: function(){},
				
				// complete callback
				complete: function(){},
				
				// failure callback
				failure: function(){},
				
				// success callback
				success: function( $form ){
					$form.submit();
				}
			});
			
			// return true if is valid - allows form submit
			if( this.get('status') == 'valid' ) {
				return true;
			}
			
			// return false if is currently validating - prevents form submit
			if( this.get('status') == 'validating' ) {
				return false;
			}
			
			// return true if no PDC fields exist (no need to validate)
			if( !this.$('.pdc-field').length ) {
				return true;
			}
			
			// if event is provided, create a new success callback.
			if( args.event ) {
				var event = $.Event(null, args.event);
				args.success = function(){
					pdc.enableSubmit( $(event.target) ).trigger( event );
				}
			}
			
			// action for 3rd party
			pdc.doAction('validation_begin', this.$el);
			
			// lock form
			pdc.lockForm( this.$el );
						
			// loading callback
			args.loading( this.$el );
			
			// update status
			this.set('status', 'validating');
			
			// success callback
			var onSuccess = function( json ){
				
				// validate
				if( !pdc.isAjaxSuccess(json) ) {
					return;
				}
				
				// filter
				var data = pdc.applyFilters('validation_complete', json.data, this.$el);
				
				// add errors
				if( !data.valid ) {
					this.addErrors( data.errors );
				}
			};
			
			// complete
			var onComplete = function(){
				
				// unlock form
				pdc.unlockForm( this.$el );
				
				// failure
				if( this.hasErrors() ) {
					
					// update status
					this.set('status', 'invalid');
			
					// action
					pdc.doAction('validation_failure', this.$el);
					
					// display errors
					this.showErrors();
					
					// failure callback
					args.failure( this.$el );
				
				// success
				} else {
					
					// update status
					this.set('status', 'valid');
					
					// remove previous error message
					if( this.has('notice') ) {
						this.get('notice').update({
							type: 'success',
							text: pdc.__('Validation successful'),
							timeout: 1000
						});
					}
					
					// action
					pdc.doAction('validation_success', this.$el);
					pdc.doAction('submit', this.$el);
					
					// success callback (submit form)
					args.success( this.$el );
					
					// lock form
					pdc.lockForm( this.$el );
					
					// reset
					if( args.reset ) {
						this.reset();	
					}
				}
				
				// complete callback
				args.complete( this.$el );
				
				// clear errors
				this.clearErrors();
			};
			
			// serialize form data
			var data = pdc.serialize( this.$el );
			data.action = 'pdc/validate_save_post';
			
			// ajax
			$.ajax({
				url: pdc.get('ajaxurl'),
				data: pdc.prepareForAjax(data),
				type: 'post',
				dataType: 'json',
				context: this,
				success: onSuccess,
				complete: onComplete
			});
		},
		
		/**
		*  setup
		*
		*  Called during the constructor function to setup this instance
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	jQuery $form The form element.
		*  @return	void
		*/
		setup: function( $form ){
			
			// set $el
			this.$el = $form;
		},
		
		/**
		*  reset
		*
		*  Rests the validation to be used again.
		*
		*  @date	6/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	void
		*/
		reset: function(){
			
			// reset data
			this.set('errors', []);
			this.set('notice', null);
			this.set('status', '');
			
			// unlock form
			pdc.unlockForm( this.$el );
		}
	});
	
	/**
	*  getValidator
	*
	*  Returns the instance for a given form element.
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	jQuery $el The form element.
	*  @return	object
	*/
	var getValidator = function( $el ){
		
		// instantiate
		var validator = $el.data('pdc');
		if( !validator ) {
			validator = new Validator( $el );
		}
		
		// return
		return validator;
	};
	
	/**
	*  pdc.validateForm
	*
	*  A helper function for the Validator.validate() function.
	*  Returns true if form is valid, or fetches a validation request and returns false.
	*
	*  @date	4/4/18
	*  @since	5.6.9
	*
	*  @param	object args A list of settings to customize the validation process.
	*  @return	bool
	*/
	
	pdc.validateForm = function( args ){
		return getValidator( args.form ).validate( args );
	};
	
	/**
	*  pdc.enableSubmit
	*
	*  Enables a submit button and returns the element.
	*
	*  @date	30/8/18
	*  @since	5.7.4
	*
	*  @param	jQuery $submit The submit button.
	*  @return	jQuery
	*/
	pdc.enableSubmit = function( $submit ){
		return $submit.removeClass('disabled');
	};
		
	/**
	*  pdc.disableSubmit
	*
	*  Disables a submit button and returns the element.
	*
	*  @date	30/8/18
	*  @since	5.7.4
	*
	*  @param	jQuery $submit The submit button.
	*  @return	jQuery
	*/
	pdc.disableSubmit = function( $submit ){
		return $submit.addClass('disabled');
	};
	
	/**
	*  pdc.showSpinner
	*
	*  Shows the spinner element.
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	jQuery $spinner The spinner element.
	*  @return	jQuery
	*/
	pdc.showSpinner = function( $spinner ){
		$spinner.addClass('is-active');				// add class (WP > 4.2)
		$spinner.css('display', 'inline-block');	// css (WP < 4.2)
		return $spinner;
	};
	
	/**
	*  pdc.hideSpinner
	*
	*  Hides the spinner element.
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	jQuery $spinner The spinner element.
	*  @return	jQuery
	*/
	pdc.hideSpinner = function( $spinner ){
		$spinner.removeClass('is-active');			// add class (WP > 4.2)
		$spinner.css('display', 'none');			// css (WP < 4.2)
		return $spinner;
	};
	
	/**
	*  pdc.lockForm
	*
	*  Locks a form by disabeling its primary inputs and showing a spinner.
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	jQuery $form The form element.
	*  @return	jQuery
	*/
	pdc.lockForm = function( $form ){
		
		// vars
		var $wrap = findSubmitWrap( $form );
		var $submit = $wrap.find('.button, [type="submit"]');
		var $spinner = $wrap.find('.spinner, .pdc-spinner');
		
		// hide all spinners (hides the preview spinner)
		pdc.hideSpinner( $spinner );
		
		// lock
		pdc.disableSubmit( $submit );
		pdc.showSpinner( $spinner.last() );
		return $form;
	};
	
	/**
	*  pdc.unlockForm
	*
	*  Unlocks a form by enabeling its primary inputs and hiding all spinners.
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	jQuery $form The form element.
	*  @return	jQuery
	*/
	pdc.unlockForm = function( $form ){
		
		// vars
		var $wrap = findSubmitWrap( $form );
		var $submit = $wrap.find('.button, [type="submit"]');
		var $spinner = $wrap.find('.spinner, .pdc-spinner');
		
		// unlock
		pdc.enableSubmit( $submit );
		pdc.hideSpinner( $spinner );
		return $form;
	};
	
	/**
	*  findSubmitWrap
	*
	*  An internal function to find the 'primary' form submit wrapping element.
	*
	*  @date	4/9/18
	*  @since	5.7.5
	*
	*  @param	jQuery $form The form element.
	*  @return	jQuery
	*/
	var findSubmitWrap = function( $form ){
		
		// default post submit div
		var $wrap = $form.find('#submitdiv');
		if( $wrap.length ) {
			return $wrap;
		}
		
		// 3rd party publish box
		var $wrap = $form.find('#submitpost');
		if( $wrap.length ) {
			return $wrap;
		}
		
		// term, user
		var $wrap = $form.find('p.submit').last();
		if( $wrap.length ) {
			return $wrap;
		}
		
		// front end form
		var $wrap = $form.find('.pdc-form-submit');
		if( $wrap.length ) {
			return $wrap;
		}
		
		// default
		return $form;
	};
	
	/**
	*  pdc.validation
	*
	*  Global validation logic
	*
	*  @date	4/4/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	pdc.validation = new pdc.Model({
		
		/** @var string The model identifier. */
		id: 'validation',
		
		/** @var bool The active state. Set to false before 'prepare' to prevent validation. */
		active: true,
		
		/** @var string The model initialize time. */
		wait: 'prepare',
		
		/** @var object The model actions. */
		actions: {
			'ready':	'addInputEvents',
			'append':	'addInputEvents'
		},
		
		/** @var object The model events. */
		events: {
			'click input[type="submit"]':	'onClickSubmit',
			'click button[type="submit"]':	'onClickSubmit',
			'click #save-post':				'onClickSave',
			'mousedown #post-preview':		'onClickPreview', // use mousedown to hook in before WP click event
			'submit form':					'onSubmit',
		},
		
		/**
		*  initialize
		*
		*  Called when initializing the model.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	void
		*/
		initialize: function(){
			
			// check 'validation' setting
			if( !pdc.get('validation') ) {
				this.active = false;
				this.actions = {};
				this.events = {};
			}
		},
		
		/**
		*  enable
		*
		*  Enables validation.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	void
		*/
		enable: function(){
			this.active = true;
		},
		
		/**
		*  disable
		*
		*  Disables validation.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	void
		*  @return	void
		*/
		disable: function(){
			this.active = false;
		},
		
		/**
		*  reset
		*
		*  Rests the form validation to be used again
		*
		*  @date	6/9/18
		*  @since	5.7.5
		*
		*  @param	jQuery $form The form element.
		*  @return	void
		*/
		reset: function( $form ){
			getValidator( $form ).reset();
		},
		
		/**
		*  addInputEvents
		*
		*  Adds 'invalid' event listeners to HTML inputs.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	jQuery $el The element being added / readied.
		*  @return	void
		*/
		addInputEvents: function( $el ){
			
			// vars
			var $inputs = $('.pdc-field [name]', $el);
			
			// check
			if( $inputs.length ) {
				this.on( $inputs, 'invalid', 'onInvalid' );
			}
		},
		
		/**
		*  onInvalid
		*
		*  Callback for the 'invalid' event.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object e The event object.
		*  @param	jQuery $el The input element.
		*  @return	void
		*/
		onInvalid: function( e, $el ){
			
			// prevent default
			// - prevents browser error message
			// - also fixes chrome bug where 'hidden-by-tab' field throws focus error
			e.preventDefault();
				
			// vars
			var $form = $el.closest('form');
			
			// check form exists
			if( $form.length ) {
				
				// add error to validator
				getValidator( $form ).addError({
					input: $el.attr('name'),
					message: e.target.validationMessage
				});
				
				// trigger submit on $form
				// - allows for "save", "preview" and "publish" to work
				$form.submit();
			}
		},
		
		/**
		*  onClickSubmit
		*
		*  Callback when clicking submit.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object e The event object.
		*  @param	jQuery $el The input element.
		*  @return	void
		*/
		onClickSubmit: function( e, $el ){
			
			// store the "click event" for later use in this.onSubmit()
			this.set('originalEvent', e);
		},
		
		/**
		*  onClickSave
		*
		*  Set ignore to true when saving a draft.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object e The event object.
		*  @param	jQuery $el The input element.
		*  @return	void
		*/
		onClickSave: function( e, $el ) {
			this.set('ignore', true);
		},
		
		/**
		*  onClickPreview
		*
		*  Set ignore to true when previewing a post.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object e The event object.
		*  @param	jQuery $el The input element.
		*  @return	void
		*/
		onClickPreview: function( e, $el ) {
			this.set('ignore', true);
			
			// if post has previously been published but prevented by an error, WP core has
			// added a custom 'submit.edit-post' event which causes the input buttons to become disabled.
			// remove this event to prevent UX issues.
			$('form#post').off('submit.edit-post');
		},
		
		/**
		*  onSubmit
		*
		*  Callback when the form is submit.
		*
		*  @date	4/9/18
		*  @since	5.7.5
		*
		*  @param	object e The event object.
		*  @param	jQuery $el The input element.
		*  @return	void
		*/
		onSubmit: function( e, $el ){
			
			// bail early if is disabled
			if( !this.active ) {
				return;
			}
			
			// bail early if is ignore
			if( this.get('ignore') ) {
				this.set('ignore', false);
				return;
			}
			
			// validate
			var valid = pdc.validateForm({
				form: $el,
				event: this.get('originalEvent')
			});
			
			// if not valid, stop event and allow validation to continue
			if( !valid ) {
				e.preventDefault();
			}
		}
	});
	
})(jQuery);

(function($, undefined){
	
	/**
	*  refreshHelper
	*
	*  description
	*
	*  @date	1/7/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var refreshHelper = new pdc.Model({
		priority: 90,
		timeout: 0,
		actions: {
			'new_field':	'refresh',
			'show_field':	'refresh',
			'hide_field':	'refresh',
			'remove_field':	'refresh'
		},
		refresh: function(){
			clearTimeout( this.timeout );
			this.timeout = setTimeout(function(){
				pdc.doAction('refresh');
			}, 0);
		}
	});
	
	
	/**
	*  sortableHelper
	*
	*  Adds compatibility for sorting a <tr> element
	*
	*  @date	6/3/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
		
	var sortableHelper = new pdc.Model({
		actions: {
			'sortstart': 'onSortstart'
		},
		onSortstart: function( $item, $placeholder ){
			
			// if $item is a tr, apply some css to the elements
			if( $item.is('tr') ) {
				
				// temp set as relative to find widths
				$item.css('position', 'relative');
				
				// set widths for td children		
				$item.children().each(function(){
					$(this).width($(this).width());
				});
				
				// revert position css
				$item.css('position', 'absolute');
				
				// add markup to the placeholder
				$placeholder.html('<td style="height:' + $item.height() + 'px; padding:0;" colspan="' + $item.children('td').length + '"></td>');
			}
		}
	});
	
	/**
	*  duplicateHelper
	*
	*  Fixes browser bugs when duplicating an element
	*
	*  @date	6/3/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	var duplicateHelper = new pdc.Model({
		actions: {
			'after_duplicate': 'onAfterDuplicate'
		},
		onAfterDuplicate: function( $el, $el2 ){
			
			// get original values
			var vals = [];
			$el.find('select').each(function(i){
				vals.push( $(this).val() );
			});
			
			// set duplicate values
			$el2.find('select').each(function(i){
				$(this).val( vals[i] );
			});
		}
	});
	
	/**
	*  tableHelper
	*
	*  description
	*
	*  @date	6/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var tableHelper = new pdc.Model({
		
		id: 'tableHelper',
		
		priority: 20,
		
		actions: {
			'refresh': 	'renderTables'
		},
		
		renderTables: function( $el ){ 
			
			// loop
			var self = this;
			$('.pdc-table:visible').each(function(){
				self.renderTable( $(this) );
			});
		},
		
		renderTable: function( $table ){
			
			// vars
			var $ths = $table.find('> thead > tr:visible > th[data-key]');
			var $tds = $table.find('> tbody > tr:visible > td[data-key]');
			
			// bail early if no thead
			if( !$ths.length || !$tds.length ) {
				return false;
			}
			
			
			// visiblity
			$ths.each(function( i ){
				
				// vars
				var $th = $(this);
				var key = $th.data('key');
				var $cells = $tds.filter('[data-key="' + key + '"]');
				var $hidden = $cells.filter('.pdc-hidden');
				
				// always remove empty and allow cells to be hidden
				$cells.removeClass('pdc-empty');
				
				// hide $th if all cells are hidden
				if( $cells.length === $hidden.length ) {
					pdc.hide( $th );
					
				// force all hidden cells to appear empty
				} else {
					pdc.show( $th );
					$hidden.addClass('pdc-empty');
				}
			});
			
			
			// clear width
			$ths.css('width', 'auto');
			
			// get visible
			$ths = $ths.not('.pdc-hidden');
			
			// vars
			var availableWidth = 100;
			var colspan = $ths.length;
			
			// set custom widths first
			var $fixedWidths = $ths.filter('[data-width]');
			$fixedWidths.each(function(){
				var width = $(this).data('width');
				$(this).css('width', width + '%');
				availableWidth -= width;
			});
			
			// set auto widths
			var $auoWidths = $ths.not('[data-width]');
			if( $auoWidths.length ) {
				var width = availableWidth / $auoWidths.length;
				$auoWidths.css('width', width + '%');
				availableWidth = 0;
			}
			
			// avoid stretching issue
			if( availableWidth > 0 ) {
				$ths.last().css('width', 'auto');
			}
			
			
			// update colspan on collapsed
			$tds.filter('.-collapsed-target').each(function(){
				
				// vars
				var $td = $(this);
				
				// check if collapsed
				if( $td.parent().hasClass('-collapsed') ) {
					$td.attr('colspan', $ths.length);
				} else {
					$td.removeAttr('colspan');
				}
			});
		}
	});
	
	
	/**
	*  fieldsHelper
	*
	*  description
	*
	*  @date	6/3/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var fieldsHelper = new pdc.Model({
		
		id: 'fieldsHelper',
		
		priority: 30,
		
		actions: {
			'refresh': 	'renderGroups'
		},
		
		renderGroups: function(){
			
			// loop
			var self = this;
			$('.pdc-fields:visible').each(function(){
				self.renderGroup( $(this) );
			});
		},
		
		renderGroup: function( $el ){
			
			// vars
			var top = 0;
			var height = 0;
			var $row = $();
			
			// get fields
			var $fields = $el.children('.pdc-field[data-width]:visible');
			
			// bail early if no fields
			if( !$fields.length ) {
				return false;
			}
			
			// bail ealry if is .-left
			if( $el.hasClass('-left') ) {
				$fields.removeAttr('data-width');
				$fields.css('width', 'auto');
				return false;
			}
			
			// reset fields
			$fields.removeClass('-r0 -c0').css({'min-height': 0});
			
			// loop
			$fields.each(function( i ){
				
				// vars
				var $field = $(this);
				var position = $field.position();
				var thisTop = Math.ceil( position.top );
				var thisLeft = Math.ceil( position.left );
				
				// detect change in row
				if( $row.length && thisTop > top ) {

					// set previous heights
					$row.css({'min-height': height+'px'});
					
					// update position due to change in row above
					position = $field.position();
					thisTop = Math.ceil( position.top );
					thisLeft = Math.ceil( position.left );
				
					// reset vars
					top = 0;
					height = 0;
					$row = $();
				}
				
				// rtl
				if( pdc.get('rtl') ) {
					thisLeft = Math.ceil( $field.parent().width() - (position.left + $field.outerWidth()) );
				}
				
				// add classes
				if( thisTop == 0 ) {
					$field.addClass('-r0');
				} else if( thisLeft == 0 ) {
					$field.addClass('-c0');
				}
				
				// get height after class change
				// - add 1 for subpixel rendering
				var thisHeight = Math.ceil( $field.outerHeight() ) + 1;
				
				// set height
				height = Math.max( height, thisHeight );
				
				// set y
				top = Math.max( top, thisTop );
				
				// append
				$row = $row.add( $field );
			});
			
			// clean up
			if( $row.length ) {
				$row.css({'min-height': height+'px'});
			}
		}
	});
		
})(jQuery);

(function($, undefined){
	
	/**
	*  pdc.newCompatibility
	*
	*  Inserts a new __proto__ object compatibility layer
	*
	*  @date	15/2/18
	*  @since	5.6.9
	*
	*  @param	object instance The object to modify.
	*  @param	object compatibilty Optional. The compatibilty layer.
	*  @return	object compatibilty
	*/
	
	pdc.newCompatibility = function( instance, compatibilty ){
		
		// defaults
		compatibilty = compatibilty || {};
		
		// inherit __proto_-
		compatibilty.__proto__ = instance.__proto__;
		
		// inject
		instance.__proto__ = compatibilty;
		
		// reference
		instance.compatibility = compatibilty;
		
		// return
		return compatibilty;
	};
	
	/**
	*  pdc.getCompatibility
	*
	*  Returns the compatibility layer for a given instance
	*
	*  @date	13/3/18
	*  @since	5.6.9
	*
	*  @param	object		instance		The object to look in.
	*  @return	object|null	compatibility	The compatibility object or null on failure.
	*/
	
	pdc.getCompatibility = function( instance ) {
		return instance.compatibility || null;
	};
	
	/**
	*  pdc (compatibility)
	*
	*  Compatibility layer for the pdc object
	*
	*  @date	15/2/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	var _pdc = pdc.newCompatibility(pdc, {
		
		// storage
		l10n:	{},
		o:		{},
		fields: {},
		
		// changed function names
		update:					pdc.set,
		add_action:				pdc.addAction,
		remove_action:			pdc.removeAction,
		do_action:				pdc.doAction,
		add_filter:				pdc.addFilter,
		remove_filter:			pdc.removeFilter,
		apply_filters:			pdc.applyFilters,
		parse_args:				pdc.parseArgs,
		disable_el:				pdc.disable,
		disable_form:			pdc.disable,
		enable_el:				pdc.enable,
		enable_form:			pdc.enable,
		update_user_setting:	pdc.updateUserSetting,
		prepare_for_ajax:		pdc.prepareForAjax,
		is_ajax_success:		pdc.isAjaxSuccess,
		remove_el:				pdc.remove,
		remove_tr:				pdc.remove,
		str_replace:			pdc.strReplace,
		render_select:			pdc.renderSelect,
		get_uniqid:				pdc.uniqid,
		serialize_form:			pdc.serialize,
		esc_html:				pdc.strEscape,
		str_sanitize:			pdc.strSanitize,
	
	});
	
	_pdc._e = function( k1, k2 ){
		
		// defaults
		k1 = k1 || '';
		k2 = k2 || '';
		
		// compability
		var compatKey = k2 ? k1 + '.' + k2 : k1;
		var compats = {
			'image.select': 'Select Image',
			'image.edit': 	'Edit Image',
			'image.update': 'Update Image'
		};
		if( compats[compatKey] ) {
			return pdc.__(compats[compatKey]);
		}
		
		// try k1
		var string = this.l10n[ k1 ] || '';
		
		// try k2
		if( k2 ) {
			string = string[ k2 ] || '';
		}
		
		// return
		return string;
	};
	
	_pdc.get_selector = function( s ) {
			
		// vars
		var selector = '.pdc-field';
		
		// bail early if no search
		if( !s ) {
			return selector;
		}
		
		// compatibility with object
		if( $.isPlainObject(s) ) {
			if( $.isEmptyObject(s) ) {
				return selector;
			} else {
				for( var k in s ) { s = s[k]; break; }
			}
		}

		// append
		selector += '-' + s;
			
		// replace underscores (split/join replaces all and is faster than regex!)
		selector = pdc.strReplace('_', '-', selector);
		
		// remove potential double up
		selector = pdc.strReplace('field-field-', 'field-', selector);
		
		// return
		return selector;
	};
	
	_pdc.get_fields = function( s, $el, all ){
		
		// args
		var args = {
			is: s || '',
			parent: $el || false,
			suppressFilters: all || false,
		};
		
		// change 'field_123' to '.pdc-field-123'
		if( args.is ) {
			args.is = this.get_selector( args.is );
		}
		
		// return
		return pdc.findFields(args);			
	};
	
	_pdc.get_field = function( s, $el ){
		
		// get fields
		var $fields = this.get_fields.apply(this, arguments);
		
		// return
		if( $fields.length ) {
			return $fields.first();
		} else {
			return false;
		}
	};
		
	_pdc.get_closest_field = function( $el, s ){
		return $el.closest( this.get_selector(s) );
	};
	
	_pdc.get_field_wrap = function( $el ){
		return $el.closest( this.get_selector() );
	};
	
	_pdc.get_field_key = function( $field ){
		return $field.data('key');
	};
	
	_pdc.get_field_type = function( $field ){
		return $field.data('type');
	};
		
	_pdc.get_data = function( $el, defaults ){
		return pdc.parseArgs( $el.data(), defaults );			
	};
				
	_pdc.maybe_get = function( obj, key, value ){
			
		// default
		if( value === undefined ) {
			value = null;
		}
		
		// get keys
		keys = String(key).split('.');
		
		// pdc.isget
		for( var i = 0; i < keys.length; i++ ) {
			if( !obj.hasOwnProperty(keys[i]) ) {
				return value;
			}
			obj = obj[ keys[i] ];
		}
		return obj;
	};
	
	
	/**
	*  hooks
	*
	*  Modify add_action and add_filter functions to add compatibility with changed $field parameter
	*  Using the pdc.add_action() or pdc.add_filter() functions will interpret new field parameters as jQuery $field
	*
	*  @date	12/5/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	var compatibleArgument = function( arg ){
		return ( arg instanceof pdc.Field ) ? arg.$el : arg;
	};
	
	var compatibleArguments = function( args ){
		return pdc.arrayArgs( args ).map( compatibleArgument );
	}
	
	var compatibleCallback = function( origCallback ){
		return function(){
			
			// convert to compatible arguments
			if( arguments.length ) {
				var args = compatibleArguments(arguments);
			
			// add default argument for 'ready', 'append' and 'load' events
			} else {
				var args = [ $(document) ];
			}
			
			// return
			return origCallback.apply(this, args);
		}
	}
	
	_pdc.add_action = function( action, callback, priority, context ){
		
		// handle multiple actions
		var actions = action.split(' ');
		var length = actions.length;
		if( length > 1 ) {
			for( var i = 0; i < length; i++) {
				action = actions[i];
				_pdc.add_action.apply(this, arguments);
			}
			return this;
		}
		
		// single
		var callback = compatibleCallback(callback);
		return pdc.addAction.apply(this, arguments);
	};
	
	_pdc.add_filter = function( action, callback, priority, context ){
		var callback = compatibleCallback(callback);
		return pdc.addFilter.apply(this, arguments);
	};

	/*
	*  pdc.model
	*
	*  This model acts as a scafold for action.event driven modules
	*
	*  @type	object
	*  @date	8/09/2014
	*  @since	5.0.0
	*
	*  @param	(object)
	*  @return	(object)
	*/
	
	_pdc.model = {
		actions: {},
		filters: {},
		events: {},
		extend: function( args ){
			
			// extend
			var model = $.extend( {}, this, args );
			
			// setup actions
			$.each(model.actions, function( name, callback ){
				model._add_action( name, callback );
			});
			
			// setup filters
			$.each(model.filters, function( name, callback ){
				model._add_filter( name, callback );
			});
			
			// setup events
			$.each(model.events, function( name, callback ){
				model._add_event( name, callback );
			});
			
			// return
			return model;
		},
		
		_add_action: function( name, callback ) {
			
			// split
			var model = this,
				data = name.split(' ');
			
			// add missing priority
			var name = data[0] || '',
				priority = data[1] || 10;
			
			// add action
			pdc.add_action(name, model[ callback ], priority, model);
			
		},
		
		_add_filter: function( name, callback ) {
			
			// split
			var model = this,
				data = name.split(' ');
			
			// add missing priority
			var name = data[0] || '',
				priority = data[1] || 10;
			
			// add action
			pdc.add_filter(name, model[ callback ], priority, model);
		},
		
		_add_event: function( name, callback ) {
			
			// vars
			var model = this,
				i = name.indexOf(' '),
				event = (i > 0) ? name.substr(0,i) : name,
				selector = (i > 0) ? name.substr(i+1) : '';
			
			// event
			var fn = function( e ){
				
				// append $el to event object
				e.$el = $(this);
				
				// append $field to event object (used in field group)
				if( pdc.field_group ) {
					e.$field = e.$el.closest('.pdc-field-object');
				}
				
				// event
				if( typeof model.event === 'function' ) {
					e = model.event( e );
				}
				
				// callback
				model[ callback ].apply(model, arguments);
				
			};
			
			// add event
			if( selector ) {
				$(document).on(event, selector, fn);
			} else {
				$(document).on(event, fn);
			}
		},
		
		get: function( name, value ){
			
			// defaults
			value = value || null;
			
			// get
			if( typeof this[ name ] !== 'undefined' ) {
				value = this[ name ];
			}
			
			// return
			return value;
		},
		
		set: function( name, value ){
			
			// set
			this[ name ] = value;
			
			// function for 3rd party
			if( typeof this[ '_set_' + name ] === 'function' ) {
				this[ '_set_' + name ].apply(this);
			}
			
			// return for chaining
			return this;
		}
	};
	
	/*
	*  field
	*
	*  This model sets up many of the field's interactions
	*
	*  @type	function
	*  @date	21/02/2014
	*  @since	3.5.1
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	_pdc.field = pdc.model.extend({
		type:		'',
		o:			{},
		$field:		null,
		_add_action: function( name, callback ) {
			
			// vars
			var model = this;
			
			// update name
			name = name + '_field/type=' + model.type;
			
			// add action
			pdc.add_action(name, function( $field ){
				
				// focus
				model.set('$field', $field);
				
				// callback
				model[ callback ].apply(model, arguments);
			});
		},
		
		_add_filter: function( name, callback ) {
			
			// vars
			var model = this;
			
			// update name
			name = name + '_field/type=' + model.type;
			
			// add action
			pdc.add_filter(name, function( $field ){
				
				// focus
				model.set('$field', $field);
				
				// callback
				model[ callback ].apply(model, arguments);
			});
		},
		
		_add_event: function( name, callback ) {
			
			// vars
			var model = this,
				event = name.substr(0,name.indexOf(' ')),
				selector = name.substr(name.indexOf(' ')+1),
				context = pdc.get_selector(model.type);
			
			// add event
			$(document).on(event, context + ' ' + selector, function( e ){
				
				// vars
				var $el = $(this);
				var $field = pdc.get_closest_field( $el, model.type );
				
				// bail early if no field
				if( !$field.length ) return;
				
				// focus
				if( !$field.is(model.$field) ) {
					model.set('$field', $field);
				}
				
				// append to event
				e.$el = $el;
				e.$field = $field;
				
				// callback
				model[ callback ].apply(model, [e]);
			});
		},
		
		_set_$field: function(){
			
			// callback
			if( typeof this.focus === 'function' ) {
				this.focus();
			}
		},
		
		// depreciated
		doFocus: function( $field ){
			return this.set('$field', $field);
		}
	});
	
	
	/**
	*  validation
	*
	*  description
	*
	*  @date	15/2/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	var _validation = pdc.newCompatibility(pdc.validation, {
		remove_error: function( $field ){
			pdc.getField( $field ).removeError();
		},
		add_warning: function( $field, message ){
			pdc.getField( $field ).showNotice({
				text: message,
				type: 'warning',
				timeout: 1000
			});
		},
		fetch:			pdc.validateForm,
		enableSubmit: 	pdc.enableSubmit,
		disableSubmit: 	pdc.disableSubmit,
		showSpinner:	pdc.showSpinner,
		hideSpinner:	pdc.hideSpinner,
		unlockForm:		pdc.unlockForm,
		lockForm:		pdc.lockForm
	});
	
	
	/**
	*  tooltip
	*
	*  description
	*
	*  @date	15/2/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	_pdc.tooltip = {
		
		tooltip: function( text, $el ){
			
			var tooltip = pdc.newTooltip({
				text: text,
				target: $el
			});
			
			// return
			return tooltip.$el;
		},
		
		temp: function( text, $el ){
			
			var tooltip = pdc.newTooltip({
				text: text,
				target: $el,
				timeout: 250
			});
		},
		
		confirm: function( $el, callback, text, button_y, button_n ){
			
			var tooltip = pdc.newTooltip({
				confirm: true,
				text: text,
				target: $el,
				confirm: function(){
					callback(true);
				},
				cancel: function(){
					callback(false);
				}
			});
		},
		
		confirm_remove: function( $el, callback ){
			
			var tooltip = pdc.newTooltip({
				confirmRemove: true,
				target: $el,
				confirm: function(){
					callback(true);
				},
				cancel: function(){
					callback(false);
				}
			});
		},
	};
	
	/**
	*  tooltip
	*
	*  description
	*
	*  @date	15/2/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	_pdc.media = new pdc.Model({
		activeFrame: false,
		actions: {
			'new_media_popup': 'onNewMediaPopup'
		},
		
		frame: function(){
			return this.activeFrame;
		},
		
		onNewMediaPopup: function( popup ){
			this.activeFrame = popup.frame;
		},
		
		popup: function( props ){
			
			// update props
			if( props.mime_types ) {
				props.allowedTypes = props.mime_types;
			}
			if( props.id ) {
				props.attachment = props.id;
			}
			
			// new
			var popup = pdc.newMediaPopup( props );
			
			// append
/*
			if( props.selected ) {
				popup.selected = props.selected;
			}
*/
			
			// return
			return popup.frame;
		}
	});
	
	
	/**
	*  Select2
	*
	*  description
	*
	*  @date	11/6/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	_pdc.select2 = {
		init: function( $select, args, $field ){
			
			// compatible args
			if( args.allow_null ) {
				args.allowNull = args.allow_null;
			}
			if( args.ajax_action ) {
				args.ajaxAction = args.ajax_action;
			}
			if( $field ) {
				args.field = pdc.getField($field);
			}
			
			// return
			return pdc.newSelect2( $select, args );	
		},
		
		destroy: function( $select ){
			return pdc.getInstance( $select ).destroy();
			
		},
	};
	
	/**
	*  postbox
	*
	*  description
	*
	*  @date	11/6/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	_pdc.postbox = {
		render: function( args ){
			
			// compatible args
			if( args.edit_url ) {
				args.editLink = args.edit_url;
			}
			if( args.edit_title ) {
				args.editTitle = args.edit_title;
			}
			
			// return
			return pdc.newPostbox( args );
		}
	};
	
	/**
	*  pdc.screen
	*
	*  description
	*
	*  @date	11/6/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	pdc.newCompatibility(pdc.screen, {
		update: function(){
			return this.set.apply(this, arguments);
		},
		fetch: pdc.screen.check
	});
	_pdc.ajax = pdc.screen;
	
})(jQuery);

// @codekit-prepend "../js/pdc.js";
// @codekit-prepend "../js/pdc-hooks.js";
// @codekit-prepend "../js/pdc-model.js";
// @codekit-prepend "../js/pdc-popup.js";
// @codekit-prepend "../js/pdc-unload.js";
// @codekit-prepend "../js/pdc-panel.js";
// @codekit-prepend "../js/pdc-notice.js";
// @codekit-prepend "../js/pdc-postbox.js";
// @codekit-prepend "../js/pdc-tooltip.js";
// @codekit-prepend "../js/pdc-field.js";
// @codekit-prepend "../js/pdc-fields.js";
// @codekit-prepend "../js/pdc-field-accordion.js";
// @codekit-prepend "../js/pdc-field-button-group.js";
// @codekit-prepend "../js/pdc-field-checkbox.js";
// @codekit-prepend "../js/pdc-field-color-picker.js";
// @codekit-prepend "../js/pdc-field-date-picker.js";
// @codekit-prepend "../js/pdc-field-date-time-picker.js";
// @codekit-prepend "../js/pdc-field-google-map.js";
// @codekit-prepend "../js/pdc-field-image.js";
// @codekit-prepend "../js/pdc-field-file.js";
// @codekit-prepend "../js/pdc-field-link.js";
// @codekit-prepend "../js/pdc-field-oembed.js";
// @codekit-prepend "../js/pdc-field-radio.js";
// @codekit-prepend "../js/pdc-field-range.js";
// @codekit-prepend "../js/pdc-field-relationship.js";
// @codekit-prepend "../js/pdc-field-select.js";
// @codekit-prepend "../js/pdc-field-tab.js";
// @codekit-prepend "../js/pdc-field-post-object.js";
// @codekit-prepend "../js/pdc-field-page-link.js";
// @codekit-prepend "../js/pdc-field-user.js";
// @codekit-prepend "../js/pdc-field-taxonomy.js";
// @codekit-prepend "../js/pdc-field-time-picker.js";
// @codekit-prepend "../js/pdc-field-true-false.js";
// @codekit-prepend "../js/pdc-field-url.js";
// @codekit-prepend "../js/pdc-field-wysiwyg.js";
// @codekit-prepend "../js/pdc-condition.js";
// @codekit-prepend "../js/pdc-conditions.js";
// @codekit-prepend "../js/pdc-condition-types.js";
// @codekit-prepend "../js/pdc-media.js";
// @codekit-prepend "../js/pdc-screen.js";
// @codekit-prepend "../js/pdc-select2.js";
// @codekit-prepend "../js/pdc-tinymce.js";
// @codekit-prepend "../js/pdc-validation.js";
// @codekit-prepend "../js/pdc-helpers.js";
// @codekit-prepend "../js/pdc-compatibility";


