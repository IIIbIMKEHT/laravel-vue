export default class Gate{

    constructor(user){
        this.user = user;
    }


    isAdmin(){
        return this.user.type === 'admin';
    }

    isUser(){
        return this.user.type === 'user';
    }

    isAdminOrModer(){
        if(this.user.type === 'admin' || this.user.type === 'moderator'){
            return true;
        }
    }

    isModerOrUser(){
        if(this.user.type === 'user' || this.user.type === 'moderator'){
            return true;
        }
    }

}
