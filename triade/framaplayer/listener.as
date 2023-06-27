// -----------------------------------------------------------
// Univeral method for javascript->flash setvariable
// http://www.mustardlab.com/developer/flash/jscommunication
// -----------------------------------------------------------
if(!_level0.$jslisten_init){
	Stage.$jsvarlistener = new LocalConnection();
	Stage.$jsvarlistener.setVariables = function(query) {
		var i, values;
		var chunk = query.split("&");
		for (i in chunk) {
			values = chunk[i].split("=");
			_root[values[0]] = values[1];
		}
	};
	Stage.$jsvarlistener.connect(_level0.movieid);
	_level0.$jslisten_init = true;
}
//domain = Stage.$jsvarlistener.domain();