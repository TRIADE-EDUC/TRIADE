
class Helper {
	
	camelize(string) {
	    return string.toLowerCase().replace(/[^a-zA-Z0-9]+(.)/g, function(match, chr) {
	        return chr.toUpperCase();
	    });
	}
	
	cloneObject(obj) {
		if (obj instanceof Array) {
			let clone = new Array();
			for (let index in obj) {
				clone[index] = this.cloneObject(obj[index]);
			}	
			return clone;
		} else if (obj instanceof Object) {
			// On clone object on fait en sorte d'avoir les getter/setter
			let clone = {...obj};
			let descriptors = Object.getOwnPropertyDescriptors(clone);
			Object.defineProperties(clone, descriptors);
			return clone;
		}
		return obj; 
	}

	generateRandomString(length) {
		let result = '';

		const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		const charactersLength = characters.length;

		for (let i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		
		return result;
	}

	// Calcul la couleur du texte en fonction d'une couleur de fond
	calculateTextColor(backgroundHexColor) {

		// Convert hex to RGB
		const hexToRgb = (hex) => {
			const bigint = parseInt(hex.slice(1), 16);
			const r = (bigint >> 16) & 255;
			const g = (bigint >> 8) & 255;
			const b = bigint & 255;
			return { r, g, b };
		};

		// Calculate text color based on luminosity
		const calculateLuminosityTextColor = (r, g, b) => {
			const luminosity = (r * 299 + g * 587 + b * 114) / 1000;

			const textColor = (luminosity - 128) * -255000;
			return { r: textColor, g: textColor, b: textColor };
		};

		// Extract RGB values from hex color
		const { r, g, b } = hexToRgb(backgroundHexColor);

		// Calculate text color based on luminosity
		const textColor = calculateLuminosityTextColor(r, g, b);

		// Return the text color as a CSS string
		return `rgb(${textColor.r}, ${textColor.g}, ${textColor.b})`;
	}

	// Assonbri la couleur
	darkenColor(rgbaColor, factor) {
		factor = Math.min(1, Math.max(0, factor));
	
		const rgbaArray = rgbaColor.split('(')[1].split(')')[0].split(',');
		const red = Math.floor(parseInt(rgbaArray[0]) * (1 - factor));
		const green = Math.floor(parseInt(rgbaArray[1]) * (1 - factor));
		const blue = Math.floor(parseInt(rgbaArray[2]) * (1 - factor));
		const alpha = parseFloat(rgbaArray[3]);
	
		return `rgba(${red}, ${green}, ${blue}, ${alpha})`;
	}

	djb2(str) {
		let hash = 5381;
		for (let i = 0; i < str.length; i++) {
			hash = (hash * 33) ^ str.charCodeAt(i);
		}
		return hash >>> 0;
	}

	getColorIndex(seed, colors) {
		return this.djb2(seed) % colors.length;
	}
}

export default new Helper();