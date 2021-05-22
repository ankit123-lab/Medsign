angular.module("medeasy")
        .config(function ($translateProvider) {
            $translateProvider.translations('en', {
                PASSWORD_ERROR_UPPERCASE: 'Please enter atleast one uppercase character',
                PASSWORD_ERROR_LOWERCASE: 'Please enter atleast one lowercase character',
                PASSWORD_ERROR_SPECIAL: 'Please enter atleast one special character',
                PASSWORD_ERROR_DIGIT: 'Please enter atleast one digit',
            });
            $translateProvider.translations('hi', {
                PASSWORD_ERROR_UPPERCASE: 'Please enter atleast one uppercase character',
                PASSWORD_ERROR_LOWERCASE: 'Please enter atleast one lowercase character',
                PASSWORD_ERROR_SPECIAL: 'Please enter atleast one special character',
                PASSWORD_ERROR_DIGIT: 'Please enter atleast one digit',
            });
            $translateProvider.useSanitizeValueStrategy('escaped');
            $translateProvider.preferredLanguage('en');
        });