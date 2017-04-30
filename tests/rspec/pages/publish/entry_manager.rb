class EntryManager < ControlPanelPage
  set_url '/system/index.php?/cp/publish/edit{&perpage}{&filter_by_channel}'

  elements :entry_rows, '.w-16 .tbl-ctrls form table tbody tr'
  elements :entry_checkboxes, '.w-16 .tbl-ctrls form table tbody tr input[type="checkbox"]'

  # Create a number of entries
  #
  # @param [Number] n = 10 Set a specific number of entries to create, defaults
  #   to 10
  # @return [void]
  def create_entries(n = 10, channel = 1)
    command = "cd fixtures && ruby entries.rb\
      --db-name #{$test_config[:db_name]}\
      --db-username #{$test_config[:db_username]}\
      --number #{n}"

    if $test_config[:db_password].empty?
      command += "--db-password #{$test_config[:db_password]}"
    end

    command += " #{channel}"

    system(command)
  end

  def create_channel(opts)
    command = "cd fixtures && ruby channels.rb\
      --db-name #{$test_config[:db_name]}\
      --db-username #{$test_config[:db_username]}"

    # include opts, change _ in hash symbols to - to standardize CLI behavior
    opts.each do |key, val|
      key = key.to_s.gsub('_', '-')
      command += " --#{key} #{val}"
    end

    if ! $test_config[:db_password].empty?
      command += " --db-password #{$test_config[:db_password]}"
    end

    channel_json = nil
    Open3.popen3(command) do |stdin, stdout, stderr, thread|
      channel_json = stdout.read
    end
  end
end
