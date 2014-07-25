require './bootstrap.rb'

feature 'Statistics' do

	before(:each) do
		cp_session
		@page = Stats.new
		@page.load

		@page.should be_displayed
		@page.title.text.should eq 'Manage Statistics'
		@page.should have_content_table
		@page.should have_bulk_action
		@page.should have_action_submit_button
	end

	it "shows the Manage Statistics page" do
		@page.should have(4).rows # 3 rows + header
		@page.sources.map {|source| source.text}.should == ["Channel Entries", "Members", "Sites"]
		@page.counts.map {|count| count.text}.should == ["10", "1", "1"]
	end

	it "can sort by source" do
		@page.all('a.sort')[0].click
		@page.sources.map {|source| source.text}.should == ["Sites", "Members", "Channel Entries"]
		@page.content_table.find('th.highlight').text.should eq 'Source'

		@page.all('a.sort')[0].click
		@page.sources.map {|source| source.text}.should == ["Channel Entries", "Members", "Sites"]
		@page.content_table.find('th.highlight').text.should eq 'Source'
	end

	it "can sort by count" do
		@page.all('a.sort')[1].click
		@page.counts.map {|count| count.text}.should == ["10", "1", "1"]
		@page.content_table.find('th.highlight').text.should eq 'Record Count'

		@page.all('a.sort')[1].click
		@page.counts.map {|count| count.text}.should == ["1", "1", "10"]
		@page.content_table.find('th.highlight').text.should eq 'Record Count'
	end

	it "can sync one source" do
		@page.content_table.find('tr:nth-child(2) li.sync a').click

		@page.should have_alert
		@page.should have_css('div.alert.success')
	end

	it "can sync multiple sources" do
		@page.find('input[type="checkbox"][title="select all"]').set(true)
		@page.bulk_action.select "Sync"
		@page.action_submit_button.click

		@page.should have_alert
		@page.should have_css('div.alert.success')
	end

end