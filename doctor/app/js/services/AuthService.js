/*
 * Service Name: AuthService
 * Use: This service is used for storing/retriving Logged in user data from Localstorage
 *
 */
angular
        .module("app.login")
        .service('AuthService', function ($localStorage) {
            /* Login function */
            return {
                login: function (user_data, access_token) {
                    $localStorage.currentUser = {};
                    $localStorage.currentUser = user_data;
                    $localStorage.currentUser.access_token = access_token;
                },
                logout: function () {
                    $localStorage.currentUser = {};
                    $localStorage.sidebar = '';
                    $localStorage.role_type = '';
                },
                isLoggedIn: function () {
                    if ($localStorage.currentUser) {
                        if ($localStorage.currentUser.user_id != undefined) {
                            return true;
                        }
                    }
                    return false;
                },
                currentUser: function () {
                    return $localStorage.currentUser;
                },
                setSideMenu: function (sidemenu,role,role_type) {
                    $localStorage.sidebar = sidemenu;
                    $localStorage.role = role;
                    $localStorage.role_type = role_type;
                },
                getSideMenuFromLocal: function () {
                    return $localStorage.sidebar;
                },
                getRoleFromLocal: function () {
                    return $localStorage.role;
                }
            };
        });