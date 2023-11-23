<script setup>
  if(window.isSecureContext) {
    console.log('Using secure context.');
  }

  function ab2str(buf) {
    return String.fromCharCode.apply(null, new Uint8Array(buf));
  }
</script>

<template>
  <div class="home">
    Upload a file!

    <h1>Development Todo</h1>

    <ul style="width: 250px; margin: 0 auto; text-align: left">
      <li><s>Generate / Load a keypair</s></li>
      <li><s>Test signing arbitrary data</s></li>
      <li>Sign a challenge from the API</li>
      <li>Actually upload a file!!!</li>
      <li>Implement user registration</li>
    </ul>

    <hr style="margin: 1em 0" />

    <button @click="fetchChallenge">Fetch API Challenge</button>
  </div>
</template>

<script>
/*
Convert an ArrayBuffer into a string
from https://developer.chrome.com/blog/how-to-convert-arraybuffer-to-and-from-string/
*/
function ab2str(buf) {
  return String.fromCharCode.apply(null, new Uint8Array(buf));
}

export default {
  name: 'Home',
  components: {
  },

  methods: {
    async generateKeyPair() {
      const keyPair = await crypto.subtle.generateKey(
        {
          name: "RSA-PSS",
          modulusLength: 4096,
          publicExponent: new Uint8Array([1, 0, 1]),
          hash: "SHA-256",
        },

        true, // TODO: SET THIS BACK TO FALSE!!! // Don't allow this key to be exported

        ["sign", "verify"] // Permissions needed by this key
      );

        return keyPair;
    },

    async getPubkey(keyPair) {
      const exported = await window.crypto.subtle.exportKey(
       "spki",
       keyPair.publicKey
     );

      const exportedAsString = ab2str(exported);
      const exportedAsBase64 = window.btoa(exportedAsString);

      return `-----BEGIN PUBLIC KEY-----\n${exportedAsBase64}\n-----END PUBLIC KEY-----`;
    },

    async fetchChallenge() {
      const keyPair = await this.generateKeyPair();
      const pubkey = await this.getPubkey(keyPair);

      const options = {
        method: "POST",
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({pubkey}),
      };

      const response = await fetch("http://upload.local/api/v1/challenge", options);
      const challenge = await response.json();

      console.log(challenge);
    },
  },

  async created() {
    /*
    let encoder = new TextEncoder();
    let encodedMessage = encoder.encode("hello world");

    let signature = await window.crypto.subtle.sign(
      {
        name: "RSA-PSS",
        saltLength: 32,
      },
      keyPair.privateKey,
      encodedMessage
    );

    let buffer = new Uint8Array(signature, 0, 512);

    console.log("Key Pair", keyPair);

    const signatureAsString = ab2str(buffer);
    const signatureAsBase64 = window.btoa(signatureAsString);

    console.log("Generated signature", signatureAsBase64);

    const exported = await window.crypto.subtle.exportKey(
       "pkcs8",
       keyPair.privateKey
     );

     const exportedAsString = ab2str(exported);
     const exportedAsBase64 = window.btoa(exportedAsString);
     const pemExported = `-----BEGIN PRIVATE KEY-----\n${exportedAsBase64}\n-----END PRIVATE KEY-----`;

     console.log(pemExported);
    */
  },
}
</script>
