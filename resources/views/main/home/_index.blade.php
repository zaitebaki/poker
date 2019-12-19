{{-- главный экран --}}
<!-- ============================================================================== -->
<!-- навигационная панель в header'e -->
<div class="uk-section-primary uk-panel uk-light uk-padding-small">
	<!-- основной контейнер навигационной панели -->
	<div class="uk-container uk-container-large">
		<div uk-grid>

			<!-- логотип с описанием -->
			<div class="uk-width-2-3">
				<a href="#" style="text-decoration: none;" class="uk-text-large"><span
						class="uk-margin-small-right uk-icon" uk-icon="icon: github-alt; ratio: 2.2"></span>
						@lang('main_page_content.nameProject')</a>
			</div>
			<!-- конец - логотип с описанием -->

			<!-- кнопка войти -->
			@auth
				<div class="uk-width-expand uk-text-center uk-margin-medium-right">
					{{-- <p>valera</p> --}}
					{{-- <form id="logout-form" class="uk-form" action="{{ route('logout') }}" method="POST" style="display: none;"> --}}
						{{-- @csrf --}}
						{{-- <button class="uk-button">Выйти</button> --}}
						{{-- <button class="uk-button uk-button-default" href="/logout">Выйти</a> --}}
						{{-- <button class="uk-button uk-width-1-1" type="submit">Выйти</button> --}}

						<form class="uk-form" action="{{ route('logout') }}" method="POST">
							@csrf
							<fieldset data-uk-margin>
								<button class="uk-button" type="submit">Выйти</button>
							</fieldset>
						
						</form>
					</form>
				</div>
			@endauth
			
			{{-- <div class="uk-width-expand uk-text-center uk-margin-medium-right"> --}}
				{{-- <div uk-grid> --}}
				{{-- <a class="uk-button uk-button-default" href="{{ route('adminIndex') }}">Админка</a> --}}
				{{-- <a class="uk-button uk-button-default" href="/">Админка</a> --}}
				{{-- <a class="uk-button uk-button-default" href="{{ url('/workout') }}">Войти</a> --}}
				{{-- </div> --}}
			
			<!-- конец - кнопка войти -->

		</div>
		<!-- конец -основной контейнер навигационной панели -->
	</div>
	<!-- конец - навигационная панель в header'е -->
</div>

<!-- основной контейнер главного экрана -->
<div class="uk-container uk-container-large">
	<div class="uk-grid-match uk-grid-small" uk-grid>
		<div class="uk-width-2-3 uk-padding-large uk-flex uk-flex-middle">
			<h2 class="uk-text-center uk-text-muted">@lang('main_page_content.nameProject') — @lang('main_page_content.mainHeader')</h2>
			<h4 class="uk-text-center uk-text-muted">@lang('main_page_content.mainDescription')</h4>
		</div>

		<!-- 2-ая колока главного экрана с формой регистрации -->
		<div class="uk-width-expand uk-padding">

			<!-- форма регистрации -->
			<div class="uk-card uk-card-default uk-card-body uk-box-shadow-large uk-flex uk-flex-middle"
				style="padding-top: 0px">

				<form class="uk-align-center" action="{{ route('authencticate') }}" method="POST">
				{{-- <form class="uk-align-center" method="POST"> --}}
					{{ csrf_field() }}
					<h3 class="uk-card-title">@lang('main_page_content.formCaption')</h3>
					<fieldset class="uk-fieldset">
						<div class="uk-inline uk-width-1-1 uk-margin-bottom">
							<span class=" uk-form-icon" uk-icon="icon: user"></span>
							<input id="email" class="uk-input" type="email" name="email" required autofocus placeholder="@lang('main_page_content.inputEmailCaption')">
						</div>
						<div class="uk-inline uk-width-1-1 uk-margin-bottom">
							<span class=" uk-form-icon" uk-icon="icon: lock"></span>
							{{-- <input class="uk-input uk-width-1-1" type="password" placeholder="@lang('main_page_content.inputPasswordCaption')"> --}}

							<input class="uk-input uk-width-1-1" id="password" type="password" name="password" placeholder="@lang('main_page_content.inputPasswordCaption')" required>
						</div>

						{{-- <div class="">
							<label class="uk-text-meta uk-margin-top"><input class="uk-checkbox uk-text-lefts" type="checkbox" checked> Регистрируясь, Вы соглашаетесь с условиями <a href="">политики конфеденциальности</a></label>
						</div> --}}

						<!-- кнопка отправить -->
						<button class="uk-button uk-width-1-1" type="submit">@lang('main_page_content.formButtonCaption')</button>
						<!-- конец - кнопка отправить -->
						<label>
							<p class="uk-text-center uk-margin-medium-top">@lang('main_page_content.formDescription')<a href="/login"> @lang('main_page_content.formLoginText')</a></p>
						</label>

						<ul class="uk-grid-small uk-grid uk-text-centers uk-flex uk-flex-center" uk-grid="">
							<li><a class="uk-icon-button uk-icon" href="#" uk-icon="icon: facebook"></a></li>
							<li><a class="uk-icon-button uk-icon" href="#" uk-icon="icon: twitter"></a></li>
							<li><a class="uk-icon-button uk-icon" href="#" uk-icon="icon: google-plus"></a></li>
						</ul>
						{{-- <div class="uk-margin-top">
							<label class="uk-text-small uk-margin-top"><input class="uk-checkbox uk-text-lefts" type="checkbox" checked> Регистрируясь, я соглашаюсь с условиями <br /><a href="">Политики конфедециальности</a></label>
						</div> --}}

					</fieldset>
				</form>
			</div>
			<!-- конец - форма регистрации -->
		</div>
		<!-- конец 2-ая колока главного экрана с формой регистрации -->
	</div>
	<!-- конец - сетка главного контейнера -->
</div>
<!-- конец - основной контейнер главного экрана -->

<hr>
<!-- конец - главный экран -->

{{-- @foreach ($dictionary as $element)
		<a href="{{ route('portfolios.show',['alias' => $element->filter_alias]) }}">{{ $element->filter->title .
		' ' . $element->img->par2  }}</a>
@endforeach --}}