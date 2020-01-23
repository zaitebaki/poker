<template>
<div class="uk-section-secondary uk-preserve-color">
        <nav class="uk-navbar-container uk-navbar-transparent uk-light">
            <div class="uk-container uk-container-expand">
                <div uk-navbar>
                    <div class="uk-navbar-left uk-margin-small-left">
                        <a class="uk-navbar-item header__logo-text" href="/">
                            <img src="/assets/images/logo.svg" alt="" class="logo__img">
                            Зайте баки
                        </a>
                        <div class="uk-navbar-item">
                            <span><img src="/assets/images/logo.svg" alt="" class="logo__img"></span>
                            <p class="uk-text uk-margin-remove header__user-name">{{ user.name }}
                            </p>
                        </div>
                        <div class="uk-navbar-item">
                            <span><img src="/assets/images/logo.svg" alt="" class="logo__img"></span>
                            <p class="uk-text uk-margin-remove header__statistic-text">{{ content.balance }}: 
                                <span>
                                    {{ user.balance }}&nbsp;
                                </span>
                            </p>
                            <svg class="logo__ruble-icon" aria-hidden="true">
                                <use xlink:href="/assets/images/game/ruble-icon.svg#Capa_1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="uk-navbar-right">
                        <div class="uk-navbar-item">
                            <div class="uk-flex">
                                <div class="uk-margin-small-right">
                                    <p class="uk-text uk-margin-remove uk-text-small header__victory-text">/в:
                                        <span>{{ user.victory }}</span>
                                    </p>
                                </div>
                                <div>
                                    <p class="uk-text uk-margin-remove uk-text-small header__gameover-text">п:
                                        <span>{{ user.gameover }}/</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="uk-navbar-item uk-margin-xlarge-right">
                            <a class="uk-navbar-toggle uk-text-warning" href="#">
                                <span uk-navbar-toggle-icon></span>
                            </a>
                            <div class="uk-navbar-dropdown">
                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                    <li class="uk-active"><a href="#">Active</a></li>
                                    <li><a href="#">Item</a></li>
                                    <li><a href="#">Item</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <!-- <li class="uk-active"><a href="#">Active</a></li>
                        <li>
                            <a href="#">Parent</a>
                            <div class="uk-navbar-dropdown">
                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                    <li class="uk-active"><a href="#">Active</a></li>
                                    <li><a href="#">Item</a></li>
                                    <li><a href="#">Item</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="#">Item</a></li> -->
                    <!-- </ul> -->

                </div>
            </div>
        </nav>
    <!-- </div> -->

</div>
<!-- навигационная панель в header'e -->
<!-- <div class="uk-panel uk-light uk-padding-small uk-margin-medium-bottom"> -->
    <!-- <div class="uk-navbar-container" uk-navbar>
        <div class="uk-navbar-left">
            <a class="uk-navbar-toggle" uk-navbar-toggle-icon href=""></a>
        </div>
    </div> -->
    <!-- основной контейнер навигационной панели -->
    <!-- <div class="uk-container uk-align-center"> -->
        <!-- <nav class="uk-navbar-container" uk-navbar> -->
            <!-- логотип с описанием -->
            <!-- <div class="uk-navbar-left">
                <ul class="uk-navbar-nav">
                    <li>
                        <a href="/">
                            <img src="/assets/images/logo.svg" alt="" class="logo__img">
                        </a>
                    <li>
                    <li>
                        <a href="/" class="uk-link header__logo-text">
                            {{ content.shortNameProject }}
                        </a>
                    </li>
                    <li>
                        <p class="uk-text uk-margin-remove header__statistic-text">{{ content.balance }}: 
                            <span>
                                {{ user.balance }}
                            </span>
                        </p>
                    </li>
                    <li>
                        <div class="uk-flex">
                            <div class="uk-margin-small-right">
                                <p class="uk-text uk-text-small header__victory-text">В
                                    <span>10</span>
                                </p>
                            </div>
                            <div>
                                <p class="uk-textuk-text-small header__gameover-text">П
                                    <span>20</span>
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div> -->

            <!-- <div>
                <div class="uk-width-expand">
                    <p class="uk-text-right">Валера</p>
                </div>
                
            </div> -->
        <!-- </nav> -->
        <!-- конец - логотип с описанием -->
    <!-- </div> -->
    <!-- конец -основной контейнер навигационной панели -->
<!-- </div> -->
</template>

<script>
export default {
    props: {
        content: Object,
        user: Object
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            balance: 100
        }
    }
}
</script>