#!/usr/bin/env ruby

require 'optparse'
require 'pathname'
require 'rexml/document'
require 'mysql2'

class Point
	attr_accessor :date
	attr_accessor :text
	attr_accessor :state
	attr_accessor :check
	attr_accessor :order
end

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

def loadXml(filename)
	file = File.new(filename)
	REXML::Document.new(file)
end

def extractPoints(xml)
	points = []
	xml.elements.each('diary/day') do |day_element|
		order = 1
		day_element.elements.each('point') do |point_element|
			point = Point.new
			point.date = day_element.attributes['date']
			point.text = point_element.cdatas().join('')
			point.state = point_element.attributes['state']
			point.check =
				!!point_element.attributes['check'] &&
				(point_element.attributes['check'] == 'true' ||
				point_element.attributes['check'] == '1')
			point.order = order

			points << point
			order += 2
		end
	end

	points
end

def generateSql(points, table_prefix)
	"DELETE FROM `#{table_prefix}points`;\n" +
	"INSERT INTO `#{table_prefix}points` " +
		"(`date`, `text`, `state`, `check`, `order`)\n" +
	"VALUES\n" +
	points.map do |point|
		text = Mysql2::Client.escape(point.text)
		"\t(" +
			"'#{point.date}', " +
			"'#{text}', " +
			"'#{point.state}', " +
			"#{point.check}, " +
			"#{point.order}" +
		")"
	end.join(",\n") + ';'
end

begin
	options = parseOptions
	xml = loadXml(options[:filename])
	points = extractPoints(xml)
	sql = generateSql(points, options[:prefix])
	puts sql
rescue Exception => exception
	puts "Error: \"#{exception.message}\"."
end
