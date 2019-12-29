<main-home-component
    :name-project="{{ json_encode(__('main_page_content.nameProject'), JSON_UNESCAPED_UNICODE) }}"
    :content="{{ json_encode(__('main_page_content.userPage.friends'), JSON_UNESCAPED_UNICODE) }}"
    :invitation-card-content="{{ json_encode(__('main_page_content.userPage.invitationCard'), JSON_UNESCAPED_UNICODE) }}"
    :friends="{{ $friends }}"
    :user="{{ Auth::user() }}"
    :form-start-game-route="{{ json_encode(route('startGame'), JSON_UNESCAPED_UNICODE) }}">
</main-home-component>