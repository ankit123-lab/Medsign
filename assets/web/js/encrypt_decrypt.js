const SECRET = CryptoJS.enc.Hex.parse('253D3FB468A0E24677C28A624BE0F939');
const IV = CryptoJS.enc.Hex.parse('0');

CryptoJS.pad.ZeroPadding = {
    pad: function (data, blockSize) {
        // Shortcut
        var blockSizeBytes = blockSize * 4;
        // Pad
        data.clamp();
        data.sigBytes += blockSizeBytes - ((data.sigBytes % blockSizeBytes) || blockSizeBytes);
    },
    unpad: function (data) {
        // Shortcut
        var dataWords = data.words;
        // Unpad
        var i = data.sigBytes - 1;
        while (!((dataWords[i >>> 2] >>> (24 - (i % 4) * 8)) & 0xff)) {
            i--;
        }
        data.sigBytes = i + 1;
    }
};

function my_encrypt(string){
    if(string != undefined && string != '') {
        var encrypted = CryptoJS.AES.encrypt(string,SECRET,{iv: IV, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.ZeroPadding});
        return encrypted.ciphertext.toString(CryptoJS.enc.Base64);
    } else {
        return '';
    }
}

function my_decrypt(ciphertext){
    if(ciphertext != undefined && ciphertext != '') {
        var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(ciphertext)});
        var decrypted = CryptoJS.AES.decrypt(cipherParams,SECRET,{iv: IV, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.ZeroPadding});
        return decrypted.toString(CryptoJS.enc.Utf8);
    } else {
        return '';
    }
}