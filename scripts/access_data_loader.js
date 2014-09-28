var AccessData = {};

$(document).ready(
	function() {
		// original by Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		var HtmlEncode = function(string) {
			var text = document.createTextNode(string);
			var block = document.createElement('div');
			block.appendChild(text);

			return block.innerHTML;
		};
		var WrapIfExists = function(object, element, options) {
			options = options || {};
			options.prepend = options.prepend || '';
			options.append = options.append || '';
			options.default_value = options.default_value || '';

			return object[element]
				? options.prepend + HtmlEncode(object[element]) + options.append
				: options.default_value;
		};
		var FormatIpData = function(ip, data) {
			return '<p>' + ip + '</p>'
				+ '<p class = "small-text additional-info">'
					+ '<a href = "http://maps.google.com/maps?'
						+ '&q=' + encodeURIComponent(
							data.loc
						) + '">'
						+ '<span class = "glyphicon '
							+ 'glyphicon-map-marker">'
						+ '</span>'
					+ '</a> '
				+ COUNTRIES_CODES.getNameByAlpha2(
					data.country
				)
				+ WrapIfExists(
					data,
					'region',
					{prepend: ', '}
				)
				+ WrapIfExists(
					data,
					'city',
					{prepend: ', '}
				)
				+ '</p>';
		};
		var FormatUserAgentData = function(user_agent, data) {
			var os_type = data.os_type || '&mdash;';
			var os_name = data.os_name || '&mdash;';
			if (data.linux_distibution) {
				if (os_type == 'Linux' && os_name == 'Linux') {
					os_name = data.linux_distibution;
				} else {
					os_name += ' (' + HtmlEncode(data.linux_distibution) + ')';
				}
			}

			return '<p>' + user_agent + '</p>'
				+ '<p class = "small-text additional-info">'
					+ 'ОС:'
					+ ' ' + HtmlEncode(os_type)
					+ ', ' + HtmlEncode(os_name)
					+ WrapIfExists(data, 'os_versionName', {prepend: ' '})
					+ WrapIfExists(data, 'os_versionNumber', {prepend: ' '})
					+ '.'
				+ '</p>'
				+ '<p class = "small-text additional-info">'
					+ 'Агент:'
					+ WrapIfExists(
						data,
						'agent_type',
						{
							prepend: ' ',
							default_value: ', &mdash;'
						}
					)
					+ WrapIfExists(
						data,
						'agent_name',
						{
							prepend: ', ',
							default_value: ', &mdash;'
						}
					)
					+ WrapIfExists(data, 'agent_version', {prepend: ' '})
					+ '.'
				+ '</p>';
		};

		AccessData.load = function() {
			$('#access-list .access-data').each(
				function() {
					var access_data = $(this);

					var ip = access_data.data('ip');
					var decode_ip_url = access_data.data('decode-ip-url');
					$.get(
						decode_ip_url,
						function(data) {
							$('.access-ip', access_data).html(
								FormatIpData(ip, data)
							);
						},
						'json'
					).fail(
						AjaxErrorDialog.handler
					);

					var user_agent = access_data.data('user-agent');
					var decode_user_agent_url = access_data.data(
						'decode-user-agent-url'
					);
					$.get(
						decode_user_agent_url,
						function(data) {
							$('.access-user-agent', access_data).html(
								FormatUserAgentData(user_agent, data)
							);
						},
						'json'
					).fail(
						AjaxErrorDialog.handler
					);
				}
			);
		};

		AccessData.load();
	}
);
