#!/usr/bin/env ruby

require 'pathname'

begin
    raise "need to specify a file to process" unless ARGV.length > 0
    backup_filename = ARGV.first
    p backup_filename
rescue Exception => exception
    puts "Error: \"#{exception.message}\"."
end
