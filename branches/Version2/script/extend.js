	
	/**
	 * jQuery MD5 hash algorithm function
	 * 
	 * 	<code>
	 * 		Calculate the md5 hash of a String 
	 * 		String $.md5 ( String str )
	 * 	</code>
	 * 
	 * Calculates the MD5 hash of str using the » RSA Data Security, Inc. MD5 Message-Digest Algorithm, and returns that hash. 
	 * MD5 (Message-Digest algorithm 5) is a widely-used cryptographic hash function with a 128-bit hash value. MD5 has been employed in a wide variety of security applications, and is also commonly used to check the integrity of data. The generated hash is also non-reversable. Data cannot be retrieved from the message digest, the digest uniquely identifies the data.
	 * MD5 was developed by Professor Ronald L. Rivest in 1994. Its 128 bit (16 byte) message digest makes it a faster implementation than SHA-1.
	 * This script is used to process a variable length message into a fixed-length output of 128 bits using the MD5 algorithm. It is fully compatible with UTF-8 encoding. It is very useful when u want to transfer encrypted passwords over the internet. If you plan using UTF-8 encoding in your project don't forget to set the page encoding to UTF-8 (Content-Type meta tag). 
	 * This function orginally get from the WebToolkit and rewrite for using as the jQuery plugin.
	 * 
	 * Example
	 * 	Code
	 * 		<code>
	 * 			$.md5("I'm Persian."); 
	 * 		</code>
	 * 	Result
	 * 		<code>
	 * 			"b8c901d0f02223f9761016cfff9d68df"
	 * 		</code>
	 * 
	 * @alias Muhammad Hussein Fattahizadeh < muhammad [AT] semnanweb [DOT] com >
	 * @link http://www.semnanweb.com/jquery-plugin/md5.html
	 * @see http://www.webtoolkit.info/
	 * @license http://www.gnu.org/licenses/gpl.html [GNU General Public License]
	 * @param {jQuery} {md5:function(string))
	 * @return string
	 */
	
	(function($){
		
		var rotateLeft = function(lValue, iShiftBits) {
			return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
		}
		
		var addUnsigned = function(lX, lY) {
			var lX4, lY4, lX8, lY8, lResult;
			lX8 = (lX & 0x80000000);
			lY8 = (lY & 0x80000000);
			lX4 = (lX & 0x40000000);
			lY4 = (lY & 0x40000000);
			lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
			if (lX4 & lY4) return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
			if (lX4 | lY4) {
				if (lResult & 0x40000000) return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
				else return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
			} else {
				return (lResult ^ lX8 ^ lY8);
			}
		}
		
		var F = function(x, y, z) {
			return (x & y) | ((~ x) & z);
		}
		
		var G = function(x, y, z) {
			return (x & z) | (y & (~ z));
		}
		
		var H = function(x, y, z) {
			return (x ^ y ^ z);
		}
		
		var I = function(x, y, z) {
			return (y ^ (x | (~ z)));
		}
		
		var FF = function(a, b, c, d, x, s, ac) {
			a = addUnsigned(a, addUnsigned(addUnsigned(F(b, c, d), x), ac));
			return addUnsigned(rotateLeft(a, s), b);
		};
		
		var GG = function(a, b, c, d, x, s, ac) {
			a = addUnsigned(a, addUnsigned(addUnsigned(G(b, c, d), x), ac));
			return addUnsigned(rotateLeft(a, s), b);
		};
		
		var HH = function(a, b, c, d, x, s, ac) {
			a = addUnsigned(a, addUnsigned(addUnsigned(H(b, c, d), x), ac));
			return addUnsigned(rotateLeft(a, s), b);
		};
		
		var II = function(a, b, c, d, x, s, ac) {
			a = addUnsigned(a, addUnsigned(addUnsigned(I(b, c, d), x), ac));
			return addUnsigned(rotateLeft(a, s), b);
		};
		
		var convertToWordArray = function(string) {
			var lWordCount;
			var lMessageLength = string.length;
			var lNumberOfWordsTempOne = lMessageLength + 8;
			var lNumberOfWordsTempTwo = (lNumberOfWordsTempOne - (lNumberOfWordsTempOne % 64)) / 64;
			var lNumberOfWords = (lNumberOfWordsTempTwo + 1) * 16;
			var lWordArray = Array(lNumberOfWords - 1);
			var lBytePosition = 0;
			var lByteCount = 0;
			while (lByteCount < lMessageLength) {
				lWordCount = (lByteCount - (lByteCount % 4)) / 4;
				lBytePosition = (lByteCount % 4) * 8;
				lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
				lByteCount++;
			}
			lWordCount = (lByteCount - (lByteCount % 4)) / 4;
			lBytePosition = (lByteCount % 4) * 8;
			lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
			lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
			lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
			return lWordArray;
		};
		
		var wordToHex = function(lValue) {
			var WordToHexValue = "", WordToHexValueTemp = "", lByte, lCount;
			for (lCount = 0; lCount <= 3; lCount++) {
				lByte = (lValue >>> (lCount * 8)) & 255;
				WordToHexValueTemp = "0" + lByte.toString(16);
				WordToHexValue = WordToHexValue + WordToHexValueTemp.substr(WordToHexValueTemp.length - 2, 2);
			}
			return WordToHexValue;
		};
		
		var uTF8Encode = function(string) {
			string = string.replace(/\x0d\x0a/g, "\x0a");
			var output = "";
			for (var n = 0; n < string.length; n++) {
				var c = string.charCodeAt(n);
				if (c < 128) {
					output += String.fromCharCode(c);
				} else if ((c > 127) && (c < 2048)) {
					output += String.fromCharCode((c >> 6) | 192);
					output += String.fromCharCode((c & 63) | 128);
				} else {
					output += String.fromCharCode((c >> 12) | 224);
					output += String.fromCharCode(((c >> 6) & 63) | 128);
					output += String.fromCharCode((c & 63) | 128);
				}
			}
			return output;
		};
		
		$.extend({
			md5: function(string) {
				var x = Array();
				var k, AA, BB, CC, DD, a, b, c, d;
				var S11=7, S12=12, S13=17, S14=22;
				var S21=5, S22=9 , S23=14, S24=20;
				var S31=4, S32=11, S33=16, S34=23;
				var S41=6, S42=10, S43=15, S44=21;
				string = uTF8Encode(string);
				x = convertToWordArray(string);
				a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
				for (k = 0; k < x.length; k += 16) {
					AA = a; BB = b; CC = c; DD = d;
					a = FF(a, b, c, d, x[k+0],  S11, 0xD76AA478);
					d = FF(d, a, b, c, x[k+1],  S12, 0xE8C7B756);
					c = FF(c, d, a, b, x[k+2],  S13, 0x242070DB);
					b = FF(b, c, d, a, x[k+3],  S14, 0xC1BDCEEE);
					a = FF(a, b, c, d, x[k+4],  S11, 0xF57C0FAF);
					d = FF(d, a, b, c, x[k+5],  S12, 0x4787C62A);
					c = FF(c, d, a, b, x[k+6],  S13, 0xA8304613);
					b = FF(b, c, d, a, x[k+7],  S14, 0xFD469501);
					a = FF(a, b, c, d, x[k+8],  S11, 0x698098D8);
					d = FF(d, a, b, c, x[k+9],  S12, 0x8B44F7AF);
					c = FF(c, d, a, b, x[k+10], S13, 0xFFFF5BB1);
					b = FF(b, c, d, a, x[k+11], S14, 0x895CD7BE);
					a = FF(a, b, c, d, x[k+12], S11, 0x6B901122);
					d = FF(d, a, b, c, x[k+13], S12, 0xFD987193);
					c = FF(c, d, a, b, x[k+14], S13, 0xA679438E);
					b = FF(b, c, d, a, x[k+15], S14, 0x49B40821);
					a = GG(a, b, c, d, x[k+1],  S21, 0xF61E2562);
					d = GG(d, a, b, c, x[k+6],  S22, 0xC040B340);
					c = GG(c, d, a, b, x[k+11], S23, 0x265E5A51);
					b = GG(b, c, d, a, x[k+0],  S24, 0xE9B6C7AA);
					a = GG(a, b, c, d, x[k+5],  S21, 0xD62F105D);
					d = GG(d, a, b, c, x[k+10], S22, 0x2441453);
					c = GG(c, d, a, b, x[k+15], S23, 0xD8A1E681);
					b = GG(b, c, d, a, x[k+4],  S24, 0xE7D3FBC8);
					a = GG(a, b, c, d, x[k+9],  S21, 0x21E1CDE6);
					d = GG(d, a, b, c, x[k+14], S22, 0xC33707D6);
					c = GG(c, d, a, b, x[k+3],  S23, 0xF4D50D87);
					b = GG(b, c, d, a, x[k+8],  S24, 0x455A14ED);
					a = GG(a, b, c, d, x[k+13], S21, 0xA9E3E905);
					d = GG(d, a, b, c, x[k+2],  S22, 0xFCEFA3F8);
					c = GG(c, d, a, b, x[k+7],  S23, 0x676F02D9);
					b = GG(b, c, d, a, x[k+12], S24, 0x8D2A4C8A);
					a = HH(a, b, c, d, x[k+5],  S31, 0xFFFA3942);
					d = HH(d, a, b, c, x[k+8],  S32, 0x8771F681);
					c = HH(c, d, a, b, x[k+11], S33, 0x6D9D6122);
					b = HH(b, c, d, a, x[k+14], S34, 0xFDE5380C);
					a = HH(a, b, c, d, x[k+1],  S31, 0xA4BEEA44);
					d = HH(d, a, b, c, x[k+4],  S32, 0x4BDECFA9);
					c = HH(c, d, a, b, x[k+7],  S33, 0xF6BB4B60);
					b = HH(b, c, d, a, x[k+10], S34, 0xBEBFBC70);
					a = HH(a, b, c, d, x[k+13], S31, 0x289B7EC6);
					d = HH(d, a, b, c, x[k+0],  S32, 0xEAA127FA);
					c = HH(c, d, a, b, x[k+3],  S33, 0xD4EF3085);
					b = HH(b, c, d, a, x[k+6],  S34, 0x4881D05);
					a = HH(a, b, c, d, x[k+9],  S31, 0xD9D4D039);
					d = HH(d, a, b, c, x[k+12], S32, 0xE6DB99E5);
					c = HH(c, d, a, b, x[k+15], S33, 0x1FA27CF8);
					b = HH(b, c, d, a, x[k+2],  S34, 0xC4AC5665);
					a = II(a, b, c, d, x[k+0],  S41, 0xF4292244);
					d = II(d, a, b, c, x[k+7],  S42, 0x432AFF97);
					c = II(c, d, a, b, x[k+14], S43, 0xAB9423A7);
					b = II(b, c, d, a, x[k+5],  S44, 0xFC93A039);
					a = II(a, b, c, d, x[k+12], S41, 0x655B59C3);
					d = II(d, a, b, c, x[k+3],  S42, 0x8F0CCC92);
					c = II(c, d, a, b, x[k+10], S43, 0xFFEFF47D);
					b = II(b, c, d, a, x[k+1],  S44, 0x85845DD1);
					a = II(a, b, c, d, x[k+8],  S41, 0x6FA87E4F);
					d = II(d, a, b, c, x[k+15], S42, 0xFE2CE6E0);
					c = II(c, d, a, b, x[k+6],  S43, 0xA3014314);
					b = II(b, c, d, a, x[k+13], S44, 0x4E0811A1);
					a = II(a, b, c, d, x[k+4],  S41, 0xF7537E82);
					d = II(d, a, b, c, x[k+11], S42, 0xBD3AF235);
					c = II(c, d, a, b, x[k+2],  S43, 0x2AD7D2BB);
					b = II(b, c, d, a, x[k+9],  S44, 0xEB86D391);
					a = addUnsigned(a, AA);
					b = addUnsigned(b, BB);
					c = addUnsigned(c, CC);
					d = addUnsigned(d, DD);
				}
				var tempValue = wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d);
				return tempValue.toLowerCase();
			}
		});
	})(jQuery);
	
	
	/**
	 * jQuery SHA1 hash algorithm function
	 * 
	 * 	<code>
	 * 		Calculate the sha1 hash of a String 
	 * 		String $.sha1 ( String str )
	 * 	</code>
	 * 
	 * Calculates the sha1 hash of str using the US Secure Hash Algorithm 1.
	 * SHA-1 the Secure Hash Algorithm (SHA) was developed by NIST and is specified in the Secure Hash Standard (SHS, FIPS 180).
	 * This script is used to process variable length message into a fixed-length output using the SHA-1 algorithm. It is fully compatible with UTF-8 encoding.
	 * If you plan using UTF-8 encoding in your project don't forget to set the page encoding to UTF-8 (Content-Type meta tag).
	 * This function orginally get from the WebToolkit and rewrite for using as the jQuery plugin.
	 * 
	 * Example
	 * 	Code
	 * 		<code>
	 * 			$.sha1("I'm Persian."); 
	 * 		</code>
	 * 	Result
	 * 		<code>
	 * 			"1d302f9dc925d62fc859055999d2052e274513ed"
	 * 		</code>
	 * 
	 * @alias Muhammad Hussein Fattahizadeh < muhammad [AT] semnanweb [DOT] com >
	 * @link http://www.semnanweb.com/jquery-plugin/sha1.html
	 * @see http://www.webtoolkit.info/
	 * @license http://www.gnu.org/licenses/gpl.html [GNU General Public License]
	 * @param {jQuery} {sha1:function(string))
	 * @return string
	 */
	
	(function($){
		
		var rotateLeft = function(lValue, iShiftBits) {
			return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
		}
		
		var lsbHex = function(value) {
			var string = "";
			var i;
			var vh;
			var vl;
			for(i = 0;i <= 6;i += 2) {
				vh = (value>>>(i * 4 + 4))&0x0f;
				vl = (value>>>(i*4))&0x0f;
				string += vh.toString(16) + vl.toString(16);
			}
			return string;
		};
		
		var cvtHex = function(value) {
			var string = "";
			var i;
			var v;
			for(i = 7;i >= 0;i--) {
				v = (value>>>(i * 4))&0x0f;
				string += v.toString(16);
			}
			return string;
		};
		
		var uTF8Encode = function(string) {
			string = string.replace(/\x0d\x0a/g, "\x0a");
			var output = "";
			for (var n = 0; n < string.length; n++) {
				var c = string.charCodeAt(n);
				if (c < 128) {
					output += String.fromCharCode(c);
				} else if ((c > 127) && (c < 2048)) {
					output += String.fromCharCode((c >> 6) | 192);
					output += String.fromCharCode((c & 63) | 128);
				} else {
					output += String.fromCharCode((c >> 12) | 224);
					output += String.fromCharCode(((c >> 6) & 63) | 128);
					output += String.fromCharCode((c & 63) | 128);
				}
			}
			return output;
		};
		
		$.extend({
			sha1: function(string) {
				var blockstart;
				var i, j;
				var W = new Array(80);
				var H0 = 0x67452301;
				var H1 = 0xEFCDAB89;
				var H2 = 0x98BADCFE;
				var H3 = 0x10325476;
				var H4 = 0xC3D2E1F0;
				var A, B, C, D, E;
				var tempValue;
				string = uTF8Encode(string);
				var stringLength = string.length;
				var wordArray = new Array();
				for(i = 0;i < stringLength - 3;i += 4) {
					j = string.charCodeAt(i)<<24 | string.charCodeAt(i + 1)<<16 | string.charCodeAt(i + 2)<<8 | string.charCodeAt(i + 3);
					wordArray.push(j);
				}
				switch(stringLength % 4) {
					case 0:
						i = 0x080000000;
					break;
					case 1:
						i = string.charCodeAt(stringLength - 1)<<24 | 0x0800000;
					break;
					case 2:
						i = string.charCodeAt(stringLength - 2)<<24 | string.charCodeAt(stringLength - 1)<<16 | 0x08000;
					break;
					case 3:
						i = string.charCodeAt(stringLength - 3)<<24 | string.charCodeAt(stringLength - 2)<<16 | string.charCodeAt(stringLength - 1)<<8 | 0x80;
					break;
				}
				wordArray.push(i);
				while((wordArray.length % 16) != 14 ) wordArray.push(0);
				wordArray.push(stringLength>>>29);
				wordArray.push((stringLength<<3)&0x0ffffffff);
				for(blockstart = 0;blockstart < wordArray.length;blockstart += 16) {
					for(i = 0;i < 16;i++) W[i] = wordArray[blockstart+i];
					for(i = 16;i <= 79;i++) W[i] = rotateLeft(W[i-3] ^ W[i-8] ^ W[i-14] ^ W[i-16], 1);
					A = H0;
					B = H1;
					C = H2;
					D = H3;
					E = H4;
					for(i = 0;i <= 19;i++) {
						tempValue = (rotateLeft(A, 5) + ((B&C) | (~B&D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
						E = D;
						D = C;
						C = rotateLeft(B, 30);
						B = A;
						A = tempValue;
					}
					for(i = 20;i <= 39;i++) {
						tempValue = (rotateLeft(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
						E = D;
						D = C;
						C = rotateLeft(B, 30);
						B = A;
						A = tempValue;
					}
					for(i = 40;i <= 59;i++) {
						tempValue = (rotateLeft(A, 5) + ((B&C) | (B&D) | (C&D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
						E = D;
						D = C;
						C = rotateLeft(B, 30);
						B = A;
						A = tempValue;
					}
					for(i = 60;i <= 79;i++) {
						tempValue = (rotateLeft(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
						E = D;
						D = C;
						C = rotateLeft(B, 30);
						B = A;
						A = tempValue;
					}
					H0 = (H0 + A) & 0x0ffffffff;
					H1 = (H1 + B) & 0x0ffffffff;
					H2 = (H2 + C) & 0x0ffffffff;
					H3 = (H3 + D) & 0x0ffffffff;
					H4 = (H4 + E) & 0x0ffffffff;
				}
				var tempValue = cvtHex(H0) + cvtHex(H1) + cvtHex(H2) + cvtHex(H3) + cvtHex(H4);
				return tempValue.toLowerCase();
			}
		});
	})(jQuery);
	
	
	/**
	 * jQuery BASE64 functions
	 * 
	 * 	<code>
	 * 		Encodes the given data with base64. 
	 * 		String $.base64Encode ( String str )
	 *		<br />
	 * 		Decodes a base64 encoded data.
	 * 		String $.base64Decode ( String str )
	 * 	</code>
	 * 
	 * Encodes and Decodes the given data in base64.
	 * This encoding is designed to make binary data survive transport through transport layers that are not 8-bit clean, such as mail bodies.
	 * Base64-encoded data takes about 33% more space than the original data. 
	 * This javascript code is used to encode / decode data using base64 (this encoding is designed to make binary data survive transport through transport layers that are not 8-bit clean). Script is fully compatible with UTF-8 encoding. You can use base64 encoded data as simple encryption mechanism.
	 * If you plan using UTF-8 encoding in your project don't forget to set the page encoding to UTF-8 (Content-Type meta tag). 
	 * This function orginally get from the WebToolkit and rewrite for using as the jQuery plugin.
	 * 
	 * Example
	 * 	Code
	 * 		<code>
	 * 			$.base64Encode("I'm Persian."); 
	 * 		</code>
	 * 	Result
	 * 		<code>
	 * 			"SSdtIFBlcnNpYW4u"
	 * 		</code>
	 * 	Code
	 * 		<code>
	 * 			$.base64Decode("SSdtIFBlcnNpYW4u");
	 * 		</code>
	 * 	Result
	 * 		<code>
	 * 			"I'm Persian."
	 * 		</code>
	 * 
	 * @alias Muhammad Hussein Fattahizadeh < muhammad [AT] semnanweb [DOT] com >
	 * @link http://www.semnanweb.com/jquery-plugin/base64.html
	 * @see http://www.webtoolkit.info/
	 * @license http://www.gnu.org/licenses/gpl.html [GNU General Public License]
	 * @param {jQuery} {base64Encode:function(input))
	 * @param {jQuery} {base64Decode:function(input))
	 * @return string
	 */
	
	(function($){
		
		var keyString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		
		var uTF8Encode = function(string) {
			string = string.replace(/\x0d\x0a/g, "\x0a");
			var output = "";
			for (var n = 0; n < string.length; n++) {
				var c = string.charCodeAt(n);
				if (c < 128) {
					output += String.fromCharCode(c);
				} else if ((c > 127) && (c < 2048)) {
					output += String.fromCharCode((c >> 6) | 192);
					output += String.fromCharCode((c & 63) | 128);
				} else {
					output += String.fromCharCode((c >> 12) | 224);
					output += String.fromCharCode(((c >> 6) & 63) | 128);
					output += String.fromCharCode((c & 63) | 128);
				}
			}
			return output;
		};
		
		var uTF8Decode = function(input) {
			var string = "";
			var i = 0;
			var c = c1 = c2 = 0;
			while ( i < input.length ) {
				c = input.charCodeAt(i);
				if (c < 128) {
					string += String.fromCharCode(c);
					i++;
				} else if ((c > 191) && (c < 224)) {
					c2 = input.charCodeAt(i+1);
					string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
					i += 2;
				} else {
					c2 = input.charCodeAt(i+1);
					c3 = input.charCodeAt(i+2);
					string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
					i += 3;
				}
			}
			return string;
		}
		
		$.extend({
			base64Encode: function(input) {
				var output = "";
				var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
				var i = 0;
				input = uTF8Encode(input);
				while (i < input.length) {
					chr1 = input.charCodeAt(i++);
					chr2 = input.charCodeAt(i++);
					chr3 = input.charCodeAt(i++);
					enc1 = chr1 >> 2;
					enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
					enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
					enc4 = chr3 & 63;
					if (isNaN(chr2)) {
						enc3 = enc4 = 64;
					} else if (isNaN(chr3)) {
						enc4 = 64;
					}
					output = output + keyString.charAt(enc1) + keyString.charAt(enc2) + keyString.charAt(enc3) + keyString.charAt(enc4);
				}
				return output;
			},
			base64Decode: function(input) {
				var output = "";
				var chr1, chr2, chr3;
				var enc1, enc2, enc3, enc4;
				var i = 0;
				input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
				while (i < input.length) {
					enc1 = keyString.indexOf(input.charAt(i++));
					enc2 = keyString.indexOf(input.charAt(i++));
					enc3 = keyString.indexOf(input.charAt(i++));
					enc4 = keyString.indexOf(input.charAt(i++));
					chr1 = (enc1 << 2) | (enc2 >> 4);
					chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
					chr3 = ((enc3 & 3) << 6) | enc4;
					output = output + String.fromCharCode(chr1);
					if (enc3 != 64) {
						output = output + String.fromCharCode(chr2);
					}
					if (enc4 != 64) {
						output = output + String.fromCharCode(chr3);
					}
				}
				output = uTF8Decode(output);
				return output;
			}
		});
	})(jQuery);

	function strPad(i,l,s) {
		var o = i.toString();
		if (!s) { s = ' '; }
		while (o.length < l) {
			o = o + s;
		}
		return o;
	};
	
	/**
	 * jQuery.timers - Timer abstractions for jQuery
	 * Written by Blair Mitchelmore (blair DOT mitchelmore AT gmail DOT com)
	 * Licensed under the WTFPL (http://sam.zoy.org/wtfpl/).
	 * Date: 2009/10/16
	 *
	 * @author Blair Mitchelmore
	 * @version 1.2
	 *
	 **/

	jQuery.fn.extend({
		everyTime: function(interval, label, fn, times) {
			return this.each(function() {
				jQuery.timer.add(this, interval, label, fn, times);
			});
		},
		oneTime: function(interval, label, fn) {
			return this.each(function() {
				jQuery.timer.add(this, interval, label, fn, 1);
			});
		},
		stopTime: function(label, fn) {
			return this.each(function() {
				jQuery.timer.remove(this, label, fn);
			});
		}
	});

	jQuery.extend({
		timer: {
			global: [],
			guid: 1,
			dataKey: "jQuery.timer",
			regex: /^([0-9]+(?:\.[0-9]*)?)\s*(.*s)?$/,
			powers: {
				// Yeah this is major overkill...
				'ms': 1,
				'cs': 10,
				'ds': 100,
				's': 1000,
				'das': 10000,
				'hs': 100000,
				'ks': 1000000
			},
			timeParse: function(value) {
				if (value == undefined || value == null)
					return null;
				var result = this.regex.exec(jQuery.trim(value.toString()));
				if (result[2]) {
					var num = parseFloat(result[1]);
					var mult = this.powers[result[2]] || 1;
					return num * mult;
				} else {
					return value;
				}
			},
			add: function(element, interval, label, fn, times) {
				var counter = 0;
				
				if (jQuery.isFunction(label)) {
					if (!times) 
						times = fn;
					fn = label;
					label = interval;
				}
				
				interval = jQuery.timer.timeParse(interval);

				if (typeof interval != 'number' || isNaN(interval) || interval < 0)
					return;

				if (typeof times != 'number' || isNaN(times) || times < 0) 
					times = 0;
				
				times = times || 0;
				
				var timers = jQuery.data(element, this.dataKey) || jQuery.data(element, this.dataKey, {});
				
				if (!timers[label])
					timers[label] = {};
				
				fn.timerID = fn.timerID || this.guid++;
				
				var handler = function() {
					if ((++counter > times && times !== 0) || fn.call(element, counter) === false)
						jQuery.timer.remove(element, label, fn);
				};
				
				handler.timerID = fn.timerID;
				
				if (!timers[label][fn.timerID])
					timers[label][fn.timerID] = window.setInterval(handler,interval);
				
				this.global.push( element );
				
			},
			remove: function(element, label, fn) {
				var timers = jQuery.data(element, this.dataKey), ret;
				
				if ( timers ) {
					
					if (!label) {
						for ( label in timers )
							this.remove(element, label, fn);
					} else if ( timers[label] ) {
						if ( fn ) {
							if ( fn.timerID ) {
								window.clearInterval(timers[label][fn.timerID]);
								delete timers[label][fn.timerID];
							}
						} else {
							for ( var fn in timers[label] ) {
								window.clearInterval(timers[label][fn]);
								delete timers[label][fn];
							}
						}
						
						for ( ret in timers[label] ) break;
						if ( !ret ) {
							ret = null;
							delete timers[label];
						}
					}
					
					for ( ret in timers ) break;
					if ( !ret ) 
						jQuery.removeData(element, this.dataKey);
				}
			}
		}
	});

	jQuery(window).bind("unload", function() {
		jQuery.each(jQuery.timer.global, function(index, item) {
			jQuery.timer.remove(item);
		});
	});

	/*
	 * jQuery Calculation Plug-in
	 *
	 * Copyright (c) 2007 Dan G. Switzer, II
	 *
	 * Dual licensed under the MIT and GPL licenses:
	 *   http://www.opensource.org/licenses/mit-license.php
	 *   http://www.gnu.org/licenses/gpl.html
	 *
	 * Revision: 11
	 * Version: 0.4.07
	 *
	 * Revision History
	 * v0.4.07
	 * - Added trim to parseNumber to fix issue with whitespace in elements
	 * 
	 * v0.4.06
	 * - Added support for calc() "format" callback so that if return value
	 *   is null, then value is not updated
	 * - Added jQuery.isFunction() check for calc() callbacks
	 * 
	 * v0.4.05
	 * - Added support to the sum() & calc() method for automatically fixing precision
	 *   issues (will detect the max decimal spot in the number and fix to that
	 *   depth)
	 * 
	 * v0.4.04
	 * - Fixed bug #5420 by adding the defaults.cleanseNumber handler; you can
	 *   override this function to handle stripping number of extra digits
	 * 
	 * v0.4.02
	 * - Fixed bug where bind parameter was not being detecting if you specified
	 *   a string in method like sum(), avg(), etc.
	 * 
	 * v0.4a
	 * - Fixed bug in aggregate functions so that a string is passed to jQuery's
	 *   text() method (since numeric zero is interpetted as false)
	 * 
	 * v0.4
	 * - Added support for -$.99 values
	 * - Fixed regex so that decimal values without leading zeros are correctly
	 *   parsed
	 * - Removed defaults.comma setting
	 * - Changed secondary regex that cleans additional formatting from parsed
	 *   number
	 * 
	 * v0.3
	 * - Refactored the aggregate methods (since they all use the same core logic)
	 *   to use the $.extend() method
	 * - Added support for negative numbers in the regex)
	 * - Added min/max aggregate methods
	 * - Added defaults.onParseError and defaults.onParseClear methods to add logic for
	 *   parsing errors
	 * 
	 * v0.2
	 * - Fixed bug in sMethod in calc() (was using getValue, should have been setValue)
	 * - Added arguments for sum() to allow auto-binding with callbacks
	 * - Added arguments for avg() to allow auto-binding with callbacks
	 * 
	 * v0.1a
	 * - Added semi-colons after object declaration (for min protection)
	 * 
	 * v0.1
	 * - First public release
	 *
	*/
	(function($){

		// set the defaults
		var defaults = {
			// regular expression used to detect numbers, if you want to force the field to contain
			// numbers, you can add a ^ to the beginning or $ to the end of the regex to force the
			// the regex to match the entire string: /^(-|-\$)?(\d+(,\d{3})*(\.\d{1,})?|\.\d{1,})$/g
			reNumbers: /(-|-\$)?(\d+(,\d{3})*(\.\d{1,})?|\.\d{1,})/g
			// this function is used in the parseNumber() to cleanse up any found numbers
			// the function is intended to remove extra information found in a number such
			// as extra commas and dollar signs. override this function to strip European values
			, cleanseNumber: function (v){
				// cleanse the number one more time to remove extra data (like commas and dollar signs)
				// use this for European numbers: v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".")
				return v.replace(/[^0-9.\-]/g, "");
			}
			// should the Field plug-in be used for getting values of :input elements?
			, useFieldPlugin: (!!$.fn.getValue)
			// a callback function to run when an parsing error occurs
			, onParseError: null
			// a callback function to run once a parsing error has cleared
			, onParseClear: null
		};
		
		// set default options
		$.Calculation = {
			version: "0.4.07",
			setDefaults: function(options){
				$.extend(defaults, options);
			}
		};


		/*
		 * jQuery.fn.parseNumber()
		 *
		 * returns Array - detects the DOM element and returns it's value. input
		 *                 elements return the field value, other DOM objects
		 *                 return their text node
		 *
		 * NOTE: Breaks the jQuery chain, since it returns a Number.
		 *
		 * Examples:
		 * $("input[name^='price']").parseNumber();
		 * > This would return an array of potential number for every match in the selector
		 *
		 */
		// the parseNumber() method -- break the chain
		$.fn.parseNumber = function(options){
			var aValues = [];
			options = $.extend(options, defaults);
			
			this.each(
				function (){
					var
						// get a pointer to the current element
						$el = $(this),
						// determine what method to get it's value
						sMethod = ($el.is(":input") ? (defaults.useFieldPlugin ? "getValue" : "val") : "text"),
						// parse the string and get the first number we find
						v = $.trim($el[sMethod]()).match(defaults.reNumbers, "");
						
					// if the value is null, use 0
					if( v == null ){
						v = 0; // update value
						// if there's a error callback, execute it
						if( jQuery.isFunction(options.onParseError) ) options.onParseError.apply($el, [sMethod]);
						$.data($el[0], "calcParseError", true);
					// otherwise we take the number we found and remove any commas
					} else {
						// clense the number one more time to remove extra data (like commas and dollar signs)
						v = options.cleanseNumber.apply(this, [v[0]]);
						// if there's a clear callback, execute it
						if( $.data($el[0], "calcParseError") && jQuery.isFunction(options.onParseClear) ){
							options.onParseClear.apply($el, [sMethod]);
							// clear the error flag
							$.data($el[0], "calcParseError", false);
						} 
					}
					aValues.push(parseFloat(v, 10));
				}
			);

			// return an array of values
			return aValues;
		};

		/*
		 * jQuery.fn.calc()
		 *
		 * returns Number - performance a calculation and updates the field
		 *
		 * Examples:
		 * $("input[name='price']").calc();
		 * > This would return the sum of all the fields named price
		 *
		 */
		// the calc() method
		$.fn.calc = function(expr, vars, cbFormat, cbDone){
			var
				// create a pointer to the jQuery object
				$this = this
				// the value determine from the expression
				, exprValue = ""
				// track the precision to use
				, precision = 0
				// a pointer to the current jQuery element
				, $el
				// store an altered copy of the vars
				, parsedVars = {}
				// temp variable
				, tmp
				// the current method to use for updating the value
				, sMethod
				// a hash to store the local variables
				, _
				// track whether an error occured in the calculation
				, bIsError = false;

			// look for any jQuery objects and parse the results into numbers			
			for( var k in vars ){
				// replace the keys in the expression
				expr = expr.replace( (new RegExp("(" + k + ")", "g")), "_.$1");
				if( !!vars[k] && !!vars[k].jquery ){
					parsedVars[k] = vars[k].parseNumber();
				} else {
					parsedVars[k] = vars[k];
				}
			}
			
			this.each(
				function (i, el){
					var p, len;
					// get a pointer to the current element
					$el = $(this);
					// determine what method to get it's value
					sMethod = ($el.is(":input") ? (defaults.useFieldPlugin ? "setValue" : "val") : "text");

					// initialize the hash vars
					_ = {};
					for( var k in parsedVars ){
						if( typeof parsedVars[k] == "number" ){
							_[k] = parsedVars[k];
						} else if( typeof parsedVars[k] == "string" ){
							_[k] = parseFloat(parsedVars[k], 10);
						} else if( !!parsedVars[k] && (parsedVars[k] instanceof Array) ) {
							// if the length of the array is the same as number of objects in the jQuery
							// object we're attaching to, use the matching array value, otherwise use the
							// value from the first array item
							tmp = (parsedVars[k].length == $this.length) ? i : 0;
							_[k] = parsedVars[k][tmp];
						}

						// if we're not a number, make it 0
						if( isNaN(_[k]) ) _[k] = 0;

						// check for decimals and check the precision
						p = _[k].toString().match(/\.\d+$/gi);
						len = (p) ? p[0].length-1 : 0;

						// track the highest level of precision
						if( len > precision ) precision = len; 
					}


					// try the calculation
					try {
						exprValue = eval( expr );
						
						// fix any the precision errors
						if( precision ) exprValue = Number(exprValue.toFixed(Math.max(precision, 4)));

						// if there's a format callback, call it now
						if( jQuery.isFunction(cbFormat) ){
							// get return value
							var tmp = cbFormat.apply(this, [exprValue])
							// if we have a returned value (it's null null) use it
							if( !!tmp ) exprValue = tmp;
						}
			
					// if there's an error, capture the error output
					} catch(e){
						exprValue = e;
						bIsError = true;
					}
					
					// update the value
					$el[sMethod](exprValue.toString());
				}
			);
			
			// if there's a format callback, call it now
			if( jQuery.isFunction(cbDone) ) cbDone.apply(this, [this]);

			return this;
		};

		/*
		 * Define all the core aggregate functions. All of the following methods
		 * have the same functionality, but they perform different aggregate 
		 * functions.
		 * 
		 * If this methods are called without any arguments, they will simple
		 * perform the specified aggregate function and return the value. This
		 * will break the jQuery chain. 
		 * 
		 * However, if you invoke the method with any arguments then a jQuery
		 * object is returned, which leaves the chain intact.
		 * 
		 * 
		 * jQuery.fn.sum()
		 * returns Number - the sum of all fields
		 *
		 * jQuery.fn.avg()
		 * returns Number - the avg of all fields
		 *
		 * jQuery.fn.min()
		 * returns Number - the minimum value in the field
		 *
		 * jQuery.fn.max()
		 * returns Number - the maximum value in the field
		 * 
		 * Examples:
		 * $("input[name='price']").sum();
		 * > This would return the sum of all the fields named price
		 *
		 * $("input[name='price1'], input[name='price2'], input[name='price3']").sum();
		 * > This would return the sum of all the fields named price1, price2 or price3
		 *
		 * $("input[name^=sum]").sum("keyup", "#totalSum");
		 * > This would update the element with the id "totalSum" with the sum of all the 
		 * > fields whose name started with "sum" anytime the keyup event is triggered on
		 * > those field.
		 *
		 * NOTE: The syntax above is valid for any of the aggregate functions
		 *
		 */
		$.each(["sum", "avg", "min", "max"], function (i, method){
			$.fn[method] = function (bind, selector){
				// if no arguments, then return the result of the aggregate function
				if( arguments.length == 0 )
					return math[method](this.parseNumber());

				// if the selector is an options object, get the options
				var bSelOpt = selector && (selector.constructor == Object) && !(selector instanceof jQuery);

				// configure the options for this method
				var opt = bind && bind.constructor == Object ? bind : {
					  bind: bind || "keyup"
					, selector: (!bSelOpt) ? selector : null
					, oncalc: null
				};
				
				// if the selector is an options object, extend	the options
				if( bSelOpt ) opt = jQuery.extend(opt, selector);
		
				// if the selector exists, make sure it's a jQuery object
				if( !!opt.selector ) opt.selector = $(opt.selector);
				
				var self = this
					, sMethod
					, doCalc = function (){
						// preform the aggregate function
						var value = math[method](self.parseNumber(opt));
						// check to make sure we have a selector				
						if( !!opt.selector ){
							// determine how to set the value for the selector
							sMethod = (opt.selector.is(":input") ? (defaults.useFieldPlugin ? "setValue" : "val") : "text");
							// update the value
							opt.selector[sMethod](value.toString());
						}
						// if there's a callback, run it now
						if( jQuery.isFunction(opt.oncalc) ) opt.oncalc.apply(self, [value, opt]);
					};
				
				// perform the aggregate function now, to ensure init values are updated
				doCalc();
				
				// bind the doCalc function to run each time a key is pressed
				return self.bind(opt.bind, doCalc);
			}
		});
		
		/*
		 * Mathmatical functions
		 */
		var math = {
			// sum an array
			sum: function (a){
				var total = 0, precision = 0;
				
				// loop through the value and total them
				$.each(a, function (i, v){
					// check for decimals and check the precision
					var p = v.toString().match(/\.\d+$/gi), len = (p) ? p[0].length-1 : 0;
					// track the highest level of precision
					if( len > precision ) precision = len; 
					// we add 0 to the value to ensure we get a numberic value
					total += v;
				});

				// fix any the precision errors
				if( precision ) total = Number(total.toFixed(precision));
		
				// return the values as a comma-delimited string
				return total;
			},
			// average an array
			avg: function (a){
				// return the values as a comma-delimited string
				return math.sum(a)/a.length;
			},
			// lowest number in array
			min: function (a){
				return Math.min.apply(Math, a);
			},
			// highest number in array
			max: function (a){
				return Math.max.apply(Math, a);
			}
		};
		

	})(jQuery);

	
	