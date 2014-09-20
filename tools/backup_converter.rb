#!/usr/bin/env ruby

require 'rexml/document'
require 'mysql2'

begin
	raise "need to specify a file to process" unless ARGV.length > 0
	backup_filename = ARGV.first

	backup_file = File.new(backup_filename)
	xml = REXML::Document.new(backup_file)
	xml.elements.each('diary/day') do |day|
		date = day.attributes['date']
		order = 1
		day.elements.each('point') do |point|
			raw_text = point.cdatas().join('')
			escaped_text = Mysql2::Client.escape(raw_text)

			p date
			p point.attributes['state']
			p point.attributes['check'] ? 1 : 0
			p escaped_text
			p order
			p ''

			order += 2
		end
		p ''
	end
rescue Exception => exception
	puts "Error: \"#{exception.message}\"."
end
