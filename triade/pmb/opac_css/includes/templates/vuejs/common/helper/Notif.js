dojo.require('dojo.topic');

class Notif {
	
	info(msg) {
		dojo.topic.publish('dGrowl', msg, {'sticky' : false, 'duration' : 5000, 'channel' : 'info'});
	}

	error(msg) {
		dojo.topic.publish('dGrowl', msg, {'sticky' : false, 'duration' : 5000, 'channel' : 'error'});
	}
	
}

export default new Notif();