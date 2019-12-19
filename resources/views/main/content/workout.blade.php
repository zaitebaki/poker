{{-- главный экран --}}
<!-- ============================================================================== -->
<!-- навигационная панель в header'e -->
<div id="app">
	<div class="uk-section-primary uk-panel uk-light uk-padding-small">
		<!-- основной контейнер навигационной панели -->
		<div class="uk-container uk-container-large">
			<div uk-grid>
				<div uk-grid class="uk-grid-collapse">
					<!-- логотип с описанием -->
					<div>
						<a href="{{ route('startPage') }}" class="uk-text-large"><span class="uk-margin-small-right uk-icon" uk-icon="icon: github-alt; ratio: 2.2"></span>neirohabio</a>
						</div>
					<div class="uk-text-large" style="color: white;">
						<p>&#160;|&#160;</p>
					</div>
					<div>
						<a href="{{ route('startPage') }}" class="uk-text-large uk-text-right">личный кабинет</a>
					</div>
					<!-- конец логотип с описанием -->
				</div>
				<div class="uk-width-expand uk-margin-medium-right">
					<div class="uk-flex uk-flex-right">
						<div class="uk-margin-right">
							{{-- <a class="uk-text-right uk-text-large">{{ Auth::user()->name }}</a> --}}
						</div>
						<div class="uk-inline uk-text-muted">
							<div><a href="" class="uk-text-large"><span uk-icon="menu"></span></a></div>
							<div uk-dropdown="mode: click">
								<ul class="uk-list">
									{{-- <li><a href="{{ route('DictionariesIndex') }}" style="color: #999">Список словарей</a></li> --}}
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		<!-- конец -основной контейнер навигационной панели -->
		</div>
	<!-- конец - навигационная панель в header'е -->
	</div>

	{{-- панели для работы со словарями --}}
	<div class="uk-container uk-container-large">
		{{-- панели для работы со словарями --}}
		<div class="uk-margin-small-top" uk-grid>

	{{-- 		<div class="uk-width-1-4">
				<div class="uk-card uk-card-default uk-card-body"><h5>Режим тренировки</h5>
					<ul class="uk-list">
						<li>Режим "Карточка"</li>
						<li>Режим "Повтори ввод"</li>
					</ul>
				</div>
			</div> --}}

			<current-dictionary-component cur-directory="{{ $curDictionary ? $curDictionary->title : 'не назначен' }}"> </current-dictionary-component>

			<training-field-component cur-directory="{{ $curDictionary ? 'Словарь «' . $curDictionary->title .'»' : 'не назначен'}}"></training-field-component>

			{{-- <flashcard-component cur-directory="{{ $curDictionary ? 'Словарь «' . $curDictionary->title .'»' : 'не назначен'}}"></flashcard-component> --}}
			{{-- <enter-word-component></enter-word-component> --}}

			{{-- панель со списком доступных словарей --}}
			<div class="uk-width-1-4">
				<div class="uk-card uk-card-default uk-card-body">
					<h5>Доступные словари</h5>

					@if(!$dictionaries->isEmpty())
						<ul class="uk-list">
							@foreach($dictionaries as $dictionary)
								<li><a href="{{ route('workoutDictionaryController', ['dict_alias' => $dictionary->alias]) }}">{{ $dictionary->title }}</a></li>
							@endforeach
						</ul>
					@else
						<p>Для данного пользователя словари недоступны</p>
					@endif

				</div>
			{{-- конец - панель со списком доступных словарей --}}
			</div>


		{{-- конец - панели для работы со словарями --}}
		</div>
	{{-- конец - панели для работы со словарями --}}
	</div>
</div>
