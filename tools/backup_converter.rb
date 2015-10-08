#!/usr/bin/env ruby

require 'mysql2'
require 'optparse'
require 'pathname'
require 'rexml/document'
require 'clipboard'

DEFAULT_TABLE_PREFIX = 'diary_'

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

	def to_s
		escaped_text = escapeForSql text
		"('#{date}', '#{escaped_text}', '#{state}', #{daily}, #{order})"
	end
end

class PointGroup
	def initialize xml, table_prefix = DEFAULT_TABLE_PREFIX
		@points = []
		xml.elements.each 'diary/days/day' do |day_element|
			order = 3
			day_element.elements.each 'point' do |point_element|
				@points << Point.new(
					date: day_element.attributes['date'],
					text: point_element.cdatas().join(''),
					state: point_element.attributes['state'],
					daily: getBooleanValue(point_element, 'daily'),
					order: order
				)

				order += 2
			end
		end

		@table_prefix = table_prefix
	end

	def to_s
		points_description = @points.join ",\n\t"
		"DELETE FROM `#{@table_prefix}points`;\n" +
			"INSERT INTO `#{@table_prefix}points` " +
				"(`date`, `text`, `state`, `daily`, `order`)\n" +
			"VALUES\n\t#{points_description};\n"
	end
end

class DailyPoint
	include Initializable

	attr_accessor :text
	attr_accessor :order

	def to_s
		escaped_text = escapeForSql text
		"('#{escaped_text}', #{order})"
	end
end

class DailyPointGroup
	def initialize xml, table_prefix = DEFAULT_TABLE_PREFIX
		@daily_points = []
		order = 3
		xml.elements.each 'diary/daily-points/daily-point' do |element|
			@daily_points << DailyPoint.new(
				text: element.cdatas().join(''),
				order: order
			)

			order += 2
		end

		@table_prefix = table_prefix
	end

	def to_s
		daily_points_description = @daily_points.join ",\n\t"
		"DELETE FROM `#{@table_prefix}daily_points`;\n" +
			"INSERT INTO `#{@table_prefix}daily_points` (`text`, `order`)\n" +
			"VALUES\n\t#{daily_points_description};\n"
	end
end

def escapeForSql text
	Mysql2::Client.escape text
end

def getBooleanValue element, attribute
	!!element.attributes[attribute] &&
		(element.attributes[attribute] == 'true' ||
		element.attributes[attribute] == '1')
end

def parseOptions
	options = {:prefix => DEFAULT_TABLE_PREFIX}
	OptionParser.new do |option_parser|
		option_parser.program_name = Pathname.new($0).basename
		option_parser.banner =
			"Usage: #{option_parser.program_name} [options] filename"

		option_parser.on(
			'-p PREFIX',
			'--prefix PREFIX',
			' - table name prefix;'
		) do |prefix|
			options[:prefix] = prefix
		end
		option_parser.on(
			'-t',
			'--no-transaction',
			' - disable a transaction using;'
		) do |prefix|
			options[:no_transaction] = true
		end
		option_parser.on(
			'-q',
			'--quiet',
			' - disable a printing to stdout;'
		) do |prefix|
			options[:quiet] = true
		end
		option_parser.on(
			'-c',
			'--no-clipboard',
			' - disable a copying to clipboard.'
		) do |prefix|
			options[:no_clipboard] = true
		end
	end.parse!

	options[:filename] = ARGV.pop
	raise "need to specify a file to process" unless options[:filename]

	options
end

def loadXml filename
	file = File.new filename
	REXML::Document.new file
end

def generateSql groups, no_transaction
	sql = groups.join "\n"
	if !no_transaction
		sql = "START TRANSACTION;\n\n#{sql}\nCOMMIT;"
	end

	sql
end

def outputSql sql, options
	if !options[:quiet]
		puts sql
	end
	if !options[:no_transaction]
		Clipboard.copy sql
	end
end

begin
	options = parseOptions
	xml = loadXml options[:filename]
	points = PointGroup.new xml, options[:prefix]
	daily_points = DailyPointGroup.new xml, options[:prefix]
	sql = generateSql [points, daily_points], options[:no_transaction]
	outputSql sql, options
rescue Exception => exception
	if exception.message != 'exit'
		puts "Error: \"#{exception.message}\"."
	end
end
