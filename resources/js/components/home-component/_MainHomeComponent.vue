<template>
<header>
    <header-component
        :name-project="nameProject">
    </header-component>
    <view-friends-component
        :content="content"
        :invitation-card-content="invitationCardContent"
        :friends="friends"
        :user="user"
    ></view-friends-component>
</header>
</template>

<script>
import HeaderComponent from './HeaderComponent'
import ViewFriendsComponent from './ViewFriendsComponent'

export default {
    props: {
        nameProject: String,
        content: Object,
        invitationCardContent: Object,
        friends: Array,
        user: Object,
        formStartGameRoute: String
    },

    components: {
        'header-component': HeaderComponent,
        'view-friends-component': ViewFriendsComponent
    }
}
</script>