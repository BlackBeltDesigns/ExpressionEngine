class ChannelLayoutForm < ControlPanelPage
  set_url_matcher /channel\/layouts/

  element :heading, 'div.col.w-12 div.box.publish h1'
  element :add_tab_button, 'a.btn.add-tab'

  elements :tabs, 'ul.tabs li a'
  element :publish_tab, 'ul.tabs li:first-child a'
  element :date_tab, 'ul.tabs li:nth-child(2) a'
  element :hide_date_tab, 'ul.tabs li:nth-child(2) span'
  element :categories_tab, 'ul.tabs li:nth-child(3) a'
  element :hide_categories_tab, 'ul.tabs li:nth-child(3) span'
  element :options_tab, 'ul.tabs li:nth-child(4) a'
  element :hide_options_tab, 'ul.tabs li:nth-child(4) span'

  elements :fields, 'div.tab fieldset.col-group'

  # Layout Options
  element :layout_name, 'form fieldset input[name=layout_name]'
  elements :member_groups, 'form fieldset input[name="member_groups[]"]'
  element :submit_button, 'form fieldset.form-ctrls input[type=submit]'

  element :add_tab_modal, 'div.modal-add-new-tab', visible: false
  element :add_tab_modal_tab_name, 'div.modal-add-new-tab input[name="tab_name"]', visible: false
  element :add_tab_modal_submit_button, 'div.modal-add-new-tab .form-ctrls .btn', visible: false

  def move_tool(node)
    return node.find('.layout-tools .toolbar .move a')
  end

  def visibiltiy_tool(node)
    tools = node.all('.layout-tools .toolbar li')
    if tools.length > 1 then
      return tools[1]
    end

    return nil
  end

  def minimize_tool(node)
    return node.find('.setting-txt h3 span')
  end

  def load
    self.create(1)
  end

    def create(number)
    visit '/system/index.php?/cp/channels/layouts/create/' + number.to_s
    end

    def edit(number)
    visit '/system/index.php?/cp/channels/layouts/edit/' + number.to_s
    end

end
