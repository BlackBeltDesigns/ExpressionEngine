class Communicate < ControlPanelPage
	set_url_matcher /utilities\/communicate/

	element :title, 'div.box h1'
	element :alert, 'div.alert'

	element :subject, 'input[name="subject"]'
	element :body , 'textarea[name="message"]'
	element :mailtype, 'select[name="mailtype"]'
	element :wordwrap, 'input[name="wordwrap"]'
	element :from_email, 'input[name="from"]'
	element :attachment, 'input[name="attachment"]'
	element :recipient, 'input[name="recipient"]'
	element :cc, 'input[name="cc"]'
	element :bcc, 'input[name="bcc"]'
	elements :member_groups, 'input[name^="group_"]'
	element :submit_button, 'input[type="submit"]'

	def load
		self.open_dev_menu
		click_link 'Utilities'
		# click_link 'Communicate'
	end
end