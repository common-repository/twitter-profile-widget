var twitterProfileUpdate = function(data) {
	(function($) {
		$( function() {
			var tp = new twitterProfile(data.screen_name);
			tp.update(data,'tp_');
		});
		var twitterProfile = function(screen_name) {
			this.profile = $('#TwitterProfile_'+screen_name);
			this.ssl = ("https:" == document.location.protocol);
			this.textFormat = function(text) {
				if(!text)
					return text;
				text = text.replace( /(https?:\/\/[a-zA-Z0-9.\/%#\?]+)/, '<a href="$1" target="_blank">$1</a>' );
				text = text.replace( /@([a-zA-Z0-9_]+)/, '<a href="http://twitter.com/$1" target="_blank">@$1</a>' );
				return text.replace( /#([^\s^　]+)/, '<a href="http://twitter.com/#search?q=$1" target="_blank">#$1</a>' );
			};
			this.numberFormat = function(number) {
				return number?number.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' ):'';
			};
			this.dateFormat = function(date) {
				return date;//date?new Date(date).toString():'';
			};
			this.update = function(data,prefix) {
                if(data.error == undefined) {
					var self = this;
					$.each(data, function(prop,value) {
						switch(typeof value) {
							case 'object':
								if(prop == 'status' && value) {
									self.update(value,prefix+prop+'_');
									break;
								}
							case 'boolean':
								break;
							case 'number':
								if(prop.match(/_count$/))
									value = self.numberFormat(value);
								else
									value = value.toString();
							default:
								if(value != null) {
									if(prop.match(/_at$/))
										value = self.dateFormat(value);
									if(prop.match(/image_url/) || prop == 'source') {
										if(prop == 'profile_image_url')
											$('img.'+prefix+'profile_image',self.profile).attr('src',self.ssl?data['profile_image_url_https']:value);
									} else {
										value = self.textFormat(value);
									}
									$('.'+prefix+prop,self.profile).html(value);
								}
								break;
						}
					});
				}
			};
		};
	})(jQuery);
};