<div id="app">
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

    {{-- карточка с формой --}}
    <div class="uk-container">
        <!-- форма регистрации -->
        <div class="uk-card uk-card-default uk-align-center uk-width-1-1 uk-width-large@s uk-card-body uk-box-shadow-small uk-flex uk-flex-middle">

            {{-- <form class="uk-align-center"action="{{ route('authencticate') }}" method="POST"> --}}
            <form class="uk-align-center" method="POST">
                {{-- <form class="uk-align-center" method="POST"> --}}
                {{ csrf_field() }}
                <h3 class="uk-card-title">@lang('main_page_content.formCaption')</h3>
                <fieldset class="uk-fieldset">
                    <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                        <span class=" uk-form-icon" uk-icon="icon: user"></span>
                        <input id="email" class="uk-input" type="email" name="email" required autofocus
                            placeholder="@lang('main_page_content.inputEmailCaption')">
                    </div>
                    <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                        <span class=" uk-form-icon" uk-icon="icon: lock"></span>
                        {{-- <input class="uk-input uk-width-1-1" type="password" placeholder="@lang('main_page_content.inputPasswordCaption')"> --}}

                        <input class="uk-input uk-width-1-1" id="password" type="password" name="password"
                            placeholder="@lang('main_page_content.inputPasswordCaption')" required>
                    </div>

                    {{-- <div class="">
                                    <label class="uk-text-meta uk-margin-top"><input class="uk-checkbox uk-text-lefts" type="checkbox" checked> Регистрируясь, Вы соглашаетесь с условиями <a href="">политики конфеденциальности</a></label>
                                </div> --}}

                    <!-- кнопка отправить -->
                    <button class="uk-button uk-width-1-1" type="submit">@lang('main_page_content.formButtonCaption')</button>
                    <!-- конец - кнопка отправить -->
                    <label>
                        <p class="uk-text-center uk-margin-medium-top">@lang('main_page_content.formDescription')<a
                                href="/login"> @lang('main_page_content.formLoginText')</a></p>
                    </label>
                </fieldset>
            </form>
        </div>
    </div>
</div>