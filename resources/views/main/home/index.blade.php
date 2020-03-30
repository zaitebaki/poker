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
<login-and-registration-component type-form={{ session('typeForm') }}
  :props-array="{{ json_encode(__('main_page_content.startPage'), JSON_UNESCAPED_UNICODE) }}"
  :form-route-login="{{ json_encode(route('authenticate'), JSON_UNESCAPED_UNICODE) }}"
  :form-route-registration="{{ json_encode(route('registration'), JSON_UNESCAPED_UNICODE) }}"
  old-login="{{ old('login') }}" old-reg-login="{{ old('reg-login') }}" old-name="{{ old('name') }}"
  old-errors="{{ $errors }}">
</login-and-registration-component>
