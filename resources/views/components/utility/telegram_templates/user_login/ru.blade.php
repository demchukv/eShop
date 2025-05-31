    <b>📱 Новый вход на {{ $device ?? 'Linux' }}</b>
    Привет, {{ $username ?? 'User' }}👋,
    Мы сообщаем о новом входе в ваш {{ $system_settings['app_name'] }} аккаунт с {{ $device ?? 'Linux' }} устройства
    {{ ($currentDateTime ?? '') . ' ' . ($timeZone ?? '') }}.
    Усли это были вы, то проигнорируйте это сообщение.
    Если это были не вы, пожалуйста измените свой пароль в {{ $system_settings['app_name'] }} <a
        href="{{ customUrl('password-recovery') }}" style=" text-decoration: underline;">Change Password</a>.
    Спасибо,
    Команда {{ $system_settings['app_name'] }}
