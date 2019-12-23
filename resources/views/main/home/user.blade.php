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

<div class="uk-container">
    <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <h3 class="uk-card-title">@lang('main_page_content.userPage.friends.header')</h3>
        @if(!$friends->isEmpty())
            <ul class="uk-list uk-list-divider">
                @foreach($friends as $friend)
                    <li>
                        {{ $friend->name }} | Пригласить в игру
                    </li>
                @endforeach
            </ul>
        @else
            <p>У вас нет друзей</p>
        @endif
    </div>
</div>

<div class="container">
    <private-chat-component></private-chat-component>
</div>
