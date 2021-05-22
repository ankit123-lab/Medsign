/* Generic Services */
angular.module('medeasy').service("EncryptDecrypt", function ($rootScope) {
	return {
		my_encrypt: function (string) {
			var encrypted = CryptoJS.AES.encrypt(string,$rootScope.app.secretKey,{iv: $rootScope.app.iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.ZeroPadding});
			return encrypted.ciphertext.toString(CryptoJS.enc.Base64);
		},
		my_decrypt: function (ciphertext) {
			try {
				var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(ciphertext)});
				var decrypted = CryptoJS.AES.decrypt(cipherParams,$rootScope.app.secretKey,{iv: $rootScope.app.iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.ZeroPadding});
				return decrypted.toString(CryptoJS.enc.Utf8);
			} catch (ex) {
				return '';
			}
		}
	}}
);