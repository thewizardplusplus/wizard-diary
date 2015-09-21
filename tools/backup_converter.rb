#!/usr/bin/env ruby

require 'optparse'
require 'pathname'
require 'rexml/document'
require 'mysql2'
require 'clipboard'

module Initializable
	def initialize parameters = {}
		parameters.each do |key, value|
			send "#{key}=", value
		end
	end
end

class Point
	include Initializable

	attr_accessor :date
	attr_accessor :text
	attr_accessor :state
	attr_accessor :daily
	attr_accessor :order
end

class DailyPoint
	include Initializable

	attr_accessor :text
	attr_accessor :order
end

class Import
	include Initializable

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

def getBooleanValue(element, attribute)
	!!element.attributes[attribute] &&
	(element.attributes[attribute] == 'true' ||
	element.attributes[attribute] == '1')
end

def extractPoints(xml)
	points = []
	xml.elements.each('diary/days/day') do |day_element|
		order = 3
		day_element.elements.each('point') do |point_element|
			points << Point.new(
				date: day_element.attributes['date'],
				text: point_element.cdatas().join(''),
				state: point_element.attributes['state'],
				daily: getBooleanValue(point_element, 'daily'),
				order: order
			)

			order += 2
		end
	end

	points
end

def extractDailyPoints(xml)
	daily_points = []
	order = 3
	xml.elements.each('diary/daily-points/daily-point') do |daily_point_element|
		daily_points << DailyPoint.new(
			text: daily_point_element.cdatas().join(''),
			order: order
		)

		order += 2
	end

	daily_points
end

def extractImports(xml)
	imports = []
	xml.elements.each('diary/imports/import') do |import_element|
		imports << Import.new(
			date: import_element.attributes['date'],
			points_description: import_element.cdatas().join(''),
			imported: getBooleanValue(import_element, 'imported')
		)
	end

	imports
end

def generatePointsSql(points, table_prefix)
	"DELETE FROM `#{table_prefix}points`;\n" +
	"INSERT INTO `#{table_prefix}points` " +
		"(`date`, `text`, `state`, `daily`, `order`)\n" +
	"VALUES\n" +
	points.map do |point|
		text = Mysql2::Client.escape(point.text)
		"\t(" +
			"'#{point.date}', " +
			"'#{text}', " +
			"'#{point.state}', " +
			"#{point.daily}, " +
			"#{point.order}" +
		")"
	end.join(",\n") + ";\n"
end

def generateDailyPointsSql(daily_points, table_prefix)
	"DELETE FROM `#{table_prefix}daily_points`;\n" +
	"INSERT INTO `#{table_prefix}daily_points` (`text`, `order`)\n" +
	"VALUES\n" +
	daily_points.map do |daily_point|
		text = Mysql2::Client.escape(daily_point.text)
		"\t(" +
			"'#{text}', " +
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

	Clipboard.copy(sql)
	puts sql
rescue Exception => exception
	puts "Error: \"#{exception.message}\"."
end
