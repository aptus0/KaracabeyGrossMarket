require 'xcodeproj'

project_path = 'Karacabey Gross Market/Karacabey Gross Market.xcodeproj'
project = Xcodeproj::Project.open(project_path)
target = project.targets.first

group_name = 'Karacabey Gross Market'
main_group = project.main_group.find_subpath(group_name, true)

# Folders to add
folders_to_add = ['App', 'Networking', 'Models', 'ViewModels', 'Views', 'Components', 'Utils', 'Fonts']

folders_to_add.each do |folder_name|
  folder_path = File.join(group_name, folder_name)
  next unless Dir.exist?(folder_path)

  group = main_group.children.find { |c| c.name == folder_name || c.path == folder_name }
  if group.nil?
    group = main_group.new_group(folder_name, folder_name)
  end

  Dir.foreach(folder_path) do |file_name|
    next if file_name == '.' || file_name == '..'
    file_path = File.join(folder_path, file_name)
    
    # Check if file is already in group
    existing_file = group.children.find { |c| c.path == file_name }
    next if existing_file

    file_ref = group.new_reference(file_name)
    
    if file_name.end_with?('.swift')
      target.source_build_phase.add_file_reference(file_ref)
    elsif file_name.end_with?('.ttf')
      target.resources_build_phase.add_file_reference(file_ref)
    end
  end
end

project.save
puts "Successfully added files and folders to the Xcode project."
