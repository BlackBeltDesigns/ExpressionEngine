class ControlPanelPage < SitePrism::Page

	section :main_menu, MenuSection, 'section.menu-wrap'
	element :submit_button, '.form-ctrls input.btn'
	element :submit_button_disabled, '.form-ctrls input.btn.disable'
	element :fieldset_errors, 'fieldset.invalid'
	element :settings_btn, 'b.ico.settings'
	elements :error_messages, 'em.ee-form-error-message'

	# Tables
	element :select_all, 'th.check-ctrl input'
	elements :sort_links, 'table a.sort'

	def open_dev_menu
		main_menu.dev_menu.click
	end

	def submit
		submit_button.click
	end

	def submit_enabled?
		submit_button.value != 'Fix Errors, Please' &&
		submit_button[:disabled] != true &&
		self.has_submit_button_disabled? == false
	end

	# Waits until the error message is gone before proceeding;
	# if we just check for invisible but it's already gone,
	# Capybara will complain, so we must do this
	def wait_for_error_message_count(count)
		i = 0;
		element_count = nil;
		# This is essentially our own version of wait_until_x_invisible/visible,
		# except we're not going to throw an exception if the element
		# is already gone thus breaking our test; if the element is already
		# gone, AJAX and the DOM have already done their job
		while element_count != count && i < 1000
			begin
				element_count = self.error_messages.size
			rescue
				# If we're here and we're waiting for 0 errors,
				# an exception was likely thrown because there are
				# no errors, so bail out of loop
				if count == 0
					element_count = 0
				end
			end
			sleep 0.01
			i += 1 # Prevent infinite loop
		end
	end
end