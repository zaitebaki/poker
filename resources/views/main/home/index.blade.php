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

    <login-and-registration-component :props-array="{{ json_encode(__('main_page_content.startPage')) }}"></login-and-registration-component>

</div>