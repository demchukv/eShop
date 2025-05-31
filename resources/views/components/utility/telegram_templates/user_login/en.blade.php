    <b>📱 A new sign-in on {{ $device ?? 'Linux' }}</b>
    Hi {{ $username ?? 'User' }}👋,
    We noticed a new sign-in to your {{ $system_settings['app_name'] }} Account from a {{ $device ?? 'Linux' }} device
    on
    {{ ($currentDateTime ?? '') . ' ' . ($timeZone ?? '') }}.
    If this was you, please disregard this message. No further action is needed.
    If this wasn't you, please change your password in the {{ $system_settings['app_name'] }} <a
        href="{{ customUrl('password-recovery') }}" style=" text-decoration: underline;">Change Password</a>.
    Thanks,
    Team {{ $system_settings['app_name'] }}
