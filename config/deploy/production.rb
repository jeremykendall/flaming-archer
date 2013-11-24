set :stage, :production

role :web, %w{jeremykendall@365.jeremykendall.net}
role :app, %w{jeremykendall@365.jeremykendall.net}
set :ssh_options, {
    forward_agent: true
}
