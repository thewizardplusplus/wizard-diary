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

class DailyPoint
	attr_accessor :text
	attr_accessor :check
	attr_accessor :order
end

class Import
	attr_accessor :date
	attr_accessor :points_description
	attr_accessor :imported
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
	xml.elements.each('diary/days/day') do |day_element|
		order = 3
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

def extractDailyPoints(xml)
	daily_points = []
	order = 3
	xml.elements.each('diary/daily-points/daily-point') do |daily_point_element|
		daily_point = DailyPoint.new
		daily_point.text = daily_point_element.cdatas().join('')
		daily_point.check =
			!!daily_point_element.attributes['check'] &&
			(daily_point_element.attributes['check'] == 'true' ||
			daily_point_element.attributes['check'] == '1')
		daily_point.order = order

		daily_points << daily_point
		order += 2
	end

	daily_points
end

def extractImports(xml)
	imports = []
	xml.elements.each('diary/imports/import') do |import_element|
		import = Import.new
		import.date = import_element.attributes['date']
		import.points_description = import_element.cdatas().join('')
		import.imported =
			!!import_element.attributes['imported'] &&
			(import_element.attributes['imported'] == 'true' ||
			import_element.attributes['imported'] == '1')

		imports << import
	end

	imports
end

def generatePointsSql(points, table_prefix)
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
	end.join(",\n") + ";\n"
end

def generateDailyPointsSql(daily_points, table_prefix)
	"DELETE FROM `#{table_prefix}daily_points`;\n" +
	"INSERT INTO `#{table_prefix}daily_points` " +
		"(`text`, `check`, `order`)\n" +
	"VALUES\n" +
	daily_points.map do |daily_point|
		text = Mysql2::Client.escape(daily_point.text)
		"\t(" +
			"'#{text}', " +
			"#{daily_point.check}, " +
			"#{daily_point.order}" +
		")"
	end.join(",\n") + ";\n"
end

def generateImportsSql(imports, table_prefix)
	"DELETE FROM `#{table_prefix}imports`;\n" +
	"INSERT INTO `#{table_prefix}imports` " +
		"(`date`, `points_description`, `imported`)\n" +
	"VALUES\n" +
	imports.map do |import|
		points_description = Mysql2::Client.escape(import.points_description)
		"\t(" +
			"'#{import.date}', " +
			"'#{points_description}', " +
			"#{import.imported}" +
		")"
	end.join(",\n") + ";\n"
end

begin
	options = parseOptions
	xml = loadXml(options[:filename])
	points = extractPoints(xml)
	daily_points = extractDailyPoints(xml)
	imports = extractImports(xml)
	sql =
		generatePointsSql(points, options[:prefix]) + "\n" +
		generateDailyPointsSql(daily_points, options[:prefix]) + "\n" +
		generateImportsSql(imports, options[:prefix])

	puts sql
rescue Exception => exception
	puts "Error: \"#{exception.message}\"."
end
