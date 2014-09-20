#!/usr/bin/env ruby

require 'optparse'
require 'pathname'
require 'rexml/document'
require 'mysql2'

def parseOptions
	options = {:prefix => 'diary_'}
	OptionParser.new do |option_parser|
		option_parser.program_name = Pathname.new($0).basename
		option_parser.banner =
			"Usage: #{option_parser.program_name} [options] filename"

		option_parser.on('--prefix PREFIX', 'table name prefix') do |prefix|
			options[:prefix] = prefix
		end
	end.parse!

	options[:filename] = ARGV.pop
	raise "need to specify a file to process" unless options[:filename]

	options
end

begin
	options = parseOptions

	puts "DELETE FROM `#{options[:prefix]}points`;"
	puts "INSERT INTO `#{options[:prefix]}points` " +
		'(`date`, `text`, `state`, `check`, `order`)'
	puts 'VALUES'

	backup_file = File.new(options[:filename])
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
