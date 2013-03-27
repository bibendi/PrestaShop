#!/usr/bin/env ruby
puts 'Starting parse...'

base_url = 'http://demo.presthemes.com/buddie'
theme_name = 'buddie'
rejected_exts = %w(.php)
upload_dir = File.expand_path(File.dirname(__FILE__))
files = (Dir["default/*.*"] + Dir["default/**/*.*"]).delete_if{ |x| rejected_exts.include?(File.extname(x)) }

puts "Found #{files.size} files"

files.each do |file_name|
  upload_file_name = file_name.sub('default', theme_name)
  url = "#{base_url}/themes/#{upload_file_name}"
  abc_upload_file_name = "#{upload_dir}/#{upload_file_name}"
  `mkdir -p #{File.dirname(abc_upload_file_name)}`
  cmd = "wget -q --no-clobber --max-redirect=0 --output-document='#{abc_upload_file_name}' #{url}"
  `wget #{cmd}`
  `rm #{abc_upload_file_name}` if File.size(abc_upload_file_name).zero?
  #puts cmd
end

puts 'Parsing finished'