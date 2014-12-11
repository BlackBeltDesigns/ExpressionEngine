class UploadDirectories < ControlPanelPage

	element :table, 'table'
	element :sort_col, 'table th.highlight'
	elements :directories, 'table tr td:nth-child(2)'

	def load
		settings_btn.click
		within 'div.sidebar' do
			click_link 'Upload Directories'
		end
	end
end