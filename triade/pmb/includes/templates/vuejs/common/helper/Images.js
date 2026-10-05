
class Images {
	
	get(code) {
		const image = pmbDojo.images.getImage(code);
		return ("" != image) ? image : code;
	}

}

export default new Images();