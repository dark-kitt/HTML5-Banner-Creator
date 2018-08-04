var colorSwap = function(o) {

	var target = gid(o.id) || gcl(o.cl) || gtn(o.tn) || o.el,
		delay = o.delay || (o.delay === 0 ? 0 : 1000),
		fps = o.fps || 20,
		steps = o.steps || 20,
		start = colourNameToHex(o.start) || o.start || '#000',
		end = colourNameToHex(o.end) || o.end || '#FFF',
		style = o.style || null;

		var Rstart, Gstart, Bstart, Astart, Rend, Gend, Bend, Aend, stop;
		if (start.match(/hsla/g) || start.match(/rgba/g) || end.match(/hsla/g) || end.match(/rgba/g)) {
			Rstart = hexToRGB(start).r || hsl2rgb(start)[0] || parseRGB(start)[0];
			Gstart = hexToRGB(start).g || hsl2rgb(start)[1] || parseRGB(start)[1];
			Bstart = hexToRGB(start).b || hsl2rgb(start)[2] || parseRGB(start)[2];
			Astart = (start.match(/hsla/g)) ? hsl2rgb(start)[3] : (start.match(/rgba/g)) ? parseRGB(start)[3] : 1;

			Rend = hexToRGB(end).r || hsl2rgb(end)[0] || parseRGB(end)[0];
			Gend = hexToRGB(end).g || hsl2rgb(end)[1] || parseRGB(end)[1];
			Bend = hexToRGB(end).b || hsl2rgb(end)[2] || parseRGB(end)[2];
			Aend = (end.match(/hsla/g)) ? hsl2rgb(end)[3] : (end.match(/rgba/g)) ? parseRGB(end)[3] : 1;

			stop = 'rgba(' + Rend + ',' + Gend + ',' + Bend + ',' + Aend + ')';
		} else {
			Rstart = hexToRGB(start).r || hsl2rgb(start)[0] || parseRGB(start)[0];
			Gstart = hexToRGB(start).g || hsl2rgb(start)[1] || parseRGB(start)[1];
			Bstart = hexToRGB(start).b || hsl2rgb(start)[2] || parseRGB(start)[2];

			Rend = hexToRGB(end).r || hsl2rgb(end)[0] || parseRGB(end)[0];
			Gend = hexToRGB(end).g || hsl2rgb(end)[1] || parseRGB(end)[1];
			Bend = hexToRGB(end).b || hsl2rgb(end)[2] || parseRGB(end)[2];

			stop = 'rgb(' + Rend + ',' + Gend + ',' + Bend + ')';
		}

	function hexToRGB(hex) {
		var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
			hex = hex.replace(shorthandRegex, function(m, r, g, b) {
			return r + r + g + g + b + b;
		});

		var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		return result ? {
			r: parseInt(result[1], 16),
			g: parseInt(result[2], 16),
			b: parseInt(result[3], 16)
		} : false;
	}

	function hue2rgb(p, q, t){
		if(t < 0) t += 1;
		if(t > 1) t -= 1;
		if(t < 1/6) return p + (q - p) * 6 * t;
		if(t < 1/2) return q;
		if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
		return p;
	}

	function hsl2rgb(hsl){
		if (hsl.match(/hsl/g)) {
			var hslArr = hsl.match(/$|(\d{3}|\d{2}|\d\.\d{2}|\d)/g),
				r, g, b,
				h = parseFloat(hslArr[0])/360,
				s = parseFloat(hslArr[1])/100,
				l = parseFloat(hslArr[2])/100;
			if(s === 0){
				r = g = b = l;
			} else {
				var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
				var p = 2 * l - q;
				r = hue2rgb(p, q, h + 1/3);
				g = hue2rgb(p, q, h);
				b = hue2rgb(p, q, h - 1/3);
			}
			if (hslArr.length <= 4) {
				return [fixValue(r * 255, 0), fixValue(g * 255, 0), fixValue(b * 255, 0)];
			} else {
				return [fixValue(r * 255, 0), fixValue(g * 255, 0), fixValue(b * 255, 0), fixValue(hslArr[3])];
			}
		} else {
			return false;
		}
	}

	function parseRGB(rgb) {
		if (rgb.match(/rgb/g)) {
			var rgbaCheck = rgb.match(/$|(\d{3}|\d{2}|\d\.\d{2}|\d)/g),
				subMatch = (rgbaCheck.length <= 4) ? 4 : 5;
			return rgb.substring(subMatch, rgb.length - 1)
				.replace(/ /g, '')
				.split(',').map(function(str) {
					return parseFloat(str);
				});
		} else {
			return false;
		}
	}

	function colourNameToHex(color) {
		var colors = {"aliceblue":"#f0f8ff","antiquewhite":"#faebd7","aqua":"#00ffff","aquamarine":"#7fffd4","azure":"#f0ffff","beige":"#f5f5dc","bisque":"#ffe4c4","black":"#000000","blanchedalmond":"#ffebcd","blue":"#0000ff","blueviolet":"#8a2be2","brown":"#a52a2a","burlywood":"#deb887","cadetblue":"#5f9ea0","chartreuse":"#7fff00","chocolate":"#d2691e","coral":"#ff7f50","cornflowerblue":"#6495ed","cornsilk":"#fff8dc","crimson":"#dc143c","cyan":"#00ffff","darkblue":"#00008b","darkcyan":"#008b8b","darkgoldenrod":"#b8860b","darkgray":"#a9a9a9","darkgreen":"#006400","darkkhaki":"#bdb76b","darkmagenta":"#8b008b","darkolivegreen":"#556b2f","darkorange":"#ff8c00","darkorchid":"#9932cc","darkred":"#8b0000","darksalmon":"#e9967a","darkseagreen":"#8fbc8f","darkslateblue":"#483d8b","darkslategray":"#2f4f4f","darkturquoise":"#00ced1","darkviolet":"#9400d3","deeppink":"#ff1493","deepskyblue":"#00bfff","dimgray":"#696969","dodgerblue":"#1e90ff","firebrick":"#b22222","floralwhite":"#fffaf0","forestgreen":"#228b22","fuchsia":"#ff00ff","gainsboro":"#dcdcdc","ghostwhite":"#f8f8ff","gold":"#ffd700","goldenrod":"#daa520","gray":"#808080","green":"#008000","greenyellow":"#adff2f","honeydew":"#f0fff0","hotpink":"#ff69b4","indianred":"#cd5c5c","indigo":"#4b0082","ivory":"#fffff0","khaki":"#f0e68c","lavender":"#e6e6fa","lavenderblush":"#fff0f5","lawngreen":"#7cfc00","lemonchiffon":"#fffacd","lightblue":"#add8e6","lightcoral":"#f08080","lightcyan":"#e0ffff","lightgoldenrodyellow":"#fafad2","lightgrey":"#d3d3d3","lightgreen":"#90ee90","lightpink":"#ffb6c1","lightsalmon":"#ffa07a","lightseagreen":"#20b2aa","lightskyblue":"#87cefa","lightslategray":"#778899","lightsteelblue":"#b0c4de","lightyellow":"#ffffe0","lime":"#00ff00","limegreen":"#32cd32","linen":"#faf0e6","magenta":"#ff00ff","maroon":"#800000","mediumaquamarine":"#66cdaa","mediumblue":"#0000cd","mediumorchid":"#ba55d3","mediumpurple":"#9370d8","mediumseagreen":"#3cb371","mediumslateblue":"#7b68ee","mediumspringgreen":"#00fa9a","mediumturquoise":"#48d1cc","mediumvioletred":"#c71585","midnightblue":"#191970","mintcream":"#f5fffa","mistyrose":"#ffe4e1","moccasin":"#ffe4b5","navajowhite":"#ffdead","navy":"#000080","oldlace":"#fdf5e6","olive":"#808000","olivedrab":"#6b8e23","orange":"#ffa500","orangered":"#ff4500","orchid":"#da70d6","palegoldenrod":"#eee8aa","palegreen":"#98fb98","paleturquoise":"#afeeee","palevioletred":"#d87093","papayawhip":"#ffefd5","peachpuff":"#ffdab9","peru":"#cd853f","pink":"#ffc0cb","plum":"#dda0dd","powderblue":"#b0e0e6","purple":"#800080","rebeccapurple":"#663399","red":"#ff0000","rosybrown":"#bc8f8f","royalblue":"#4169e1","saddlebrown":"#8b4513","salmon":"#fa8072","sandybrown":"#f4a460","seagreen":"#2e8b57","seashell":"#fff5ee","sienna":"#a0522d","silver":"#c0c0c0","skyblue":"#87ceeb","slateblue":"#6a5acd","slategray":"#708090","snow":"#fffafa","springgreen":"#00ff7f","steelblue":"#4682b4","tan":"#d2b48c","teal":"#008080","thistle":"#d8bfd8","tomato":"#ff6347","turquoise":"#40e0d0","violet":"#ee82ee","wheat":"#f5deb3","white":"#ffffff","whitesmoke":"#f5f5f5","yellow":"#ffff00","yellowgreen":"#9acd32"};

		if (typeof colors[color.toLowerCase()] !== 'undefined') {
			return colors[color.toLowerCase()];
		} else {
			return false;
		}
	}

	function countColor(property) {

		if (o.id || o.el) {
			if (target.style[property] != start) {
				target.style[property] = start;
			}
		} else {
			for (var i = 0; i < target.length; i++) {
				if (target[i].style[property] != start) {
					target[i].style[property] = start;
				}
			}
		}

		var Rcurrent, Gcurrent, Bcurrent, Acurrent;
		var colorCount = animationInterval(function() {

			if (start.match(/hsla/g) || start.match(/rgba/g) || end.match(/hsla/g) || end.match(/rgba/g)) {
				if (o.id || o.el) {
					Rcurrent = parseRGB(target.style[property])[0];
					Gcurrent = parseRGB(target.style[property])[1];
					Bcurrent = parseRGB(target.style[property])[2];
					Acurrent = parseRGB(target.style[property])[3];
				} else {
					for (var i = 0; i < target.length; i++) {
						Rcurrent = parseRGB(target[i].style[property])[0];
						Gcurrent = parseRGB(target[i].style[property])[1];
						Bcurrent = parseRGB(target[i].style[property])[2];
						Acurrent = parseRGB(target[i].style[property])[3];
					}
				}
			} else {
				if (o.id || o.el) {
					Rcurrent = parseRGB(target.style[property])[0];
					Gcurrent = parseRGB(target.style[property])[1];
					Bcurrent = parseRGB(target.style[property])[2];
				} else {
					for (var j = 0; j < target.length; j++) {
						Rcurrent = parseRGB(target[j].style[property])[0];
						Gcurrent = parseRGB(target[j].style[property])[1];
						Bcurrent = parseRGB(target[j].style[property])[2];
					}
				}
			}

			var Rnew, Gnew, Bnew, Anew, color;

			if (Rstart > Rend) {
				Rnew = ((Rcurrent - steps) > Rend) ? Rcurrent - steps : Rend;
			} else {
				Rnew = ((Rcurrent + steps) < Rend) ? Rcurrent + steps : Rend;
			}
			if (Gstart > Gend) {
				Gnew = ((Gcurrent - steps) > Gend) ? Gcurrent - steps : Gend;
			} else {
				Gnew = ((Gcurrent + steps) < Gend) ? Gcurrent + steps : Gend;
			}
			if (Bstart > Bend) {
				Bnew = ((Bcurrent + steps) > Bend) ? Bcurrent - steps : Bend;
			} else {
				Bnew = ((Bcurrent + steps) < Bend) ? Bcurrent + steps : Bend;
			}
			if (start.match(/hsla/g) || start.match(/rgba/g) || end.match(/hsla/g) || end.match(/rgba/g)) {
				if (Astart > Aend) {
					Anew = ((Acurrent + (steps / 100)) > Aend) ? Acurrent - (steps / 100) : Aend;
				} else {
					Anew = ((Acurrent + (steps / 100)) < Aend) ? Acurrent + (steps / 100) : Aend;
				}

				color = 'rgba(' + Rnew + ',' + Gnew + ',' + Bnew + ',' + Anew + ')';
			} else {
				color = 'rgb(' + Rnew + ',' + Gnew + ',' + Bnew + ')';
			}

			if (o.id || o.el) {
				target.style[property] = color;
			} else {
				for (var k = 0; k < target.length; k++) {
					target[k].style[property] = color;
				}
			}

			if (color === stop) {
				if( !window.cancelAnimationFrame ) {
					clearAnimationInterval(colorCount);
				}
				return true;
			}
			return false;
		}, fixValue(1000 / fps, 0));
	}

	return animationTimeout(function() {

		switch (style) {
			case 'background':
				var backgroundColor = 'backgroundColor';
				countColor(backgroundColor);
				break;
			case 'border':
				var borderColor = 'borderColor';
				countColor(borderColor);
				break;
			case 'color':
				var color = 'color';
				countColor(color);
				break;
			case 'fill':
				var fill = 'fill';
				countColor(fill);
				break;
			case 'stroke':
				var stroke = 'stroke';
				countColor(stroke);
				break;
			default:
				alert('Please choose a style => background, border, color or fill');
				break;
		}

	}, delay);
};
