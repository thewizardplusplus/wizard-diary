from sys import argv
from xml.dom.minidom import parse
from base64 import b64decode

def Escape(text):
	text = text.replace('\\', '\\\\')
	for sample, replace in {'\'': '\\\'', '"': '\\"', '\n': '\\n', '\r': '\\r', '\x00': '\\0', '\x1a': '\\Z'}.iteritems():
		text = text.replace(sample, replace);

	return text

if len(argv) != 2:
	print 'Error: invalid arguments.'
	print 'Using: %s filename' % __file__

	exit(1)

try:
	sql = 'DELETE FROM `diary_points`;\n'
	sql += 'INSERT INTO `diary_points` (`date`, `text`, `state`, `check`, `order`) VALUES\n'

	dom = parse(argv[1])
	for day in dom.getElementsByTagName('day'):
		date = day.attributes['date'].value
		order = 3
		for point in day.getElementsByTagName('point'):
			text = Escape(b64decode(point.firstChild.nodeValue)) if len(point.childNodes) == 1 else ''
			state = point.attributes['state'].value
			check = 1 if point.hasAttribute('check') and point.attributes['check'].value == 'true' else 0
			sql += "\t('{0}', '{1}', '{2}', {3}, {4}),\n".format(date, text, state, check, order)
			order += 2
	sql = sql[:-2] + ';'

	with open(argv[1].replace('.xml', '') + '.sql', 'w') as out:
		out.write(sql)
except IOError as exception:
	print 'Error! %s.' % str(exception)
