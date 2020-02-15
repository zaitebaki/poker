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
                        <ul class="uk-navbar-nav">
                            <li>
                                <a class="uk-navbar-toggle uk-text-warning" href="#">
                                    <span uk-navbar-toggle-icon></span>
                                </a>
                                <div class="uk-navbar-dropdown">
                                    <ul class="uk-nav uk-navbar-dropdown-nav">
                                        <li><a href="#">Статистика</a></li>
                                        <li>
                                            <a 
                                                href="#"
                                                v-on:click="finishGameSession">
                                                Закончить игру</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
</template>

<script>
export default {
    props: {
        content: Object,
        user: Object,
        roomId: String,
        roomName: String
    },
    data() {
        return {
            roomUrl: '/game/room/' + this.roomId + '/finish_game_session',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    },

    mounted() {
        console.log(this.roomUrl);
    },

    methods: {
        // Закончить сеанс игры
        finishGameSession() {
            axios.post(this.roomUrl, {}).then( (response) => {
                console.log(response.data);
                window.location.replace('/home');
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        }
    }
}
</script>