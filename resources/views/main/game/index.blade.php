<game-component :content="{{ json_encode(__('main_page_content.gamePage'), JSON_UNESCAPED_UNICODE) }}"
  :user="{{ Auth::user() }}" :game-parameters="{{ json_encode($gameParameters, JSON_UNESCAPED_UNICODE) }}">
</game-component>
