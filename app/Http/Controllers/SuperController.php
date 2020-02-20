<?php

namespace App\Http\Controllers;

class SuperController extends Controller
{
    /**
     * @var $layout
     * хранит данные шаблона макета
     */
    protected $layout;

    /**
     * @var $contentTemplate
     * хранит данные шаблона контентной части
     */
    protected $contentTemplate;

    /**
     * @var $title
     * заголовок текущей страницы
     */
    protected $title;

    /**
     * @var $vars
     * массив переменных, которые передаются в шаблон
     */
    protected $vars;

    /**
     * @var $content
     * хранит контент отображаемого вида
     */
    protected $content = false;

    /**
     * @var $user
     * данные аутентифицированного пользователя
     */
    protected $user;

    /**
     * Description
     * @return type
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    /**
     * Сгенерировать вид
     * @return type
     */
    public function renderOutput()
    {
        // передаем заголовок
        $this->vars = array_add($this->vars, 'title', $this->title);

        // передача в шаблон контентной части
        if ($this->content) {
            $this->vars = array_add($this->vars, 'content', $this->content);
        }

        return view($this->layout)->with($this->vars);
    }
}
