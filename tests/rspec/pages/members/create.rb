module Member
  # Member Registration
  class Create < ControlPanelPage
    set_url '/system/index.php?/cp/members/create'

    element :member_group, 'select[name=group_id]'
    element :username, 'input[name=username]'
    element :email, 'input[name=email]'
    element :password, 'input[name=password]'
    element :confirm_password, 'input[name=confirm_password]'
  end
end
