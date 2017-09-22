require './bootstrap.rb'

feature 'Fluid Block Field' do
  before(:each) do
    cp_session
    @list = ChannelFields.new
    @page = ChannelFieldForm.new
    @page.load
    no_php_js_errors
  end

  it 'creates a fluid block field' do
    @page.field_type.select 'Fluid Block'
    @page.field_label.set 'Fluid Block Body'
    @page.field_name.set 'fluid_block_body'
    find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").click
    @page.submit

    @list.alert.has_content?('The field Fluid Block Body has been').should == true

    @list.fields.any? { |f| f.text.include?('Fluid Block Body') }.should == true
    @list.fields.any? { |f| f.text.include?('{fluid_block_body}') }.should == true

    @page.load_edit_for_custom_field('Fluid Block Body')
    @page.field_type.value.should eq 'fluid_block'
    @page.field_label.value.should eq 'Fluid Block Body'
    @page.field_name.value.should eq 'fluid_block_body'
    find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").checked?.should == true
    find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").checked?.should == true
    find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").checked?.should == true
    find("input[type='checkbox'][name='field_channel_fields[]'][value='4']").checked?.should == false
    find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").checked?.should == true
    find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").checked?.should == true
    find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").checked?.should == true
  end

  context 'when editing a fluid block field' do
    it 'can add a new field to the block' do
      @page.field_type.select 'Fluid Block'
      @page.field_label.set 'Fluid Block Body'
      @page.field_name.set 'fluid_block_body'
      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").click
      @page.submit

      @list.alert.has_content?('The field Fluid Block Body has been').should == true

      @page.load_edit_for_custom_field('Fluid Block Body')
      # confirm our state
      find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").checked?.should == false
      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='4']").checked?.should == false
      find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").checked?.should == true

      find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").click
      @page.submit

      @page.load_edit_for_custom_field('Fluid Block Body')
      find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='4']").checked?.should == false
      find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").checked?.should == true
    end

    it 'can remove a field from the block' do
      @page.field_type.select 'Fluid Block'
      @page.field_label.set 'Fluid Block Body'
      @page.field_name.set 'fluid_block_body'
      find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").click
      find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").click
      @page.submit

      @list.alert.has_content?('The field Fluid Block Body has been').should == true

      @page.load_edit_for_custom_field('Fluid Block Body')
      # confirm our state
      find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='4']").checked?.should == false
      find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").checked?.should == true

      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").click
      @page.submit
      @page.wait_for_modal_submit_button
      @page.modal_submit_button.click

      @page.load_edit_for_custom_field('Fluid Block Body')
      find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").checked?.should == false
      find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='4']").checked?.should == false
      find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").checked?.should == true
      find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").checked?.should == true
    end
  end

  it 'deletes a fluid block field' do
    @page.field_type.select 'Fluid Block'
    @page.field_label.set 'Fluid Block Body'
    @page.field_name.set 'fluid_block_body'
    find("input[type='checkbox'][name='field_channel_fields[]'][value='1']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='2']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='3']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='5']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='6']").click
    find("input[type='checkbox'][name='field_channel_fields[]'][value='7']").click
    @page.submit

    @list.alert.has_content?('The field Fluid Block Body has been').should == true

    @list.fields_checkboxes[7].click
    @list.wait_for_bulk_action

    @list.has_bulk_action?.should == true
    @list.has_action_submit_button?.should == true

    @list.bulk_action.select 'Remove'
    @list.action_submit_button.click

    @list.wait_for_modal_submit_button
    @list.modal_submit_button.click

    @list.fields[0].text.should_not include 'Fluid Block Body'
  end

end
