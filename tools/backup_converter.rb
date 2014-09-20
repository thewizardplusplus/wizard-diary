#!/usr/bin/env ruby

require 'rexml/document'
require 'mysql2'

begin
	raise "need to specify a file to process" unless ARGV.length > 0
	backup_filename = ARGV.first

	puts 'DELETE FROM `diary_points`;'
	puts 'INSERT INTO `diary_points` ' +
		'(`date`, `text`, `state`, `check`, `order`)'
	puts 'VALUES'

	backup_file = File.new(backup_filename)
	xml = REXML::Document.new(backup_file)
	xml.elements.each('diary/day') do |day|
		date = day.attributes['date']
		order = 1
		day.elements.each('point') do |point|
			state = point.attributes['state']
			check = point.attributes['check'] ? 1 : 0

			raw_text = point.cdatas().join('')
			escaped_text = Mysql2::Client.escape(raw_text)

			separator = day.next_element() || point.next_element() ? ',' : ';'

			puts "\t(" +
				"'#{date}', " +
				"'#{escaped_text}', " +
				"'#{state}', " +
				"#{check}, " +
				"#{order}" +
			")#{separator}"

			order += 2
		end
	end
rescue Exception => exception
	puts "Error: \"#{exception.message}\"."
end
