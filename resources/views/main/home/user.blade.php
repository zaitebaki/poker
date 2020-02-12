<main-home-component
    :name-project="{{ json_encode(__('main_page_content.nameProject'), JSON_UNESCAPED_UNICODE) }}"
    :content="{{ json_encode(__('main_page_content.userPage.friends'), JSON_UNESCAPED_UNICODE) }}"
    :invitation-card-content="{{ json_encode(__('main_page_content.userPage.invitationCard'), JSON_UNESCAPED_UNICODE) }}"
    :friends="{{ $friends }}"
    :user="{{ Auth::user() }}"
    :content-payments="{{ json_encode(__('main_page_content.userPage.payments'), JSON_UNESCAPED_UNICODE) }}"
    :payments="{{ json_encode($payments, JSON_UNESCAPED_UNICODE) }}"
    :form-join-game-route="{{ json_encode(route('invitationMessage'), JSON_UNESCAPED_UNICODE) }}"
    :cancel-payment-route="{{ json_encode(route('cancelPayment'), JSON_UNESCAPED_UNICODE) }}"
    status="{{ session('status') }}"
    session-status-user-login="{{ session('sessionStatusUserLogin') }}">
</main-home-component>