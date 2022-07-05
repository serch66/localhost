(()=>{
	let mapper = {
		'D':"E",
		'N': Application.getPlatform() === 'ios'? "c" : "u",
		'd': "dd",
		'j': "d",
		'M':'MMM',
		'F':"MMMM",
		'm':"MM",
		'n':"M",
		'i':"m",
		'l':"EEEE"
	}

	let convert = value => value.replace(/\b(\w+)\b/g, find => mapper[find] ? mapper[find] : find)
	this.dateFormatter = {
		formats:(()=> {
			if (typeof this.jnExtensionData.get("date")["formats"] !== "undefined") {
				return this.jnExtensionData.get("date").formats;
			}
			return {};
		})(),
		get:(timestamp, format, locale)=> DateFormatter.getDateString(timestamp, convert(format), locale)
	}
})();

