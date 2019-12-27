<!-- навигационная панель в header'e -->
<div class="uk-section-primary uk-panel uk-light uk-padding-small uk-margin-medium-bottom">
    <!-- основной контейнер навигационной панели -->
    <div class="uk-container uk-container-small uk-align-center">
        <!-- логотип с описанием -->
        <div class="logo">
            <h4 class="uk-text-center uk-padding-remove uk-margin-remove">
                <a href="/" class="uk-link-heading">
                    <img src="/assets/images/logo.svg" alt="" class="logo__img">
                    @lang('main_page_content.nameProject')
                </a>
            </h4>
        </div>
        <!-- конец - логотип с описанием -->
    </div>
    <!-- конец -основной контейнер навигационной панели -->
</div>
<!-- конец - навигационная панель в header'е -->

<view-friends-component
    :content="{{ json_encode(__('main_page_content.userPage.friends'), JSON_UNESCAPED_UNICODE) }}"
    :friends="{{ $friends }}"
    :user="{{ Auth::user() }}">
</view-friends-component>